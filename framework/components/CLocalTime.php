<?php
/**
 * CLocalTime provides work with locales and timezones
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2020 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC:                    PROTECTED:                    PRIVATE:
 * ----------               ----------                  ----------
 * __construct
 * init (static)
 * setTimeZone
 * getTimeZone
 * getLocales
 * getTimeZoneOffset
 * getTimeZones
 *
 */

class CLocalTime extends CComponent
{
	
	/**
	 * Class default constructor
	 */
	function __construct()
	{
	
	}
	
	/**
	 * Returns the instance of object
	 * @return current class
	 */
	public static function init()
	{
		return parent::init(__CLASS__);
	}
	
	/**
	 * Sets the time zone used by the application
	 * @param string $value
	 * @see http://php.net/manual/en/function.date-default-timezone-set.php
	 */
	public function setTimeZone($value)
	{
		date_default_timezone_set($value);
	}
	
	/**
	 * Returns the time zone used by the application
	 * @return string
	 * @see http://php.net/manual/en/function.date-default-timezone-set.php
	 */
	public function getTimeZone()
	{
		return date_default_timezone_get();
	}
	
	/**
	 * Returns a list of locales
	 * @return array
	 */
	public function getLocales()
	{
		return array(
			'sq_AL' => A::t('i18n', 'languages.sq') . ' - ' . A::t('i18n', 'countries.al'),
			'ar_AE' => A::t('i18n', 'languages.ar') . ' - ' . A::t('i18n', 'countries.ae'),
			'ar_BH' => A::t('i18n', 'languages.ar') . ' - ' . A::t('i18n', 'countries.bh'),
			'ar_DZ' => A::t('i18n', 'languages.ar') . ' - ' . A::t('i18n', 'countries.dz'),
			'ar_EG' => A::t('i18n', 'languages.ar') . ' - ' . A::t('i18n', 'countries.eg'),
			'ar_IN' => A::t('i18n', 'languages.ar') . ' - ' . A::t('i18n', 'countries.in'),
			'ar_IQ' => A::t('i18n', 'languages.ar') . ' - ' . A::t('i18n', 'countries.iq'),
			'ar_JO' => A::t('i18n', 'languages.ar') . ' - ' . A::t('i18n', 'countries.jo'),
			'ar_KW' => A::t('i18n', 'languages.ar') . ' - ' . A::t('i18n', 'countries.kw'),
			'ar_LB' => A::t('i18n', 'languages.ar') . ' - ' . A::t('i18n', 'countries.lb'),
			'ar_LY' => A::t('i18n', 'languages.ar') . ' - ' . A::t('i18n', 'countries.ly'),
			'ar_MA' => A::t('i18n', 'languages.ar') . ' - ' . A::t('i18n', 'countries.ma'),
			'ar_OM' => A::t('i18n', 'languages.ar') . ' - ' . A::t('i18n', 'countries.om'),
			'ar_QA' => A::t('i18n', 'languages.ar') . ' - ' . A::t('i18n', 'countries.qa'),
			'ar_SA' => A::t('i18n', 'languages.ar') . ' - ' . A::t('i18n', 'countries.sa'),
			'ar_SD' => A::t('i18n', 'languages.ar') . ' - ' . A::t('i18n', 'countries.sd'),
			'ar_SY' => A::t('i18n', 'languages.ar') . ' - ' . A::t('i18n', 'countries.sy'),
			'ar_TN' => A::t('i18n', 'languages.ar') . ' - ' . A::t('i18n', 'countries.tn'),
			'ar_YE' => A::t('i18n', 'languages.ar') . ' - ' . A::t('i18n', 'countries.ye'),
			'eu_ES' => A::t('i18n', 'languages.eu') . ' - ' . A::t('i18n', 'countries.es'),
			'be_BY' => A::t('i18n', 'languages.be') . ' - ' . A::t('i18n', 'countries.by'),
			'bg_BG' => A::t('i18n', 'languages.bg') . ' - ' . A::t('i18n', 'countries.bg'),
			'ca_ES' => A::t('i18n', 'languages.ca') . ' - ' . A::t('i18n', 'countries.es'),
			'zh_CN' => A::t('i18n', 'languages.zh') . ' - ' . A::t('i18n', 'countries.cn'),
			'zh_HK' => A::t('i18n', 'languages.zh') . ' - ' . A::t('i18n', 'countries.hk'),
			'zh_TW' => A::t('i18n', 'languages.zh') . ' - ' . A::t('i18n', 'countries.tw'),
			'hr_HR' => A::t('i18n', 'languages.hr') . ' - ' . A::t('i18n', 'countries.hr'),
			'cs_CZ' => A::t('i18n', 'languages.cs') . ' - ' . A::t('i18n', 'countries.cz'),
			'da_DK' => A::t('i18n', 'languages.da') . ' - ' . A::t('i18n', 'countries.dk'),
			'nl_BE' => A::t('i18n', 'languages.nl') . ' - ' . A::t('i18n', 'countries.be'),
			'nl_NL' => A::t('i18n', 'languages.nl') . ' - ' . A::t('i18n', 'countries.nl'),
			'de_AT' => A::t('i18n', 'languages.de') . ' - ' . A::t('i18n', 'countries.at'),
			'de_BE' => A::t('i18n', 'languages.de') . ' - ' . A::t('i18n', 'countries.be'),
			'de_CH' => A::t('i18n', 'languages.de') . ' - ' . A::t('i18n', 'countries.ch'),
			'de_DE' => A::t('i18n', 'languages.de') . ' - ' . A::t('i18n', 'countries.de'),
			'de_LU' => A::t('i18n', 'languages.de') . ' - ' . A::t('i18n', 'countries.lu'),
			'en_AU' => A::t('i18n', 'languages.en') . ' - ' . A::t('i18n', 'countries.au'),
			'en_CA' => A::t('i18n', 'languages.en') . ' - ' . A::t('i18n', 'countries.ca'),
			'en_GB' => A::t('i18n', 'languages.en') . ' - ' . A::t('i18n', 'countries.gb'),
			'en_IN' => A::t('i18n', 'languages.en') . ' - ' . A::t('i18n', 'countries.in'),
			'en_NZ' => A::t('i18n', 'languages.en') . ' - ' . A::t('i18n', 'countries.nz'),
			'en_PH' => A::t('i18n', 'languages.en') . ' - ' . A::t('i18n', 'countries.ph'),
			'en_US' => A::t('i18n', 'languages.en') . ' - ' . A::t('i18n', 'countries.us'),
			'en_ZA' => A::t('i18n', 'languages.en') . ' - ' . A::t('i18n', 'countries.za'),
			'en_ZW' => A::t('i18n', 'languages.en') . ' - ' . A::t('i18n', 'countries.zw'),
			'et_EE' => A::t('i18n', 'languages.et') . ' - ' . A::t('i18n', 'countries.ee'),
			'fi_FI' => A::t('i18n', 'languages.fi') . ' - ' . A::t('i18n', 'countries.fi'),
			'fo_FO' => A::t('i18n', 'languages.fo') . ' - ' . A::t('i18n', 'countries.fo'),
			'fr_BE' => A::t('i18n', 'languages.fr') . ' - ' . A::t('i18n', 'countries.be'),
			'fr_CA' => A::t('i18n', 'languages.fr') . ' - ' . A::t('i18n', 'countries.ca'),
			'fr_CH' => A::t('i18n', 'languages.fr') . ' - ' . A::t('i18n', 'countries.ch'),
			'fr_FR' => A::t('i18n', 'languages.fr') . ' - ' . A::t('i18n', 'countries.fr'),
			'fr_LU' => A::t('i18n', 'languages.fr') . ' - ' . A::t('i18n', 'countries.lu'),
			'gl_ES' => A::t('i18n', 'languages.gl') . ' - ' . A::t('i18n', 'countries.es'),
			'el_GR' => A::t('i18n', 'languages.el') . ' - ' . A::t('i18n', 'countries.gr'),
			'gu_IN' => A::t('i18n', 'languages.gu') . ' - ' . A::t('i18n', 'countries.in'),
			'he_IL' => A::t('i18n', 'languages.he') . ' - ' . A::t('i18n', 'countries.il'),
			'hi_IN' => A::t('i18n', 'languages.hi') . ' - ' . A::t('i18n', 'countries.in'),
			'hu_HU' => A::t('i18n', 'languages.hu') . ' - ' . A::t('i18n', 'countries.hu'),
			'id_ID' => A::t('i18n', 'languages.id') . ' - ' . A::t('i18n', 'countries.id'),
			'is_IS' => A::t('i18n', 'languages.is') . ' - ' . A::t('i18n', 'countries.is'),
			'it_CH' => A::t('i18n', 'languages.it') . ' - ' . A::t('i18n', 'countries.ch'),
			'it_IT' => A::t('i18n', 'languages.it') . ' - ' . A::t('i18n', 'countries.it'),
			'ja_JP' => A::t('i18n', 'languages.ja') . ' - ' . A::t('i18n', 'countries.jp'),
			'ko_KR' => A::t('i18n', 'languages.ko') . ' - ' . A::t('i18n', 'countries.kr'),
			'lt_LT' => A::t('i18n', 'languages.lt') . ' - ' . A::t('i18n', 'countries.lt'),
			'lv_LV' => A::t('i18n', 'languages.lv') . ' - ' . A::t('i18n', 'countries.lv'),
			'mk_MK' => A::t('i18n', 'languages.mk') . ' - ' . A::t('i18n', 'countries.mk'),
			'mn_MN' => A::t('i18n', 'languages.mn') . ' - ' . A::t('i18n', 'countries.mn'),
			'ms_MY' => A::t('i18n', 'languages.ms') . ' - ' . A::t('i18n', 'countries.my'),
			'nb_NO' => A::t('i18n', 'languages.nb') . ' - ' . A::t('i18n', 'countries.no'),
			'no_NO' => A::t('i18n', 'languages.no') . ' - ' . A::t('i18n', 'countries.no'),
			'pl_PL' => A::t('i18n', 'languages.pl') . ' - ' . A::t('i18n', 'countries.pl'),
			'pt_BR' => A::t('i18n', 'languages.pt') . ' - ' . A::t('i18n', 'countries.br'),
			'pt_PT' => A::t('i18n', 'languages.pt') . ' - ' . A::t('i18n', 'countries.pt'),
			'ro_RO' => A::t('i18n', 'languages.ro') . ' - ' . A::t('i18n', 'countries.ro'),
			'ru_RU' => A::t('i18n', 'languages.ru') . ' - ' . A::t('i18n', 'countries.ru'),
			'ru_UA' => A::t('i18n', 'languages.ru') . ' - ' . A::t('i18n', 'countries.ua'),
			'sk_SK' => A::t('i18n', 'languages.sk') . ' - ' . A::t('i18n', 'countries.sk'),
			'sl_SI' => A::t('i18n', 'languages.sl') . ' - ' . A::t('i18n', 'countries.si'),
			'sr_YU' => A::t('i18n', 'languages.sr') . ' - ' . A::t('i18n', 'countries.rs'),
			'es_AR' => A::t('i18n', 'languages.es') . ' - ' . A::t('i18n', 'countries.ar'),
			'es_BO' => A::t('i18n', 'languages.es') . ' - ' . A::t('i18n', 'countries.bo'),
			'es_CL' => A::t('i18n', 'languages.es') . ' - ' . A::t('i18n', 'countries.cl'),
			'es_CO' => A::t('i18n', 'languages.es') . ' - ' . A::t('i18n', 'countries.co'),
			'es_CR' => A::t('i18n', 'languages.es') . ' - ' . A::t('i18n', 'countries.cr'),
			'es_DO' => A::t('i18n', 'languages.es') . ' - ' . A::t('i18n', 'countries.do'),
			'es_EC' => A::t('i18n', 'languages.es') . ' - ' . A::t('i18n', 'countries.ec'),
			'es_ES' => A::t('i18n', 'languages.es') . ' - ' . A::t('i18n', 'countries.es'),
			'es_GT' => A::t('i18n', 'languages.es') . ' - ' . A::t('i18n', 'countries.gt'),
			'es_HN' => A::t('i18n', 'languages.es') . ' - ' . A::t('i18n', 'countries.hn'),
			'es_MX' => A::t('i18n', 'languages.es') . ' - ' . A::t('i18n', 'countries.mx'),
			'es_NI' => A::t('i18n', 'languages.es') . ' - ' . A::t('i18n', 'countries.ni'),
			'es_PA' => A::t('i18n', 'languages.es') . ' - ' . A::t('i18n', 'countries.pa'),
			'es_PE' => A::t('i18n', 'languages.es') . ' - ' . A::t('i18n', 'countries.pe'),
			'es_PR' => A::t('i18n', 'languages.es') . ' - ' . A::t('i18n', 'countries.pr'),
			'es_PY' => A::t('i18n', 'languages.es') . ' - ' . A::t('i18n', 'countries.py'),
			'es_SV' => A::t('i18n', 'languages.es') . ' - ' . A::t('i18n', 'countries.sv'),
			'es_US' => A::t('i18n', 'languages.es') . ' - ' . A::t('i18n', 'countries.us'),
			'es_UY' => A::t('i18n', 'languages.es') . ' - ' . A::t('i18n', 'countries.uy'),
			'es_VE' => A::t('i18n', 'languages.es') . ' - ' . A::t('i18n', 'countries.ve'),
			'sv_FI' => A::t('i18n', 'languages.sv') . ' - ' . A::t('i18n', 'countries.fi'),
			'sv_SE' => A::t('i18n', 'languages.sv') . ' - ' . A::t('i18n', 'countries.se'),
			'ta_IN' => A::t('i18n', 'languages.ta') . ' - ' . A::t('i18n', 'countries.in'),
			'te_IN' => A::t('i18n', 'languages.te') . ' - ' . A::t('i18n', 'countries.in'),
			'th_TH' => A::t('i18n', 'languages.th') . ' - ' . A::t('i18n', 'countries.th'),
			'tr_TR' => A::t('i18n', 'languages.tr') . ' - ' . A::t('i18n', 'countries.tr'),
			'uk_UA' => A::t('i18n', 'languages.uk') . ' - ' . A::t('i18n', 'countries.ua'),
			'ur_PK' => A::t('i18n', 'languages.ur') . ' - ' . A::t('i18n', 'countries.pk'),
			'vi_VN' => A::t('i18n', 'languages.vi') . ' - ' . A::t('i18n', 'countries.vn'),
		);
	}
	
