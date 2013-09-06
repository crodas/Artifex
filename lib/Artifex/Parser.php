<?php
/* Driver template for the PHP_Artifex_rGenerator parser generator. (PHP port of LEMON)
*/

/**
 * This can be used to store both the string representation of
 * a token, and any useful meta-data associated with the token.
 *
 * meta-data should be stored as an array
 */
class Artifex_yyToken implements ArrayAccess
{
    public $string = '';
    public $metadata = array();

    function __construct($s, $m = array())
    {
        if ($s instanceof Artifex_yyToken) {
            $this->string = $s->string;
            $this->metadata = $s->metadata;
        } else {
            $this->string = (string) $s;
            if ($m instanceof Artifex_yyToken) {
                $this->metadata = $m->metadata;
            } elseif (is_array($m)) {
                $this->metadata = $m;
            }
        }
    }

    function __toString()
    {
        return $this->string;
    }

    function offsetExists($offset)
    {
        return isset($this->metadata[$offset]);
    }

    function offsetGet($offset)
    {
        return $this->metadata[$offset];
    }

    function offsetSet($offset, $value)
    {
        if ($offset === null) {
            if (isset($value[0])) {
                $x = ($value instanceof Artifex_yyToken) ?
                    $value->metadata : $value;
                $this->metadata = array_merge($this->metadata, $x);
                return;
            }
            $offset = count($this->metadata);
        }
        if ($value === null) {
            return;
        }
        if ($value instanceof Artifex_yyToken) {
            if ($value->metadata) {
                $this->metadata[$offset] = $value->metadata;
            }
        } elseif ($value) {
            $this->metadata[$offset] = $value;
        }
    }

    function offsetUnset($offset)
    {
        unset($this->metadata[$offset]);
    }
}

/** The following structure represents a single element of the
 * parser's stack.  Information stored includes:
 *
 *   +  The state number for the parser at this level of the stack.
 *
 *   +  The value of the token stored at this level of the stack.
 *      (In other words, the "major" token.)
 *
 *   +  The semantic value stored at this level of the stack.  This is
 *      the information used by the action routines in the grammar.
 *      It is sometimes called the "minor" token.
 */
class Artifex_yyStackEntry
{
    public $stateno;       /* The state-number */
    public $major;         /* The major token value.  This is the code
                     ** number for the token at this stack level */
    public $minor; /* The user-supplied minor token value.  This
                     ** is the value of the token  */
};

// code external to the class is included here
#line 2 "lib/Artifex/Parser.y"

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

use \Artifex\Runtime\Assign,
    \Artifex\Runtime\Concat,
    \Artifex\Runtime\Exec,
    \Artifex\Runtime\Expr,
    \Artifex\Runtime\Expr_If,
    \Artifex\Runtime\Expr_Foreach,
    \Artifex\Runtime\Expr_Function,
    \Artifex\Runtime\Expr_Return,
    \Artifex\Runtime\Expr_Continue,
    \Artifex\Runtime\RawString,
    \Artifex\Runtime\Whitespace,
    \Artifex\Runtime\Term,
    \Artifex\Runtime\Variable;
#line 150 "lib/Artifex/Parser.php"

// declare_class is output here
#line 53 "lib/Artifex/Parser.y"
class Artifex_Parser #line 155 "lib/Artifex/Parser.php"
{
/* First off, code is included which follows the "include_class" declaration
** in the input file. */
#line 54 "lib/Artifex/Parser.y"

    public $body = array();

