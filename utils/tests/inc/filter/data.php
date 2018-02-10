<?php
/**
 * Format: type, value, result
 */
$prepare_data = array(
	'sanitize'   => array(
		array('string', 		'wrwer_wer', 					true),
		array('string', 		'asd$56fgh', 					true),
		array('string', 		'a_+sdTgg#$56fgh', 				true),
		array('email', 			"^()!#$%&'*+-/=?^_`{|}~@.[]", 	false),
		array('email', 			'email.me@email.me', 			true),
		array('email', 			'email@e+-mail.me', 			true),
		array('url', 			"http://www.domain.com/$-_!2=+-", true),
		array('alpha', 			'asd@#$23423234', 				false),
		array('alpha', 			'asdAss', 						true),
		array('alpha', 			'abc123', 						false),
		array('alphanumeric', 	'asdAss_+12345', 				false),
		array('alphanumeric', 	'asdAss12345', 					true),
		array('number_int', 	'12345', 						true),
		array('number_int', 	'0123', 						true),
		array('number_int', 	'1_eRf45_)(*', 					true),
		array('number_float', 	'123', 							true),
		array('number_float', 	'+123', 						true),
		array('number_float', 	'123.12', 						true),
		array('number_float', 	'123,12', 						true),
		array('number_float', 	'123.12a',	 					true),
		array('number_float', 	'123e+02',	 					true),
		array('number_float', 	'0.123e+02', 					true),
	),
);
	