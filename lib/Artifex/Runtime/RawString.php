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

class RawString extends Base
{
    protected $args;
    protected $isString;

    public function __construct($string, $isString = false)
    {
        $this->args = $string;
        $this->isString = $isString;
    }

    public function isString()
    {
        return $this->isString;
    }

    public function execute(Runtime $vm)
    {
        $text = preg_replace_callback("/__(@?[a-z][a-z0-9_]*)__/i", function($var) use ($vm) {
            if ($var[1][0] == '@') {
                $var[1]   = substr($var[1], 1);
                $varValue = true;
            }
            $value = $vm->get($var[1]);
            if (is_null($value)) {
                /* variable is not found, we ignore it */
                return $var[0];
            }
            
            $result = $vm->getValue($value);

            if (!empty($varValue)) {
                $result = var_export($result, true);
            }

            if (is_object($result) && is_callable(array($result, '__toString'))) {
                $result = (string)$result;
            }
            if (!is_scalar($result)) {
                throw new \RuntimeException("Only scalar values may be replaced. Use @ to get the string representation.");
            }

            return $result;
        }, $this->args);

        if ($this->isString()) {
            $prev = $this;
            while ($prev->getParent()) {
                $prev = $prev->getParent();
            }
        }

        $vm->doPrint($text);
    }
}
