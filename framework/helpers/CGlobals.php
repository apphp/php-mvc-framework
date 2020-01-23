<?php
/**
 * CGlobals is a helper class file that provides access to global classes
 * that replaces common access to components via Apphp
 *
 * @project   ApPHP Framework
 * @author    ApPHP <info@apphp.com>
 * @link      http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2019 ApPHP Framework
 * @license   http://www.apphpframework.com/license/
 *
 *
 */

/**
 * Replacement for usage of A::app()->getRequest();
 * Usage: CRequest()->getBaseUrl();
 * Example: <base href="<?= CRequest()->getBaseUrl(); ?>"/>
 * @return CHttpRequest
 */
if (!function_exists('CRequest')) {
	function CRequest()
	{
		return A::app()->getRequest();
	}
}
