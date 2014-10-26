JSONPointer
===========

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

echo $pointer->get("/foo");
echo $pointer->set("/qux", "corge");
echo $pointer->get("/qux");
```