    public function setPrevNext($a, $b)
    {
        $a->setNext($b);
        $b->setPrev($a);
    }
#line 169 "lib/Artifex/Parser.php"

/* Next is all token values, as class constants
*/
/* 
** These constants (all generated automatically by the parser generator)
** specify the various kinds of tokens (terminals) that the parser
** understands. 
**
** Each symbol here is a terminal symbol in the grammar.
*/
    const T_COMMA                        =  1;
    const T_OBJ                          =  2;
    const T_CURLY_OPEN                   =  3;
    const T_AND                          =  4;
    const T_OR                           =  5;
    const T_NOT                          =  6;
    const T_EQ                           =  7;
    const T_NE                           =  8;
    const T_ASSIGN                       =  9;
    const T_GT                           = 10;
    const T_GE                           = 11;
    const T_LT                           = 12;
    const T_LE                           = 13;
    const T_IN                           = 14;
    const T_DOT                          = 15;
    const T_PLUS                         = 16;
    const T_MINUS                        = 17;
    const T_TIMES                        = 18;
    const T_DIV                          = 19;
    const T_MOD                          = 20;
    const T_PIPE                         = 21;
    const T_BITWISE                      = 22;
    const T_RAW_STRING                   = 23;
    const T_NEW_LINE                     = 24;
    const T_STRING                       = 25;
    const T_WHITESPACE                   = 26;
    const T_RETURN                       = 27;
    const T_CONTINUE                     = 28;
    const T_FOREACH                      = 29;
    const T_LPARENT                      = 30;
    const T_AS                           = 31;
    const T_RPARENT                      = 32;
    const T_END                          = 33;
    const T_DOUBLE_ARROW                 = 34;
    const T_FUNCTION                     = 35;
    const T_ALPHA                        = 36;
    const T_IF                           = 37;
    const T_ELSE                         = 38;
    const T_AT                           = 39;
    const T_DOLLAR                       = 40;
    const T_SUBSCR_OPEN                  = 41;
    const T_SUBSCR_CLOSE                 = 42;
    const T_TRUE                         = 43;
    const T_FALSE                        = 44;
    const T_NUMBER                       = 45;
    const T_CURLY_CLOSE                  = 46;
    const T_COLON                        = 47;
    const YY_NO_ACTION = 182;
    const YY_ACCEPT_ACTION = 181;
    const YY_ERROR_ACTION = 180;

/* Next are that tables used to determine what action to take based on the
** current state and lookahead token.  These tables are used to implement
** functions that take a state number and lookahead value and return an
** action integer.  
**
** Suppose the action integer is N.  Then the action is determined as
** follows
**
**   0 <= N < self::YYNSTATE                              Shift N.  That is,
**                                                        push the lookahead
**                                                        token onto the stack
**                                                        and goto state N.
**
**   self::YYNSTATE <= N < self::YYNSTATE+self::YYNRULE   Reduce by rule N-YYNSTATE.
**
**   N == self::YYNSTATE+self::YYNRULE                    A syntax error has occurred.
**
**   N == self::YYNSTATE+self::YYNRULE+1                  The parser accepts its
**                                                        input. (and concludes parsing)
**
**   N == self::YYNSTATE+self::YYNRULE+2                  No such action.  Denotes unused
**                                                        slots in the yy_action[] table.
**
** The action table is constructed as a single large static array $yy_action.
** Given state S and lookahead X, the action is computed as
**
**      self::$yy_action[self::$yy_shift_ofst[S] + X ]
**
** If the index value self::$yy_shift_ofst[S]+X is out of range or if the value
** self::$yy_lookahead[self::$yy_shift_ofst[S]+X] is not equal to X or if
** self::$yy_shift_ofst[S] is equal to self::YY_SHIFT_USE_DFLT, it means that
** the action is not in the table and that self::$yy_default[S] should be used instead.  
**
** The formula above is for computing the action when the lookahead is
** a terminal symbol.  If the lookahead is a non-terminal (as occurs after
** a reduce action) then the static $yy_reduce_ofst array is used in place of
** the static $yy_shift_ofst array and self::YY_REDUCE_USE_DFLT is used in place of
** self::YY_SHIFT_USE_DFLT.
**
** The following are the tables generated in this section:
**
**  self::$yy_action        A single table containing all actions.
**  self::$yy_lookahead     A table containing the lookahead for each entry in
**                          yy_action.  Used to detect hash collisions.
**  self::$yy_shift_ofst    For each state, the offset into self::$yy_action for
**                          shifting terminals.
**  self::$yy_reduce_ofst   For each state, the offset into self::$yy_action for
**                          shifting non-terminals after a reduce.
**  self::$yy_default       Default action for each state.
*/
    const YY_SZ_ACTTAB = 471;
static public $yy_action = array(
 /*     0 */    23,   20,   18,   11,   11,    1,   11,   11,   11,   11,
 /*    10 */    11,    9,   12,   12,   10,   10,   10,    8,    8,   68,
 /*    20 */    70,   85,   90,   23,   20,   39,   11,   11,   38,   11,
 /*    30 */    11,   11,   11,   11,    9,   12,   12,   10,   10,   10,
 /*    40 */     8,    8,   41,   93,   36,   31,   23,   20,  104,   11,
 /*    50 */    11,   99,   11,   11,   11,   11,   11,    9,   12,   12,
 /*    60 */    10,   10,   10,    8,    8,  181,   28,   15,   29,   23,
 /*    70 */    20,   14,   11,   11,   40,   11,   11,   11,   11,   11,
 /*    80 */     9,   12,   12,   10,   10,   10,    8,    8,   23,   20,
 /*    90 */   114,   11,   11,    6,   11,   11,   11,   11,   11,    9,
 /*   100 */    12,   12,   10,   10,   10,    8,    8,   96,   20,   17,
 /*   110 */    11,   11,    3,   11,   11,   11,   11,   11,    9,   12,
 /*   120 */    12,   10,   10,   10,    8,    8,   11,   11,    2,   11,
 /*   130 */    11,   11,   11,   11,    9,   12,   12,   10,   10,   10,
 /*   140 */     8,    8,   30,  117,  116,   22,   16,   77,   87,  113,
 /*   150 */   103,  102,   19,  110,   72,    2,   79,   37,   92,  101,
 /*   160 */    80,   75,   73,   35,  107,   33,   78,    1,   47,   13,
 /*   170 */    69,   97,  117,   65,   21,   76,   95,   34,   32,   33,
 /*   180 */     7,   33,   98,  106,  115,   87,   42,  103,  102,   19,
 /*   190 */   110,   72,    8,    8,  109,   84,   14,   80,   75,   73,
 /*   200 */    74,   26,   33,   87,    2,  103,  102,   19,  110,   72,
 /*   210 */    71,    4,   81,   88,    5,   80,   75,   73,   60,   87,
 /*   220 */    33,  103,  102,   19,  110,   72,   25,   94,   64,  112,
 /*   230 */   127,   80,   75,   73,   27,  105,   33,   87,   24,  103,
 /*   240 */   102,   19,  110,   72,  127,   30,   30,  111,  127,   80,
 /*   250 */    75,   73,  127,   87,   33,  103,  102,   19,  110,   72,
 /*   260 */    79,  127,  127,  127,  127,   80,   75,   73,  107,  127,
 /*   270 */    33,   12,   12,   10,   10,   10,    8,    8,   75,  118,
 /*   280 */   127,  127,   33,    7,    7,  127,   98,  106,  115,  127,
 /*   290 */    51,  127,   69,   97,  117,   89,   82,  127,   95,   61,
 /*   300 */   119,   67,  127,   83,   91,   47,  127,   69,   97,  117,
 /*   310 */    59,   89,   82,   95,  127,   61,  119,  127,  127,   83,
 /*   320 */    86,   47,  127,   69,   97,  117,   62,  127,   47,   95,
 /*   330 */    69,   97,  117,  108,  127,  127,   95,  127,   89,   82,
 /*   340 */   127,  127,   61,  119,  127,  127,   83,   10,   10,   10,
 /*   350 */     8,    8,   50,  127,   69,   97,  117,  127,  127,   43,
 /*   360 */    95,   69,   97,  117,  127,  127,   45,   95,   69,   97,
 /*   370 */   117,  127,  127,   46,   95,   69,   97,  117,  127,  127,
 /*   380 */    52,   95,   69,   97,  117,  127,  127,   49,   95,   69,
 /*   390 */    97,  117,  127,  127,  127,   95,   44,  127,   69,   97,
 /*   400 */   117,  127,  127,   55,   95,   69,   97,  117,  127,  127,
 /*   410 */    48,   95,   69,   97,  117,  127,  127,   58,   95,   69,
 /*   420 */    97,  117,  127,  127,   54,   95,   69,   97,  117,  127,
 /*   430 */   127,   53,   95,   69,   97,  117,  127,  127,  100,   95,
 /*   440 */    69,   97,  117,  127,  127,   56,   95,   69,   97,  117,
 /*   450 */   127,  127,   66,   95,   69,   97,  117,  127,  127,   57,
 /*   460 */    95,   69,   97,  117,  117,  127,  127,   95,   77,  127,
 /*   470 */    63,
    );
    static public $yy_lookahead = array(
 /*     0 */     4,    5,    1,    7,    8,   30,   10,   11,   12,   13,
 /*    10 */    14,   15,   16,   17,   18,   19,   20,   21,   22,   54,
 /*    20 */    55,   56,   57,    4,    5,   32,    7,    8,   32,   10,
 /*    30 */    11,   12,   13,   14,   15,   16,   17,   18,   19,   20,
 /*    40 */    21,   22,   32,   42,   34,    1,    4,    5,   24,    7,
 /*    50 */     8,   32,   10,   11,   12,   13,   14,   15,   16,   17,
 /*    60 */    18,   19,   20,   21,   22,   49,   50,   30,   30,    4,
 /*    70 */     5,   41,    7,    8,   32,   10,   11,   12,   13,   14,
 /*    80 */    15,   16,   17,   18,   19,   20,   21,   22,    4,    5,
 /*    90 */    46,    7,    8,   30,   10,   11,   12,   13,   14,   15,
 /*   100 */    16,   17,   18,   19,   20,   21,   22,   42,    5,   47,
 /*   110 */     7,    8,   30,   10,   11,   12,   13,   14,   15,   16,
 /*   120 */    17,   18,   19,   20,   21,   22,    7,    8,    1,   10,
 /*   130 */    11,   12,   13,   14,   15,   16,   17,   18,   19,   20,
 /*   140 */    21,   22,    3,   57,   45,    6,    9,   61,   23,   63,
 /*   150 */    25,   26,   27,   28,   29,    1,   17,    2,   33,   32,
 /*   160 */    35,   36,   37,   38,   25,   40,   36,   30,   53,   30,
 /*   170 */    55,   56,   57,   58,   30,   36,   61,   31,   39,   40,
 /*   180 */    41,   40,   43,   44,   45,   23,   32,   25,   26,   27,
 /*   190 */    28,   29,   21,   22,   36,   33,   41,   35,   36,   37,
 /*   200 */    55,   50,   40,   23,    1,   25,   26,   27,   28,   29,
 /*   210 */    62,   50,   37,   33,   50,   35,   36,   37,   55,   23,
 /*   220 */    40,   25,   26,   27,   28,   29,   50,   55,   62,   33,
 /*   230 */    65,   35,   36,   37,   50,   32,   40,   23,   50,   25,
 /*   240 */    26,   27,   28,   29,   65,    3,    3,   33,   65,   35,
 /*   250 */    36,   37,   65,   23,   40,   25,   26,   27,   28,   29,
 /*   260 */    17,   65,   65,   65,   65,   35,   36,   37,   25,   65,
 /*   270 */    40,   16,   17,   18,   19,   20,   21,   22,   36,   36,
 /*   280 */    65,   65,   40,   41,   41,   65,   43,   44,   45,   65,
 /*   290 */    53,   65,   55,   56,   57,   51,   52,   65,   61,   55,
 /*   300 */    56,   64,   65,   59,   60,   53,   65,   55,   56,   57,
 /*   310 */    58,   51,   52,   61,   65,   55,   56,   65,   65,   59,
 /*   320 */    60,   53,   65,   55,   56,   57,   58,   65,   53,   61,
 /*   330 */    55,   56,   57,   58,   65,   65,   61,   65,   51,   52,
 /*   340 */    65,   65,   55,   56,   65,   65,   59,   18,   19,   20,
 /*   350 */    21,   22,   53,   65,   55,   56,   57,   65,   65,   53,
 /*   360 */    61,   55,   56,   57,   65,   65,   53,   61,   55,   56,
 /*   370 */    57,   65,   65,   53,   61,   55,   56,   57,   65,   65,
 /*   380 */    53,   61,   55,   56,   57,   65,   65,   53,   61,   55,
 /*   390 */    56,   57,   65,   65,   65,   61,   53,   65,   55,   56,
 /*   400 */    57,   65,   65,   53,   61,   55,   56,   57,   65,   65,
 /*   410 */    53,   61,   55,   56,   57,   65,   65,   53,   61,   55,
 /*   420 */    56,   57,   65,   65,   53,   61,   55,   56,   57,   65,
 /*   430 */    65,   53,   61,   55,   56,   57,   65,   65,   53,   61,
 /*   440 */    55,   56,   57,   65,   65,   53,   61,   55,   56,   57,
 /*   450 */    65,   65,   53,   61,   55,   56,   57,   65,   65,   53,
 /*   460 */    61,   55,   56,   57,   57,   65,   65,   61,   61,   65,
 /*   470 */    63,
);
    const YY_SHIFT_USE_DFLT = -26;
    const YY_SHIFT_MAX = 82;
    static public $yy_shift_ofst = array(
 /*     0 */   -26,  139,  139,  139,  125,  125,  139,  139,  139,  139,
 /*    10 */   139,  139,  139,  139,  139,  139,  139,  139,  139,  139,
 /*    20 */   139,  139,  139,  139,  196,  214,  180,  162,  230,  242,
 /*    30 */   243,  243,  141,  158,  141,  175,  141,  158,  -26,  -26,
 /*    40 */   -26,  -26,  -26,   42,   -4,   65,   19,   84,   84,   84,
 /*    50 */    84,   84,   84,  103,  119,  119,  119,  255,  329,  127,
 /*    60 */    10,  137,  154,   44,  155,  203,  171,    1,  146,  -25,
 /*    70 */   -25,   30,   38,   37,   -7,   63,   63,   62,   82,   99,
 /*    80 */   130,  144,   24,
);
    const YY_REDUCE_USE_DFLT = -36;
    const YY_REDUCE_MAX = 42;
    static public $yy_reduce_ofst = array(
 /*     0 */    16,  115,  275,  268,  260,  244,  252,  237,  385,  406,
 /*    10 */   399,  371,  364,  320,  313,  306,  299,  327,  334,  357,
 /*    20 */   350,  343,  392,  378,  287,  287,  287,  287,  287,  -35,
 /*    30 */   407,   86,  172,  166,  163,  151,  145,  148,  164,  184,
 /*    40 */   161,  176,  188,
);
    static public $yyExpectedTokens = array(
        /* 0 */ array(),
        /* 1 */ array(3, 6, 17, 25, 30, 36, 39, 40, 41, 43, 44, 45, ),
        /* 2 */ array(3, 6, 17, 25, 30, 36, 39, 40, 41, 43, 44, 45, ),
        /* 3 */ array(3, 6, 17, 25, 30, 36, 39, 40, 41, 43, 44, 45, ),
        /* 4 */ array(23, 25, 26, 27, 28, 29, 33, 35, 36, 37, 38, 40, ),
        /* 5 */ array(23, 25, 26, 27, 28, 29, 33, 35, 36, 37, 38, 40, ),
        /* 6 */ array(3, 6, 17, 25, 30, 36, 39, 40, 41, 43, 44, 45, ),
        /* 7 */ array(3, 6, 17, 25, 30, 36, 39, 40, 41, 43, 44, 45, ),
        /* 8 */ array(3, 6, 17, 25, 30, 36, 39, 40, 41, 43, 44, 45, ),
        /* 9 */ array(3, 6, 17, 25, 30, 36, 39, 40, 41, 43, 44, 45, ),
        /* 10 */ array(3, 6, 17, 25, 30, 36, 39, 40, 41, 43, 44, 45, ),
        /* 11 */ array(3, 6, 17, 25, 30, 36, 39, 40, 41, 43, 44, 45, ),
        /* 12 */ array(3, 6, 17, 25, 30, 36, 39, 40, 41, 43, 44, 45, ),
        /* 13 */ array(3, 6, 17, 25, 30, 36, 39, 40, 41, 43, 44, 45, ),
        /* 14 */ array(3, 6, 17, 25, 30, 36, 39, 40, 41, 43, 44, 45, ),
        /* 15 */ array(3, 6, 17, 25, 30, 36, 39, 40, 41, 43, 44, 45, ),
        /* 16 */ array(3, 6, 17, 25, 30, 36, 39, 40, 41, 43, 44, 45, ),
        /* 17 */ array(3, 6, 17, 25, 30, 36, 39, 40, 41, 43, 44, 45, ),
        /* 18 */ array(3, 6, 17, 25, 30, 36, 39, 40, 41, 43, 44, 45, ),
        /* 19 */ array(3, 6, 17, 25, 30, 36, 39, 40, 41, 43, 44, 45, ),
        /* 20 */ array(3, 6, 17, 25, 30, 36, 39, 40, 41, 43, 44, 45, ),
        /* 21 */ array(3, 6, 17, 25, 30, 36, 39, 40, 41, 43, 44, 45, ),
        /* 22 */ array(3, 6, 17, 25, 30, 36, 39, 40, 41, 43, 44, 45, ),
        /* 23 */ array(3, 6, 17, 25, 30, 36, 39, 40, 41, 43, 44, 45, ),
        /* 24 */ array(23, 25, 26, 27, 28, 29, 33, 35, 36, 37, 40, ),
        /* 25 */ array(23, 25, 26, 27, 28, 29, 33, 35, 36, 37, 40, ),
        /* 26 */ array(23, 25, 26, 27, 28, 29, 33, 35, 36, 37, 40, ),
        /* 27 */ array(23, 25, 26, 27, 28, 29, 33, 35, 36, 37, 40, ),
        /* 28 */ array(23, 25, 26, 27, 28, 29, 35, 36, 37, 40, ),
        /* 29 */ array(3, 36, 40, 41, ),
        /* 30 */ array(3, 17, 25, 36, 41, 43, 44, 45, ),
        /* 31 */ array(3, 17, 25, 36, 41, 43, 44, 45, ),
        /* 32 */ array(40, ),
        /* 33 */ array(36, ),
        /* 34 */ array(40, ),
        /* 35 */ array(37, ),
        /* 36 */ array(40, ),
        /* 37 */ array(36, ),
        /* 38 */ array(),
        /* 39 */ array(),
        /* 40 */ array(),
        /* 41 */ array(),
        /* 42 */ array(),
        /* 43 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 32, ),
        /* 44 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 32, ),
        /* 45 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 42, ),
        /* 46 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 32, ),
        /* 47 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, ),
        /* 48 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, ),
        /* 49 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, ),
        /* 50 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, ),
        /* 51 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, ),
        /* 52 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, ),
        /* 53 */ array(5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, ),
        /* 54 */ array(7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, ),
        /* 55 */ array(7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, ),
        /* 56 */ array(7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, ),
        /* 57 */ array(16, 17, 18, 19, 20, 21, 22, ),
        /* 58 */ array(18, 19, 20, 21, 22, ),
        /* 59 */ array(1, 32, ),
        /* 60 */ array(32, 34, ),
        /* 61 */ array(9, 30, ),
        /* 62 */ array(1, 32, ),
        /* 63 */ array(1, 46, ),
        /* 64 */ array(2, 41, ),
        /* 65 */ array(1, 32, ),
        /* 66 */ array(21, 22, ),
        /* 67 */ array(1, 42, ),
        /* 68 */ array(31, ),
        /* 69 */ array(30, ),
        /* 70 */ array(30, ),
        /* 71 */ array(41, ),
        /* 72 */ array(30, ),
        /* 73 */ array(30, ),
        /* 74 */ array(32, ),
        /* 75 */ array(30, ),
        /* 76 */ array(30, ),
        /* 77 */ array(47, ),
        /* 78 */ array(30, ),
        /* 79 */ array(45, ),
        /* 80 */ array(36, ),
        /* 81 */ array(30, ),
        /* 82 */ array(24, ),
        /* 83 */ array(),
        /* 84 */ array(),
        /* 85 */ array(),
        /* 86 */ array(),
        /* 87 */ array(),
        /* 88 */ array(),
        /* 89 */ array(),
        /* 90 */ array(),
        /* 91 */ array(),
        /* 92 */ array(),
        /* 93 */ array(),
        /* 94 */ array(),
        /* 95 */ array(),
        /* 96 */ array(),
        /* 97 */ array(),
        /* 98 */ array(),
        /* 99 */ array(),
        /* 100 */ array(),
        /* 101 */ array(),
        /* 102 */ array(),
        /* 103 */ array(),
        /* 104 */ array(),
        /* 105 */ array(),
        /* 106 */ array(),
        /* 107 */ array(),
        /* 108 */ array(),
        /* 109 */ array(),
        /* 110 */ array(),
        /* 111 */ array(),
        /* 112 */ array(),
        /* 113 */ array(),
        /* 114 */ array(),
        /* 115 */ array(),
        /* 116 */ array(),
        /* 117 */ array(),
        /* 118 */ array(),
        /* 119 */ array(),
);
    static public $yy_default = array(
 /*     0 */   122,  160,  160,  160,  180,  180,  160,  179,  180,  180,
 /*    10 */   180,  180,  180,  180,  180,  180,  180,  180,  180,  180,
 /*    20 */   180,  180,  180,  180,  180,  180,  180,  180,  120,  180,
 /*    30 */   176,  176,  180,  180,  180,  122,  180,  180,  122,  122,
 /*    40 */   122,  122,  122,  180,  180,  180,  180,  159,  128,  177,
 /*    50 */   136,  178,  175,  146,  149,  147,  145,  153,  148,  180,
 /*    60 */   180,  180,  180,  180,  161,  180,  150,  180,  180,  154,
 /*    70 */   132,  162,  180,  180,  180,  180,  165,  180,  180,  180,
 /*    80 */   180,  180,  124,  140,  131,  133,  141,  123,  143,  121,
 /*    90 */   134,  142,  144,  173,  156,  155,  163,  157,  166,  152,
 /*   100 */   151,  138,  127,  126,  125,  139,  167,  168,  158,  164,
 /*   110 */   129,  130,  135,  174,  172,  169,  170,  171,  165,  137,
);
/* The next thing included is series of defines which control
** various aspects of the generated parser.
**    self::YYNOCODE      is a number which corresponds
**                        to no legal terminal or nonterminal number.  This
**                        number is used to fill in empty slots of the hash 
**                        table.
**    self::YYFALLBACK    If defined, this indicates that one or more tokens
**                        have fall-back values which should be used if the
**                        original value of the token will not parse.
**    self::YYSTACKDEPTH  is the maximum depth of the parser's stack.
**    self::YYNSTATE      the combined number of states.
**    self::YYNRULE       the number of rules in the grammar
**    self::YYERRORSYMBOL is the code number of the error symbol.  If not
**                        defined, then do no error processing.
*/
    const YYNOCODE = 66;
    const YYSTACKDEPTH = 100;
    const YYNSTATE = 120;
    const YYNRULE = 60;
    const YYERRORSYMBOL = 48;
    const YYERRSYMDT = 'yy0';
    const YYFALLBACK = 0;
    /** The next table maps tokens into fallback tokens.  If a construct
     * like the following:
     * 
     *      %fallback ID X Y Z.
     *
     * appears in the grammer, then ID becomes a fallback token for X, Y,
     * and Z.  Whenever one of the tokens X, Y, or Z is input to the parser
     * but it does not parse, the type of the token is changed to ID and
     * the parse is retried before an error is thrown.
     */
    static public $yyFallback = array(
    );
    /**
     * Turn parser tracing on by giving a stream to which to write the trace
     * and a prompt to preface each trace message.  Tracing is turned off
     * by making either argument NULL 
     *
     * Inputs:
     * 
     * - A stream resource to which trace output should be written.
     *   If NULL, then tracing is turned off.
     * - A prefix string written at the beginning of every
     *   line of trace output.  If NULL, then tracing is
     *   turned off.
     *
     * Outputs:
     * 
     * - None.
     * @param resource
     * @param string
     */
    static function Trace($TraceFILE, $zTracePrompt)
    {
        if (!$TraceFILE) {
            $zTracePrompt = 0;
        } elseif (!$zTracePrompt) {
            $TraceFILE = 0;
        }
        self::$yyTraceFILE = $TraceFILE;
        self::$yyTracePrompt = $zTracePrompt;
    }

