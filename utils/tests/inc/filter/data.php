<?php

$prepare_data = array(
	'sanitize'   => array(
		array('string', 'wrwer_wer'),
		array('string', 'asd$56fgh'),
		array('string', 'a_+sdTgg#$56fgh'),
		array('email', "^()!#$%&'*+-/=?^_`{|}~@.[]"),
		array('email', 'email.me@email.me'),
		array('email', 'email@e+-mail.me'),
		array('url', "http://www.domain.com/$-_!2=+-"),
		array('alpha', 'asd@#$23423234'),
		array('alpha', 'asdAss'),
		array('alpha', 'abc123'),
		array('alphanumeric', 'asdAss_+12345'),
		array('alphanumeric', 'asdAss12345'),
		array('number_int', '12345'),
		array('number_int', '0123'),
		array('number_int', '1_eRf45_)(*'),
		array('number_float', '123'),
		array('number_float', '+123'),
		array('number_float', '123.12'),
		array('number_float', '123,12'),
		array('number_float', '123.12a'),
		array('number_float', '123e+02'),
		array('number_float', '0.123e+02'),
	),
);
	