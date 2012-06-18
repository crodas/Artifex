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

namespace Artifex\Util;

/**
 *  Simple class that makes easy to iterate
 *  through the output of `token_get_all`
 *
 *  @author Cesar Rodas <crodas@php.net>  
 */
class PHPTokens
{
    protected $tokens = array();
    protected $offset = 0;
    protected $total  = 0;
    protected $events = array();
    protected $stack  = array();

    public function reset()
    {
        $this->offset = 0;
        $this->total  = 0;
        $this->tokens = array();
    }

    public function setFile($file)
    {
        $this->reset();
        if (!is_readable($file)) {
            throw new \RuntimeException("{$file} is not readable");
        }
        $this->setTokens(token_get_all(file_get_contents($file)));
        $this->run();
    }

    public function WhileNot(Array $searchTokens)
    {
        $tokens = $this->tokens;
        for ($i = &$this->offset; $i < $this->total; $i++) {
            if (is_array($tokens[$i])) {
                if (in_array($tokens[$i][0], $searchTokens)) {
                    return $this;
                }
            } else if (in_array($tokens[$i], $searchTokens)) {
                return $this;
            }
        }
        throw new \RuntimeException("Cannot find any of " . print_r($searchTokens, true));
    }

    public function revWhileNot(Array $searchTokens)
    {
        $tokens = $this->tokens;
        for ($i = &$this->offset; $i >= 0; $i--) {
            if (is_array($tokens[$i])) {
                if (in_array($tokens[$i][0], $searchTokens)) {
                    return $this;
                }
            } else if (in_array($tokens[$i], $searchTokens)) {
                return $this;
            }
        }
        throw new \RuntimeException("Cannot find any of " . print_r($searchTokens, true));
    }

    public function moveWhile(Array $searchTokens)
    {
        $tokens = $this->tokens;
        for ($i = &$this->offset; $i < $this->total; $i++) {
            if (is_array($tokens[$i])) {
                if (!in_array($tokens[$i][0], $searchTokens)) {
                    break;
                }
            } else if (!in_array($tokens[$i], $searchTokens)) {
                break;
            }
        }
        return $this;
    }

    public function moveWhileNot(Array $searchTokens)
    {
        $tokens = $this->tokens;
        for ($i = &$this->offset; $i < $this->total; $i++) {
            if (is_array($tokens[$i])) {
                if (in_array($tokens[$i][0], $searchTokens)) {
                    return $this;
                }
            } else if (in_array($tokens[$i], $searchTokens)) {
                return $this;
            }
        }
        throw new \RuntimeException("Cannot find any of " . print_r($searchTokens, true));
    }

    public function move($inc = 1)
    {
        $this->offset += $inc;
        return $this;
    }

    public function getOffset()
    {
        return $this->offset;
    }

    public function getStack()
    {
        return $this->stack;
    }

    public function getToken()
    {
        return $this->tokens[$this->offset];
    }

    public function getTokens($start, $len)
    {
        return array_slice($this->tokens, $start, $len);
    }

    public function run()
    {
        $tokens = $this->tokens;
        $trait  = defined('T_TRAIT') ? T_TRAIT : -1; 
        $i = &$this->offset;
        for($i=0; $i < $this->total; $i++) {
            switch ($tokens[$i]) {
            case '{':
                $x = $i;
                $this->revWhileNot(array(
                    T_FUNCTION, T_CLASS, T_NAMESPACE, T_IF, T_ELSE, 
                    T_WHILE, T_FOR, T_FOREACH, T_DO, T_ELSEIF, T_INTERFACE,
                    $trait
                ));
                $this->stack[] = $tokens[$i][0];
                $i = $x;
                break;
            case '}':
                array_pop($this->stack);
                break;
            }
            $value = is_array($tokens[$i]) ? $tokens[$i][0] : $tokens[$i];
            if (isset($this->events[$value])) {
                $x = $i;
                foreach ($this->events[$value] as $callback) {
                    call_user_func($callback, $this, $tokens[$i]);
                }
                $i = $x;
            }
        }
    }

    public function setTokens(Array $tokens)
    {
        $this->tokens = $tokens;
        $this->total  = count($tokens);
    }

    public function on($event, $callback)
    {
        if (!is_callable($callback)) {
            throw new \RuntimeException("{$callback} is not callable");
        }
        if (empty($this->events[$event])) {
            $this->events[$event] = array();
        }
        $this->events[$event][] = $callback;
    }

}