    /**
     * Output debug information to output (php://output stream)
     */
    static function PrintTrace()
    {
        self::$yyTraceFILE = fopen('php://output', 'w');
        self::$yyTracePrompt = '';
    }

    /**
     * @var resource|0
     */
    static public $yyTraceFILE;
    /**
     * String to prepend to debug output
     * @var string|0
     */
    static public $yyTracePrompt;
    /**
     * @var int
     */
    public $yyidx = -1;                    /* Index of top element in stack */
    /**
     * @var int
     */
    public $yyerrcnt;                 /* Shifts left before out of the error */
    /**
     * @var array
     */
    public $yystack = array();  /* The parser's stack */

    /**
     * For tracing shifts, the names of all terminals and nonterminals
     * are required.  The following table supplies these names
     * @var array
     */
    static public $yyTokenName = array( 
  '$',             'T_COMMA',       'T_OBJ',         'T_CURLY_OPEN',
  'T_AND',         'T_OR',          'T_NOT',         'T_EQ',        
  'T_NE',          'T_ASSIGN',      'T_GT',          'T_GE',        
  'T_LT',          'T_LE',          'T_IN',          'T_DOT',       
  'T_PLUS',        'T_MINUS',       'T_TIMES',       'T_DIV',       
  'T_MOD',         'T_PIPE',        'T_BITWISE',     'T_RAW_STRING',
  'T_NEW_LINE',    'T_STRING',      'T_WHITESPACE',  'T_RETURN',    
  'T_CONTINUE',    'T_FOREACH',     'T_LPARENT',     'T_AS',        
  'T_RPARENT',     'T_END',         'T_DOUBLE_ARROW',  'T_FUNCTION',  
  'T_ALPHA',       'T_IF',          'T_ELSE',        'T_AT',        
  'T_DOLLAR',      'T_SUBSCR_OPEN',  'T_SUBSCR_CLOSE',  'T_TRUE',      
  'T_FALSE',       'T_NUMBER',      'T_CURLY_CLOSE',  'T_COLON',     
  'error',         'start',         'body',          'line',        
  'code',          'expr',          'foreach_source',  'variable',    
  'fnc_call',      'json',          'args',          'if',          
  'else_if',       'term',          'var',           'json_obj',    
  'json_arr',    
    );

