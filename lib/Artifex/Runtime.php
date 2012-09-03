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
    \Artifex\Runtime\Expr_Function,
    \Artifex\Runtime\Variable;

class Runtime 
{
    protected $stack = array();
    /**
     *  Keeps in track the latest statement processed
     */
    protected $lastStmt;
    protected $stmts;
    /** 
     *  Parent virtual machine
     */
    protected $parent;
    /**
     *  The Parent virtual machine
     *  has a list of their childrens
     */
    protected $children = array();
    protected $variables = array();
    protected $functions = array();
    protected $return  = NULL;
    protected $isStopped = false;
    protected $isSuspended = false;

    public function __construct(Array $stmts)
    {
        foreach ($stmts as $stmt) {
            if ($stmt instanceof Expr_Function) {
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
        $vm->children[] = $this;
    }

    public function getParentVm()
    {
        return $this->parent;
    }

    public function setPwd($dir)
    {
        if (!is_dir($dir)) {
            throw new \RuntimeException("{$dir} is not a directory");
        }
        $this->pwd = $dir;
        return $this;
    }

    public function getPath($tpl) 
    {
        if (is_file($this->pwd . '/' . $tpl)) {
            $file = $this->pwd . '/' . $tpl;
        } else {
            $file = stream_resolve_include_path($tpl);
            if (empty($file)) {
                throw new \RuntimeException("Cannot include template {$tpl}");
            }
        }
        return $file;
    }

    public function doInclude($tpl)
    {
        $file = $this->getPath($tpl);
        $vm   = \Artifex::load($file, $this->variables);
        $fnc  = array_merge($this->functions, $vm->functions);
        $this->functions = $fnc;
        $vm->functions   = $fnc;
        return $vm->run();
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

    public function getLatestWhitespace(Base $prev)
    {
        do {
            while ($prev->getPrev()) {
                if ($prev instanceof Runtime\Raw && !$prev->isString()) {
                    break; 
                }
                $prev = $prev->getPrev();
            }

            if ($prev instanceof Runtime\Raw && !$prev->isString() || !$prev->getParent()) {
                break;
            }
            $prev = $prev->getParent();
        } while (true);

        return $prev instanceof Runtime\Whitespace ? $prev : NULL;
    }

    public function printIndented($output, Base $current = NULL)
    {
        $vm = $this;
        if (count($this->children) > 0) {
            $vm = $this->children[ count($this->children)-1];
        }
        if (is_null($current)) {
            $current = $vm->lastStmt;
        }
        $indent = $vm->getLatestWhitespace($current);
        if ($indent) {
            $indent = $this->getValue($indent);
            $lines  = array_map(function($line) use ($indent) {
                return $indent . $line;
            }, explode("\n", rtrim($output, "\n")));
            $output = implode("\n", $lines) . "\n";
        }
        $vm->doPrint($output);
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
        $isLocal = false;
        if (is_null($func)) {
            throw new \RuntimeException("Can't find function {$name}");
        } 

        if ($func instanceof \Closure) {
            return $func;
        }

        $vm = $this;
        $isLocal = true;
        return function() use ($func, $vm) {
            return $func->body($vm, func_get_args());
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
                $part = $key[$i] instanceof Base ? $this->getValue($key[$i]) : $key[$i];
                try {
                    if (!is_scalar($part)) {
                        $value = NULL;
                        break;
                    }
                    if (is_array($value) || (is_object($value) && $value instanceof \ArrayAccess)) {
                        if (!array_key_exists($part, $value)) {
                            if (!is_object($value) || !$value->offsetExists($part)) {
                                throw new \Exception;
                            }
                        }
                        $value = $value[$part];
                    } else if (is_object($value)) {
                        if (!property_exists($value, $part)) {
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

    public function halt($return = NULL)
    {
        $this->return    = $return;
        $this->isStopped = true;
    }

    public function isRunning()
    {
        return !$this->isStopped;
    }

    public function isStopped($halt = NULL)
    {
        if (is_null($halt)) {
            return $this->isStopped;
        }
        $this->isStopped = (bool)$halt;
    }

    public function isSuspended($suspended = NULL)
    {
        if (is_null($suspended)) {
            return $this->isSuspended;
        }
        $this->isSuspended = (bool)$suspended;
    }

    public function run()
    {
        $this->buffer = "";
        $this->execStmts($this->stmts);
        if ($this->parent) {
            array_pop($this->parent->children);
        }
        return $this->buffer;
    }

    public function getBuffer()
    {
        return $this->buffer;
    }

    public function getReturn()
    {
        return $this->return;
    }

    public function __toString()
    {
        return $this->buffer;
    }

    public function execute(Base $stmt) {
        if (!$stmt instanceof Variable) {
            $this->lastStmt = $stmt;
        }
        $stmt->execute($this);
    }

    public function execStmts(Array $stmts)
    {
        foreach ($stmts as $stmt) {
            $this->execute($stmt);
            if ($this->isStopped || $this->isSuspended) {
                break;
            }
        }
    }

    public function getValue(Base $obj, $extra = NULL)
    {
        if (!$obj instanceof Variable) {
            $this->lastStmt = $obj;
        }
        $obj = $obj->getValue($this, $extra);
        while ($obj instanceof Base) {
            $obj = $obj->getValue($this, $extra);
        }
        return $obj;
    }

}
