<?php

require dirname(__DIR__).'/Translate.php';


// compile and parse
$locale   = 'en';
$filename = __DIR__.'/input/'.$locale.'.json';
\Translate\Translate::parse($locale,
							\Translate\Translate::compile('Welcome $PSEUDO !'),
							array(
								 'PSEUDO' => 'Dauclem',
							));
\Translate\Translate::parse($locale,
							\Translate\Translate::compile('You have {$NUM, plural, zero[No new messages] one[$NUM new message] other[$NUM new messages]} in your mailbox.'),
							array(
								 'NUM' => 2,
							));
\Translate\Translate::parse($locale,
							\Translate\Translate::compile('You have {$NUM, plural, zero[No new messages] one[$NUM new message] other[$NUM new messages]} in your mailbox from {$NUM2, plural, zero[no friends] one[$NUM2 friend] other[$NUM2 friends]}.'),
							array(
								 'NUM'  => 2,
								 'NUM2' => 1,
							));
\Translate\Translate::parse($locale,
							\Translate\Translate::compile('{$SEL1, select, other [Define : {$PLUR1, plural, one [1] other [{$SEL2, select, other [deep in the heart.] }] }] }'),
							array(
								 'SEL1'  => 'test',
								 'PLUR1' => 3,
								 'SEL2'  => 'test2',
							));


// preCompile + parse
\Translate\Translate::preCompile(__DIR__.'/input', __DIR__.'/output');
$translate_keys = array();
require __DIR__.'/output/en.php';

\Translate\Translate::parse($locale,
							$translate_keys['en']['user_pseudo'],
							array(
								 'PSEUDO' => 'Dauclem',
							));
\Translate\Translate::parse($locale,
							$translate_keys['en']['new_message'],
							array(
								 'NUM' => 2,
							));
\Translate\Translate::parse($locale,
							$translate_keys['en']['new_message_more'],
							array(
								 'NUM'  => 2,
								 'NUM2' => 1,
							));
\Translate\Translate::parse($locale,
							$translate_keys['en']['complex_string'],
							array(
								 'SEL1'  => 'test',
								 'PLUR1' => 3,
								 'SEL2'  => 'test2',
							));