    /**
     * For tracing reduce actions, the names of all rules are required.
     * @var array
     */
    static public $yyRuleName = array(
 /*   0 */ "start ::= body",
 /*   1 */ "body ::= body line",
 /*   2 */ "body ::=",
 /*   3 */ "line ::= T_RAW_STRING",
 /*   4 */ "line ::= code",
 /*   5 */ "line ::= code T_NEW_LINE",
 /*   6 */ "line ::= T_STRING",
 /*   7 */ "line ::= T_WHITESPACE",
 /*   8 */ "code ::= T_RETURN expr",
 /*   9 */ "code ::= T_CONTINUE",
 /*  10 */ "code ::= T_FOREACH T_LPARENT foreach_source T_AS variable T_RPARENT body T_END",
 /*  11 */ "code ::= T_FOREACH T_LPARENT foreach_source T_AS variable T_DOUBLE_ARROW variable T_RPARENT body T_END",
 /*  12 */ "foreach_source ::= variable",
 /*  13 */ "foreach_source ::= fnc_call",
 /*  14 */ "foreach_source ::= json",
 /*  15 */ "code ::= T_FUNCTION T_ALPHA T_LPARENT args T_RPARENT body T_END",
 /*  16 */ "code ::= variable T_ASSIGN expr",
 /*  17 */ "code ::= fnc_call",
 /*  18 */ "fnc_call ::= T_ALPHA T_LPARENT args T_RPARENT",
 /*  19 */ "fnc_call ::= variable T_LPARENT args T_RPARENT",
 /*  20 */ "code ::= if",
 /*  21 */ "if ::= T_IF T_LPARENT expr T_RPARENT body else_if",
 /*  22 */ "else_if ::= T_ELSE T_IF T_LPARENT expr T_RPARENT body else_if",
 /*  23 */ "else_if ::= T_ELSE body T_END",
 /*  24 */ "else_if ::= T_END",
 /*  25 */ "expr ::= T_NOT expr",
 /*  26 */ "expr ::= expr T_AND expr",
 /*  27 */ "expr ::= expr T_OR expr",
 /*  28 */ "expr ::= expr T_PLUS|T_MINUS expr",
 /*  29 */ "expr ::= expr T_EQ|T_NE|T_GT|T_GE|T_LT|T_LE|T_IN expr",
 /*  30 */ "expr ::= expr T_TIMES|T_DIV|T_MOD expr",
 /*  31 */ "expr ::= expr T_BITWISE|T_PIPE expr",
 /*  32 */ "expr ::= T_LPARENT expr T_RPARENT",
 /*  33 */ "expr ::= expr T_DOT expr",
 /*  34 */ "expr ::= variable",
 /*  35 */ "expr ::= term",
 /*  36 */ "expr ::= T_AT variable",
 /*  37 */ "expr ::= fnc_call",
 /*  38 */ "args ::= args T_COMMA args",
 /*  39 */ "args ::= expr",
 /*  40 */ "args ::=",
 /*  41 */ "variable ::= T_DOLLAR var",
 /*  42 */ "var ::= var T_OBJ var",
 /*  43 */ "var ::= var T_SUBSCR_OPEN expr T_SUBSCR_CLOSE",
 /*  44 */ "var ::= T_ALPHA",
 /*  45 */ "term ::= T_ALPHA",
 /*  46 */ "term ::= T_TRUE",
 /*  47 */ "term ::= T_FALSE",
 /*  48 */ "term ::= T_STRING",
 /*  49 */ "term ::= T_NUMBER",
 /*  50 */ "term ::= T_MINUS T_NUMBER",
 /*  51 */ "term ::= json",
 /*  52 */ "json ::= T_CURLY_OPEN json_obj T_CURLY_CLOSE",
 /*  53 */ "json ::= T_SUBSCR_OPEN json_arr T_SUBSCR_CLOSE",
 /*  54 */ "json_obj ::= json_obj T_COMMA json_obj",
 /*  55 */ "json_obj ::= term T_COLON expr",
 /*  56 */ "json_obj ::=",
 /*  57 */ "json_arr ::= json_arr T_COMMA expr",
 /*  58 */ "json_arr ::= expr",
 /*  59 */ "json_arr ::=",
    );

    /**
     * This function returns the symbolic name associated with a token
     * value.
     * @param int
     * @return string
     */
    function tokenName($tokenType)
    {
        if ($tokenType === 0) {
            return 'End of Input';
        }
        if ($tokenType > 0 && $tokenType < count(self::$yyTokenName)) {
            return self::$yyTokenName[$tokenType];
        } else {
            return "Unknown";
        }
    }

    /**
     * The following function deletes the value associated with a
     * symbol.  The symbol can be either a terminal or nonterminal.
     * @param int the symbol code
     * @param mixed the symbol's value
     */
    static function yy_destructor($yymajor, $yypminor)
    {
        switch ($yymajor) {
        /* Here is inserted the actions which take place when a
        ** terminal or non-terminal is destroyed.  This can happen
        ** when the symbol is popped from the stack during a
        ** reduce or during error processing or when a parser is 
        ** being destroyed before it is finished parsing.
        **
        ** Note: during a reduce, the only symbols destroyed are those
        ** which appear on the RHS of the rule, but which are not used
        ** inside the C code.
        */
            default:  break;   /* If no destructor action specified: do nothing */
        }
    }

    /**
     * Pop the parser's stack once.
     *
     * If there is a destructor routine associated with the token which
     * is popped from the stack, then call it.
     *
     * Return the major token number for the symbol popped.
     * @param Artifex_yyParser
     * @return int
     */
    function yy_pop_parser_stack()
    {
        if (!count($this->yystack)) {
            return;
        }
        $yytos = array_pop($this->yystack);
        if (self::$yyTraceFILE && $this->yyidx >= 0) {
            fwrite(self::$yyTraceFILE,
                self::$yyTracePrompt . 'Popping ' . self::$yyTokenName[$yytos->major] .
                    "\n");
        }
        $yymajor = $yytos->major;
        self::yy_destructor($yymajor, $yytos->minor);
        $this->yyidx--;
        return $yymajor;
    }

