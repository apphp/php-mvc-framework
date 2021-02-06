<?php
/**
 * CSecureHeaders is a helper class that provides basic secure headers
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2021 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):			PROTECTED:					PRIVATE:
 * ----------               ----------                  ----------
 * renderHeaders
 *
 */

class CSecureHeaders
{
    /**
     * Render headers
     *
     * @return mixed
     */
    public static function renderHeaders()
    {
        /*
         |-------------------------------------------------------------------------------
         | Prevent browsers from incorrectly detecting non-scripts as scripts
         | SEE MORE: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Content-Type-Options
         |-------------------------------------------------------------------------------
         */
        header('X-Content-Type-Options: nosniff');

        /*
         |-------------------------------------------------------------------------------
         | Only allow my site to frame itself
         | ------------------------
         | Other options:
         | header('X-Frame-Options', 'ALLOW FROM https://example.com/')
         | SEE MORE: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Frame-Options
         |-------------------------------------------------------------------------------
         */
        header('X-Frame-Options: SAMEORIGIN');

        /*
         |-------------------------------------------------------------------------------
         | Block pages from loading when they detect reflected XSS attacks
         | SEE MORE: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-XSS-Protection
         |-------------------------------------------------------------------------------
         */
        header('X-XSS-Protection: 1; mode=block');

        /*
         |-------------------------------------------------------------------------------
         | Only connect to this site via HTTPS for the two years
         | ------------------------
         | Other options:
         | header('Strict-Transport-Security', 'max-age=63072000; includeSubDomains; preload')
         | SEE MORE: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Strict-Transport-Security
         |-------------------------------------------------------------------------------
         */
        header('Strict-Transport-Security: max-age=63072000');

        /*
         |-------------------------------------------------------------------------------
         | Will not allow any information to be sent when a scheme downgrade happens (the user is navigating from HTTPS to HTTP)
         | ------------------------
         | Other options:
         | SEE MORE: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Referrer-Policy
         |-------------------------------------------------------------------------------
         */
        header('Referrer-Policy: strict-origin-when-cross-origin');

        /*
         |-------------------------------------------------------------------------------
         | Allow or deny the use of browser features in its own frame, and in content within any <iframe>
         | ------------------------
         | Other options:
         | SEE MORE: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Feature-Policy
         |-------------------------------------------------------------------------------
         */
        header("Feature-Policy: microphone 'none'; camera 'none'; geolocation 'none';");

        /*
         |-------------------------------------------------------------------------------
         | Disable disable framing and disable plugins
         | ------------------------
         | Other options:
         | default-src 'none'; font-src https://fonts.gstatic.com; img-src 'self' https://i.imgur.com; object-src 'none'; script-src 'self'; style-src 'self'
         | SEE MORE: https://infosec.mozilla.org/guidelines/web_security#content-security-policy
         | SEE MORE: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/
         |-------------------------------------------------------------------------------
         */
        header("Content-Security-Policy: default-src 'self' https:; font-src http://fonts.googleapis.com; img-src 'self' http://www.w3.org");
        header("Content-Security-Policy: frame-ancestors 'none'; object-src 'none'; img-src 'self' data:; script-src 'self' 'unsafe-eval' 'unsafe-inline'; form-action 'self'; base-uri 'self'; style-src https://fonts.googleapis.com 'self' 'unsafe-inline' 'unsafe-eval';");
    }
}