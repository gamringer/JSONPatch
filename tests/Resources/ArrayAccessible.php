<?php

namespace gamringer\JSONPointer\Test\Resources;

class ArrayAccessible implements \ArrayAccess
{
	public function __construct($attributes)
	{
		foreach ($attributes as $attribute => $value) {
			$this->{$attribute} = $value;
		}
	}

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