    /**
     * Deallocate and destroy a parser.  Destructors are all called for
     * all stack elements before shutting the parser down.
     */
    function __destruct()
    {
        while ($this->yyidx >= 0) {
            $this->yy_pop_parser_stack();
        }
        if (is_resource(self::$yyTraceFILE)) {
            fclose(self::$yyTraceFILE);
        }
    }

    /**
     * Based on the current state and parser stack, get a list of all
     * possible lookahead tokens
     * @param int
     * @return array
     */
    function yy_get_expected_tokens($token)
    {
        $state = $this->yystack[$this->yyidx]->stateno;
        $expected = self::$yyExpectedTokens[$state];
        if (in_array($token, self::$yyExpectedTokens[$state], true)) {
            return $expected;
        }
        $stack = $this->yystack;
        $yyidx = $this->yyidx;
        do {
            $yyact = $this->yy_find_shift_action($token);
            if ($yyact >= self::YYNSTATE && $yyact < self::YYNSTATE + self::YYNRULE) {
                // reduce action
                $done = 0;
                do {
                    if ($done++ == 100) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // too much recursion prevents proper detection
                        // so give up
                        return array_unique($expected);
                    }
                    $yyruleno = $yyact - self::YYNSTATE;
                    $this->yyidx -= self::$yyRuleInfo[$yyruleno]['rhs'];
                    $nextstate = $this->yy_find_reduce_action(
                        $this->yystack[$this->yyidx]->stateno,
                        self::$yyRuleInfo[$yyruleno]['lhs']);
                    if (isset(self::$yyExpectedTokens[$nextstate])) {
                        $expected += self::$yyExpectedTokens[$nextstate];
                            if (in_array($token,
                                  self::$yyExpectedTokens[$nextstate], true)) {
                            $this->yyidx = $yyidx;
                            $this->yystack = $stack;
                            return array_unique($expected);
                        }
                    }
                    if ($nextstate < self::YYNSTATE) {
                        // we need to shift a non-terminal
                        $this->yyidx++;
                        $x = new Artifex_yyStackEntry;
                        $x->stateno = $nextstate;
                        $x->major = self::$yyRuleInfo[$yyruleno]['lhs'];
                        $this->yystack[$this->yyidx] = $x;
                        continue 2;
                    } elseif ($nextstate == self::YYNSTATE + self::YYNRULE + 1) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // the last token was just ignored, we can't accept
                        // by ignoring input, this is in essence ignoring a
                        // syntax error!
                        return array_unique($expected);
                    } elseif ($nextstate === self::YY_NO_ACTION) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // input accepted, but not shifted (I guess)
                        return $expected;
                    } else {
                        $yyact = $nextstate;
                    }
                } while (true);
            }
            break;
        } while (true);
        return array_unique($expected);
    }

    /**
     * Based on the parser state and current parser stack, determine whether
     * the lookahead token is possible.
     * 
     * The parser will convert the token value to an error token if not.  This
     * catches some unusual edge cases where the parser would fail.
     * @param int
     * @return bool
     */
    function yy_is_expected_token($token)
    {
        if ($token === 0) {
            return true; // 0 is not part of this
        }
        $state = $this->yystack[$this->yyidx]->stateno;
        if (in_array($token, self::$yyExpectedTokens[$state], true)) {
            return true;
        }
        $stack = $this->yystack;
        $yyidx = $this->yyidx;
        do {
            $yyact = $this->yy_find_shift_action($token);
            if ($yyact >= self::YYNSTATE && $yyact < self::YYNSTATE + self::YYNRULE) {
                // reduce action
                $done = 0;
                do {
                    if ($done++ == 100) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // too much recursion prevents proper detection
                        // so give up
                        return true;
                    }
                    $yyruleno = $yyact - self::YYNSTATE;
                    $this->yyidx -= self::$yyRuleInfo[$yyruleno]['rhs'];
                    $nextstate = $this->yy_find_reduce_action(
                        $this->yystack[$this->yyidx]->stateno,
                        self::$yyRuleInfo[$yyruleno]['lhs']);
                    if (isset(self::$yyExpectedTokens[$nextstate]) &&
                          in_array($token, self::$yyExpectedTokens[$nextstate], true)) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        return true;
                    }
                    if ($nextstate < self::YYNSTATE) {
                        // we need to shift a non-terminal
                        $this->yyidx++;
                        $x = new Artifex_yyStackEntry;
                        $x->stateno = $nextstate;
                        $x->major = self::$yyRuleInfo[$yyruleno]['lhs'];
                        $this->yystack[$this->yyidx] = $x;
                        continue 2;
                    } elseif ($nextstate == self::YYNSTATE + self::YYNRULE + 1) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        if (!$token) {
                            // end of input: this is valid
                            return true;
                        }
                        // the last token was just ignored, we can't accept
                        // by ignoring input, this is in essence ignoring a
                        // syntax error!
                        return false;
                    } elseif ($nextstate === self::YY_NO_ACTION) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // input accepted, but not shifted (I guess)
                        return true;
                    } else {
                        $yyact = $nextstate;
                    }
                } while (true);
            }
            break;
        } while (true);
        $this->yyidx = $yyidx;
        $this->yystack = $stack;
        return true;
    }

    /**
     * Find the appropriate action for a parser given the terminal
     * look-ahead token iLookAhead.
     *
     * If the look-ahead token is YYNOCODE, then check to see if the action is
     * independent of the look-ahead.  If it is, return the action, otherwise
     * return YY_NO_ACTION.
     * @param int The look-ahead token
     */
    function yy_find_shift_action($iLookAhead)
    {
        $stateno = $this->yystack[$this->yyidx]->stateno;
     
        /* if ($this->yyidx < 0) return self::YY_NO_ACTION;  */
        if (!isset(self::$yy_shift_ofst[$stateno])) {
            // no shift actions
            return self::$yy_default[$stateno];
        }
        $i = self::$yy_shift_ofst[$stateno];
        if ($i === self::YY_SHIFT_USE_DFLT) {
            return self::$yy_default[$stateno];
        }
        if ($iLookAhead == self::YYNOCODE) {
            return self::YY_NO_ACTION;
        }
        $i += $iLookAhead;
        if ($i < 0 || $i >= self::YY_SZ_ACTTAB ||
              self::$yy_lookahead[$i] != $iLookAhead) {
            if (count(self::$yyFallback) && $iLookAhead < count(self::$yyFallback)
                   && ($iFallback = self::$yyFallback[$iLookAhead]) != 0) {
                if (self::$yyTraceFILE) {
                    fwrite(self::$yyTraceFILE, self::$yyTracePrompt . "FALLBACK " .
                        self::$yyTokenName[$iLookAhead] . " => " .
                        self::$yyTokenName[$iFallback] . "\n");
                }
                return $this->yy_find_shift_action($iFallback);
            }
            return self::$yy_default[$stateno];
        } else {
            return self::$yy_action[$i];
        }
    }

    /**
     * Find the appropriate action for a parser given the non-terminal
     * look-ahead token $iLookAhead.
     *
     * If the look-ahead token is self::YYNOCODE, then check to see if the action is
     * independent of the look-ahead.  If it is, return the action, otherwise
     * return self::YY_NO_ACTION.
     * @param int Current state number
     * @param int The look-ahead token
     */
    function yy_find_reduce_action($stateno, $iLookAhead)
    {
        /* $stateno = $this->yystack[$this->yyidx]->stateno; */

        if (!isset(self::$yy_reduce_ofst[$stateno])) {
            return self::$yy_default[$stateno];
        }
        $i = self::$yy_reduce_ofst[$stateno];
        if ($i == self::YY_REDUCE_USE_DFLT) {
            return self::$yy_default[$stateno];
        }
        if ($iLookAhead == self::YYNOCODE) {
            return self::YY_NO_ACTION;
        }
        $i += $iLookAhead;
        if ($i < 0 || $i >= self::YY_SZ_ACTTAB ||
              self::$yy_lookahead[$i] != $iLookAhead) {
            return self::$yy_default[$stateno];
        } else {
            return self::$yy_action[$i];
        }
    }

    /**
     * Perform a shift action.
     * @param int The new state to shift in
     * @param int The major token to shift in
     * @param mixed the minor token to shift in
     */
    function yy_shift($yyNewState, $yyMajor, $yypMinor)
    {
        $this->yyidx++;
        if ($this->yyidx >= self::YYSTACKDEPTH) {
            $this->yyidx--;
            if (self::$yyTraceFILE) {
                fprintf(self::$yyTraceFILE, "%sStack Overflow!\n", self::$yyTracePrompt);
            }
            while ($this->yyidx >= 0) {
                $this->yy_pop_parser_stack();
            }
            /* Here code is inserted which will execute if the parser
            ** stack ever overflows */
            return;
        }
        $yytos = new Artifex_yyStackEntry;
        $yytos->stateno = $yyNewState;
        $yytos->major = $yyMajor;
        $yytos->minor = $yypMinor;
        array_push($this->yystack, $yytos);
        if (self::$yyTraceFILE && $this->yyidx > 0) {
            fprintf(self::$yyTraceFILE, "%sShift %d\n", self::$yyTracePrompt,
                $yyNewState);
            fprintf(self::$yyTraceFILE, "%sStack:", self::$yyTracePrompt);
            for ($i = 1; $i <= $this->yyidx; $i++) {
                fprintf(self::$yyTraceFILE, " %s",
                    self::$yyTokenName[$this->yystack[$i]->major]);
            }
            fwrite(self::$yyTraceFILE,"\n");
        }
    }

    /**
     * The following table contains information about every rule that
     * is used during the reduce.
     *
     * <pre>
     * array(
     *  array(
     *   int $lhs;         Symbol on the left-hand side of the rule
     *   int $nrhs;     Number of right-hand side symbols in the rule
     *  ),...
     * );
     * </pre>
     */
    static public $yyRuleInfo = array(
  array( 'lhs' => 49, 'rhs' => 1 ),
  array( 'lhs' => 50, 'rhs' => 2 ),
  array( 'lhs' => 50, 'rhs' => 0 ),
  array( 'lhs' => 51, 'rhs' => 1 ),
  array( 'lhs' => 51, 'rhs' => 1 ),
  array( 'lhs' => 51, 'rhs' => 2 ),
  array( 'lhs' => 51, 'rhs' => 1 ),
  array( 'lhs' => 51, 'rhs' => 1 ),
  array( 'lhs' => 52, 'rhs' => 2 ),
  array( 'lhs' => 52, 'rhs' => 1 ),
  array( 'lhs' => 52, 'rhs' => 8 ),
  array( 'lhs' => 52, 'rhs' => 10 ),
  array( 'lhs' => 54, 'rhs' => 1 ),
  array( 'lhs' => 54, 'rhs' => 1 ),
  array( 'lhs' => 54, 'rhs' => 1 ),
  array( 'lhs' => 52, 'rhs' => 7 ),
  array( 'lhs' => 52, 'rhs' => 3 ),
  array( 'lhs' => 52, 'rhs' => 1 ),
  array( 'lhs' => 56, 'rhs' => 4 ),
  array( 'lhs' => 56, 'rhs' => 4 ),
  array( 'lhs' => 52, 'rhs' => 1 ),
  array( 'lhs' => 59, 'rhs' => 6 ),
  array( 'lhs' => 60, 'rhs' => 7 ),
  array( 'lhs' => 60, 'rhs' => 3 ),
  array( 'lhs' => 60, 'rhs' => 1 ),
  array( 'lhs' => 53, 'rhs' => 2 ),
  array( 'lhs' => 53, 'rhs' => 3 ),
  array( 'lhs' => 53, 'rhs' => 3 ),
  array( 'lhs' => 53, 'rhs' => 3 ),
  array( 'lhs' => 53, 'rhs' => 3 ),
  array( 'lhs' => 53, 'rhs' => 3 ),
  array( 'lhs' => 53, 'rhs' => 3 ),
  array( 'lhs' => 53, 'rhs' => 3 ),
  array( 'lhs' => 53, 'rhs' => 3 ),
  array( 'lhs' => 53, 'rhs' => 1 ),
  array( 'lhs' => 53, 'rhs' => 1 ),
  array( 'lhs' => 53, 'rhs' => 2 ),
  array( 'lhs' => 53, 'rhs' => 1 ),
  array( 'lhs' => 58, 'rhs' => 3 ),
  array( 'lhs' => 58, 'rhs' => 1 ),
  array( 'lhs' => 58, 'rhs' => 0 ),
  array( 'lhs' => 55, 'rhs' => 2 ),
  array( 'lhs' => 62, 'rhs' => 3 ),
  array( 'lhs' => 62, 'rhs' => 4 ),
  array( 'lhs' => 62, 'rhs' => 1 ),
  array( 'lhs' => 61, 'rhs' => 1 ),
  array( 'lhs' => 61, 'rhs' => 1 ),
  array( 'lhs' => 61, 'rhs' => 1 ),
  array( 'lhs' => 61, 'rhs' => 1 ),
  array( 'lhs' => 61, 'rhs' => 1 ),
  array( 'lhs' => 61, 'rhs' => 2 ),
  array( 'lhs' => 61, 'rhs' => 1 ),
  array( 'lhs' => 57, 'rhs' => 3 ),
  array( 'lhs' => 57, 'rhs' => 3 ),
  array( 'lhs' => 63, 'rhs' => 3 ),
  array( 'lhs' => 63, 'rhs' => 3 ),
  array( 'lhs' => 63, 'rhs' => 0 ),
  array( 'lhs' => 64, 'rhs' => 3 ),
  array( 'lhs' => 64, 'rhs' => 1 ),
  array( 'lhs' => 64, 'rhs' => 0 ),
    );

    /**
     * The following table contains a mapping of reduce action to method name
     * that handles the reduction.
     * 
     * If a rule is not set, it has no handler.
     */
    static public $yyReduceMap = array(
        0 => 0,
        1 => 1,
        2 => 2,
        40 => 2,
        56 => 2,
        59 => 2,
        3 => 3,
        4 => 4,
        12 => 4,
        13 => 4,
        17 => 4,
        20 => 4,
        34 => 4,
        37 => 4,
        48 => 4,
        51 => 4,
        5 => 5,
        53 => 5,
        6 => 6,
        7 => 7,
        8 => 8,
        9 => 9,
        10 => 10,
        11 => 11,
        14 => 14,
        15 => 15,
        16 => 16,
        18 => 18,
        19 => 18,
        21 => 21,
        22 => 22,
        23 => 23,
        24 => 24,
        25 => 25,
        26 => 26,
        27 => 26,
        28 => 28,
        30 => 28,
        31 => 28,
        29 => 29,
        32 => 32,
        33 => 33,
        35 => 35,
        36 => 36,
        38 => 38,
        42 => 38,
        54 => 38,
        39 => 39,
        58 => 39,
        41 => 41,
        43 => 43,
        44 => 44,
        45 => 45,
        46 => 46,
        47 => 47,
        49 => 49,
        50 => 50,
        52 => 52,
        55 => 55,
        57 => 57,
    );
    /* Beginning here are the reduction cases.  A typical example
    ** follows:
    **  #line <lineno> <grammarfile>
    **   function yy_r0($yymsp){ ... }           // User supplied code
    **  #line <lineno> <thisfile>
    */
