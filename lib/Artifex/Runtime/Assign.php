<?php
namespace Artifex\Runtime;

use Artifex\Runtime;

class Assign extends Base 
{
    protected $var;
    protected $expr;

    public function __construct(Variable $var, $expr) {
        $this->var  = $var;
        $this->expr = $expr;
    }

    public function execute(Runtime $vm) 
    {
        $vm->define($this->var, $vm->getValue($this->expr));
    }
}

