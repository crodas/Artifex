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
    \Artifex\Runtime\DefFunction,
    \Artifex\Runtime\Variable;

class Runtime 
{
    protected $stack = array();
    protected $stmts;
    protected $parent;
    protected $variables = array();
    protected $functions = array();

    public function __construct(Array $stmts)
    {
        foreach ($stmts as $stmt) {
            if ($stmt instanceof DefFunction) {
                $this->functions[strtolower($stmt->getName())] = $stmt;
            }
        }
        $this->stmts = $stmts;
    }

    public function registerFunction($name, \Closure $closure) 
    {
        $this->functions[$name] = $closure;
        return $this;
    }

    public function setParentVm(Runtime $vm)
    {
        $this->parent = $vm;
    }

    public function getParentVm()
    {
        return $this->parent;
    }

    public function doInclude($tpl)
    {
        $file = stream_resolve_include_path($tpl);
        if (empty($file)) {
            throw new \RuntimeException("Cannot include template {$tpl}");
        }
        return \Artifex::load($file, $this->variables)->run();
    }

    public function setContext(Array $context)
    {
        foreach($context as $key => $value) {
            $this->define($key, $value);
        }
        return $this;
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

        return $this;
    }

    public function functionExists($name) {
        return !is_null($this->getFunctionObject($name));
    }

    protected function getFunctionObject($name)
    {
        $name = strtolower($name);
        if (array_key_exists($name, $this->functions)) {
            return $this->functions[$name];
        }
        if ($this->parent) {
            return $this->parent->getFunctionObject($name);
        }
        return NULL;
    }

    public function getFunction($name, &$isLocal = NULL) {
        $func = $this->getFunctionObject($name);
        if (is_null($func)) {
            throw new \RuntimeException("Can't find function {$name}");
        } 

        if ($func instanceof \Closure) {
            return $func;
        }

        $vm = $this;
        $isLocal = true;
        return function() use ($func, $vm) {
            return $func->execute($vm, func_get_args());
        };
    }

    public function get($key)
    {
        if ($key instanceof Variable) {
            $key = $key->getNative();
        }
        if (is_array($key) && count($key) == 1) {
            $key = $key[0];
        } else if (is_array($key)) {
            $value = $this->get($key[0]);
            if (empty($value)) {
                throw new \RuntimeException("Undefined variable {$key[0]}");
            }

            $value = $value->getValue($this);

            for ($i=1; $i < count($key); $i++) {
                $part = $this->getValue($key[$i]);
                try {
                    if (!is_scalar($part)) {
                        $value = NULL;
                        break;
                    }
                    if (is_array($value)) {
                        if (!array_key_exists($part, $value)) {
                            throw new \Exception;
                        }
                        $value = $value[$part];
                    } else if (is_object($value)) {
                        if (!class_property_exists($part, $value)) {
                            throw new \Exception;
                        }
                        $value = $value->$part;
                    } else {
                        throw new \Exception;
                    }
                } catch (\Exception $e) {
                    $value = NULL;
                    break;
                }
            }
            return $value;
        }

        if (array_key_exists($key, $this->variables)) {
            return $this->variables[$key];
        }

        if ($this->parent) {
            return $this->parent->get($key);
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
