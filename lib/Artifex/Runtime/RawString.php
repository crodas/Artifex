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
                /* variable is not found, we ignore it */
                return $var[0];
            }
            
            $result = $vm->getValue($value);
            if (!is_scalar($result)) {
                throw new \RuntimeException("Only scalar values may be replaced. Use @ to get the string representation.");
            }
            return $result;
        }, $this->args);

        $vm->doPrint($text);
    }
}
