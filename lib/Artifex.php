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

use \Artifex\Tokenizer,
    \Artifex_Parser as Parser,
    \Artifex\Runtime;

class Artifex
{
    public static function save($path, $code)
    {
        return file_put_contents($path, $code, LOCK_EX) !== false;
    }

    public static function compile($bytes)
    {
        $tokens = new Tokenizer($bytes);
        $parser = new Parser;
        foreach ($tokens->getAll() as $token) {
            if ($token[0] == -1) continue;
            $parser->line = $token[2];
            $parser->doParse($token[0], $token[1]);
        }
        $parser->doParse(0, 0);
        return new Runtime($parser->body);
    }

    public static function execute($bytes, $context = array())
    {
        $vm = self::compile($bytes);
        $vm->setContext($context);
        return $vm->run();
    }
    
    public static function load($file, $context = array())
    {
        if (!is_readable($file)) {
            throw new \RuntimeException("Cannot read file {$file}");
        }
        $vm = self::compile(file_get_contents($file));
        $vm->setContext($context);
        $vm->setPwd(dirname($file));
        return $vm;
    }

    public static function registerAutoloader()
    {
        require __DIR__ . "/Artifex/autoload.php";
    }
}
