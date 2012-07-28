<?php
/*
  +---------------------------------------------------------------------------------+
  | Copyright (c) 2012 César Rodas                                                  |
  +---------------------------------------------------------------------------------+
  | Redistribution and use in source and binary forms, with or without              |
  | modification, are permitted provided that the following conditions are met:     |
  | 1. Redistributions of source code must retain the above copyright               |
  |    notice, this list of conditions and the following disclaimer.                |
  |                                                                                 |
  | 2. Redistributions in binary form must reproduce the above copyright            |
  |    notice, this list of conditions and the following disclaimer in the          |
  |    documentation and/or other materials provided with the distribution.         |
  |                                                                                 |
  | 3. All advertising materials mentioning features or use of this software        |
  |    must display the following acknowledgement:                                  |
  |    This product includes software developed by César D. Rodas.                  |
  |                                                                                 |
  | 4. Neither the name of the César D. Rodas nor the                               |
  |    names of its contributors may be used to endorse or promote products         |
  |    derived from this software without specific prior written permission.        |
  |                                                                                 |
  | THIS SOFTWARE IS PROVIDED BY CÉSAR D. RODAS ''AS IS'' AND ANY                   |
  | EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED       |
  | WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE          |
  | DISCLAIMED. IN NO EVENT SHALL CÉSAR D. RODAS BE LIABLE FOR ANY                  |
  | DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES      |
  | (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;    |
  | LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND     |
  | ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT      |
  | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS   |
  | SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE                     |
  +---------------------------------------------------------------------------------+
  | Authors: César Rodas <crodas@php.net>                                           |
  +---------------------------------------------------------------------------------+
*/

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

    public function functionEmpty(Array $args, Runtime $vm)
    {
        if (count($args) != 1) {
            throw new \RuntimeException("empty expects 1 argument");
            throw new \RuntimeException("empty expects 1 argument");
        }

        return empty($args[0]);
    }
 
    public function functionPrint(Array $args, Runtime $vm)
    {
        return $vm->doPrint($args[0]);
    }

    public function functionInclude(Array $args, Runtime $vm)
    {
        $content = $vm->doInclude($args[0]);
        $vm->printIndented($content, $this);
    }

    public function getValue(Runtime $vm, $doPrint = true) 
    {
        $args = array();
        foreach ($this->args as $arg) {
            if (is_null($arg)) continue;
            $val = $vm->getValue($arg);
            $args[] = $val;
        }

        $function = $this->function;
        if (is_string($function) && is_callable(array($this, 'function' . $this->function))) {
            return $this->{'function' . $this->function}($args, $vm);
        }

        if ($function instanceof Variable) {
            // call methods
            if ($function->isObject()) {
                $object = $vm->getValue($function->getParent());
                $method = $function->getPart(-1);
                if (!is_callable(array($object, $method))) {
                    throw new \RuntimeException(get_class($object) . '::' . $method . ' is not callable');
                }
                return call_user_func_array(array($object, $method), $args);
            } else {
                // $foo();
                $function = $vm->getValue($function);
            }
        }

        $isLocal = false;
        if ($vm->functionExists($function)) {
            $function = $vm->getFunction($function, $isLocal);
        }

        if (!is_callable($function)) {
            throw new \RuntimeException("{$function} is not a function");
        }

        $output = call_user_func_array($function, $args);

        if ($doPrint) {
            // if the function is indeed a local function
            // (and not defined in the php side) then
            // we should print and leave
            $vm->printIndented($isLocal ? $output->getBuffer() : $output, $this);
            return;
        }

        if ($isLocal && $output->getReturn()) {
            $vm->printIndented($output->getBuffer(), $this);
            return $output->getReturn();
        }

        return $output;
    }

    public function Execute(Runtime $vm)
    {
        return $this->getValue($vm);
    }
}

