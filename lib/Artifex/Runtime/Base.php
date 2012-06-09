<?php
namespace Artifex\Runtime;

use Artifex\Runtime;

class Base
{
    protected $args;

    public function __construct() 
    {
        $this->args = func_get_args();
        if (count($this->args) == 1) {
            $this->args = $this->args[0];
        }
    }
}

