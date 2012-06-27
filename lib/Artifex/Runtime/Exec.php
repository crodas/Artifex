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

        $function = $this->function;
        if ($vm->functionExists($function)) {
            $function = $vm->getFunction($function);
            $output = call_user_func_array($function , $args);
            if  ($this->prev instanceof Whitespace) {
                $indent = $vm->getValue($this->prev);
                $lines  = array_map(function($line) use ($indent) {
                    return $indent . $line;
                }, explode("\n", rtrim($output, "\n")));
                $output = implode("\n", $lines) . "\n";
            }
            $vm->doPrint($output);
            return;
        }
        return call_user_func_array($function , $args);
    }

    public function Execute(Runtime $vm)
    {
        return $this->getValue($vm);
    }
}

