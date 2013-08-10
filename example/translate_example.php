<?php

require dirname(__DIR__).'/Translate.php';


// compile and parse
$translate = new \Translate\Translate(__DIR__.'/input', 'en');

$translate->parse(
	$translate->compile('Welcome $PSEUDO !'),
	array(
		 'PSEUDO' => 'Dauclem',
	));
$translate->parse(
	$translate->compile('You have {$NUM, plural, zero[No new messages] one[$NUM new message] other[$NUM new messages]} in your mailbox.'),
	array(
		 'NUM' => 2,
	));
$translate->parse(
	$translate->compile('You have {$NUM, plural, zero[No new messages] one[$NUM new message] other[$NUM new messages]} in your mailbox from {$NUM2, plural, zero[no friends] one[$NUM2 friend] other[$NUM2 friends]}.'),
	array(
		 'NUM'  => 2,
		 'NUM2' => 1,
	));
$translate->parse(
	$translate->compile('{$SEL1, select, other [Define : {$PLUR1, plural, one [1] other [{$SEL2, select, other [deep in the heart.] }] }] }'),
	array(
		 'SEL1'  => 'test',
		 'PLUR1' => 3,
		 'SEL2'  => 'test2',
	));


// preCompile + parse
$translate->preCompile(__DIR__.'/input', __DIR__.'/output');
$translate_keys = array();
require __DIR__.'/output/en.php';

$translate->parse(
	$translate_keys['en']['user_pseudo'],
	array(
		 'PSEUDO' => 'Dauclem',
	));
$translate->parse(
	$translate_keys['en']['new_message'],
	array(
		 'NUM' => 2,
	));
$translate->parse(
	$translate_keys['en']['new_message_more'],
	array(
		 'NUM'  => 2,
		 'NUM2' => 1,
	));
$translate->parse(
	$translate_keys['en']['complex_string'],
	array(
		 'SEL1'  => 'test',
		 'PLUR1' => 3,
		 'SEL2'  => 'test2',
	));