#line 88 "lib/Artifex/Parser.y"
    function yy_r0(){ $this->body = $this->yystack[$this->yyidx + 0]->minor;     }
#line 1229 "lib/Artifex/Parser.php"
#line 90 "lib/Artifex/Parser.y"
    function yy_r1(){
    $last = end($this->yystack[$this->yyidx + -1]->minor);
    if ($last) {
        $this->setPrevNext($last, $this->yystack[$this->yyidx + 0]->minor);
    }
    $this->yystack[$this->yyidx + -1]->minor[] = $this->yystack[$this->yyidx + 0]->minor; 
    $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor; 
    }
#line 1239 "lib/Artifex/Parser.php"
#line 98 "lib/Artifex/Parser.y"
    function yy_r2(){ $this->_retvalue = array();     }
#line 1242 "lib/Artifex/Parser.php"
#line 100 "lib/Artifex/Parser.y"
    function yy_r3(){ $this->_retvalue = new RawString($this->yystack[$this->yyidx + 0]->minor);     }
#line 1245 "lib/Artifex/Parser.php"
#line 101 "lib/Artifex/Parser.y"
    function yy_r4(){ $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;     }
#line 1248 "lib/Artifex/Parser.php"
#line 102 "lib/Artifex/Parser.y"
    function yy_r5(){ $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor;     }
#line 1251 "lib/Artifex/Parser.php"
#line 103 "lib/Artifex/Parser.y"
    function yy_r6(){ $this->_retvalue = new RawString($this->yystack[$this->yyidx + 0]->minor, true);     }
#line 1254 "lib/Artifex/Parser.php"
#line 104 "lib/Artifex/Parser.y"
    function yy_r7(){ $this->_retvalue = new Whitespace($this->yystack[$this->yyidx + 0]->minor);     }
#line 1257 "lib/Artifex/Parser.php"
#line 106 "lib/Artifex/Parser.y"
    function yy_r8(){ $this->_retvalue = new Expr_Return($this->yystack[$this->yyidx + 0]->minor);     }
#line 1260 "lib/Artifex/Parser.php"
#line 107 "lib/Artifex/Parser.y"
    function yy_r9(){ $this->_retvalue = new Expr_Continue;     }
#line 1263 "lib/Artifex/Parser.php"
#line 110 "lib/Artifex/Parser.y"
    function yy_r10(){ 
    $this->_retvalue = new Expr_Foreach($this->yystack[$this->yyidx + -5]->minor, $this->yystack[$this->yyidx + -3]->minor, NULL, $this->yystack[$this->yyidx + -1]->minor); 
    $this->_retvalue->setChild($this->yystack[$this->yyidx + -1]->minor);
    }
