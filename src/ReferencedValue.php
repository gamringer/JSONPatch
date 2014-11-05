<?php

namespace gamringer\JSONPointer;

class ReferencedValue
{
    private $owner;
    private $token;

    public function __construct(&$owner, $token = null)
    {

        if ($token !== null && !isset($owner[$token])) {
            throw new Exception('Referenced value does not exist');
        }

        $this->owner = &$owner;
        $this->token = $token;
    }

    public function getValue()
    {
        if ($this->token == null) {
            return $this->owner;
        }

        return $this->owner[$this->token];
    }

    public function setValue($value)
    {
        if ($this->token == null) {
            $this->owner = $value;
        }

        $this->owner[$this->token] = $value;
    }

    public function unsetValue()
    {
        if ($this->token == null) {
            $this->owner = null;
        }

        unset($this->owner[$this->token]);
    }
}
