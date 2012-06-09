<?php

namespace Artifex\Runtime;

use Artifex\Runtime;

class Exec extends Base
{
    protected $function;
    protected $args;

    public function __construct($function, $arguments) 
    {
        $this->function = $function;
        $this->args     = $arguments;
    }

    public function getValue(Runtime $vm) 
    {
        $args = array();
        foreach ($this->args as $arg) {
            if (is_null($arg)) continue;
            $val = $vm->getValue($arg);
            $args[] = $val;
        }
        if (strtolower($this->function) == "print") {
            return $vm->doPrint($args[0]);
        }
        return call_user_func_array($this->function , $args);
    }

    public function Execute(Runtime $vm)
    {
        return $this->getValue($vm);
    }
}

