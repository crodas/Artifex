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

class Expr_Foreach extends Base
{
    protected $source;
    protected $value;
    protected $key;
    protected $body;

    public function __construct($source, Variable $value, Variable $key = NULL,  Array $body)
    {
        foreach (array('source', 'key', 'value', 'body') as $var) {
            $this->$var = $$var;
        }
    }

    public function execute(Runtime $vm)
    {
        foreach (array('source', 'key', 'value', 'body') as $var) {
            $$var = $this->$var;
        }

        if ($source instanceof Variable) {
            /* it's a json definition */
            $val = $vm->get($source);
            if (is_null($val)) {
                throw new \RuntimeException("Cannot find variable " . $source );
            }
            $source = $vm->getValue($val);
        } else {
            $source = $vm->getValue($source);
        }

        foreach ($source as $zkey => $zvalue) {
            if ($key) {
                $vm->define($key, new Term($zkey));
            }
            $vm->define($value, $zvalue);
            foreach ($body as $stmt) {
                $vm->execute($stmt);
                if ($vm->isSuspended()) {
                    $vm->isSuspended(false);
                    break;
                }
                if ($vm->isStopped()) {
                    break 2;
                }
            }
        }
    }
}
