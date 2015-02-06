<?php

include 'vendor/autoload.php';

$target = [
	'foo' =>'bar'
];

try{
	$patch = \gamringer\JSONPatch\Patch::fromJSON(file_get_contents(__DIR__ . '/../tests/Resources/patches/patch-add.json'));
} catch(\Exception $e) {
	var_dump($e);
}
$patch->apply($target);

var_dump($target);
