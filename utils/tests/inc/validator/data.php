<?php

/**
 * Format: 'value' => expected result true|false
 */
$prepare_data = array(
	'isAlpha' => array(
		'abcdef'	=> true,
		'AbcdZxc'	=> true,
		'123abc'	=> false,
		12345		=> false,
		"-*.,/"		=> false
	),
	'isNumeric' => array(
		'12345'		=> true,
		'01234'		=> true,
		'123abc'	=> false,
		12345		=> true,
		"-*.,/"		=> false,
		+12			=> true,
		'+12'		=> false,
		-1			=> false,
		'+1'		=> false,
		0			=> true
	),
	'isEmail' => array(
		'me@email.com'				=> true,
		'me.email'					=> false,
		'email@email@.com'			=> false,
		'com.email@me'				=> false,
		'me.email@.com'				=> false,
		'me.email@email.com'		=> true,
		'me.email.me@email.info'	=> true,
		'me.e_mail99@email.info'	=> true,
		'me.e_*mail@email.info'		=> false
	),
);