#line 1269 "lib/Artifex/Parser.php"
#line 115 "lib/Artifex/Parser.y"
    function yy_r11(){
    $this->_retvalue = new Expr_Foreach($this->yystack[$this->yyidx + -7]->minor, $this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -5]->minor, $this->yystack[$this->yyidx + -1]->minor);
    $this->_retvalue->setChild($this->yystack[$this->yyidx + -1]->minor);
    }
#line 1275 "lib/Artifex/Parser.php"
#line 122 "lib/Artifex/Parser.y"
    function yy_r14(){ $this->_retvalue = new Term($this->yystack[$this->yyidx + 0]->minor);     }
#line 1278 "lib/Artifex/Parser.php"
#line 126 "lib/Artifex/Parser.y"
    function yy_r15(){
    $this->_retvalue = new Expr_Function($this->yystack[$this->yyidx + -5]->minor, $this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -1]->minor);
    $this->_retvalue->setChild($this->yystack[$this->yyidx + -1]->minor);
    }
#line 1284 "lib/Artifex/Parser.php"
#line 133 "lib/Artifex/Parser.y"
    function yy_r16(){ 
    $this->_retvalue = new Assign($this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor); 
    $this->setPrevNext($this->_retvalue, $this->yystack[$this->yyidx + -2]->minor);
    $this->setPrevNext($this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);
    }
#line 1291 "lib/Artifex/Parser.php"
#line 142 "lib/Artifex/Parser.y"
    function yy_r18(){ 
    $this->_retvalue = new Exec($this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -1]->minor);
    $this->_retvalue->setChild($this->yystack[$this->yyidx + -1]->minor);
    }
#line 1297 "lib/Artifex/Parser.php"
#line 155 "lib/Artifex/Parser.y"
    function yy_r21(){
    $this->_retvalue = new Expr_If($this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + 0]->minor);
    $this->_retvalue->setChild($this->yystack[$this->yyidx + -1]->minor);
    if (is_array($this->yystack[$this->yyidx + 0]->minor)) {
        $this->_retvalue->setChild($this->yystack[$this->yyidx + 0]->minor);
    } else if (is_object($this->yystack[$this->yyidx + 0]->minor)) {
        $this->setPrevNext($this->_retvalue, $this->yystack[$this->yyidx + 0]->minor);
    }
    }
#line 1308 "lib/Artifex/Parser.php"
#line 165 "lib/Artifex/Parser.y"
    function yy_r22(){ 
    $this->_retvalue = new Expr_If($this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + 0]->minor); 
    $this->_retvalue->setChild($this->yystack[$this->yyidx + -1]->minor);
    if (is_array($this->yystack[$this->yyidx + 0]->minor)) {
        $this->_retvalue->setChild($this->yystack[$this->yyidx + 0]->minor);
    } else if (is_object($this->yystack[$this->yyidx + 0]->minor)) {
        $this->_retvalue->setNext($this->yystack[$this->yyidx + 0]->minor);
        $this->yystack[$this->yyidx + 0]->minor->setPrev($this->_retvalue);
    }
    }
#line 1320 "lib/Artifex/Parser.php"
#line 175 "lib/Artifex/Parser.y"
    function yy_r23(){ 
    $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor; 
    }
#line 1325 "lib/Artifex/Parser.php"
#line 178 "lib/Artifex/Parser.y"
    function yy_r24(){ $this->_retvalue = NULL;     }
#line 1328 "lib/Artifex/Parser.php"
#line 182 "lib/Artifex/Parser.y"
    function yy_r25(){ $this->_retvalue = new Expr('not', $this->yystack[$this->yyidx + 0]->minor);     }
