<?php
namespace Artifex\Runtime;

use Artifex\Runtime;

class Term extends Base
{
    protected $value;

    public function __construct($value) 
    {
        $this->value = $value;
    }

    public function getValue(Runtime $vm)
    {
        return $this->value;
    }

}
