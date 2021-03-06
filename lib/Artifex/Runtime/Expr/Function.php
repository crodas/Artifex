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

use Artifex\Runtime,
    \Artifex\Runtime\Variable;

class Expr_Function extends Base
{
    protected $name;
    protected $args;
    protected $code;
    protected static $fixedXDebug = false;

    public function __construct($name, Array $args, Array $code)
    {
        $this->name = $name;
        $this->args = $args;
        $this->code = $code;
        foreach ($args as $arg) {
            if (!($arg instanceof Variable)) {
                throw new \RuntimeException("Invalid variable, it should be a variable");
            }
        }
    }

    public function getName() 
    {
        return $this->name;
    }

    /**
     *  If XDebug is installed it might give problems if the 
     *  templates to process has recursive function calls.
     *
     *  This function attempts to set a higher value for xdebug's
     *  max nesting levels.
     *  
     *  If XDebug is not found, then nothing happens.
     *
     *  @return void
     */
    public function fixXdebugRecursion()
    {
        if (self::$fixedXDebug) {
            return;
        }
        $level   = 2000;
        $current = ini_get('xdebug.max_nesting_level');
        if (is_numeric($current) && $current < $level) {
            ini_set('xdebug.max_nesting_level', $level);
        }
        self::$fixedXDebug = true;
    }
    
    public function body(Runtime $vm, Array $args = NULL) 
    {
        $fncargs = array();
        self::fixXDebugRecursion();
        foreach ($this->args as $id => $arg) {
            if (empty($args[$id])) break;
            $fncargs[current($arg->getNative())] = $args[$id];
        }
        $pzVm = new Runtime($this->code);
        if ($vm->getParentVm()) {
            $pzVm->setParentVm($vm->getParentVm());
        } else {
            $pzVm->setParentVm($vm);
        }
        if (count($fncargs) > 0) {
            $pzVm->setContext($fncargs);
        }
        $pzVm->run();
        return $pzVm;
    }
}
