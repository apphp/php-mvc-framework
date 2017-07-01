<?php
/**
 * CSoap is a helper class file that provides basic functions for work with SOAP protocol
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2016 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * USAGE:
 * ----------
 * 1. Standard call CSoap::config() + CSoap::call()
 * 2. Simple call with default parameters CSoap::call()
 *
 * If send:
 * <SOAP-ENV:Envelope xmlns:soapenv=http://schemas.xmlsoap.org/soap/envelope/>
 *      <SOAP-ENV:Body>
 *          <checkVat xmlns="urn:ec.europa.eu:taxud:vies:services:checkVat:types">
 *              <countryCode>MS</urn:countryCode>
 *              <vatNumber>TESTVATNUMBER</urn:vatNumber>
 *          </checkVat>
 *      </SOAP-ENV:Body>
 * </SOAP-ENV:Envelope>
 * Then:
 * 1) Standard - CSoap::config(array('namespace'=>'SOAP-ENV', 'operation'=>'checkVat', 'wsdl'=>true)) +
 *               CSoap::call('http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl', array('parameters'=>array('countryCode'=>'BG', 'vatNumber'=>'175074752')))
 * 2) Simple - CSoap::call('http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl', array('checkVat'=>array('countryCode'=>'BG', 'vatNumber'=>'175074752')))
 *
 * PUBLIC (static):         PROTECTED:                  PRIVATE:
 * ----------               ----------                  ----------
 * config
 * call
 * getError
 *
 */

if(!class_exists('SoapClient')){
    // Include the main library.
    include(dirname(__FILE__).'/../vendors/nusoap/nusoap.php');
}

class CSoap
{
    /* @var */
    private static $_nusoap          = null;
    private static $_errorMessage    = '';
    private static $_namespace       = 'SOAP-ENV';
    private static $_operation       = '';
    private static $_wsdl            = true;
    private static $_encoding        = 'UTF-8';
    private static $_proxyhost       = false;
    private static $_proxyport       = false;
    private static $_proxyusername   = false;
    private static $_proxypassword   = false;
    private static $_timeout         = 0;
    private static $_responseTimeout = 30;
    private static $_portName        = '';
    private static $_soapAction      = '';
    private static $_headers         = false;
    private static $_style           = 'rcp';
    private static $_use             = 'encoded';


    /**
     * Sets a basic configuration
     * @param array $params
     * Usage:
     * CSoap::config(array(
     *      'namespace' => 'http://schemas.xmlsoap.org/soap/envelope/',
     *      'operation' => 'checkVat',
     *      'wsdl' => true,
     *      'encoding' => 'UTF-8',
     *      'proxyhost' => '',
     *      'proxyport' => '',
     *      'proxyusername' => '',
     *      'proxypassword' => '',
     *      'timeout' => 0,
     *      'responseTimeout' => 30,
     *      'portName' => '',
     *      'style' => 'rpc',
     *      'use' => 'encoded'
     * ))
     *
     * @return void
     */
    public static function config($params)
    {
        /* Optional method namespace (WSDL can override). For 'wsdl == true' can have the following values:
            - SOAP-ENV (http://schemas.xmlsoap.org/soap/envelope/)
            - xsd (http://www.w3.org/2001/XMLSchema)
            - xsi (http://www.w3.org/2001/XMLSchema-instance)
            - SOAP-ENC (http://schemas.xmlsoap.org/soap/encoding/) */
        if(isset($params['namespace']))       self::$_namespace       = $params['namespace'];
        // SOAP server URL or path
        if(isset($params['operation']))       self::$_operation       = $params['operation'];
        // Set to 'wsdl'|true if must using WSDL
        if(isset($params['wsdl']))            self::$_wsdl            = $params['wsdl'];
        // Set encoding
        if(isset($params['encoding']))        self::$_encoding        = $params['encoding'];
        if(isset($params['proxyhost']))       self::$_proxyhost       = $params['proxyhost'];
        if(isset($params['proxyport']))       self::$_proxyport       = $params['proxyport'];
        if(isset($params['proxyusername']))   self::$_proxyusername   = $params['proxyusername'];
        if(isset($params['proxypassword']))   self::$_proxypassword   = $params['proxypassword'];
        // Set the connection timeout
        if(isset($params['timeout']))         self::$_timeout         = $params['timeout'];
        // Set the response timeout
        if(isset($params['responseTimeout'])) self::$_responseTimeout = $params['responseTimeout'];
        // Port name to use in WSDL
        if(isset($params['portName']))        self::$_portName        = $params['portName'];
        // SOAPAction value (WSDL can override)
        if(isset($params['soapAction']))      self::$_soapAction      = $params['soapAction'];
        // Associative array for SOAP headers
        if(isset($params['headers']))         self::$_headers         = $params['headers'];
        // (rpc|document) the style to use when serializing parameters (WSDL can override)
        if(isset($params['style']))           self::$_style           = $params['style'];
        // (encoded|literal) the use when serializing parameters (WSDL can override)
        if(isset($params['use']))             self::$_use             = $params['use'];
    }

