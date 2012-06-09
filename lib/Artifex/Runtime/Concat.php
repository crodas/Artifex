<?php
namespace Artifex\Runtime;

use Artifex\Runtime;

class Concat extends Base
{
    public function getValue(Runtime $vm)
    {
        $str = "";
        foreach ($this->args as $part) {
            $str .= $vm->getValue($part);
        }
        return $str;
    }
}
