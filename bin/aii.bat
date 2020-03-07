@echo off

rem -------------------------------------------------------------
rem  Aii command line script for Windows.
rem
rem  This is the bootstrap script for running aii on Windows.
rem
rem  @project ApPHP Framework
rem  @author ApPHP <info@apphp.com>
rem  @link http://www.apphpframework.com/
rem  @copyright Copyright (c) 2012 - 2020 ApPHP Framework
rem  @license http://www.apphpframework.com/license/
rem -------------------------------------------------------------

@setlocal

set APPHP_PATH=%~dp0

if "%PHP_COMMAND%" == "" set PHP_COMMAND=php.exe

"%PHP_COMMAND%" "%APPHP_PATH%aii" %*

@endlocal