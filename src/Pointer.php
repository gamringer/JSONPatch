<?php

namespace gamringer\JSONPointer;

class Pointer
{
    private $target;

    public function __construct(&$target = null)
    {
        if ($target !== null) {
            $this->setTarget($target);
        }
    }

    public function setTarget(&$target)
    {
        if(!(
            gettype($target) === 'array'
         || $target instanceof \ArrayAccess
        )) {
            throw new \InvalidArgumentException('$target must be either an Array or ArrayAccess object');
        }

        $this->target = &$target;
    }

    public function getTarget()
    {
        return $this->target;
    }

    private function reference($path)
    {
        if (!isset($this->target)) {
            throw new Exception('No target defined');
        }

        if (substr($path, 0, 1) === '#') {
            $path = urldecode(substr($path, 1));
        } else {
            $path = stripslashes($path);
        }

        if (empty($path)) {
            return new ReferencedValue($this->target);
        }

        if (substr($path, 0, 1) !== '/') {
            throw new Exception('Invalid pointer syntax');
        }

        $target = &$this->target;

        $tokens = explode('/', substr($path, 1));
        foreach ($tokens as $token) {

            if(!(
                gettype($target) === 'array'
             || $target instanceof \ArrayAccess
            )) {
                throw new Exception('JSONPointer can only walk through Array or ArrayAccess instances');
            }

            $token = $this->unescape($token);
            if (!isset($target[$token])) {
                throw new Exception('Referenced value does not exist');
            }

            $target = &$target[$token];
        }

        return new ReferencedValue($target);
    }

    public function get($path)
    {
        return $this->reference($path)->getValue();
    }

    public function set($path, $value)
    {
        $this->reference($path)->setValue($value);

        return $this;
    }

    private function unescape($token)
    {
        $token = (string) $token;

        if (preg_match('/~[^01]/', $token)) {
            throw new Exception('Invalid pointer syntax');
        }

        $token = str_replace('~1', '/', $token);
        $token = str_replace('~0', '~', $token);

        return $token;
    }
}