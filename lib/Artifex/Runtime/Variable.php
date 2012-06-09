<?php
namespace Artifex\Runtime;

use Artifex\Runtime;

class Variable extends Base {
    protected $var;

    public function __construct(Array $variable) 
    {
        $this->var = $variable;
    }

    public function getNative()
    {
        return $this->var;
    }

    public function __toString()
    {
        return "$" . implode("->", $this->var);
    }

    public function getValue(Runtime $vm)
    {
        return $vm->get($this);
    }

}

