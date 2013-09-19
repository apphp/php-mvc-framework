<?php

$prepare_data = array(
	'isAlpha'   => array('abcdef', 'AbcdZxc', '123abc', 12345, "-*.,/"),
	'isNumeric' => array('12345', '01234', '123abc', 12345, "-*.,/", +12, '+12', -1, '+1', 0),
	'isEmail'   => array('me@email.com', 'me.email', 'email@email@.com', 'com.email@me', 'me.email@.com', 'me.email@email.com', 'me.email.me@email.info', 'me.e_mail99@email.info', 'me.e_*mail@email.info'),
);