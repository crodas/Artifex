<?php
namespace Artifex\Runtime;

use Artifex\Runtime;

class RawString extends Base
{
    public function execute(Runtime $vm)
    {
        $text = preg_replace_callback("/__([a-z][a-z0-9_]*)__/i", function($var) use ($vm) {
            $value = $vm->get($var[1]);
            if (is_null($value)) {
                return $var[0];
            }
            
            $result = $value->getValue($vm);
            if ($result instanceof Base) {
                $result = $result->getValue($vm);
            }
            return $result;
        }, $this->args);

        $vm->doPrint($text);
    }
}
