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
    \Artifex\Runtime\RawString,
    \Artifex\Runtime\Term,
    \Artifex\Runtime\DefFunction,
    \Artifex\Runtime\Variable;
#line 147 "lib/Artifex/Parser.php"

// declare_class is output here
#line 50 "lib/Artifex/Parser.y"
class Artifex_Parser #line 152 "lib/Artifex/Parser.php"
{
/* First off, code is included which follows the "include_class" declaration
** in the input file. */
#line 51 "lib/Artifex/Parser.y"

    public $body = array();
#line 160 "lib/Artifex/Parser.php"

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
    const T_FOREACH                      = 25;
    const T_LPARENT                      = 26;
    const T_AS                           = 27;
    const T_RPARENT                      = 28;
    const T_END                          = 29;
    const T_DOUBLE_ARROW                 = 30;
    const T_FUNCTION                     = 31;
    const T_ALPHA                        = 32;
    const T_IF                           = 33;
    const T_ELSE                         = 34;
    const T_AT                           = 35;
    const T_DOLLAR                       = 36;
    const T_CURLY_CLOSE                  = 37;
    const T_TRUE                         = 38;
    const T_FALSE                        = 39;
    const T_STRING                       = 40;
    const T_NUMBER                       = 41;
    const T_SUBSCR_OPEN                  = 42;
    const T_SUBSCR_CLOSE                 = 43;
    const T_COLON                        = 44;
    const YY_NO_ACTION = 166;
    const YY_ACCEPT_ACTION = 165;
    const YY_ERROR_ACTION = 164;

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
    const YY_SZ_ACTTAB = 403;
static public $yy_action = array(
 /*     0 */    12,   16,    1,   15,   15,    4,   15,   15,   15,   15,
 /*    10 */    15,   13,   19,   19,    9,    9,    9,   20,   20,   12,
 /*    20 */    16,    3,   15,   15,   37,   15,   15,   15,   15,   15,
 /*    30 */    13,   19,   19,    9,    9,    9,   20,   20,   12,   16,
 /*    40 */    21,   15,   15,   41,   15,   15,   15,   15,   15,   13,
 /*    50 */    19,   19,    9,    9,    9,   20,   20,   12,   16,    8,
 /*    60 */    15,   15,   96,   15,   15,   15,   15,   15,   13,   19,
 /*    70 */    19,    9,    9,    9,   20,   20,    4,  100,   84,   39,
 /*    80 */    68,   31,  109,   70,   82,   63,   72,   73,   76,   32,
 /*    90 */    95,   34,   69,   79,   78,   12,   16,   18,   15,   15,
 /*   100 */    85,   15,   15,   15,   15,   15,   13,   19,   19,    9,
 /*   110 */     9,    9,   20,   20,   16,   77,   15,   15,   64,   15,
 /*   120 */    15,   15,   15,   15,   13,   19,   19,    9,    9,    9,
 /*   130 */    20,   20,   15,   15,   28,   15,   15,   15,   15,   15,
 /*   140 */    13,   19,   19,    9,    9,    9,   20,   20,   28,    7,
 /*   150 */    84,   11,   68,  103,   86,   74,   98,   65,   72,   73,
 /*   160 */    76,  105,  111,   34,    2,   29,   84,   34,   68,   20,
 /*   170 */    20,   14,   80,    5,   72,   73,   76,   66,   99,   34,
 /*   180 */    33,   34,   34,   90,   91,   92,   93,    5,    2,  100,
 /*   190 */    84,   89,   68,   97,    2,   70,   81,  107,   72,   73,
 /*   200 */    76,  108,   28,   34,   19,   19,    9,    9,    9,   20,
 /*   210 */    20,   35,   10,   71,   84,   83,   68,  165,   27,   67,
 /*   220 */   110,   38,   72,   73,   76,   25,   59,   34,   30,   40,
 /*   230 */    23,  101,   22,   17,   84,   36,   68,   90,   91,   92,
 /*   240 */    93,    5,   72,   73,   76,   26,  116,   34,   75,  100,
 /*   250 */    61,   47,   94,   86,   74,  102,   65,    6,  116,   24,
 /*   260 */   105,  111,   88,  116,  116,  116,   75,  100,  116,   50,
 /*   270 */    94,  116,  116,  102,  116,  116,   57,  116,  116,   75,
 /*   280 */   100,   62,   47,   94,  116,  116,  102,  116,   75,  100,
 /*   290 */   104,   47,   94,   86,   74,  102,   65,  116,  116,  116,
 /*   300 */   105,  111,   87,   75,  100,   58,   47,   94,   75,  100,
 /*   310 */   102,   48,   94,   75,  100,  102,  106,   94,   75,  100,
 /*   320 */   102,   53,   94,  116,  116,  102,  116,  116,   75,  100,
 /*   330 */   116,   45,   94,   75,  100,  102,   56,   94,   75,  100,
 /*   340 */   102,   55,   94,   75,  100,  102,   51,   94,   75,  100,
 /*   350 */   102,   42,   94,   75,  100,  102,   60,   94,   75,  100,
 /*   360 */   102,   46,   94,  116,  116,  102,    9,    9,    9,   20,
 /*   370 */    20,   75,  100,  116,   44,   94,  116,  116,  102,  116,
 /*   380 */    75,  100,  116,   49,   94,   75,  100,  102,   54,   94,
 /*   390 */    75,  100,  102,   52,   94,   75,  100,  102,   43,   94,
 /*   400 */   116,  116,  102,
    );
    static public $yy_lookahead = array(
 /*     0 */     4,    5,   26,    7,    8,   26,   10,   11,   12,   13,
 /*    10 */    14,   15,   16,   17,   18,   19,   20,   21,   22,    4,
 /*    20 */     5,   26,    7,    8,   28,   10,   11,   12,   13,   14,
 /*    30 */    15,   16,   17,   18,   19,   20,   21,   22,    4,    5,
 /*    40 */     1,    7,    8,   28,   10,   11,   12,   13,   14,   15,
 /*    50 */    16,   17,   18,   19,   20,   21,   22,    4,    5,    9,
 /*    60 */     7,    8,   28,   10,   11,   12,   13,   14,   15,   16,
 /*    70 */    17,   18,   19,   20,   21,   22,   26,   52,   23,   28,
 /*    80 */    25,   30,   43,   58,   29,   60,   31,   32,   33,   34,
 /*    90 */    37,   36,   50,   51,   52,    4,    5,   26,    7,    8,
 /*   100 */    24,   10,   11,   12,   13,   14,   15,   16,   17,   18,
 /*   110 */    19,   20,   21,   22,    5,   32,    7,    8,   59,   10,
 /*   120 */    11,   12,   13,   14,   15,   16,   17,   18,   19,   20,
 /*   130 */    21,   22,    7,    8,    3,   10,   11,   12,   13,   14,
 /*   140 */    15,   16,   17,   18,   19,   20,   21,   22,    3,   47,
 /*   150 */    23,    6,   25,   32,   48,   49,   29,   51,   31,   32,
 /*   160 */    33,   55,   56,   36,    1,    1,   23,   36,   25,   21,
 /*   170 */    22,   26,   29,   42,   31,   32,   33,   32,   59,   36,
 /*   180 */    35,   36,   36,   38,   39,   40,   41,   42,    1,   52,
 /*   190 */    23,   28,   25,   51,    1,   58,   29,   60,   31,   32,
 /*   200 */    33,   37,    3,   36,   16,   17,   18,   19,   20,   21,
 /*   210 */    22,    2,    3,   51,   23,   28,   25,   46,   47,   33,
 /*   220 */    29,   28,   31,   32,   33,   47,   51,   36,   26,   28,
 /*   230 */    47,   32,   26,   44,   23,   27,   25,   38,   39,   40,
 /*   240 */    41,   42,   31,   32,   33,   47,   62,   36,   51,   52,
 /*   250 */    53,   54,   55,   48,   49,   58,   51,   47,   62,   47,
 /*   260 */    55,   56,   57,   62,   62,   62,   51,   52,   62,   54,
 /*   270 */    55,   62,   62,   58,   62,   62,   61,   62,   62,   51,
 /*   280 */    52,   53,   54,   55,   62,   62,   58,   62,   51,   52,
 /*   290 */    53,   54,   55,   48,   49,   58,   51,   62,   62,   62,
 /*   300 */    55,   56,   57,   51,   52,   53,   54,   55,   51,   52,
 /*   310 */    58,   54,   55,   51,   52,   58,   54,   55,   51,   52,
 /*   320 */    58,   54,   55,   62,   62,   58,   62,   62,   51,   52,
 /*   330 */    62,   54,   55,   51,   52,   58,   54,   55,   51,   52,
 /*   340 */    58,   54,   55,   51,   52,   58,   54,   55,   51,   52,
 /*   350 */    58,   54,   55,   51,   52,   58,   54,   55,   51,   52,
 /*   360 */    58,   54,   55,   62,   62,   58,   18,   19,   20,   21,
 /*   370 */    22,   51,   52,   62,   54,   55,   62,   62,   58,   62,
 /*   380 */    51,   52,   62,   54,   55,   51,   52,   58,   54,   55,
 /*   390 */    51,   52,   58,   54,   55,   51,   52,   58,   54,   55,
 /*   400 */    62,   62,   58,
);
    const YY_SHIFT_USE_DFLT = -25;
    const YY_SHIFT_MAX = 77;
    static public $yy_shift_ofst = array(
 /*     0 */   -25,  145,  145,  145,  145,  145,   55,   55,  145,  145,
 /*    10 */   145,  145,  145,  145,  145,  145,  145,  145,  145,  145,
 /*    20 */   145,  145,  145,  143,  127,  167,  191,  211,  199,  199,
 /*    30 */   131,  146,  186,  146,  121,  121,  146,  -25,  -25,  -25,
 /*    40 */   -25,  -25,   53,   34,   -4,   15,   91,   91,   91,   91,
 /*    50 */    91,  109,  125,  125,  125,  188,  348,   39,  187,   51,
 /*    60 */   148,  193,  163,  164,  209,   50,   -5,  206,  202,  208,
 /*    70 */   189,  201,   83,   -5,   76,  -21,   71,  -24,
);
    const YY_REDUCE_USE_DFLT = -1;
    const YY_REDUCE_MAX = 41;
    static public $yy_reduce_ofst = array(
 /*     0 */   171,  197,  237,  228,  252,  215,  205,  245,  307,  302,
 /*    10 */   297,  267,  292,  287,  344,  334,  339,  329,  320,  282,
 /*    20 */   262,  257,  277,  106,  106,  106,  106,  106,   25,  137,
 /*    30 */    42,  162,  178,  142,   59,  119,  175,  210,  212,  198,
 /*    40 */   183,  102,
);
    static public $yyExpectedTokens = array(
        /* 0 */ array(),
        /* 1 */ array(3, 6, 26, 32, 35, 36, 38, 39, 40, 41, 42, ),
        /* 2 */ array(3, 6, 26, 32, 35, 36, 38, 39, 40, 41, 42, ),
        /* 3 */ array(3, 6, 26, 32, 35, 36, 38, 39, 40, 41, 42, ),
        /* 4 */ array(3, 6, 26, 32, 35, 36, 38, 39, 40, 41, 42, ),
        /* 5 */ array(3, 6, 26, 32, 35, 36, 38, 39, 40, 41, 42, ),
        /* 6 */ array(23, 25, 29, 31, 32, 33, 34, 36, ),
        /* 7 */ array(23, 25, 29, 31, 32, 33, 34, 36, ),
        /* 8 */ array(3, 6, 26, 32, 35, 36, 38, 39, 40, 41, 42, ),
        /* 9 */ array(3, 6, 26, 32, 35, 36, 38, 39, 40, 41, 42, ),
        /* 10 */ array(3, 6, 26, 32, 35, 36, 38, 39, 40, 41, 42, ),
        /* 11 */ array(3, 6, 26, 32, 35, 36, 38, 39, 40, 41, 42, ),
        /* 12 */ array(3, 6, 26, 32, 35, 36, 38, 39, 40, 41, 42, ),
        /* 13 */ array(3, 6, 26, 32, 35, 36, 38, 39, 40, 41, 42, ),
        /* 14 */ array(3, 6, 26, 32, 35, 36, 38, 39, 40, 41, 42, ),
        /* 15 */ array(3, 6, 26, 32, 35, 36, 38, 39, 40, 41, 42, ),
        /* 16 */ array(3, 6, 26, 32, 35, 36, 38, 39, 40, 41, 42, ),
        /* 17 */ array(3, 6, 26, 32, 35, 36, 38, 39, 40, 41, 42, ),
        /* 18 */ array(3, 6, 26, 32, 35, 36, 38, 39, 40, 41, 42, ),
        /* 19 */ array(3, 6, 26, 32, 35, 36, 38, 39, 40, 41, 42, ),
        /* 20 */ array(3, 6, 26, 32, 35, 36, 38, 39, 40, 41, 42, ),
        /* 21 */ array(3, 6, 26, 32, 35, 36, 38, 39, 40, 41, 42, ),
        /* 22 */ array(3, 6, 26, 32, 35, 36, 38, 39, 40, 41, 42, ),
        /* 23 */ array(23, 25, 29, 31, 32, 33, 36, ),
        /* 24 */ array(23, 25, 29, 31, 32, 33, 36, ),
        /* 25 */ array(23, 25, 29, 31, 32, 33, 36, ),
        /* 26 */ array(23, 25, 29, 31, 32, 33, 36, ),
        /* 27 */ array(23, 25, 31, 32, 33, 36, ),
        /* 28 */ array(3, 32, 38, 39, 40, 41, 42, ),
        /* 29 */ array(3, 32, 38, 39, 40, 41, 42, ),
        /* 30 */ array(3, 36, 42, ),
        /* 31 */ array(36, ),
        /* 32 */ array(33, ),
        /* 33 */ array(36, ),
        /* 34 */ array(32, ),
        /* 35 */ array(32, ),
        /* 36 */ array(36, ),
        /* 37 */ array(),
        /* 38 */ array(),
        /* 39 */ array(),
        /* 40 */ array(),
        /* 41 */ array(),
        /* 42 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 37, ),
        /* 43 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 28, ),
        /* 44 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 28, ),
        /* 45 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 28, ),
        /* 46 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, ),
        /* 47 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, ),
        /* 48 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, ),
        /* 49 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, ),
        /* 50 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, ),
        /* 51 */ array(5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, ),
        /* 52 */ array(7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, ),
        /* 53 */ array(7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, ),
        /* 54 */ array(7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, ),
        /* 55 */ array(16, 17, 18, 19, 20, 21, 22, ),
        /* 56 */ array(18, 19, 20, 21, 22, ),
        /* 57 */ array(1, 43, ),
        /* 58 */ array(1, 28, ),
        /* 59 */ array(28, 30, ),
        /* 60 */ array(21, 22, ),
        /* 61 */ array(1, 28, ),
        /* 62 */ array(1, 28, ),
        /* 63 */ array(1, 37, ),
        /* 64 */ array(2, 3, ),
        /* 65 */ array(9, 26, ),
        /* 66 */ array(26, ),
        /* 67 */ array(26, ),
        /* 68 */ array(26, ),
        /* 69 */ array(27, ),
        /* 70 */ array(44, ),
        /* 71 */ array(28, ),
        /* 72 */ array(32, ),
        /* 73 */ array(26, ),
        /* 74 */ array(24, ),
        /* 75 */ array(26, ),
        /* 76 */ array(26, ),
        /* 77 */ array(26, ),
        /* 78 */ array(),
        /* 79 */ array(),
        /* 80 */ array(),
        /* 81 */ array(),
        /* 82 */ array(),
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
);
    static public $yy_default = array(
 /*     0 */   114,  147,  147,  147,  147,  164,  164,  164,  164,  164,
 /*    10 */   164,  164,  164,  164,  164,  164,  164,  164,  164,  164,
 /*    20 */   164,  164,  164,  164,  164,  164,  164,  112,  164,  164,
 /*    30 */   164,  164,  114,  164,  164,  164,  164,  114,  114,  114,
 /*    40 */   114,  114,  164,  164,  164,  164,  123,  146,  162,  161,
 /*    50 */   163,  133,  134,  132,  136,  140,  135,  164,  164,  164,
 /*    60 */   137,  164,  164,  164,  148,  164,  152,  164,  164,  164,
 /*    70 */   164,  164,  164,  164,  116,  141,  164,  164,  121,  120,
 /*    80 */   119,  130,  131,  126,  115,  117,  113,  129,  128,  125,
 /*    90 */   153,  154,  155,  156,  144,  150,  139,  143,  122,  149,
 /*   100 */   157,  152,  142,  151,  145,  124,  138,  160,  158,  159,
 /*   110 */   118,  127,
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
    const YYNOCODE = 63;
    const YYSTACKDEPTH = 100;
    const YYNSTATE = 112;
    const YYNRULE = 52;
    const YYERRORSYMBOL = 45;
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
  'T_NEW_LINE',    'T_FOREACH',     'T_LPARENT',     'T_AS',        
  'T_RPARENT',     'T_END',         'T_DOUBLE_ARROW',  'T_FUNCTION',  
  'T_ALPHA',       'T_IF',          'T_ELSE',        'T_AT',        
  'T_DOLLAR',      'T_CURLY_CLOSE',  'T_TRUE',        'T_FALSE',     
  'T_STRING',      'T_NUMBER',      'T_SUBSCR_OPEN',  'T_SUBSCR_CLOSE',
  'T_COLON',       'error',         'start',         'body',        
  'line',          'code',          'foreach_source',  'variable',    
  'json',          'args',          'expr',          'fnc_call',    
  'if',            'else_if',       'term',          'var',         
  'json_obj',      'json_arr',    
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
 /*   6 */ "code ::= T_FOREACH T_LPARENT foreach_source T_AS variable T_RPARENT body T_END",
 /*   7 */ "code ::= T_FOREACH T_LPARENT foreach_source T_AS variable T_DOUBLE_ARROW variable T_RPARENT body T_END",
 /*   8 */ "foreach_source ::= variable",
 /*   9 */ "foreach_source ::= json",
 /*  10 */ "code ::= T_FUNCTION T_ALPHA T_LPARENT args T_RPARENT body T_END",
 /*  11 */ "code ::= variable T_ASSIGN expr",
 /*  12 */ "code ::= fnc_call",
 /*  13 */ "fnc_call ::= T_ALPHA T_LPARENT args T_RPARENT",
 /*  14 */ "fnc_call ::= variable T_LPARENT args T_RPARENT",
 /*  15 */ "code ::= if",
 /*  16 */ "if ::= T_IF T_LPARENT expr T_RPARENT body else_if",
 /*  17 */ "else_if ::= T_ELSE T_IF T_LPARENT expr T_RPARENT body else_if",
 /*  18 */ "else_if ::= T_ELSE body T_END",
 /*  19 */ "else_if ::= T_END",
 /*  20 */ "expr ::= T_NOT expr",
 /*  21 */ "expr ::= expr T_AND expr",
 /*  22 */ "expr ::= expr T_OR expr",
 /*  23 */ "expr ::= expr T_PLUS|T_MINUS expr",
 /*  24 */ "expr ::= expr T_EQ|T_NE|T_GT|T_GE|T_LT|T_LE|T_IN expr",
 /*  25 */ "expr ::= expr T_TIMES|T_DIV|T_MOD expr",
 /*  26 */ "expr ::= expr T_BITWISE|T_PIPE expr",
 /*  27 */ "expr ::= T_LPARENT expr T_RPARENT",
 /*  28 */ "expr ::= expr T_DOT expr",
 /*  29 */ "expr ::= variable",
 /*  30 */ "expr ::= term",
 /*  31 */ "expr ::= T_AT variable",
 /*  32 */ "expr ::= fnc_call",
 /*  33 */ "args ::= args T_COMMA args",
 /*  34 */ "args ::= expr",
 /*  35 */ "args ::=",
 /*  36 */ "variable ::= T_DOLLAR var",
 /*  37 */ "var ::= var T_OBJ var",
 /*  38 */ "var ::= var T_CURLY_OPEN expr T_CURLY_CLOSE",
 /*  39 */ "var ::= T_ALPHA",
 /*  40 */ "term ::= T_ALPHA",
 /*  41 */ "term ::= T_TRUE",
 /*  42 */ "term ::= T_FALSE",
 /*  43 */ "term ::= T_STRING",
 /*  44 */ "term ::= T_NUMBER",
 /*  45 */ "term ::= json",
 /*  46 */ "json ::= T_CURLY_OPEN json_obj T_CURLY_CLOSE",
 /*  47 */ "json ::= T_SUBSCR_OPEN json_arr T_SUBSCR_CLOSE",
 /*  48 */ "json_obj ::= json_obj T_COMMA json_obj",
 /*  49 */ "json_obj ::= term T_COLON expr",
 /*  50 */ "json_arr ::= json_arr T_COMMA expr",
 /*  51 */ "json_arr ::= expr",
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
  array( 'lhs' => 46, 'rhs' => 1 ),
  array( 'lhs' => 47, 'rhs' => 2 ),
  array( 'lhs' => 47, 'rhs' => 0 ),
  array( 'lhs' => 48, 'rhs' => 1 ),
  array( 'lhs' => 48, 'rhs' => 1 ),
  array( 'lhs' => 48, 'rhs' => 2 ),
  array( 'lhs' => 49, 'rhs' => 8 ),
  array( 'lhs' => 49, 'rhs' => 10 ),
  array( 'lhs' => 50, 'rhs' => 1 ),
  array( 'lhs' => 50, 'rhs' => 1 ),
  array( 'lhs' => 49, 'rhs' => 7 ),
  array( 'lhs' => 49, 'rhs' => 3 ),
  array( 'lhs' => 49, 'rhs' => 1 ),
  array( 'lhs' => 55, 'rhs' => 4 ),
  array( 'lhs' => 55, 'rhs' => 4 ),
  array( 'lhs' => 49, 'rhs' => 1 ),
  array( 'lhs' => 56, 'rhs' => 6 ),
  array( 'lhs' => 57, 'rhs' => 7 ),
  array( 'lhs' => 57, 'rhs' => 3 ),
  array( 'lhs' => 57, 'rhs' => 1 ),
  array( 'lhs' => 54, 'rhs' => 2 ),
  array( 'lhs' => 54, 'rhs' => 3 ),
  array( 'lhs' => 54, 'rhs' => 3 ),
  array( 'lhs' => 54, 'rhs' => 3 ),
  array( 'lhs' => 54, 'rhs' => 3 ),
  array( 'lhs' => 54, 'rhs' => 3 ),
  array( 'lhs' => 54, 'rhs' => 3 ),
  array( 'lhs' => 54, 'rhs' => 3 ),
  array( 'lhs' => 54, 'rhs' => 3 ),
  array( 'lhs' => 54, 'rhs' => 1 ),
  array( 'lhs' => 54, 'rhs' => 1 ),
  array( 'lhs' => 54, 'rhs' => 2 ),
  array( 'lhs' => 54, 'rhs' => 1 ),
  array( 'lhs' => 53, 'rhs' => 3 ),
  array( 'lhs' => 53, 'rhs' => 1 ),
  array( 'lhs' => 53, 'rhs' => 0 ),
  array( 'lhs' => 51, 'rhs' => 2 ),
  array( 'lhs' => 59, 'rhs' => 3 ),
  array( 'lhs' => 59, 'rhs' => 4 ),
  array( 'lhs' => 59, 'rhs' => 1 ),
  array( 'lhs' => 58, 'rhs' => 1 ),
  array( 'lhs' => 58, 'rhs' => 1 ),
  array( 'lhs' => 58, 'rhs' => 1 ),
  array( 'lhs' => 58, 'rhs' => 1 ),
  array( 'lhs' => 58, 'rhs' => 1 ),
  array( 'lhs' => 58, 'rhs' => 1 ),
  array( 'lhs' => 52, 'rhs' => 3 ),
  array( 'lhs' => 52, 'rhs' => 3 ),
  array( 'lhs' => 60, 'rhs' => 3 ),
  array( 'lhs' => 60, 'rhs' => 3 ),
  array( 'lhs' => 61, 'rhs' => 3 ),
  array( 'lhs' => 61, 'rhs' => 1 ),
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
        35 => 2,
        3 => 3,
        4 => 4,
        8 => 4,
        12 => 4,
        15 => 4,
        29 => 4,
        32 => 4,
        43 => 4,
        45 => 4,
        5 => 5,
        47 => 5,
        6 => 6,
        7 => 7,
        9 => 9,
        10 => 10,
        11 => 11,
        13 => 13,
        14 => 13,
        16 => 16,
        17 => 17,
        18 => 18,
        19 => 19,
        20 => 20,
        21 => 21,
        22 => 21,
        23 => 23,
        25 => 23,
        26 => 23,
        24 => 24,
        27 => 27,
        28 => 28,
        30 => 30,
        31 => 31,
        33 => 33,
        37 => 33,
        48 => 33,
        34 => 34,
        51 => 34,
        36 => 36,
        38 => 38,
        39 => 39,
        40 => 40,
        41 => 41,
        42 => 42,
        44 => 44,
        46 => 46,
        49 => 49,
        50 => 50,
    );
    /* Beginning here are the reduction cases.  A typical example
    ** follows:
    **  #line <lineno> <grammarfile>
    **   function yy_r0($yymsp){ ... }           // User supplied code
    **  #line <lineno> <thisfile>
    */
#line 79 "lib/Artifex/Parser.y"
    function yy_r0(){ $this->body = $this->yystack[$this->yyidx + 0]->minor;     }
#line 1169 "lib/Artifex/Parser.php"
#line 81 "lib/Artifex/Parser.y"
    function yy_r1(){ $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor; $this->_retvalue[] = $this->yystack[$this->yyidx + 0]->minor;     }
#line 1172 "lib/Artifex/Parser.php"
#line 82 "lib/Artifex/Parser.y"
    function yy_r2(){ $this->_retvalue = array();     }
#line 1175 "lib/Artifex/Parser.php"
#line 84 "lib/Artifex/Parser.y"
    function yy_r3(){ $this->_retvalue = new RawString($this->yystack[$this->yyidx + 0]->minor);     }
#line 1178 "lib/Artifex/Parser.php"
#line 85 "lib/Artifex/Parser.y"
    function yy_r4(){ $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;     }
#line 1181 "lib/Artifex/Parser.php"
#line 86 "lib/Artifex/Parser.y"
    function yy_r5(){ $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor;     }
#line 1184 "lib/Artifex/Parser.php"
#line 89 "lib/Artifex/Parser.y"
    function yy_r6(){ 
    $this->_retvalue = new Expr_Foreach($this->yystack[$this->yyidx + -5]->minor, $this->yystack[$this->yyidx + -3]->minor, NULL, $this->yystack[$this->yyidx + -1]->minor); 
    }
#line 1189 "lib/Artifex/Parser.php"
#line 93 "lib/Artifex/Parser.y"
    function yy_r7(){
    $this->_retvalue = new Expr_Foreach($this->yystack[$this->yyidx + -7]->minor, $this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -5]->minor, $this->yystack[$this->yyidx + -1]->minor);
    }
