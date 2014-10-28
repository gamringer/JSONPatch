JSONPointer
===========

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/9f14b6ae-8100-4c43-9084-b17f57165026/mini.png)](https://insight.sensiolabs.com/projects/9f14b6ae-8100-4c43-9084-b17f57165026)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Test Coverage](https://codeclimate.com/github/gamringer/JSONPointer/badges/coverage.svg)](https://codeclimate.com/github/gamringer/JSONPointer)
[![Code Climate](https://codeclimate.com/github/gamringer/JSONPointer/badges/gpa.svg)](https://codeclimate.com/github/gamringer/JSONPointer)

A RFC6901 compliant JSON Pointer PHP implementation

Example
-------

```php
<?php

$target = [
	"foo" => ["bar", "baz"],
	"qux" => "quux"
];

$pointer = new \gamringer\JSONPointer\Pointer($target);

echo $pointer->get("/foo"); // ["bar", "baz"]
echo $pointer->set("/qux", "corge");
echo $pointer->get("/qux"); // "corge"
```
