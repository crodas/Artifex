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

use \Artifex_Parser as Parser;

class Tokenizer 
{
    protected $text;
    protected $offset;

    protected $keywordsMap = array(
        "return" => Parser::T_RETURN,
        "function" => Parser::T_FUNCTION,
        "continue" => Parser::T_CONTINUE,
        "foreach" => Parser::T_FOREACH,
        "if"      => Parser::T_IF,
        "else"    => Parser::T_ELSE,
        "end"     => Parser::T_END,
        "as"      => Parser::T_AS,
        "and"     => Parser::T_AND,
        "not"     => Parser::T_NOT,
        "or"      => Parser::T_OR,
        "in"      => Parser::T_IN,
        "false"   => Parser::T_FALSE,
        "true"    => Parser::T_TRUE,
    );

    const IN_TEXT = 0;
    const IN_CODE_BLOCK = 2;


    public function __construct($text) 
    {
        $this->text = $text;
    }

    public function getAll()
    {
        $line = 1;
        $text = $this->text;
        $len  = strlen($text);
        $kwords = $this->keywordsMap;
        $tokens = array();
        $status = self::IN_TEXT;
        $map = array(
            "//" => -1,
            "#*" => -1,
            "*#" => -1,
            "&&" => Parser::T_AND,
            "->" => Parser::T_OBJ,
            "==" => Parser::T_EQ,
            "!=" => Parser::T_NE,
            "!"  => Parser::T_NOT,
            "=>" => Parser::T_DOUBLE_ARROW,
            ">"  => Parser::T_GT,
            ">=" => Parser::T_GE,
            "<"  => Parser::T_LT,
            "<=" => Parser::T_LE,
            "("  => Parser::T_LPARENT,
            ")"  => Parser::T_RPARENT,
            "="  => Parser::T_ASSIGN,
            "$"  => Parser::T_DOLLAR,
            "@"  => Parser::T_AT,
            "."  => Parser::T_DOT,
            "{"  => Parser::T_CURLY_OPEN,
            "}"  => Parser::T_CURLY_CLOSE,
            "["  => Parser::T_SUBSCR_OPEN,
            "]"  => Parser::T_SUBSCR_CLOSE,
            ","  => Parser::T_COMMA,
            ":"  => Parser::T_COLON,
            "+"  => Parser::T_PLUS,
            "-"  => Parser::T_MINUS,
            "*"  => Parser::T_TIMES,
            "/"  => Parser::T_DIV,
            "%"  => Parser::T_MOD,
        );

        for ($i=0; $i < $len; $i++) {
            if ($status === self::IN_TEXT) {
                $pos = strpos($text, "#*", $i);
                if ($pos === false) {
                    $pos = $len;
                }
                $isBlock   = $pos != $len && $text[$pos+2] == '!';
                $status    = self ::IN_CODE_BLOCK;
                $raw_str   = substr($text, $i, $pos - $i);
                $clean_str = rtrim($raw_str, " \t\r");

                if (!empty($clean_str)) {
                    $tokens[] = array(Parser::T_RAW_STRING, $clean_str, $line);
                } else {
                    $i--;
                }

                if ($raw_str !== $clean_str) {
                    // whitespace tokenizer. Basically it used to remember the last
                    // identation stage.
                    $tokens[]  = array(Parser::T_WHITESPACE, substr($raw_str, strlen($clean_str)), $line);
                }

                $line += substr_count($raw_str, "\n");
                $i = $pos-1;
                continue;
            }

            switch ($text[$i]) {
            case "\n":
                $line++;
                if ($status == self::IN_CODE_BLOCK) {
                    $e = $i;
                    while ($i+1 < $len && trim($text[++$i]) == "");
                    if ($i < $len) {
                        if ($text[$i] == '#') {
                            $whitespace = substr($text, $e+1, $i-$e-1);
                            if (substr($text, $i, 2) == '#*') {
                                $i++;
                            }
                            if (!empty($whitespace)) {
                                $tokens[] = array(Parser::T_WHITESPACE, $whitespace, $line);
                            }
                        } else {
                            $status = self::IN_TEXT;
                            $i = $e;
                        }
                    }
                }
                break;
            case "\t": case "\r": case " ":
                break;
            case '"':
            case "'":
                $end = $text[$i];
                $str = "";
                for ($e = $i+1; $e < $len; $e++) {
                    switch ($text[$e]) {
                    case "\\":
                        $letter = $text[++$e];
                        switch ($letter) {
                        case "n":
                            $letter = "\n";
                            break;
                        case "t":
                            $letter = "\t";
                            break;
                        case "r":
                            $letter = "\r";
                            break;
                        }
                        $str .= $letter;
                        break;
                    case "'": case '"':
                        if ($text[$e] == $end) {
                            break 2;
                        }
                    default:
                        $str .= $text[$e];
                    } 
                }
                $tokens[] = array(Parser::T_STRING, $str, $line);
                $i = $e;
                break;

            default:
                for($e=3; $e >= 1; $e--) {
                    if (isset($map[substr($text, $i, $e)])) {
                        $token = substr($text, $i, $e);
                        switch ($token) {
                        case '//':
                            $pos = strpos($text, "\n", $i);
                            if ($pos == false) {
                                $pos = $len;
                            }
                            $i = $pos - 1;
                            continue 3;

                        case '*#':
                            $i += $e - 1;
                            $status = self::IN_TEXT;
                            continue 3;
                        }
                        $tokens[] = Array($map[$token], $token, $line);
                        $i += $e - 1;
                        continue 2;
                    }
                }

                $parts = preg_split('/[^a-zA-Z0-9_.]/', substr($text, $i), 2);
                if (trim($parts[0]) === "") { 
                    throw new \RuntimeException("Unexpected " . $text[$i]);
                }
                if (preg_match("/^[0-9]/", $parts[0])) {
                    if (!preg_match("/^[0-9]+(.[0-9]+)?(e[0-9]+)?$/", $parts[0])) {
                        throw new \RuntimeException("invalid number " . $parts[0]);
                    }
                    $tokens[] = array(Parser::T_NUMBER, $parts[0] + 0, $line);
                } else {
                    $tmp = strtolower($parts[0]);
                    if (!empty($kwords[$tmp])) {
                        $tokens[] = Array($kwords[$tmp], $tmp, $line);
                    } else {
                        $tokens[] = Array(Parser::T_ALPHA, $parts[0], $line);
                    }
                }
                $i += strlen($parts[0])-1;
                break;
            }
        }

        return $tokens;
    }
}