    /**
     * Calls method
     * @param string $host
     * @param mixed $params    An array, associative or simple, of the parameters
     *                         for the method call, or a string that is the XML
     *                         for the call.  For rpc style, this call will
     *                         wrap the XML in a tag named after the method, as
     *                         well as the SOAP Envelope and Body.  For document
     *                         style, this will only wrap with the Envelope and Body.
     *                         IMPORTANT: when using an array with document style,
     *                         in which case there
     *                         is really one parameter, the root of the fragment
     *                         used in the call, which encloses what programmers
     *                         normally think of parameters.  A parameter array
     *                         *must* include the wrapper.
     * @return false|array
     */
    public static function call($host, $params)
    {
        // If enabled the SOAP extension
        if(class_exists('SoapClient')){
           // Create the SoapClient instance
            $settingParams = array();
            if(!empty(self::$_proxyhost)) $settingParams['proxy_host'] = self::$_proxyhost;
            if(!empty(self::$_proxyport)) $settingParams['proxy_port'] = self::$_proxyport;
            if(!empty(self::$_proxyusername)) $settingParams['proxy_login'] = self::$_proxyusername;
            if(!empty(self::$_proxypassword)) $settingParams['proxy_password'] = self::$_proxypassword;
            if(!empty(self::$_encoding)) $settingParams['encoding'] = self::$_encoding;
            if(!empty(self::$_timeout)) $settingParams['connection_timeout'] = self::$_timeout;
            if(!empty(self::$_responseTimeout)) ini_set('default_socket_timeout', self::$_responseTimeout);

            if(self::$_wsdl){
                $url = $host;
            }else{
                $url = null;
                $settingParams['location'] = $host;
                $settingParams['uri'] = self::$_namespace;
                $settingParams['soapaction'] = self::$_soapAction;
                if(self::$_style){
                    if(strtolower(self::$_style) == 'rpc'){
                        $settingParams['style'] = SOAP_RPC;
                    }elseif(strtolower(self::$_style) == 'document'){
                        $settingParams['style'] = SOAP_DOCUMENT;
                    }
                }
                if(self::$_use){
                    if(strtolower(self::$_use) == 'encoded'){
                        $settingParams['use'] = SOAP_ENCODED;
                    }elseif(strtolower(self::$_use) == 'literal'){
                        $settingParams['use'] = SOAP_LITERAL;
                    }
                }
            }

            $client = new SoapClient($url, $settingParams);

            if(self::$_wsdl && empty(self::$_operation) && is_array($params) && count($params) == 1){
                // Get first element array $params
                $parameters = reset($params);
                self::$_operation = key($params);
                $params = array('parameters'=>$parameters);
            }

            $headers = null;
            if(!empty(self::$_headers)){
                $soapHeaders = new SoapVar(self::$_headers, SOAP_ENC_ARRAY);
                $headers = new SoapHeader(self::$_namespace, self::$_operation, $soapHeaders);
            }

            try{
                // Call wsdl function
                $result = (array)$client->__soapCall(self::$_operation, $params, null, $headers);
            } catch (SoapFault $e){
                self::$_errorMessage = $e->faultstring;
                $result = false;
            }
        }else{
            self::$_nusoap = new nusoap_client($host, self::$_wsdl, self::$_proxyhost, self::$_proxyport, self::$_proxyusername, self::$_proxypassword, self::$_timeout, self::$_responseTimeout, self::$_portName);
            self::$_nusoap->soap_defencoding = self::$_encoding;
            if(self::$_wsdl && empty(self::$_operation) && is_array($params) && count($params) == 1){
                // Get first element array $params
                $parameters = reset($params);
                self::$_operation = key($params);
                $params = array('parameters'=>$parameters);
            }
            $result = self::$_nusoap->call(self::$_operation, $params, self::$_namespace, self::$_soapAction, self::$_headers, null, self::$_style, self::$_use);

            if(self::$_nusoap->fault || self::$_nusoap->getError()){
                self::$_errorMessage = self::$_nusoap->getError();
                $result = false;
            }
        }

        return $result;
    }

    /**
     * Gets error
     * @return string
     */
    public static function getError()
    {
        if(!empty(self::$_errorMessage)){
            return self::$_errorMessage;
        }

        return '';
    }
}
