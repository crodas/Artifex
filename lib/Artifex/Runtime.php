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
namespace Artifex;

use \Artifex\Runtime\Assign,
    \Artifex\Runtime\Base,
    \Artifex\Runtime\Concat,
    \Artifex\Runtime\Exec,
    \Artifex\Runtime\Expr,
    \Artifex\Runtime\Expr_If,
    \Artifex\Runtime\Expr_Foreach,
    \Artifex\Runtime\RawString,
    \Artifex\Runtime\Term,
    \Artifex\Runtime\Variable;

class Runtime 
{
    protected $stmts;
    protected $varibles;

    public function __construct(Array $stmts)
    {
        $this->stmts = $stmts;
    }

    public function setContext(Array $context)
    {
        foreach($context as $key => $value) {
            $this->define($key, $value);
        }
    }

    public function define($key, $value)
    {
        if ($key instanceof Variable) {
            $key = $key->getNative();
            if (count($key) == 1) {
                $key = $key[0];
            } else {
                throw new \RuntimeException("I'm not yet implemented");
            }
        }

        $this->variables[$key] = $value instanceof Term ? $value : new Term($value);
    }

    public function get($key)
    {
        if ($key instanceof Variable) {
            $key = $key->getNative();
        }
        if (is_array($key) && count($key) == 1) {
            $key = $key[0];
        } else if (is_array($key)) {
            throw new \RuntimeException("I'm not yet implemented");
        }
        if (array_key_exists($key, $this->variables)) {
            return $this->variables[$key];
        }
        return NULL;
    }

    public function doPrint($text)
    {
        $this->buffer .= $text;
    }

    public function run()
    {
        $this->buffer = "";
        $this->execStmts($this->stmts);
        return $this->buffer;
    }

    public function execStmts(Array $stmts)
    {
        foreach ($stmts as $stmt) {
            $stmt->execute($this);
        }
    }

    public function getValue(Base $obj)
    {
        $obj = $obj->getValue($this);
        while ($obj instanceof Base) {
            $obj = $obj->getValue($this);
        }
        return $obj;
    }

}
