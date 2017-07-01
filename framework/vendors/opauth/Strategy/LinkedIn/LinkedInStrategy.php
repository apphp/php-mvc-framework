<?php
/**
 * LinkedIn strategy for Opauth
 * based on https://developer.linkedin.com/documents/authentication
 *
 * More information on Opauth: http://opauth.org
 *
 * @copyright    Copyright © U-Zyn Chua (http://uzyn.com)
 * @link         http://opauth.org
 * @package      Opauth.LinkedInStrategy
 * @license      MIT License
 */

/**
 * LinkedIn strategy for Opauth
 * based on https://developer.linkedin.com/documents/authentication
 *
 * @package			Opauth.LinkedIn
 */
class LinkedInStrategy extends OpauthStrategy{

	/**
	 * Compulsory config keys, listed as unassociative arrays
	 */
	public $expects = array('api_key', 'secret_key');

	/**
	 * Optional config keys, without predefining any default values.
	 */
	public $optionals = array('redirect_uri', 'scope', 'state', 'response_type');

	/**
	 * Optional config keys with respective default values, listed as associative arrays
	 * eg. array('scope' => 'email');
	 */
	public $defaults = array(
		'redirect_uri' => '{complete_url_to_strategy}oauth2callback',
		'response_type' => 'code'
	);

	/**
	 * Auth request
	 */
	public function request(){
		$url = 'https://www.linkedin.com/uas/oauth2/authorization';

		$params = array();

		$params = array(
			'client_id' => $this->strategy['api_key'],
			'state' => sha1(time())
		);

		foreach ($this->optionals as $key){
			if (!empty($this->strategy[$key])) {
				$params[$key] = $this->strategy[$key];
			}
		}

		$this->clientGet($url, $params);
	}

	/**
	 * Internal callback, after OAuth
	 */
	public function oauth2callback(){
		if (array_key_exists('code', $_GET) && !empty($_GET['code'])){
			$code = $_GET['code'];
			$url = 'https://www.linkedin.com/uas/oauth2/accessToken';

			$params = array(
				'grant_type' => 'authorization_code',
				'code' => $code,
				'client_id' => $this->strategy['api_key'],
				'client_secret' => $this->strategy['secret_key'],
				'redirect_uri' => $this->strategy['redirect_uri'],
			);
			$response = $this->serverPost($url, $params, null, $headers);

			$results = json_decode($response);

			if (!empty($results) && !empty($results->access_token)){
				$profile = $this->getProfile($results->access_token);

				$this->auth = array(
					'uid' => $profile['id'],
					'info' => array(),
					'credentials' => array(
						'token' => $results->access_token,
						'expires' => date('c', time() + $results->expires_in)
					),
					'raw' => $profile
				);

				$this->mapProfile($profile, 'formatted-name', 'info.name');
				$this->mapProfile($profile, 'first-name', 'info.first_name');
				$this->mapProfile($profile, 'last-name', 'info.last_name');
				$this->mapProfile($profile, 'email-address', 'info.email');
				$this->mapProfile($profile, 'headline', 'info.headline');
				$this->mapProfile($profile, 'summary', 'info.description');
				$this->mapProfile($profile, 'location.name', 'info.location');
				$this->mapProfile($profile, 'picture-url', 'info.image');
				$this->mapProfile($profile, 'public-profile-url', 'info.urls.linkedin');
				$this->mapProfile($profile, 'site-standard-profile-request.url', 'info.urls.linkedin_authenticated');

				$this->callback();
			}
			else{
				$error = array(
					'code' => 'access_token_error',
					'message' => 'Failed when attempting to obtain access token',
					'raw' => array(
						'response' => $response,
						'headers' => $headers
					)
				);

				$this->errorCallback($error);
			}
		}
		else{
			$error = array(
				'code' => 'oauth2callback_error',
				'raw' => $_GET
			);

			$this->errorCallback($error);
		}
	}

	/**
	 * Queries LinkedIn API for user info
	 *
	 * @param string $access_token
	 * @return array Parsed JSON results
	 */
	private function getProfile($access_token){
		if (empty($this->strategy['profile_fields'])) {
			$this->strategy['profile_fields'] = array('id', 'first-name', 'last-name', 'maiden-name', 'formatted-name', 'headline', 'industry', 'summary', 'email-address', 'picture-url', 'location:(name)', 'public-profile-url', 'site-standard-profile-request');
		}

		if (is_array($this->strategy['profile_fields'])) {
			$fields = '(' . implode(',', $this->strategy['profile_fields']) . ')';
		} else {
			$fields = '(' . $this->strategy['profile_fields'] . ')';
		}

		$userinfo = $this->serverGet('https://api.linkedin.com/v1/people/~:' . $fields, array('oauth2_access_token' => $access_token), null, $headers);

		if (!empty($userinfo)){
			return $this->recursiveGetObjectVars(simplexml_load_string($userinfo));
		}
		else{
			$error = array(
				'code' => 'userinfo_error',
				'message' => 'Failed when attempting to query for user information',
				'raw' => array(
					'response' => $userinfo,
					'headers' => $headers
				)
			);

			$this->errorCallback($error);
		}
	}
}