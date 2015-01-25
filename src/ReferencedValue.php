<?php

namespace gamringer\JSONPointer;

class ReferencedValue
{
    private $owner;
    private $token;

    private $isNext = false;

    public function __construct(&$owner, $token = null)
    {
        if($token == '-' && is_array($owner) && !$this->isAssoc($owner)){
            $this->isNext = true;
        }

        if ($token !== null && !isset($owner[$token]) && !$this->isNext) {
            throw new Exception('Referenced value does not exist');
        }

        $this->owner = &$owner;
        $this->token = $token;
    }

    public function getValue()
    {
        if($this->isNext){
            throw new Exception('Referenced next value can not be retrieved');
        }

        if ($this->token === null) {
            return $this->owner;
        }

        return $this->owner[$this->token];
    }

    public function setValue($value)
    {
        if($this->isNext){

            $this->owner[] = $value;

            return $this;
        }

        if ($this->token === null) {
            $this->owner = $value;

            return $this;
        }

        $this->owner[$this->token] = $value;

        return $this;
    }

    public function unsetValue()
    {
        if($this->isNext){
            throw new Exception('Referenced next value can not be removed');
        }

        if ($this->token === null) {
            $this->owner = null;

            return $this;
        }

        unset($this->owner[$this->token]);

        return $this;
    }

    private function isAssoc(Array $array) {
        return (bool)count(array_filter(array_keys($array), 'is_string'));
    }
}