#line 1331 "lib/Artifex/Parser.php"
#line 183 "lib/Artifex/Parser.y"
    function yy_r26(){ $this->_retvalue = new Expr(strtolower(@$this->yystack[$this->yyidx + -1]->minor), $this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1334 "lib/Artifex/Parser.php"
#line 185 "lib/Artifex/Parser.y"
    function yy_r28(){ $this->_retvalue = new Expr(@$this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1337 "lib/Artifex/Parser.php"
#line 186 "lib/Artifex/Parser.y"
    function yy_r29(){ $this->_retvalue = new Expr(trim(@$this->yystack[$this->yyidx + -1]->minor), $this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1340 "lib/Artifex/Parser.php"
#line 189 "lib/Artifex/Parser.y"
    function yy_r32(){ $this->_retvalue = new Expr($this->yystack[$this->yyidx + -1]->minor);     }
#line 1343 "lib/Artifex/Parser.php"
#line 190 "lib/Artifex/Parser.y"
    function yy_r33(){ $this->_retvalue = new Concat($this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1346 "lib/Artifex/Parser.php"
#line 192 "lib/Artifex/Parser.y"
    function yy_r35(){  $this->_retvalue = new Term($this->yystack[$this->yyidx + 0]->minor);     }
#line 1349 "lib/Artifex/Parser.php"
#line 193 "lib/Artifex/Parser.y"
    function yy_r36(){ 
    $this->_retvalue = new Exec('var_export', array($this->yystack[$this->yyidx + 0]->minor, new Term(true))); 
    }
#line 1354 "lib/Artifex/Parser.php"
#line 200 "lib/Artifex/Parser.y"
    function yy_r38(){ $this->_retvalue = array_merge($this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1357 "lib/Artifex/Parser.php"
#line 201 "lib/Artifex/Parser.y"
    function yy_r39(){ $this->_retvalue = array($this->yystack[$this->yyidx + 0]->minor);     }
#line 1360 "lib/Artifex/Parser.php"
#line 206 "lib/Artifex/Parser.y"
    function yy_r41(){ $this->_retvalue = new Variable($this->yystack[$this->yyidx + 0]->minor);     }
#line 1363 "lib/Artifex/Parser.php"
#line 209 "lib/Artifex/Parser.y"
    function yy_r43(){ $this->_retvalue = $this->yystack[$this->yyidx + -3]->minor ; $this->_retvalue[] = $this->yystack[$this->yyidx + -1]->minor;     }
#line 1366 "lib/Artifex/Parser.php"
#line 210 "lib/Artifex/Parser.y"
    function yy_r44(){ $this->_retvalue = array($this->yystack[$this->yyidx + 0]->minor);    }
#line 1369 "lib/Artifex/Parser.php"
#line 214 "lib/Artifex/Parser.y"
    function yy_r45(){ $this->_retvalue = trim($this->yystack[$this->yyidx + 0]->minor);     }
#line 1372 "lib/Artifex/Parser.php"
#line 215 "lib/Artifex/Parser.y"
    function yy_r46(){ $this->_retvalue = TRUE;     }
#line 1375 "lib/Artifex/Parser.php"
#line 216 "lib/Artifex/Parser.y"
    function yy_r47(){ $this->_retvalue = FALSE;     }
#line 1378 "lib/Artifex/Parser.php"
#line 218 "lib/Artifex/Parser.y"
    function yy_r49(){ $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor + 0;     }
#line 1381 "lib/Artifex/Parser.php"
#line 219 "lib/Artifex/Parser.y"
    function yy_r50(){ $this->_retvalue = -1 * ($this->yystack[$this->yyidx + 0]->minor + 0);     }
#line 1384 "lib/Artifex/Parser.php"
#line 224 "lib/Artifex/Parser.y"
    function yy_r52(){ $this->_retvalue  = $this->yystack[$this->yyidx + -1]->minor;     }
#line 1387 "lib/Artifex/Parser.php"
#line 228 "lib/Artifex/Parser.y"
    function yy_r55(){ $this->_retvalue = array($this->yystack[$this->yyidx + -2]->minor => $this->yystack[$this->yyidx + 0]->minor);     }
#line 1390 "lib/Artifex/Parser.php"
#line 231 "lib/Artifex/Parser.y"
    function yy_r57(){ $this->_retvalue = $this->yystack[$this->yyidx + -2]->minor; $this->_retvalue[] = $this->yystack[$this->yyidx + 0]->minor;     }
#line 1393 "lib/Artifex/Parser.php"

    /**
     * placeholder for the left hand side in a reduce operation.
     * 
     * For a parser with a rule like this:
     * <pre>
     * rule(A) ::= B. { A = 1; }
     * </pre>
     * 
     * The parser will translate to something like:
     * 
     * <code>
     * function yy_r0(){$this->_retvalue = 1;}
     * </code>
     */
    private $_retvalue;

    /**
     * Perform a reduce action and the shift that must immediately
     * follow the reduce.
     * 
     * For a rule such as:
     * 
     * <pre>
     * A ::= B blah C. { dosomething(); }
     * </pre>
     * 
     * This function will first call the action, if any, ("dosomething();" in our
     * example), and then it will pop three states from the stack,
     * one for each entry on the right-hand side of the expression
     * (B, blah, and C in our example rule), and then push the result of the action
     * back on to the stack with the resulting state reduced to (as described in the .out
     * file)
     * @param int Number of the rule by which to reduce
     */
    function yy_reduce($yyruleno)
    {
        //int $yygoto;                     /* The next state */
        //int $yyact;                      /* The next action */
        //mixed $yygotominor;        /* The LHS of the rule reduced */
        //Artifex_yyStackEntry $yymsp;            /* The top of the parser's stack */
        //int $yysize;                     /* Amount to pop the stack */
        $yymsp = $this->yystack[$this->yyidx];
        if (self::$yyTraceFILE && $yyruleno >= 0 
              && $yyruleno < count(self::$yyRuleName)) {
            fprintf(self::$yyTraceFILE, "%sReduce (%d) [%s].\n",
                self::$yyTracePrompt, $yyruleno,
                self::$yyRuleName[$yyruleno]);
        }

        $this->_retvalue = $yy_lefthand_side = null;
        if (array_key_exists($yyruleno, self::$yyReduceMap)) {
            // call the action
            $this->_retvalue = null;
            $this->{'yy_r' . self::$yyReduceMap[$yyruleno]}();
            $yy_lefthand_side = $this->_retvalue;
        }
        $yygoto = self::$yyRuleInfo[$yyruleno]['lhs'];
        $yysize = self::$yyRuleInfo[$yyruleno]['rhs'];
        $this->yyidx -= $yysize;
        for ($i = $yysize; $i; $i--) {
            // pop all of the right-hand side parameters
            array_pop($this->yystack);
        }
        $yyact = $this->yy_find_reduce_action($this->yystack[$this->yyidx]->stateno, $yygoto);
        if ($yyact < self::YYNSTATE) {
            /* If we are not debugging and the reduce action popped at least
            ** one element off the stack, then we can push the new element back
            ** onto the stack here, and skip the stack overflow test in yy_shift().
            ** That gives a significant speed improvement. */
            if (!self::$yyTraceFILE && $yysize) {
                $this->yyidx++;
                $x = new Artifex_yyStackEntry;
                $x->stateno = $yyact;
                $x->major = $yygoto;
                $x->minor = $yy_lefthand_side;
                $this->yystack[$this->yyidx] = $x;
            } else {
                $this->yy_shift($yyact, $yygoto, $yy_lefthand_side);
            }
        } elseif ($yyact == self::YYNSTATE + self::YYNRULE + 1) {
            $this->yy_accept();
        }
    }

    /**
     * The following code executes when the parse fails
     * 
     * Code from %parse_fail is inserted here
     */
    function yy_parse_failed()
    {
        if (self::$yyTraceFILE) {
            fprintf(self::$yyTraceFILE, "%sFail!\n", self::$yyTracePrompt);
        }
        while ($this->yyidx >= 0) {
            $this->yy_pop_parser_stack();
        }
        /* Here code is inserted which will be executed whenever the
        ** parser fails */
    }

    /**
     * The following code executes when a syntax error first occurs.
     * 
     * %syntax_error code is inserted here
     * @param int The major type of the error token
     * @param mixed The minor type of the error token
     */
    function yy_syntax_error($yymajor, $TOKEN)
    {
#line 64 "lib/Artifex/Parser.y"

    $expect = array();
    foreach ($this->yy_get_expected_tokens($yymajor) as $token) {
        $expect[] = self::$yyTokenName[$token];
    }
    throw new Exception('Unexpected ' . $this->tokenName($yymajor) .  ' in line ' . $this->line
        . ' (' . $TOKEN . ')  on line ' . $this->line
        . '. Expected: ' . print_r($expect, true));
#line 1515 "lib/Artifex/Parser.php"
    }

    /**
     * The following is executed when the parser accepts
     * 
     * %parse_accept code is inserted here
     */
    function yy_accept()
    {
        if (self::$yyTraceFILE) {
            fprintf(self::$yyTraceFILE, "%sAccept!\n", self::$yyTracePrompt);
        }
        while ($this->yyidx >= 0) {
            $stack = $this->yy_pop_parser_stack();
        }
        /* Here code is inserted which will be executed whenever the
        ** parser accepts */
    }

    /**
     * The main parser program.
     * 
     * The first argument is the major token number.  The second is
     * the token value string as scanned from the input.
     *
     * @param int   $yymajor      the token number
     * @param mixed $yytokenvalue the token value
     * @param mixed ...           any extra arguments that should be passed to handlers
     *
     * @return void
     */
    function doParse($yymajor, $yytokenvalue)
    {
//        $yyact;            /* The parser action. */
//        $yyendofinput;     /* True if we are at the end of input */
        $yyerrorhit = 0;   /* True if yymajor has invoked an error */
        
        /* (re)initialize the parser, if necessary */
        if ($this->yyidx === null || $this->yyidx < 0) {
            /* if ($yymajor == 0) return; // not sure why this was here... */
            $this->yyidx = 0;
            $this->yyerrcnt = -1;
            $x = new Artifex_yyStackEntry;
            $x->stateno = 0;
            $x->major = 0;
            $this->yystack = array();
            array_push($this->yystack, $x);
        }
        $yyendofinput = ($yymajor==0);
        
        if (self::$yyTraceFILE) {
            fprintf(
                self::$yyTraceFILE,
                "%sInput %s\n",
                self::$yyTracePrompt,
                self::$yyTokenName[$yymajor]
            );
        }
        
        do {
            $yyact = $this->yy_find_shift_action($yymajor);
            if ($yymajor < self::YYERRORSYMBOL
                && !$this->yy_is_expected_token($yymajor)
            ) {
                // force a syntax error
                $yyact = self::YY_ERROR_ACTION;
            }
            if ($yyact < self::YYNSTATE) {
                $this->yy_shift($yyact, $yymajor, $yytokenvalue);
                $this->yyerrcnt--;
                if ($yyendofinput && $this->yyidx >= 0) {
                    $yymajor = 0;
                } else {
                    $yymajor = self::YYNOCODE;
                }
            } elseif ($yyact < self::YYNSTATE + self::YYNRULE) {
                $this->yy_reduce($yyact - self::YYNSTATE);
            } elseif ($yyact == self::YY_ERROR_ACTION) {
                if (self::$yyTraceFILE) {
                    fprintf(
                        self::$yyTraceFILE,
                        "%sSyntax Error!\n",
                        self::$yyTracePrompt
                    );
                }
                if (self::YYERRORSYMBOL) {
                    /* A syntax error has occurred.
                    ** The response to an error depends upon whether or not the
                    ** grammar defines an error token "ERROR".  
                    **
                    ** This is what we do if the grammar does define ERROR:
                    **
                    **  * Call the %syntax_error function.
                    **
                    **  * Begin popping the stack until we enter a state where
                    **    it is legal to shift the error symbol, then shift
                    **    the error symbol.
                    **
                    **  * Set the error count to three.
                    **
                    **  * Begin accepting and shifting new tokens.  No new error
                    **    processing will occur until three tokens have been
                    **    shifted successfully.
                    **
                    */
                    if ($this->yyerrcnt < 0) {
                        $this->yy_syntax_error($yymajor, $yytokenvalue);
                    }
                    $yymx = $this->yystack[$this->yyidx]->major;
                    if ($yymx == self::YYERRORSYMBOL || $yyerrorhit ) {
                        if (self::$yyTraceFILE) {
                            fprintf(
                                self::$yyTraceFILE,
                                "%sDiscard input token %s\n",
                                self::$yyTracePrompt,
                                self::$yyTokenName[$yymajor]
                            );
                        }
                        $this->yy_destructor($yymajor, $yytokenvalue);
                        $yymajor = self::YYNOCODE;
                    } else {
                        while ($this->yyidx >= 0
                            && $yymx != self::YYERRORSYMBOL
                            && ($yyact = $this->yy_find_shift_action(self::YYERRORSYMBOL)) >= self::YYNSTATE
                        ) {
                            $this->yy_pop_parser_stack();
                        }
                        if ($this->yyidx < 0 || $yymajor==0) {
                            $this->yy_destructor($yymajor, $yytokenvalue);
                            $this->yy_parse_failed();
                            $yymajor = self::YYNOCODE;
                        } elseif ($yymx != self::YYERRORSYMBOL) {
                            $u2 = 0;
                            $this->yy_shift($yyact, self::YYERRORSYMBOL, $u2);
                        }
                    }
                    $this->yyerrcnt = 3;
                    $yyerrorhit = 1;
                } else {
                    /* YYERRORSYMBOL is not defined */
                    /* This is what we do if the grammar does not define ERROR:
                    **
                    **  * Report an error message, and throw away the input token.
                    **
                    **  * If the input token is $, then fail the parse.
                    **
                    ** As before, subsequent error messages are suppressed until
                    ** three input tokens have been successfully shifted.
                    */
                    if ($this->yyerrcnt <= 0) {
                        $this->yy_syntax_error($yymajor, $yytokenvalue);
                    }
                    $this->yyerrcnt = 3;
                    $this->yy_destructor($yymajor, $yytokenvalue);
                    if ($yyendofinput) {
                        $this->yy_parse_failed();
                    }
                    $yymajor = self::YYNOCODE;
                }
            } else {
                $this->yy_accept();
                $yymajor = self::YYNOCODE;
            }            
        } while ($yymajor != self::YYNOCODE && $this->yyidx >= 0);
    }
}
