<?php
namespace Artifex;

use \Artifex_Parser as Parser;

class Tokenizer 
{
    protected $text;
    protected $offset;

    protected $keywordsMap = array(
        "function" => Parser::T_FUNCTION,
        "foreach" => Parser::T_FOREACH,
        "if"      => Parser::T_IF,
        "else"    => Parser::T_ELSE,
        "end"     => Parser::T_END,
        "as"      => Parser::T_AS,
        "and"     => Parser::T_AND,
        "not"     => Parser::T_NOT,
        "or"      => Parser::T_OR,
        "in"      => Parser::T_IN,
    );

    const IN_TEXT = 0;
    const IN_CODE = 1;


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
            "#*" => Parser::T_START,
            "*#" => Parser::T_START,
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
            ";"  => Parser::T_SEMICOLON,
            "+"  => Parser::T_PLUS,
            "-"  => Parser::T_MINUS,
            "*"  => Parser::T_TIMES,
            "/"  => Parser::T_DIV,
            "%"  => Parser::T_MOD,
        );
        for ($i=0; $i < $len; $i++) {
            if ($status == self::IN_TEXT) {
                $pos = strpos($text, "#*", $i);
                if ($pos === false) {
                    $pos = $len;
                }
                $status  = self::IN_CODE;
                $raw_str = substr($text, $i, $pos - $i);
                if (trim($raw_str, " \t\r") == "") {
                    $i--;
                    continue;
                }

                $tokens[] = array(Parser::T_RAW_STRING, rtrim($raw_str, " \t\r"), $line);
                $line += substr_count($raw_str, "\n");
                $i = $pos-1;
                continue;
            }
            switch ($text[$i]) {
            case "\n":
                $line++;
                $status = self::IN_TEXT;
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
                        $str .= $text[++$e];
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
                for($e=2; $e >= 1; $e--) {
                    if (isset($map[substr($text, $i, $e)])) {
                        $token = substr($text, $i, $e);
                        if ($token == "*#") {
                            $i += $e - 1;
                            $status = self::IN_TEXT;
                            continue 2;
                        }
                        $tokens[] = Array($map[$token], $token, $line);
                        $i += $e - 1;
                        continue 2;
                    }
                }

                $parts = preg_split('/[^a-zA-Z0-9_.]/', substr($text, $i), 2);
                if (trim($parts[0]) === "") { 
                    throw new \Exception("Unexpected " . $text[$i]);
                }
                if (preg_match("/^[0-9]/", $parts[0])) {
                    if (!preg_match("/^[0-9]+(.[0-9]+)?(e[0-9]+)?$/", $parts[0])) {
                        throw new \Exception("invalid number " . $parts[0]);
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