#line 1194 "lib/Artifex/Parser.php"
#line 98 "lib/Artifex/Parser.y"
    function yy_r9(){ $this->_retvalue = new Term($this->yystack[$this->yyidx + 0]->minor);     }
#line 1197 "lib/Artifex/Parser.php"
#line 102 "lib/Artifex/Parser.y"
    function yy_r10(){
    $this->_retvalue = new DefFunction($this->yystack[$this->yyidx + -5]->minor, $this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -1]->minor);
    }
#line 1202 "lib/Artifex/Parser.php"
#line 108 "lib/Artifex/Parser.y"
    function yy_r11(){ $this->_retvalue = new Assign($this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1205 "lib/Artifex/Parser.php"
#line 113 "lib/Artifex/Parser.y"
    function yy_r13(){ 
    $this->_retvalue = new Exec($this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -1]->minor);
    }
#line 1210 "lib/Artifex/Parser.php"
#line 124 "lib/Artifex/Parser.y"
    function yy_r16(){
    $this->_retvalue = new Expr_If($this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + 0]->minor);
    }
#line 1215 "lib/Artifex/Parser.php"
#line 128 "lib/Artifex/Parser.y"
    function yy_r17(){ 
    $this->_retvalue = new Expr_If($this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + 0]->minor); 
    }