	/**
	 * Returns a timzone offset or full name by time zone name
	 * @param string $name
	 * @param string $type 'offset' - default, 'offset_name' or 'full_name'
	 * @return float|string
	 */
	public function getTimeZoneInfo($name = '', $type = 'offset')
	{
		$return = '';
		
		if (!empty($name)) {
			$timeZones = $this->getTimeZones();
			foreach ($timeZones as $timeZone) {
				foreach ($timeZone as $zoneName => $zoneInfo) {
					if (strtolower($zoneName) == strtolower($name)) {
						if ($type == 'offset') {
							$return = isset($zoneInfo['offset']) ? $zoneInfo['offset'] : '';
						} elseif ($type == 'offset_name') {
							$return = (isset($zoneInfo['offset_text']) && isset($zoneInfo['name'])) ? $zoneInfo['offset_text'] : '';
						} elseif ($type == 'full_name') {
							$return = (isset($zoneInfo['offset_text']) && isset($zoneInfo['name'])) ? $zoneInfo['offset_text'] . ' ' . $zoneInfo['name'] : '';
						}
						break(2);
					}
				}
			}
		}
		
		return $return;
	}
	
	/**
	 * Returns a nested array of timzones by continents
	 * @return array
	 */
	public function getTimeZones()
	{
		return array(
			'Africa' => array(
				'Africa/Casablanca' => array('offset' => '0', 'offset_text' => '[GMT+00:00]', 'name' => 'Western European Time (Africa/ Casablanca)'),
				'Africa/Algiers' => array('offset' => '1', 'offset_text' => '[GMT+01:00]', 'name' => 'Central European Time (Africa/ Algiers)'),
				'Africa/Bangui' => array('offset' => '1', 'offset_text' => '[GMT+01:00]', 'name' => 'Western African Time (Africa/ Bangui)'),
				'Africa/Windhoek' => array('offset' => '1', 'offset_text' => '[GMT+01:00]', 'name' => 'Western African Time (Africa/ Windhoek)'),
				'Africa/Tripoli' => array('offset' => '2', 'offset_text' => '[GMT+02:00]', 'name' => 'Eastern European Time (Africa/ Tripoli)'),
				'Africa/Johannesburg' => array('offset' => '2', 'offset_text' => '[GMT+02:00]', 'name' => 'South Africa Standard Time (Africa/ Johannesburg)'),
				'Africa/Dar_es_Salaam' => array('offset' => '3', 'offset_text' => '[GMT+03:00]', 'name' => 'Eastern African Time (EAT)'),
			),
			'America (North & South)' => array(
				'America/Scoresbysund' => array('offset' => '-1', 'offset_text' => '[GMT-01:00]', 'name' => 'Eastern Greenland Time (America/ Scoresbysund)'),
				'America/Noronha' => array('offset' => '-2', 'offset_text' => '[GMT-02:00]', 'name' => 'Fernando de Noronha Time (America/ Noronha)'),
				'America/Argentina/Buenos_Aires' => array('offset' => '-3', 'offset_text' => '[GMT-03:00]', 'name' => 'Argentine Time (AGT)'),
				'America/Belem' => array('offset' => '-3', 'offset_text' => '[GMT-03:00]', 'name' => 'Brazil Time (America/ Belem)'),
				'America/Sao_Paulo' => array('offset' => '-3', 'offset_text' => '[GMT-03:00]', 'name' => 'Brazil Time (BET)'),
				'America/Cayenne' => array('offset' => '-3', 'offset_text' => '[GMT-03:00]', 'name' => 'French Guiana Time (America/ Cayenne)'),
				'America/Miquelon' => array('offset' => '-3', 'offset_text' => '[GMT-03:00]', 'name' => 'Pierre & Miquelon Standard Time (America/ Miquelon)'),
				'America/Paramaribo' => array('offset' => '-3', 'offset_text' => '[GMT-03:00]', 'name' => 'Suriname Time (America/ Paramaribo)'),
				'America/Montevideo' => array('offset' => '-3', 'offset_text' => '[GMT-03:00]', 'name' => 'Uruguay Time (America/ Montevideo)'),
				'America/Godthab' => array('offset' => '-3', 'offset_text' => '[GMT-03:00]', 'name' => 'Western Greenland Time (America/ Godthab)'),
				'America/St_Johns' => array('offset' => '-3', 'offset_text' => '[GMT-03:30]', 'name' => 'Newfoundland Standard Time (America/ St Johns)'),
				'America/Cuiaba' => array('offset' => '-4', 'offset_text' => '[GMT-04:00]', 'name' => 'Amazon Standard Time (America/ Cuiaba)'),
				'America/Glace_Bay' => array('offset' => '-4', 'offset_text' => '[GMT-04:00]', 'name' => 'Atlantic Standard Time (America/ Glace Bay)'),
				'America/La_Paz' => array('offset' => '-4', 'offset_text' => '[GMT-04:00]', 'name' => 'Bolivia Time (America/ La Paz)'),
				'America/Santiago' => array('offset' => '-4', 'offset_text' => '[GMT-04:00]', 'name' => 'Chile Time (America/ Santiago)'),
				'America/Guyana' => array('offset' => '-4', 'offset_text' => '[GMT-04:00]', 'name' => 'Guyana Time (America/ Guyana)'),
				'America/Asuncion' => array('offset' => '-4', 'offset_text' => '[GMT-04:00]', 'name' => 'Paraguay Time (America/ Asuncion)'),
				'America/Caracas' => array('offset' => '-4', 'offset_text' => '[GMT-04:00]', 'name' => 'Venezuela Time (America/ Caracas)'),
				'America/Porto_Acre' => array('offset' => '-5', 'offset_text' => '[GMT-05:00]', 'name' => 'Acre Time (America/ Porto Acre)'),
				'America/Havana' => array('offset' => '-5', 'offset_text' => '[GMT-05:00]', 'name' => 'Central Standard Time (America/ Havana)'),
				'America/Bogota' => array('offset' => '-5', 'offset_text' => '[GMT-05:00]', 'name' => 'Colombia Time (America/ Bogota)'),
				'America/Jamaica' => array('offset' => '-5', 'offset_text' => '[GMT-05:00]', 'name' => 'Eastern Standard Time (America/ Jamaica)'),
				'America/Indianapolis' => array('offset' => '-5', 'offset_text' => '[GMT-05:00]', 'name' => 'Eastern Standard Time (US/ East-Indiana)'),
				'America/Guayaquil' => array('offset' => '-5', 'offset_text' => '[GMT-05:00]', 'name' => 'Ecuador Time (America/ Guayaquil)'),
				'America/Lima' => array('offset' => '-6', 'offset_text' => '[GMT-05:00]', 'name' => 'Peru Time (America/ Lima)'),
				'America/El_Salvador' => array('offset' => '-6', 'offset_text' => '[GMT-06:00]', 'name' => 'Central Standard Time (America/ El Salvador)'),
				'America/Regina' => array('offset' => '-6', 'offset_text' => '[GMT-06:00]', 'name' => 'Central Standard Time (Canada/ Saskatchewan)'),
				'America/Chicago' => array('offset' => '-6', 'offset_text' => '[GMT-06:00]', 'name' => 'Central Standard Time (US & Canada)'),
				'America/Phoenix' => array('offset' => '-7', 'offset_text' => '[GMT-07:00]', 'name' => 'Mountain Standard Time (US/ Arizona)'),
				'America/Los_Angeles' => array('offset' => '-8', 'offset_text' => '[GMT-08:00]', 'name' => 'Pacific Standard Time (US & Canada)'),
				'America/Anchorage' => array('offset' => '-9', 'offset_text' => '[GMT-09:00]', 'name' => 'Alaska Standard Time (AST)'),
				'America/Adak' => array('offset' => '-10', 'offset_text' => '[GMT-10:00]', 'name' => 'Hawaii-Aleutian Standard Time (America/ Adak)'),
			),
			'Antarctica' => array(
				'Antarctica/Syowa' => array('offset' => '3', 'offset_text' => '[GMT+03:00]', 'name' => 'Syowa Time (Antarctica/ Syowa)'),
				'Antarctica/Mawson' => array('offset' => '6', 'offset_text' => '[GMT+06:00]', 'name' => 'Mawson Time (Antarctica/ Mawson)'),
				'Antarctica/Vostok' => array('offset' => '6', 'offset_text' => '[GMT+06:00]', 'name' => 'Vostok Time (Antarctica/ Vostok)'),
				'Antarctica/Davis' => array('offset' => '7', 'offset_text' => '[GMT+07:00]', 'name' => 'Davis Time (Antarctica/ Davis)'),
				'Antarctica/DumontDUrville' => array('offset' => '10', 'offset_text' => '[GMT+10:00]', 'name' => 'Dumont-d\'Urville Time (Antarctica/ DumontDUrville)'),
				'Antarctica/Rothera' => array('offset' => '-3', 'offset_text' => '[GMT-03:00]', 'name' => 'Rothera Time (Antarctica/ Rothera)'),
			),
			'Asia' => array(
				'Asia/Jerusalem' => array('offset' => '2', 'offset_text' => '[GMT+02:00]', 'name' => 'Israel Standard Time (Asia/ Jerusalem)'),
				'Asia/Baghdad' => array('offset' => '3', 'offset_text' => '[GMT+03:00]', 'name' => 'Arabia Standard Time (Asia/ Baghdad)'),
				'Asia/Kuwait' => array('offset' => '3', 'offset_text' => '[GMT+03:00]', 'name' => 'Arabia Standard Time (Asia/ Kuwait)'),
				'Asia/Tehran' => array('offset' => '3.5', 'offset_text' => '[GMT+03:30]', 'name' => 'Iran Standard Time (Asia/ Tehran)'),
				'Asia/Aqtau' => array('offset' => '4', 'offset_text' => '[GMT+04:00]', 'name' => 'Aqtau Time (Asia/ Aqtau)'),
				'Asia/Yerevan' => array('offset' => '4', 'offset_text' => '[GMT+04:00]', 'name' => 'Armenia Time (NET)'),
				'Asia/Baku' => array('offset' => '4', 'offset_text' => '[GMT+04:00]', 'name' => 'Azerbaijan Time (Asia/ Baku)'),
				'Asia/Tbilisi' => array('offset' => '4', 'offset_text' => '[GMT+04:00]', 'name' => 'Georgia Time (Asia/ Tbilisi)'),
				'Asia/Dubai' => array('offset' => '4', 'offset_text' => '[GMT+04:00]', 'name' => 'Gulf Standard Time (Asia/ Dubai)'),
				'Asia/Oral' => array('offset' => '4', 'offset_text' => '[GMT+04:00]', 'name' => 'Oral Time (Asia/ Oral)'),
				'Asia/Kabul' => array('offset' => '4.5', 'offset_text' => '[GMT+04:30]', 'name' => 'Afghanistan Time (Asia/ Kabul)'),
				'Asia/Aqtobe' => array('offset' => '5', 'offset_text' => '[GMT+05:00]', 'name' => 'Aqtobe Time (Asia/ Aqtobe)'),
				'Asia/Bishkek' => array('offset' => '5', 'offset_text' => '[GMT+05:00]', 'name' => 'Kirgizstan Time (Asia/ Bishkek)'),
				'Asia/Karachi' => array('offset' => '5', 'offset_text' => '[GMT+05:00]', 'name' => 'Pakistan Time (PLT)'),
				'Asia/Dushanbe' => array('offset' => '5', 'offset_text' => '[GMT+05:00]', 'name' => 'Tajikistan Time (Asia/ Dushanbe)'),
				'Asia/Ashgabat' => array('offset' => '5', 'offset_text' => '[GMT+05:00]', 'name' => 'Turkmenistan Time (Asia/ Ashgabat)'),
				'Asia/Tashkent' => array('offset' => '5', 'offset_text' => '[GMT+05:00]', 'name' => 'Uzbekistan Time (Asia/ Tashkent)'),
				'Asia/Yekaterinburg' => array('offset' => '5', 'offset_text' => '[GMT+05:00]', 'name' => 'Yekaterinburg Time (Asia/ Yekaterinburg)'),
				'Asia/Katmandu' => array('offset' => '5.75', 'offset_text' => '[GMT+05:45]', 'name' => 'Nepal Time (Asia/ Katmandu)'),
				'Asia/Almaty' => array('offset' => '6', 'offset_text' => '[GMT+06:00]', 'name' => 'Alma-Ata Time (Asia/ Almaty)'),
				'Asia/Thimbu' => array('offset' => '6', 'offset_text' => '[GMT+06:00]', 'name' => 'Bhutan Time (Asia/ Thimbu)'),
				'Asia/Novosibirsk' => array('offset' => '6', 'offset_text' => '[GMT+06:00]', 'name' => 'Novosibirsk Time (Asia/ Novosibirsk)'),
				'Asia/Omsk' => array('offset' => '6', 'offset_text' => '[GMT+06:00]', 'name' => 'Omsk Time (Asia/ Omsk)'),
				'Asia/Qyzylorda' => array('offset' => '6', 'offset_text' => '[GMT+06:00]', 'name' => 'Qyzylorda Time (Asia/ Qyzylorda)'),
				'Asia/Colombo' => array('offset' => '6', 'offset_text' => '[GMT+06:00]', 'name' => 'Sri Lanka Time (Asia/ Colombo)'),
				'Asia/Rangoon' => array('offset' => '6.5', 'offset_text' => '[GMT+06:30]', 'name' => 'Myanmar Time (Asia/ Rangoon)'),
				'Asia/Hovd' => array('offset' => '7', 'offset_text' => '[GMT+07:00]', 'name' => 'Hovd Time (Asia/ Hovd)'),
				'Asia/Krasnoyarsk' => array('offset' => '7', 'offset_text' => '[GMT+07:00]', 'name' => 'Krasnoyarsk Time (Asia/ Krasnoyarsk)'),
				'Asia/Jakarta' => array('offset' => '7', 'offset_text' => '[GMT+07:00]', 'name' => 'West Indonesia Time (Asia/ Jakarta)'),
				'Asia/Brunei' => array('offset' => '8', 'offset_text' => '[GMT+08:00]', 'name' => 'Brunei Time (Asia/ Brunei)'),
				'Asia/Makassar' => array('offset' => '8', 'offset_text' => '[GMT+08:00]', 'name' => 'Central Indonesia Time (Asia/ Makassar)'),
				'Asia/Hong_Kong' => array('offset' => '8', 'offset_text' => '[GMT+08:00]', 'name' => 'Hong Kong Time (Asia/ Hong Kong)'),
				'Asia/Irkutsk' => array('offset' => '8', 'offset_text' => '[GMT+08:00]', 'name' => 'Irkutsk Time (Asia/ Irkutsk)'),
				'Asia/Kuala_Lumpur' => array('offset' => '8', 'offset_text' => '[GMT+08:00]', 'name' => 'Malaysia Time (Asia/ Kuala Lumpur)'),
				'Asia/Manila' => array('offset' => '8', 'offset_text' => '[GMT+08:00]', 'name' => 'Philippines Time (Asia/ Manila)'),
				'Asia/Shanghai' => array('offset' => '8', 'offset_text' => '[GMT+08:00]', 'name' => 'Shanghai Time (Asia/ Shanghai)'),
				'Asia/Singapore' => array('offset' => '8', 'offset_text' => '[GMT+08:00]', 'name' => 'Singapore Time (Asia/ Singapore)'),
				'Asia/Taipei' => array('offset' => '8', 'offset_text' => '[GMT+08:00]', 'name' => 'Taipei Time (Asia/ Taipei)'),
				'Asia/Ulaanbaatar' => array('offset' => '8', 'offset_text' => '[GMT+08:00]', 'name' => 'Ulaanbaatar Time (Asia/ Ulaanbaatar)'),
				'Asia/Choibalsan' => array('offset' => '9', 'offset_text' => '[GMT+09:00]', 'name' => 'Choibalsan Time (Asia/ Choibalsan)'),
				'Asia/Jayapura' => array('offset' => '9', 'offset_text' => '[GMT+09:00]', 'name' => 'East Indonesia Time (Asia/ Jayapura)'),
				'Asia/Dili' => array('offset' => '9', 'offset_text' => '[GMT+09:00]', 'name' => 'East Timor Time (Asia/ Dili)'),
				'Asia/Tokyo' => array('offset' => '9', 'offset_text' => '[GMT+09:00]', 'name' => 'Japan Standard Time (JST)'),
				'Asia/Seoul' => array('offset' => '9', 'offset_text' => '[GMT+09:00]', 'name' => 'Korea Standard Time (Asia/ Seoul)'),
				'Asia/Yakutsk' => array('offset' => '9', 'offset_text' => '[GMT+09:00]', 'name' => 'Yakutsk Time (Asia/ Yakutsk)'),
				'Asia/Sakhalin' => array('offset' => '10', 'offset_text' => '[GMT+10:00]', 'name' => 'Sakhalin Time (Asia/ Sakhalin)'),
				'Asia/Vladivostok' => array('offset' => '10', 'offset_text' => '[GMT+10:00]', 'name' => 'Vladivostok Time (Asia/ Vladivostok)'),
				'Asia/Magadan' => array('offset' => '11', 'offset_text' => '[GMT+11:00]', 'name' => 'Magadan Time (Asia/ Magadan)'),
				'Asia/Anadyr' => array('offset' => '12', 'offset_text' => '[GMT+12:00]', 'name' => 'Anadyr Time (Asia/ Anadyr)'),
				'Asia/Kamchatka' => array('offset' => '12', 'offset_text' => '[GMT+12:00]', 'name' => 'Petropavlovsk-Kamchatski Time (Asia/ Kamchatka)'),
			),
			'Atlantic Ocean' => array(
				'Atlantic/Jan_Mayen' => array('offset' => '1', 'offset_text' => '[GMT+01:00]', 'name' => 'Eastern Greenland Time (Atlantic/ Jan Mayen)'),
				'Atlantic/Azores' => array('offset' => '-1', 'offset_text' => '[GMT-01:00]', 'name' => 'Azores Time (Atlantic/ Azores)'),
				'Atlantic/Cape_Verde' => array('offset' => '-1', 'offset_text' => '[GMT-01:00]', 'name' => 'Cape Verde Time (Atlantic/ Cape Verde)'),
				'Atlantic/South_Georgia' => array('offset' => '-2', 'offset_text' => '[GMT-02:00]', 'name' => 'South Georgia Standard Time (Atlantic/ South Georgia)'),
				'Atlantic/Bermuda' => array('offset' => '-4', 'offset_text' => '[GMT-04:00]', 'name' => 'Atlantic Standard Time (Atlantic/ Bermuda)'),
				'Atlantic/Stanley' => array('offset' => '-4', 'offset_text' => '[GMT-04:00]', 'name' => 'Falkland Is. Time (Atlantic/ Stanley)'),
			),
			'Australia' => array(
				'Australia/Perth' => array('offset' => '8', 'offset_text' => '[GMT+08:00]', 'name' => 'Western Standard Time (Australia) (Australia/ Perth)'),
				'Australia/Broken_Hill' => array('offset' => '9.5', 'offset_text' => '[GMT+09:30]', 'name' => 'Central Standard Time (Australia/ Broken Hill)'),
				'Australia/Darwin' => array('offset' => '9.5', 'offset_text' => '[GMT+09:30]', 'name' => 'Central Standard Time (Northern Territory) (ACT)'),
				'Australia/Adelaide' => array('offset' => '9.5', 'offset_text' => '[GMT+09:30]', 'name' => 'Central Standard Time (South Australia) (Australia/ Adelaide)'),
				'Australia/Sydney' => array('offset' => '10', 'offset_text' => '[GMT+10:00]', 'name' => 'Eastern Standard Time (New South Wales) (Australia/ Sydney)'),
				'Australia/Brisbane' => array('offset' => '10', 'offset_text' => '[GMT+10:00]', 'name' => 'Eastern Standard Time (Queensland) (Australia/ Brisbane)'),
				'Australia/Hobart' => array('offset' => '10', 'offset_text' => '[GMT+10:00]', 'name' => 'Eastern Standard Time (Tasmania) (Australia/ Hobart)'),
				'Australia/Melbourne' => array('offset' => '10', 'offset_text' => '[GMT+10:00]', 'name' => 'Eastern Standard Time (Victoria) (Australia/ Melbourne)'),
				'Australia/Lord_Howe' => array('offset' => '10.5', 'offset_text' => '[GMT+10:30]', 'name' => 'Load Howe Standard Time (Australia/ Lord Howe)'),
			),
			'Europe' => array(
				'Europe/Lisbon' => array('offset' => '0', 'offset_text' => '[GMT+00:00]', 'name' => 'Western European Time (Europe/ Lisbon)'),
				'Europe/Berlin' => array('offset' => '1', 'offset_text' => '[GMT+01:00]', 'name' => 'Central European Time (Europe/ Berlin)'),
				'Europe/Istanbul' => array('offset' => '2', 'offset_text' => '[GMT+02:00]', 'name' => 'Eastern European Time (Europe/ Istanbul)'),
				'Europe/Moscow' => array('offset' => '3', 'offset_text' => '[GMT+03:00]', 'name' => 'Moscow Standard Time (Europe/ Moscow)'),
				'Europe/Samara' => array('offset' => '4', 'offset_text' => '[GMT+04:00]', 'name' => 'Samara Time (Europe/ Samara)'),
			),
			'Indian' => array(
				'Indian/Antananarivo' => array('offset' => '3', 'offset_text' => '[GMT+03:00]', 'name' => 'Antananarivo Time (Indian/ Antananarivo)'),
				'Indian/Comoro' => array('offset' => '3', 'offset_text' => '[GMT+03:00]', 'name' => 'Comoro Time (Indian/ Comoro)'),
				'Indian/Mayotte' => array('offset' => '3', 'offset_text' => '[GMT+03:00]', 'name' => 'Mayotte Time (Indian/ Mayotte)'),
				'Indian/Mauritius' => array('offset' => '4', 'offset_text' => '[GMT+04:00]', 'name' => 'Mauritius Time (Indian/ Mauritius)'),
				'Indian/Reunion' => array('offset' => '4', 'offset_text' => '[GMT+04:00]', 'name' => 'Reunion Time (Indian/ Reunion)'),
				'Indian/Mahe' => array('offset' => '4', 'offset_text' => '[GMT+04:00]', 'name' => 'Seychelles Time (Indian/ Mahe)'),
				'Indian/Kerguelen' => array('offset' => '5', 'offset_text' => '[GMT+05:00]', 'name' => 'French Southern & Antarctic Lands Time (Indian/ Kerguelen)'),
				'Indian/Maldives' => array('offset' => '5', 'offset_text' => '[GMT+05:00]', 'name' => 'Maldives Time (Indian/ Maldives)'),
				'Indian/IST' => array('offset' => '5.5', 'offset_text' => '[GMT+05:30]', 'name' => 'India Standard Time (India Time / IST)'),
				'Indian/Chagos' => array('offset' => '6', 'offset_text' => '[GMT+06:00]', 'name' => 'Indian Ocean Territory Time (Indian/ Chagos)'),
				'Indian/Cocos' => array('offset' => '6.5', 'offset_text' => '[GMT+06:30]', 'name' => 'Cocos Islands Time (Indian/ Cocos)'),
				'Indian/Christmas' => array('offset' => '7', 'offset_text' => '[GMT+07:00]', 'name' => 'Christmas Island Time (Indian/ Christmas)'),
			),
			'Pacific Ocean' => array(
				'Pacific/Palau' => array('offset' => '9', 'offset_text' => '[GMT+09:00]', 'name' => 'Palau Time (Pacific/ Palau)'),
				'Pacific/Guam' => array('offset' => '10', 'offset_text' => '[GMT+10:00]', 'name' => 'Chamorro Standard Time (Pacific/ Guam)'),
				'Pacific/Port_Moresby' => array('offset' => '10', 'offset_text' => '[GMT+10:00]', 'name' => 'Papua New Guinea Time (Pacific/ Port Moresby)'),
				'Pacific/Truk' => array('offset' => '10', 'offset_text' => '[GMT+10:00]', 'name' => 'Truk Time (Pacific/ Truk)'),
				'Pacific/Yap' => array('offset' => '10', 'offset_text' => '[GMT+10:00]', 'name' => 'Yap Time (Pacific/ Yap)'),
				'Pacific/Kosrae' => array('offset' => '11', 'offset_text' => '[GMT+11:00]', 'name' => 'Kosrae Time (Pacific/ Kosrae)'),
				'Pacific/Noumea' => array('offset' => '11', 'offset_text' => '[GMT+11:00]', 'name' => 'New Caledonia Time (Pacific/ Noumea)'),
				'Pacific/Ponape' => array('offset' => '11', 'offset_text' => '[GMT+11:00]', 'name' => 'Ponape Time (Pacific/ Ponape)'),
				'Pacific/Efate' => array('offset' => '11', 'offset_text' => '[GMT+11:00]', 'name' => 'Vanuatu Time (Pacific/ Efate)'),
				'Pacific/Norfolk' => array('offset' => '11.5', 'offset_text' => '[GMT+11:30]', 'name' => 'Norfolk Time (Pacific/ Norfolk)'),
				'Pacific/Fiji' => array('offset' => '12', 'offset_text' => '[GMT+12:00]', 'name' => 'Fiji Time (Pacific/ Fiji)'),
				'Pacific/Tarawa' => array('offset' => '12', 'offset_text' => '[GMT+12:00]', 'name' => 'Gilbert Is. Time (Pacific/ Tarawa)'),
				'Pacific/Majuro' => array('offset' => '12', 'offset_text' => '[GMT+12:00]', 'name' => 'Marshall Islands Time (Pacific/ Majuro)'),
				'Pacific/Nauru' => array('offset' => '12', 'offset_text' => '[GMT+12:00]', 'name' => 'Nauru Time (Pacific/ Nauru)'),
				'Pacific/Auckland' => array('offset' => '12', 'offset_text' => '[GMT+12:00]', 'name' => 'New Zealand Standard Time (Pacific/ Auckland)'),
				'Pacific/Funafuti' => array('offset' => '12', 'offset_text' => '[GMT+12:00]', 'name' => 'Tuvalu Time (Pacific/ Funafuti)'),
				'Pacific/Wake' => array('offset' => '12', 'offset_text' => '[GMT+12:00]', 'name' => 'Wake Time (Pacific/ Wake)'),
				'Pacific/Wallis' => array('offset' => '12', 'offset_text' => '[GMT+12:00]', 'name' => 'Wallis & Futuna Time (Pacific/ Wallis)'),
				'Pacific/Chatham' => array('offset' => '12.75', 'offset_text' => '[GMT+12:45]', 'name' => 'Chatham Standard Time (Pacific/ Chatham)'),
				'Pacific/Enderbury' => array('offset' => '13', 'offset_text' => '[GMT+13:00]', 'name' => 'Phoenix Is. Time (Pacific/ Enderbury)'),
				'Pacific/Tongatapu' => array('offset' => '13', 'offset_text' => '[GMT+13:00]', 'name' => 'Tonga Time (Pacific/ Tongatapu)'),
				'Pacific/Kiritimati' => array('offset' => '14', 'offset_text' => '[GMT+14:00]', 'name' => 'Line Is. Time (Pacific/ Kiritimati)'),
				'Pacific/Easter' => array('offset' => '-6', 'offset_text' => '[GMT-06:00]', 'name' => 'Easter Is. Time (Pacific/ Easter)'),
				'Pacific/Galapagos' => array('offset' => '-6', 'offset_text' => '[GMT-06:00]', 'name' => 'Galapagos Time (Pacific/ Galapagos)'),
				'Pacific/Pitcairn' => array('offset' => '-8', 'offset_text' => '[GMT-08:00]', 'name' => 'Pitcairn Standard Time (Pacific/ Pitcairn)'),
				'Pacific/Gambier' => array('offset' => '-9', 'offset_text' => '[GMT-09:00]', 'name' => 'Gambier Time (Pacific/ Gambier)'),
				'Pacific/Marquesas' => array('offset' => '-9.5', 'offset_text' => '[GMT-09:30]', 'name' => 'Marquesas Time (Pacific/ Marquesas)'),
				'Pacific/Rarotonga' => array('offset' => '-10', 'offset_text' => '[GMT-10:00]', 'name' => 'Cook Is. Time (Pacific/ Rarotonga)'),
				'Pacific/Tahiti' => array('offset' => '-10', 'offset_text' => '[GMT-10:00]', 'name' => 'Tahiti Time (Pacific/ Tahiti)'),
				'Pacific/Fakaofo' => array('offset' => '-10', 'offset_text' => '[GMT-10:00]', 'name' => 'Tokelau Time (Pacific/ Fakaofo)'),
				'Pacific/Niue' => array('offset' => '-11', 'offset_text' => '[GMT-11:00]', 'name' => 'Niue Time (Pacific/ Niue)'),
				'Pacific/Apia' => array('offset' => '-11', 'offset_text' => '[GMT-11:00]', 'name' => 'West Samoa Time (MIT)'),
			),
		);
	}
	
}
