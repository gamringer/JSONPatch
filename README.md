JSONPointer
============

[![License](https://poser.pugx.org/gamringer/php-json-patch/license.svg)](https://packagist.org/packages/gamringer/php-json-patch)
[![Latest Stable Version](https://poser.pugx.org/gamringer/php-json-patch/v/stable.svg)](https://packagist.org/packages/gamringer/php-json-patch)
[![Latest Unstable Version](https://poser.pugx.org/gamringer/php-json-patch/v/unstable.svg)](https://packagist.org/packages/gamringer/php-json-patch)
[![Total Downloads](https://poser.pugx.org/gamringer/php-json-patch/downloads.svg)](https://packagist.org/packages/gamringer/php-json-patch)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/9f14b6ae-8100-4c43-9084-b17f57165026/mini.png)](https://insight.sensiolabs.com/projects/9f14b6ae-8100-4c43-9084-b17f57165026)

[![Build Status](https://travis-ci.org/gamringer/JSONPatch.svg?branch=master)](https://travis-ci.org/gamringer/JSONPatch)

[![Build Status](https://scrutinizer-ci.com/g/gamringer/JSONPatch/badges/build.png?b=master)](https://scrutinizer-ci.com/g/gamringer/JSONPatch/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/gamringer/JSONPatch/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/gamringer/JSONPatch/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/gamringer/JSONPatch/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/gamringer/JSONPatch/?branch=master)

A RFC6902 compliant JSON Patch PHP implementation

#License
JSONPatch is licensed under the MIT license.

#Installation

    composer require gamringer/php-json-patch

##Tests

    composer install
    phpunit
    
#Documentation

##Operations can be constructed and applied independently
```php
<?php

$target = ['1', '2', '3'];
$operation = new \gamringer\JSONPatch\Operation\Test('/foo', 'bar');
$operation->apply($target);

```

##Operations can also be constructed fonr a JSON string
```php
<?php

$operation = \gamringer\JSONPatch\Operation\Test::fromDecodedJSON(json_decode('{"path":"/foo","value":"bar"}'));

```

##A patch can be constructed from a set of operations
```php
<?php

$patch = new \gamringer\JSONPatch\Patch();

$patch->addOperation(new \gamringer\JSONPatch\Operation\Add('/foo', 'bar'));
$patch->addOperation(new \gamringer\JSONPatch\Operation\Test('/foo', 'bar'));
```

##A patch can also be constructed from a JSON string
```php
<?php

$patch = \gamringer\JSONPatch\Patch::fromJSON('[{"op":"add","path":"/foo","value":"bar"},{"op":"test","path":"/foo","value":"bar"}]');
```

##A patch can be applied
```php
<?php

$patch = \gamringer\JSONPatch\Patch::fromJSON('[{"op":"add","path":"/foo","value":"bar"},{"op":"test","path":"/foo","value":"bar"}]');

$target = [];
$patch->apply($target);

var_dump($target);

/* Results:

array(1) {
  'foo' =>
  string(3) "bar"
}

*/

```
If the patch fails, it gets completely reverted and an exception is thrown.

```php
<?php

$patch = \gamringer\JSONPatch\Patch::fromJSON('[{"op":"add","path":"/foo","value":"bar"},{"op":"test","path":"/foo","value":"baz"}]');

$target = [];

try {
    $patch->apply($target);
} catch (\gamringer\JSONPatch\Exception $e) {
    var_dump($target);
}

/* Results:

array(0) {}

*/

```