#line 1220 "lib/Artifex/Parser.php"
#line 131 "lib/Artifex/Parser.y"
    function yy_r18(){ 
    $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor; 
    }
#line 1225 "lib/Artifex/Parser.php"
#line 134 "lib/Artifex/Parser.y"
    function yy_r19(){ $this->_retvalue = NULL;     }
#line 1228 "lib/Artifex/Parser.php"
#line 138 "lib/Artifex/Parser.y"
    function yy_r20(){ $this->_retvalue = new Expr('not', $this->yystack[$this->yyidx + 0]->minor);     }
#line 1231 "lib/Artifex/Parser.php"
#line 139 "lib/Artifex/Parser.y"
    function yy_r21(){ $this->_retvalue = new Expr(strtolower(@$this->yystack[$this->yyidx + -1]->minor), $this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1234 "lib/Artifex/Parser.php"
#line 141 "lib/Artifex/Parser.y"
    function yy_r23(){ $this->_retvalue = new Expr(@$this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1237 "lib/Artifex/Parser.php"
#line 142 "lib/Artifex/Parser.y"
    function yy_r24(){ $this->_retvalue = new Expr(trim(@$this->yystack[$this->yyidx + -1]->minor), $this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1240 "lib/Artifex/Parser.php"
#line 145 "lib/Artifex/Parser.y"
    function yy_r27(){ $this->_retvalue = new Expr($this->yystack[$this->yyidx + -1]->minor);     }
#line 1243 "lib/Artifex/Parser.php"
#line 146 "lib/Artifex/Parser.y"
    function yy_r28(){ $this->_retvalue = new Concat($this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1246 "lib/Artifex/Parser.php"
#line 148 "lib/Artifex/Parser.y"
    function yy_r30(){  $this->_retvalue = new Term($this->yystack[$this->yyidx + 0]->minor);     }
#line 1249 "lib/Artifex/Parser.php"
#line 149 "lib/Artifex/Parser.y"
    function yy_r31(){ 
    $this->_retvalue = new Exec('var_export', array($this->yystack[$this->yyidx + 0]->minor, new Term(true))); 
    }
#line 1254 "lib/Artifex/Parser.php"
#line 156 "lib/Artifex/Parser.y"
    function yy_r33(){ $this->_retvalue = array_merge($this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1257 "lib/Artifex/Parser.php"
#line 157 "lib/Artifex/Parser.y"
    function yy_r34(){ $this->_retvalue = array($this->yystack[$this->yyidx + 0]->minor);     }
#line 1260 "lib/Artifex/Parser.php"
#line 162 "lib/Artifex/Parser.y"
    function yy_r36(){ $this->_retvalue = new Variable($this->yystack[$this->yyidx + 0]->minor);     }
#line 1263 "lib/Artifex/Parser.php"
#line 165 "lib/Artifex/Parser.y"
    function yy_r38(){ $this->_retvalue = $this->yystack[$this->yyidx + -3]->minor ; $this->_retvalue[] = $this->yystack[$this->yyidx + -1]->minor;     }
#line 1266 "lib/Artifex/Parser.php"
#line 166 "lib/Artifex/Parser.y"
    function yy_r39(){ $this->_retvalue = array($this->yystack[$this->yyidx + 0]->minor);    }
#line 1269 "lib/Artifex/Parser.php"
#line 170 "lib/Artifex/Parser.y"
    function yy_r40(){ $this->_retvalue = trim($this->yystack[$this->yyidx + 0]->minor);     }
#line 1272 "lib/Artifex/Parser.php"
#line 171 "lib/Artifex/Parser.y"
    function yy_r41(){ $this->_retvalue = TRUE;     }
#line 1275 "lib/Artifex/Parser.php"
#line 172 "lib/Artifex/Parser.y"
    function yy_r42(){ $this->_retvalue = FALSE;     }
#line 1278 "lib/Artifex/Parser.php"
#line 174 "lib/Artifex/Parser.y"
    function yy_r44(){ $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor + 0;     }
#line 1281 "lib/Artifex/Parser.php"
#line 179 "lib/Artifex/Parser.y"
    function yy_r46(){ $this->_retvalue  = $this->yystack[$this->yyidx + -1]->minor;     }
#line 1284 "lib/Artifex/Parser.php"
#line 183 "lib/Artifex/Parser.y"
    function yy_r49(){ $this->_retvalue = array($this->yystack[$this->yyidx + -2]->minor => $this->yystack[$this->yyidx + 0]->minor);     }
#line 1287 "lib/Artifex/Parser.php"
#line 185 "lib/Artifex/Parser.y"
    function yy_r50(){ $this->_retvalue = $this->yystack[$this->yyidx + -2]->minor; $this->_retvalue[] = $this->yystack[$this->yyidx + 0]->minor;     }
#line 1290 "lib/Artifex/Parser.php"

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
#line 55 "lib/Artifex/Parser.y"

    $expect = array();
    foreach ($this->yy_get_expected_tokens($yymajor) as $token) {
        $expect[] = self::$yyTokenName[$token];
    }
    throw new Exception('Unexpected ' . $this->tokenName($yymajor) .  ' in line ' . $this->line
        . ' (' . $TOKEN . ') '
        . ' Expected: ' . print_r($expect, true));
#line 1412 "lib/Artifex/Parser.php"
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
