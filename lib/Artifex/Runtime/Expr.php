<?php
namespace Artifex\Runtime;

use Artifex\Runtime;

class Expr extends Base 
{
    public function getValue(Runtime $vm)
    {
        $args = $this->args;
        if ($args instanceof self) {
            return $args;
        }
        switch (strtolower($args[0])) {
        case '>':
            $value = $vm->getValue($args[1]) > $vm->getValue($args[2]);
            break;
        case '>=':
            $value = $vm->getValue($args[1]) >= $vm->getValue($args[2]);
            break;
        case '*':
            $value = $vm->getValue($args[1]) * $vm->getValue($args[2]);
            break;
        case '/':
            $value = $vm->getValue($args[1]) / $vm->getValue($args[2]);
            break;
        case '-':
            $value = $vm->getValue($args[1]) - $vm->getValue($args[2]);
            break;
        case '+':
            $value = $vm->getValue($args[1]) + $vm->getValue($args[2]);
            break;
        case '%':
            $value = $vm->getValue($args[1]) % $vm->getValue($args[2]);
            break;
        case '==':
            $value = $vm->getValue($args[1]) == $vm->getValue($args[2]);
            break;
        case 'and':
        case '&&':
            $value = $vm->getValue($args[1]) && $vm->getValue($args[2]);
            break;
        case '!=':
            $value = $vm->getValue($args[1]) != $vm->getValue($args[2]);
            break;
        case 'not':
            $value = !$vm->getValue($args[1]);
            break;
        default:
            throw new \RuntimeException("{$args[0]} is not implemented");
        }
        return $value;
    }
}
