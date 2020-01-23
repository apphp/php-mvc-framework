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
		"-*.,/"		=> false,
		'zxbcde-'	=> array('expected'=>true, 'allowed'=>'-'),
		'zxbcdef-'	=> array('expected'=>false, 'allowed'=>array('=','_'))
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
		'me@example.com'			=> true,
		'me.email'					=> false,
		'email@email@.com'			=> false,
		'com.email@me'				=> false,
		'me.email@.com'				=> false,
		'me.email@example.com'		=> true,
		'me.email.me@email.info'	=> true,
		'me.e_mail99@email.info'	=> true,
		'me.e_*mail@email.info'		=> false
	),
	'validateRegex' => array(
		'abcDef'					=> array('expected'=>true, 'pattern'=>'^[a-zA-Z]+$'),
		'abcDef-'					=> array('expected'=>true, 'pattern'=>'^[a-zA-Z\-]+$'),
		'abcDef34-'					=> array('expected'=>true, 'pattern'=>'^[a-zA-Z0-9\-]+$'),
		'abcDef34_' 				=> array('expected'=>false, 'pattern'=>'^[a-zA-Z\-]+$'),
	)
);