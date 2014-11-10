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
        if ($this->token === null) {
            return $this->owner;
        }

        return $this->owner[$this->token];
    }

    public function setValue($value)
    {
        if ($this->token === null) {
            $this->owner = $value;

            return $this;
        }

        $this->owner[$this->token] = $value;
        
        return $this;
    }

    public function unsetValue()
    {
        if ($this->token === null) {
            $this->owner = null;

            return $this;
        }

        unset($this->owner[$this->token]);

        return $this;
    }
}
