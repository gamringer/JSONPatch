<?php

include 'vendor/autoload.php';

class ArrayAccessible implements \ArrayAccess
{
	private $foo = ["bar", "baz"];
	public $qux = "quux";

	public function offsetExists($offset)
	{
		return isset($this->{$offset});
	}

	public function &offsetGet($offset)
	{
		return $this->{$offset};
	}

	public function offsetSet($offset, $value)
	{
		$this->{$offset} = $value;
	}

	public function offsetUnset($offset)
	{
		unset($this->{$offset});
	}

}

$target = new ArrayAccessible();
$pointer = new \gamringer\JSONPointer\Pointer($target);
var_dump($pointer->get('/foo'));
$pointer->set('/foo', 'lol');
var_dump($pointer->get('/foo'));
