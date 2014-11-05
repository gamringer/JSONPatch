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
        $this->assertWalkable($target);

        $this->target = &$target;
    }

    public function getTarget()
    {
        return $this->target;
    }

    private function reference($path)
    {
        $this->assertTarget();

        $path = $this->getRawPath($path);
        if (empty($path)) {
            return new ReferencedValue($this->target);
        }

        return $this->walk($path);
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

    public function remove($path)
    {
        $this->reference($path)->unsetValue();

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

    private function getRawPath($path)
    {
        $path = (string) $path;

        if (substr($path, 0, 1) === '#') {
            $path = urldecode(substr($path, 1));
        } else {
            $path = stripslashes($path);
        }

        if (isset($path[0]) && $path[0] !== '/') {
            throw new Exception('Invalid pointer syntax');
        }

        return $path;
    }

    private function walk($path)
    {
        $target = &$this->target;

        $tokens = explode('/', substr($path, 1));
        while (true) {
            $token = array_shift($tokens);

            $this->assertWalkable($target);

            $token = $this->unescape($token);

            if (empty($tokens)) {
                return new ReferencedValue($target, $token);
            }

            if (!isset($target[$token])) {
                throw new Exception('Referenced value does not exist');
            }
            
            $target = &$target[$token];
        }

        return $target;
    }

    private function assertWalkable($item)
    {
        if(!(
            gettype($item) === 'array'
         || $item instanceof \ArrayAccess
        )) {
            throw new Exception('JSONPointer can only walk through Array or ArrayAccess instances');
        }
    }

    private function assertTarget()
    {
        if (!isset($this->target)) {
            throw new Exception('No target defined');
        }
    }
}
