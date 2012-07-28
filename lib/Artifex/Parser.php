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
    \Artifex\Runtime\RawString,
    \Artifex\Runtime\Whitespace,
    \Artifex\Runtime\Term,
    \Artifex\Runtime\Variable;
#line 149 "lib/Artifex/Parser.php"

// declare_class is output here
#line 52 "lib/Artifex/Parser.y"
class Artifex_Parser #line 154 "lib/Artifex/Parser.php"
{
/* First off, code is included which follows the "include_class" declaration
** in the input file. */
#line 53 "lib/Artifex/Parser.y"

    public $body = array();

    public function setPrevNext($a, $b)
    {
        $a->setNext($b);
        $b->setPrev($a);
    }
#line 168 "lib/Artifex/Parser.php"

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
    const T_FOREACH                      = 28;
    const T_LPARENT                      = 29;
    const T_AS                           = 30;
    const T_RPARENT                      = 31;
    const T_END                          = 32;
    const T_DOUBLE_ARROW                 = 33;
    const T_FUNCTION                     = 34;
    const T_ALPHA                        = 35;
    const T_IF                           = 36;
    const T_ELSE                         = 37;
    const T_AT                           = 38;
    const T_DOLLAR                       = 39;
    const T_SUBSCR_OPEN                  = 40;
    const T_SUBSCR_CLOSE                 = 41;
    const T_TRUE                         = 42;
    const T_FALSE                        = 43;
    const T_NUMBER                       = 44;
    const T_CURLY_CLOSE                  = 45;
    const T_COLON                        = 46;
    const YY_NO_ACTION = 180;
    const YY_ACCEPT_ACTION = 179;
    const YY_ERROR_ACTION = 178;

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
    const YY_SZ_ACTTAB = 481;
static public $yy_action = array(
 /*     0 */    19,    8,  104,   12,   12,   62,   12,   12,   12,   12,
 /*    10 */    12,   22,   10,   10,   15,   15,   15,   13,   13,    8,
 /*    20 */    82,   12,   12,    1,   12,   12,   12,   12,   12,   22,
 /*    30 */    10,   10,   15,   15,   15,   13,   13,   90,   80,   72,
 /*    40 */    87,   89,   19,    8,   31,   12,   12,   33,   12,   12,
 /*    50 */    12,   12,   12,   22,   10,   10,   15,   15,   15,   13,
 /*    60 */    13,   13,   13,   18,   19,    8,    9,   12,   12,   39,
 /*    70 */    12,   12,   12,   12,   12,   22,   10,   10,   15,   15,
 /*    80 */    15,   13,   13,    2,   60,   16,   19,    8,  110,   12,
 /*    90 */    12,   93,   12,   12,   12,   12,   12,   22,   10,   10,
 /*   100 */    15,   15,   15,   13,   13,   41,  109,   34,   19,    8,
 /*   110 */    32,   12,   12,   38,   12,   12,   12,   12,   12,   22,
 /*   120 */    10,   10,   15,   15,   15,   13,   13,   12,   12,   71,
 /*   130 */    12,   12,   12,   12,   12,   22,   10,   10,   15,   15,
 /*   140 */    15,   13,   13,   30,    5,   68,   11,   91,    4,  113,
 /*   150 */   112,   21,   81,  179,   28,   17,   98,   79,   69,   70,
 /*   160 */    75,   37,    5,   32,    2,   96,  111,   35,   49,   14,
 /*   170 */    77,   85,  115,  105,  102,   76,   92,   23,   36,   32,
 /*   180 */     3,   16,   84,   83,  100,   91,    5,  113,  112,   21,
 /*   190 */    81,   40,  107,   88,  103,   20,   69,   70,   75,   95,
 /*   200 */    29,   32,   91,   24,  113,  112,   21,   81,    7,   74,
 /*   210 */   125,   99,   26,   69,   70,   75,   42,   27,   32,   25,
 /*   220 */    91,    6,  113,  112,   21,   81,  125,  125,  125,  108,
 /*   230 */   125,   69,   70,   75,  125,   91,   32,  113,  112,   21,
 /*   240 */    81,  125,  125,  125,   86,  125,   69,   70,   75,  125,
 /*   250 */   125,   32,  125,   91,  125,  113,  112,   21,   81,   30,
 /*   260 */   125,  125,  125,  125,   69,   70,   75,  125,  125,   32,
 /*   270 */   125,  125,  125,   79,   10,   10,   15,   15,   15,   13,
 /*   280 */    13,   96,  125,  125,   49,  125,   77,   85,  115,   65,
 /*   290 */   125,  116,   92,  125,  125,  125,    3,  125,   84,   83,
 /*   300 */   100,   49,  125,   77,   85,  115,   61,  125,  125,   92,
 /*   310 */   125,   48,  125,   77,   85,  115,   94,   73,  125,   92,
 /*   320 */    67,  117,   63,  125,  114,  118,   49,  125,   77,   85,
 /*   330 */   115,   64,   94,   73,   92,  125,   67,  117,  125,  125,
 /*   340 */   114,   97,   55,  125,   77,   85,  115,   94,   73,  125,
 /*   350 */    92,   67,  117,  125,  125,  114,   15,   15,   15,   13,
 /*   360 */    13,  125,   47,   30,   77,   85,  115,  125,  125,   57,
 /*   370 */    92,   77,   85,  115,  125,  125,  125,   92,   58,  125,
 /*   380 */    77,   85,  115,  125,  125,   44,   92,   77,   85,  115,
 /*   390 */   125,  125,  125,   92,   56,   70,   77,   85,  115,   32,
 /*   400 */     3,   52,   92,   77,   85,  115,  125,  125,   50,   92,
 /*   410 */    77,   85,  115,  125,  125,   51,   92,   77,   85,  115,
 /*   420 */   125,  125,   53,   92,   77,   85,  115,  125,  125,  125,
 /*   430 */    92,   59,  125,   77,   85,  115,  125,  125,   46,   92,
 /*   440 */    77,   85,  115,  125,  125,  125,   92,   43,  125,   77,
 /*   450 */    85,  115,  125,  125,   45,   92,   77,   85,  115,  125,
 /*   460 */   125,   54,   92,   77,   85,  115,  125,  125,  106,   92,
 /*   470 */    77,   85,  115,  115,  115,  125,   92,   78,   78,   66,
 /*   480 */   101,
    );
    static public $yy_lookahead = array(
 /*     0 */     4,    5,   35,    7,    8,   61,   10,   11,   12,   13,
 /*    10 */    14,   15,   16,   17,   18,   19,   20,   21,   22,    5,
 /*    20 */    61,    7,    8,   29,   10,   11,   12,   13,   14,   15,
 /*    30 */    16,   17,   18,   19,   20,   21,   22,   41,   53,   54,
 /*    40 */    55,   56,    4,    5,    1,    7,    8,    2,   10,   11,
 /*    50 */    12,   13,   14,   15,   16,   17,   18,   19,   20,   21,
 /*    60 */    22,   21,   22,    9,    4,    5,    1,    7,    8,   31,
 /*    70 */    10,   11,   12,   13,   14,   15,   16,   17,   18,   19,
 /*    80 */    20,   21,   22,   29,   54,   40,    4,    5,   45,    7,
 /*    90 */     8,   31,   10,   11,   12,   13,   14,   15,   16,   17,
 /*   100 */    18,   19,   20,   21,   22,   31,   41,   33,    4,    5,
 /*   110 */    39,    7,    8,   31,   10,   11,   12,   13,   14,   15,
 /*   120 */    16,   17,   18,   19,   20,   21,   22,    7,    8,   54,
 /*   130 */    10,   11,   12,   13,   14,   15,   16,   17,   18,   19,
 /*   140 */    20,   21,   22,    3,    1,   35,    6,   23,   29,   25,
 /*   150 */    26,   27,   28,   48,   49,   46,   32,   17,   34,   35,
 /*   160 */    36,   37,    1,   39,   29,   25,   44,   30,   52,   29,
 /*   170 */    54,   55,   56,   57,   31,   35,   60,   29,   38,   39,
 /*   180 */    40,   40,   42,   43,   44,   23,    1,   25,   26,   27,
 /*   190 */    28,   31,   31,   24,   32,   29,   34,   35,   36,   54,
 /*   200 */    29,   39,   23,   49,   25,   26,   27,   28,   49,   36,
 /*   210 */    64,   32,   49,   34,   35,   36,   31,   49,   39,   49,
 /*   220 */    23,   49,   25,   26,   27,   28,   64,   64,   64,   32,
 /*   230 */    64,   34,   35,   36,   64,   23,   39,   25,   26,   27,
 /*   240 */    28,   64,   64,   64,   32,   64,   34,   35,   36,   64,
 /*   250 */    64,   39,   64,   23,   64,   25,   26,   27,   28,    3,
 /*   260 */    64,   64,   64,   64,   34,   35,   36,   64,   64,   39,
 /*   270 */    64,   64,   64,   17,   16,   17,   18,   19,   20,   21,
 /*   280 */    22,   25,   64,   64,   52,   64,   54,   55,   56,   57,
 /*   290 */    64,   35,   60,   64,   64,   64,   40,   64,   42,   43,
 /*   300 */    44,   52,   64,   54,   55,   56,   57,   64,   64,   60,
 /*   310 */    64,   52,   64,   54,   55,   56,   50,   51,   64,   60,
 /*   320 */    54,   55,   63,   64,   58,   59,   52,   64,   54,   55,
 /*   330 */    56,   57,   50,   51,   60,   64,   54,   55,   64,   64,
 /*   340 */    58,   59,   52,   64,   54,   55,   56,   50,   51,   64,
 /*   350 */    60,   54,   55,   64,   64,   58,   18,   19,   20,   21,
 /*   360 */    22,   64,   52,    3,   54,   55,   56,   64,   64,   52,
 /*   370 */    60,   54,   55,   56,   64,   64,   64,   60,   52,   64,
 /*   380 */    54,   55,   56,   64,   64,   52,   60,   54,   55,   56,
 /*   390 */    64,   64,   64,   60,   52,   35,   54,   55,   56,   39,
 /*   400 */    40,   52,   60,   54,   55,   56,   64,   64,   52,   60,
 /*   410 */    54,   55,   56,   64,   64,   52,   60,   54,   55,   56,
 /*   420 */    64,   64,   52,   60,   54,   55,   56,   64,   64,   64,
 /*   430 */    60,   52,   64,   54,   55,   56,   64,   64,   52,   60,
 /*   440 */    54,   55,   56,   64,   64,   64,   60,   52,   64,   54,
 /*   450 */    55,   56,   64,   64,   52,   60,   54,   55,   56,   64,
 /*   460 */    64,   52,   60,   54,   55,   56,   64,   64,   52,   60,
 /*   470 */    54,   55,   56,   56,   56,   64,   60,   60,   60,   62,
 /*   480 */    62,
);
    const YY_SHIFT_USE_DFLT = -34;
    const YY_SHIFT_MAX = 82;
    static public $yy_shift_ofst = array(
 /*     0 */   -34,  140,  140,  140,  140,  140,  124,  124,  140,  140,
 /*    10 */   140,  140,  140,  140,  140,  140,  140,  140,  140,  140,
 /*    20 */   140,  140,  140,  140,  162,  212,  197,  179,  230,  360,
 /*    30 */   256,  256,  -33,  -33,   71,   71,   71,  173,  -34,  -34,
 /*    40 */   -34,  -34,  -34,   -4,   82,   38,   60,  104,  104,  104,
 /*    50 */   104,  104,  104,   14,  120,  120,  120,  258,  338,   40,
 /*    60 */    74,  185,   45,   65,  161,  143,   43,   54,   -6,  110,
 /*    70 */   119,  160,  135,  169,  166,  148,  119,  135,  109,  122,
 /*    80 */   137,  171,  141,
);
    const YY_REDUCE_USE_DFLT = -57;
    const YY_REDUCE_MAX = 42;
    static public $yy_reduce_ofst = array(
 /*     0 */   105,  249,  232,  259,  274,  116,  282,  266,  342,  356,
 /*    10 */   326,  409,  290,  416,  386,  379,  395,  349,  363,  370,
 /*    20 */   333,  310,  317,  402,  297,  297,  297,  297,  297,  -15,
 /*    30 */   417,  418,  -56,  -41,   75,   30,  145,  170,  172,  159,
 /*    40 */   168,  163,  154,
);
    static public $yyExpectedTokens = array(
        /* 0 */ array(),
        /* 1 */ array(3, 6, 17, 25, 29, 35, 38, 39, 40, 42, 43, 44, ),
        /* 2 */ array(3, 6, 17, 25, 29, 35, 38, 39, 40, 42, 43, 44, ),
        /* 3 */ array(3, 6, 17, 25, 29, 35, 38, 39, 40, 42, 43, 44, ),
        /* 4 */ array(3, 6, 17, 25, 29, 35, 38, 39, 40, 42, 43, 44, ),
        /* 5 */ array(3, 6, 17, 25, 29, 35, 38, 39, 40, 42, 43, 44, ),
        /* 6 */ array(23, 25, 26, 27, 28, 32, 34, 35, 36, 37, 39, ),
        /* 7 */ array(23, 25, 26, 27, 28, 32, 34, 35, 36, 37, 39, ),
        /* 8 */ array(3, 6, 17, 25, 29, 35, 38, 39, 40, 42, 43, 44, ),
        /* 9 */ array(3, 6, 17, 25, 29, 35, 38, 39, 40, 42, 43, 44, ),
        /* 10 */ array(3, 6, 17, 25, 29, 35, 38, 39, 40, 42, 43, 44, ),
        /* 11 */ array(3, 6, 17, 25, 29, 35, 38, 39, 40, 42, 43, 44, ),
        /* 12 */ array(3, 6, 17, 25, 29, 35, 38, 39, 40, 42, 43, 44, ),
        /* 13 */ array(3, 6, 17, 25, 29, 35, 38, 39, 40, 42, 43, 44, ),
        /* 14 */ array(3, 6, 17, 25, 29, 35, 38, 39, 40, 42, 43, 44, ),
        /* 15 */ array(3, 6, 17, 25, 29, 35, 38, 39, 40, 42, 43, 44, ),
        /* 16 */ array(3, 6, 17, 25, 29, 35, 38, 39, 40, 42, 43, 44, ),
        /* 17 */ array(3, 6, 17, 25, 29, 35, 38, 39, 40, 42, 43, 44, ),
        /* 18 */ array(3, 6, 17, 25, 29, 35, 38, 39, 40, 42, 43, 44, ),
        /* 19 */ array(3, 6, 17, 25, 29, 35, 38, 39, 40, 42, 43, 44, ),
        /* 20 */ array(3, 6, 17, 25, 29, 35, 38, 39, 40, 42, 43, 44, ),
        /* 21 */ array(3, 6, 17, 25, 29, 35, 38, 39, 40, 42, 43, 44, ),
        /* 22 */ array(3, 6, 17, 25, 29, 35, 38, 39, 40, 42, 43, 44, ),
        /* 23 */ array(3, 6, 17, 25, 29, 35, 38, 39, 40, 42, 43, 44, ),
        /* 24 */ array(23, 25, 26, 27, 28, 32, 34, 35, 36, 39, ),
        /* 25 */ array(23, 25, 26, 27, 28, 32, 34, 35, 36, 39, ),
        /* 26 */ array(23, 25, 26, 27, 28, 32, 34, 35, 36, 39, ),
        /* 27 */ array(23, 25, 26, 27, 28, 32, 34, 35, 36, 39, ),
        /* 28 */ array(23, 25, 26, 27, 28, 34, 35, 36, 39, ),
        /* 29 */ array(3, 35, 39, 40, ),
        /* 30 */ array(3, 17, 25, 35, 40, 42, 43, 44, ),
        /* 31 */ array(3, 17, 25, 35, 40, 42, 43, 44, ),
        /* 32 */ array(35, ),
        /* 33 */ array(35, ),
        /* 34 */ array(39, ),
        /* 35 */ array(39, ),
        /* 36 */ array(39, ),
        /* 37 */ array(36, ),
        /* 38 */ array(),
        /* 39 */ array(),
        /* 40 */ array(),
        /* 41 */ array(),
        /* 42 */ array(),
        /* 43 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 41, ),
        /* 44 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 31, ),
        /* 45 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 31, ),
        /* 46 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 31, ),
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
        /* 59 */ array(21, 22, ),
        /* 60 */ array(31, 33, ),
        /* 61 */ array(1, 31, ),
        /* 62 */ array(2, 40, ),
        /* 63 */ array(1, 41, ),
        /* 64 */ array(1, 31, ),
        /* 65 */ array(1, 31, ),
        /* 66 */ array(1, 45, ),
        /* 67 */ array(9, 29, ),
        /* 68 */ array(29, ),
        /* 69 */ array(35, ),
        /* 70 */ array(29, ),
        /* 71 */ array(31, ),
        /* 72 */ array(29, ),
        /* 73 */ array(24, ),
        /* 74 */ array(29, ),
        /* 75 */ array(29, ),
        /* 76 */ array(29, ),
        /* 77 */ array(29, ),
        /* 78 */ array(46, ),
        /* 79 */ array(44, ),
        /* 80 */ array(30, ),
        /* 81 */ array(29, ),
        /* 82 */ array(40, ),
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
);
    static public $yy_default = array(
 /*     0 */   121,  158,  158,  177,  158,  158,  178,  178,  178,  178,
 /*    10 */   178,  178,  178,  178,  178,  178,  178,  178,  178,  178,
 /*    20 */   178,  178,  178,  178,  178,  178,  178,  178,  119,  178,
 /*    30 */   174,  174,  178,  178,  178,  178,  178,  121,  121,  121,
 /*    40 */   121,  121,  121,  178,  178,  178,  178,  127,  176,  157,
 /*    50 */   175,  134,  173,  144,  143,  147,  145,  151,  146,  148,
 /*    60 */   178,  178,  159,  178,  178,  178,  178,  178,  178,  178,
 /*    70 */   178,  178,  130,  123,  178,  178,  163,  152,  178,  178,
 /*    80 */   178,  178,  160,  165,  164,  155,  141,  131,  124,  132,
 /*    90 */   161,  122,  153,  150,  120,  154,  166,  140,  142,  129,
 /*   100 */   167,  172,  137,  133,  162,  156,  149,  136,  128,  171,
 /*   110 */   170,  168,  126,  125,  138,  169,  163,  135,  139,
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
    const YYNOCODE = 65;
    const YYSTACKDEPTH = 100;
    const YYNSTATE = 119;
    const YYNRULE = 59;
    const YYERRORSYMBOL = 47;
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
  'T_FOREACH',     'T_LPARENT',     'T_AS',          'T_RPARENT',   
  'T_END',         'T_DOUBLE_ARROW',  'T_FUNCTION',    'T_ALPHA',     
  'T_IF',          'T_ELSE',        'T_AT',          'T_DOLLAR',    
  'T_SUBSCR_OPEN',  'T_SUBSCR_CLOSE',  'T_TRUE',        'T_FALSE',     
  'T_NUMBER',      'T_CURLY_CLOSE',  'T_COLON',       'error',       
  'start',         'body',          'line',          'code',        
  'expr',          'foreach_source',  'variable',      'fnc_call',    
  'json',          'args',          'if',            'else_if',     
  'term',          'var',           'json_obj',      'json_arr',    
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
 /*   9 */ "code ::= T_FOREACH T_LPARENT foreach_source T_AS variable T_RPARENT body T_END",
 /*  10 */ "code ::= T_FOREACH T_LPARENT foreach_source T_AS variable T_DOUBLE_ARROW variable T_RPARENT body T_END",
 /*  11 */ "foreach_source ::= variable",
 /*  12 */ "foreach_source ::= fnc_call",
 /*  13 */ "foreach_source ::= json",
 /*  14 */ "code ::= T_FUNCTION T_ALPHA T_LPARENT args T_RPARENT body T_END",
 /*  15 */ "code ::= variable T_ASSIGN expr",
 /*  16 */ "code ::= fnc_call",
 /*  17 */ "fnc_call ::= T_ALPHA T_LPARENT args T_RPARENT",
 /*  18 */ "fnc_call ::= variable T_LPARENT args T_RPARENT",
 /*  19 */ "code ::= if",
 /*  20 */ "if ::= T_IF T_LPARENT expr T_RPARENT body else_if",
 /*  21 */ "else_if ::= T_ELSE T_IF T_LPARENT expr T_RPARENT body else_if",
 /*  22 */ "else_if ::= T_ELSE body T_END",
 /*  23 */ "else_if ::= T_END",
 /*  24 */ "expr ::= T_NOT expr",
 /*  25 */ "expr ::= expr T_AND expr",
 /*  26 */ "expr ::= expr T_OR expr",
 /*  27 */ "expr ::= expr T_PLUS|T_MINUS expr",
 /*  28 */ "expr ::= expr T_EQ|T_NE|T_GT|T_GE|T_LT|T_LE|T_IN expr",
 /*  29 */ "expr ::= expr T_TIMES|T_DIV|T_MOD expr",
 /*  30 */ "expr ::= expr T_BITWISE|T_PIPE expr",
 /*  31 */ "expr ::= T_LPARENT expr T_RPARENT",
 /*  32 */ "expr ::= expr T_DOT expr",
 /*  33 */ "expr ::= variable",
 /*  34 */ "expr ::= term",
 /*  35 */ "expr ::= T_AT variable",
 /*  36 */ "expr ::= fnc_call",
 /*  37 */ "args ::= args T_COMMA args",
 /*  38 */ "args ::= expr",
 /*  39 */ "args ::=",
 /*  40 */ "variable ::= T_DOLLAR var",
 /*  41 */ "var ::= var T_OBJ var",
 /*  42 */ "var ::= var T_SUBSCR_OPEN expr T_SUBSCR_CLOSE",
 /*  43 */ "var ::= T_ALPHA",
 /*  44 */ "term ::= T_ALPHA",
 /*  45 */ "term ::= T_TRUE",
 /*  46 */ "term ::= T_FALSE",
 /*  47 */ "term ::= T_STRING",
 /*  48 */ "term ::= T_NUMBER",
 /*  49 */ "term ::= T_MINUS T_NUMBER",
 /*  50 */ "term ::= json",
 /*  51 */ "json ::= T_CURLY_OPEN json_obj T_CURLY_CLOSE",
 /*  52 */ "json ::= T_SUBSCR_OPEN json_arr T_SUBSCR_CLOSE",
 /*  53 */ "json_obj ::= json_obj T_COMMA json_obj",
 /*  54 */ "json_obj ::= term T_COLON expr",
 /*  55 */ "json_obj ::=",
 /*  56 */ "json_arr ::= json_arr T_COMMA expr",
 /*  57 */ "json_arr ::= expr",
 /*  58 */ "json_arr ::=",
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
  array( 'lhs' => 48, 'rhs' => 1 ),
  array( 'lhs' => 49, 'rhs' => 2 ),
  array( 'lhs' => 49, 'rhs' => 0 ),
  array( 'lhs' => 50, 'rhs' => 1 ),
  array( 'lhs' => 50, 'rhs' => 1 ),
  array( 'lhs' => 50, 'rhs' => 2 ),
  array( 'lhs' => 50, 'rhs' => 1 ),
  array( 'lhs' => 50, 'rhs' => 1 ),
  array( 'lhs' => 51, 'rhs' => 2 ),
  array( 'lhs' => 51, 'rhs' => 8 ),
  array( 'lhs' => 51, 'rhs' => 10 ),
  array( 'lhs' => 53, 'rhs' => 1 ),
  array( 'lhs' => 53, 'rhs' => 1 ),
  array( 'lhs' => 53, 'rhs' => 1 ),
  array( 'lhs' => 51, 'rhs' => 7 ),
  array( 'lhs' => 51, 'rhs' => 3 ),
  array( 'lhs' => 51, 'rhs' => 1 ),
  array( 'lhs' => 55, 'rhs' => 4 ),
  array( 'lhs' => 55, 'rhs' => 4 ),
  array( 'lhs' => 51, 'rhs' => 1 ),
  array( 'lhs' => 58, 'rhs' => 6 ),
  array( 'lhs' => 59, 'rhs' => 7 ),
  array( 'lhs' => 59, 'rhs' => 3 ),
  array( 'lhs' => 59, 'rhs' => 1 ),
  array( 'lhs' => 52, 'rhs' => 2 ),
  array( 'lhs' => 52, 'rhs' => 3 ),
  array( 'lhs' => 52, 'rhs' => 3 ),
  array( 'lhs' => 52, 'rhs' => 3 ),
  array( 'lhs' => 52, 'rhs' => 3 ),
  array( 'lhs' => 52, 'rhs' => 3 ),
  array( 'lhs' => 52, 'rhs' => 3 ),
  array( 'lhs' => 52, 'rhs' => 3 ),
  array( 'lhs' => 52, 'rhs' => 3 ),
  array( 'lhs' => 52, 'rhs' => 1 ),
  array( 'lhs' => 52, 'rhs' => 1 ),
  array( 'lhs' => 52, 'rhs' => 2 ),
  array( 'lhs' => 52, 'rhs' => 1 ),
  array( 'lhs' => 57, 'rhs' => 3 ),
  array( 'lhs' => 57, 'rhs' => 1 ),
  array( 'lhs' => 57, 'rhs' => 0 ),
  array( 'lhs' => 54, 'rhs' => 2 ),
  array( 'lhs' => 61, 'rhs' => 3 ),
  array( 'lhs' => 61, 'rhs' => 4 ),
  array( 'lhs' => 61, 'rhs' => 1 ),
  array( 'lhs' => 60, 'rhs' => 1 ),
  array( 'lhs' => 60, 'rhs' => 1 ),
  array( 'lhs' => 60, 'rhs' => 1 ),
  array( 'lhs' => 60, 'rhs' => 1 ),
  array( 'lhs' => 60, 'rhs' => 1 ),
  array( 'lhs' => 60, 'rhs' => 2 ),
  array( 'lhs' => 60, 'rhs' => 1 ),
  array( 'lhs' => 56, 'rhs' => 3 ),
  array( 'lhs' => 56, 'rhs' => 3 ),
  array( 'lhs' => 62, 'rhs' => 3 ),
  array( 'lhs' => 62, 'rhs' => 3 ),
  array( 'lhs' => 62, 'rhs' => 0 ),
  array( 'lhs' => 63, 'rhs' => 3 ),
  array( 'lhs' => 63, 'rhs' => 1 ),
  array( 'lhs' => 63, 'rhs' => 0 ),
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
        39 => 2,
        55 => 2,
        58 => 2,
        3 => 3,
        4 => 4,
        11 => 4,
        12 => 4,
        16 => 4,
        19 => 4,
        33 => 4,
        36 => 4,
        47 => 4,
        50 => 4,
        5 => 5,
        52 => 5,
        6 => 6,
        7 => 7,
        8 => 8,
        9 => 9,
        10 => 10,
        13 => 13,
        14 => 14,
        15 => 15,
        17 => 17,
        18 => 17,
        20 => 20,
        21 => 21,
        22 => 22,
        23 => 23,
        24 => 24,
        25 => 25,
        26 => 25,
        27 => 27,
        29 => 27,
        30 => 27,
        28 => 28,
        31 => 31,
        32 => 32,
        34 => 34,
        35 => 35,
        37 => 37,
        41 => 37,
        53 => 37,
        38 => 38,
        57 => 38,
        40 => 40,
        42 => 42,
        43 => 43,
        44 => 44,
        45 => 45,
        46 => 46,
        48 => 48,
        49 => 49,
        51 => 51,
        54 => 54,
        56 => 56,
    );
    /* Beginning here are the reduction cases.  A typical example
    ** follows:
    **  #line <lineno> <grammarfile>
    **   function yy_r0($yymsp){ ... }           // User supplied code
    **  #line <lineno> <thisfile>
    */
#line 87 "lib/Artifex/Parser.y"
    function yy_r0(){ $this->body = $this->yystack[$this->yyidx + 0]->minor;     }
#line 1224 "lib/Artifex/Parser.php"
#line 89 "lib/Artifex/Parser.y"
    function yy_r1(){
    $last = end($this->yystack[$this->yyidx + -1]->minor);
    if ($last) {
        $this->setPrevNext($last, $this->yystack[$this->yyidx + 0]->minor);
    }
    $this->yystack[$this->yyidx + -1]->minor[] = $this->yystack[$this->yyidx + 0]->minor; 
    $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor; 
    }
#line 1234 "lib/Artifex/Parser.php"
#line 97 "lib/Artifex/Parser.y"
    function yy_r2(){ $this->_retvalue = array();     }
#line 1237 "lib/Artifex/Parser.php"
#line 99 "lib/Artifex/Parser.y"
    function yy_r3(){ $this->_retvalue = new RawString($this->yystack[$this->yyidx + 0]->minor);     }
#line 1240 "lib/Artifex/Parser.php"
#line 100 "lib/Artifex/Parser.y"
    function yy_r4(){ $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;     }
#line 1243 "lib/Artifex/Parser.php"
#line 101 "lib/Artifex/Parser.y"
    function yy_r5(){ $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor;     }
#line 1246 "lib/Artifex/Parser.php"
#line 102 "lib/Artifex/Parser.y"
    function yy_r6(){ $this->_retvalue = new RawString($this->yystack[$this->yyidx + 0]->minor, true);     }
#line 1249 "lib/Artifex/Parser.php"
#line 103 "lib/Artifex/Parser.y"
    function yy_r7(){ $this->_retvalue = new Whitespace($this->yystack[$this->yyidx + 0]->minor);     }
#line 1252 "lib/Artifex/Parser.php"
#line 105 "lib/Artifex/Parser.y"
    function yy_r8(){ $this->_retvalue = new Expr_Return($this->yystack[$this->yyidx + 0]->minor);     }
#line 1255 "lib/Artifex/Parser.php"
#line 108 "lib/Artifex/Parser.y"
    function yy_r9(){ 
    $this->_retvalue = new Expr_Foreach($this->yystack[$this->yyidx + -5]->minor, $this->yystack[$this->yyidx + -3]->minor, NULL, $this->yystack[$this->yyidx + -1]->minor); 
    $this->_retvalue->setChild($this->yystack[$this->yyidx + -1]->minor);
    }
#line 1261 "lib/Artifex/Parser.php"
#line 113 "lib/Artifex/Parser.y"
    function yy_r10(){
    $this->_retvalue = new Expr_Foreach($this->yystack[$this->yyidx + -7]->minor, $this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -5]->minor, $this->yystack[$this->yyidx + -1]->minor);
    $this->_retvalue->setChild($this->yystack[$this->yyidx + -1]->minor);
    }
#line 1267 "lib/Artifex/Parser.php"
#line 120 "lib/Artifex/Parser.y"
    function yy_r13(){ $this->_retvalue = new Term($this->yystack[$this->yyidx + 0]->minor);     }
#line 1270 "lib/Artifex/Parser.php"
#line 124 "lib/Artifex/Parser.y"
    function yy_r14(){
    $this->_retvalue = new Expr_Function($this->yystack[$this->yyidx + -5]->minor, $this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -1]->minor);
    $this->_retvalue->setChild($this->yystack[$this->yyidx + -1]->minor);
    }
#line 1276 "lib/Artifex/Parser.php"
#line 131 "lib/Artifex/Parser.y"
    function yy_r15(){ 
    $this->_retvalue = new Assign($this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor); 
    $this->setPrevNext($this->_retvalue, $this->yystack[$this->yyidx + -2]->minor);
    $this->setPrevNext($this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);
    }
#line 1283 "lib/Artifex/Parser.php"
#line 140 "lib/Artifex/Parser.y"
    function yy_r17(){ 
    $this->_retvalue = new Exec($this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -1]->minor);
    }
#line 1288 "lib/Artifex/Parser.php"
#line 151 "lib/Artifex/Parser.y"
    function yy_r20(){
    $this->_retvalue = new Expr_If($this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + 0]->minor);
    $this->_retvalue->setChild($this->yystack[$this->yyidx + -1]->minor);
    if (is_array($this->yystack[$this->yyidx + 0]->minor)) {
        $this->_retvalue->setChild($this->yystack[$this->yyidx + 0]->minor);
    } else if (is_object($this->yystack[$this->yyidx + 0]->minor)) {
        $this->setPrevNext($this->_retvalue, $this->yystack[$this->yyidx + 0]->minor);
    }
    }
#line 1299 "lib/Artifex/Parser.php"
#line 161 "lib/Artifex/Parser.y"
    function yy_r21(){ 
    $this->_retvalue = new Expr_If($this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + 0]->minor); 
    $this->_retvalue->setChild($this->yystack[$this->yyidx + -1]->minor);
    if (is_array($this->yystack[$this->yyidx + 0]->minor)) {
        $this->_retvalue->setChild($this->yystack[$this->yyidx + 0]->minor);
    } else if (is_object($this->yystack[$this->yyidx + 0]->minor)) {
        $this->_retvalue->setNext($this->yystack[$this->yyidx + 0]->minor);
        $this->yystack[$this->yyidx + 0]->minor->setPrev($this->_retvalue);
    }
    }
#line 1311 "lib/Artifex/Parser.php"
#line 171 "lib/Artifex/Parser.y"
    function yy_r22(){ 
    $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor; 
    }
#line 1316 "lib/Artifex/Parser.php"
#line 174 "lib/Artifex/Parser.y"
    function yy_r23(){ $this->_retvalue = NULL;     }
#line 1319 "lib/Artifex/Parser.php"
#line 178 "lib/Artifex/Parser.y"
    function yy_r24(){ $this->_retvalue = new Expr('not', $this->yystack[$this->yyidx + 0]->minor);     }
#line 1322 "lib/Artifex/Parser.php"
#line 179 "lib/Artifex/Parser.y"
    function yy_r25(){ $this->_retvalue = new Expr(strtolower(@$this->yystack[$this->yyidx + -1]->minor), $this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1325 "lib/Artifex/Parser.php"
#line 181 "lib/Artifex/Parser.y"
    function yy_r27(){ $this->_retvalue = new Expr(@$this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1328 "lib/Artifex/Parser.php"
#line 182 "lib/Artifex/Parser.y"
    function yy_r28(){ $this->_retvalue = new Expr(trim(@$this->yystack[$this->yyidx + -1]->minor), $this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1331 "lib/Artifex/Parser.php"
#line 185 "lib/Artifex/Parser.y"
    function yy_r31(){ $this->_retvalue = new Expr($this->yystack[$this->yyidx + -1]->minor);     }
#line 1334 "lib/Artifex/Parser.php"
#line 186 "lib/Artifex/Parser.y"
    function yy_r32(){ $this->_retvalue = new Concat($this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1337 "lib/Artifex/Parser.php"
#line 188 "lib/Artifex/Parser.y"
    function yy_r34(){  $this->_retvalue = new Term($this->yystack[$this->yyidx + 0]->minor);     }
#line 1340 "lib/Artifex/Parser.php"
#line 189 "lib/Artifex/Parser.y"
    function yy_r35(){ 
    $this->_retvalue = new Exec('var_export', array($this->yystack[$this->yyidx + 0]->minor, new Term(true))); 
    }
#line 1345 "lib/Artifex/Parser.php"
#line 196 "lib/Artifex/Parser.y"
    function yy_r37(){ $this->_retvalue = array_merge($this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1348 "lib/Artifex/Parser.php"
#line 197 "lib/Artifex/Parser.y"
    function yy_r38(){ $this->_retvalue = array($this->yystack[$this->yyidx + 0]->minor);     }
#line 1351 "lib/Artifex/Parser.php"
#line 202 "lib/Artifex/Parser.y"
    function yy_r40(){ $this->_retvalue = new Variable($this->yystack[$this->yyidx + 0]->minor);     }
#line 1354 "lib/Artifex/Parser.php"
#line 205 "lib/Artifex/Parser.y"
    function yy_r42(){ $this->_retvalue = $this->yystack[$this->yyidx + -3]->minor ; $this->_retvalue[] = $this->yystack[$this->yyidx + -1]->minor;     }
#line 1357 "lib/Artifex/Parser.php"
#line 206 "lib/Artifex/Parser.y"
    function yy_r43(){ $this->_retvalue = array($this->yystack[$this->yyidx + 0]->minor);    }
#line 1360 "lib/Artifex/Parser.php"
#line 210 "lib/Artifex/Parser.y"
    function yy_r44(){ $this->_retvalue = trim($this->yystack[$this->yyidx + 0]->minor);     }
#line 1363 "lib/Artifex/Parser.php"
#line 211 "lib/Artifex/Parser.y"
    function yy_r45(){ $this->_retvalue = TRUE;     }
#line 1366 "lib/Artifex/Parser.php"
#line 212 "lib/Artifex/Parser.y"
    function yy_r46(){ $this->_retvalue = FALSE;     }
#line 1369 "lib/Artifex/Parser.php"
#line 214 "lib/Artifex/Parser.y"
    function yy_r48(){ $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor + 0;     }
#line 1372 "lib/Artifex/Parser.php"
#line 215 "lib/Artifex/Parser.y"
    function yy_r49(){ $this->_retvalue = -1 * ($this->yystack[$this->yyidx + 0]->minor + 0);     }
#line 1375 "lib/Artifex/Parser.php"
#line 220 "lib/Artifex/Parser.y"
    function yy_r51(){ $this->_retvalue  = $this->yystack[$this->yyidx + -1]->minor;     }
#line 1378 "lib/Artifex/Parser.php"
#line 224 "lib/Artifex/Parser.y"
    function yy_r54(){ $this->_retvalue = array($this->yystack[$this->yyidx + -2]->minor => $this->yystack[$this->yyidx + 0]->minor);     }
#line 1381 "lib/Artifex/Parser.php"
#line 227 "lib/Artifex/Parser.y"
    function yy_r56(){ $this->_retvalue = $this->yystack[$this->yyidx + -2]->minor; $this->_retvalue[] = $this->yystack[$this->yyidx + 0]->minor;     }
#line 1384 "lib/Artifex/Parser.php"

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
#line 63 "lib/Artifex/Parser.y"

    $expect = array();
    foreach ($this->yy_get_expected_tokens($yymajor) as $token) {
        $expect[] = self::$yyTokenName[$token];
    }
    throw new Exception('Unexpected ' . $this->tokenName($yymajor) .  ' in line ' . $this->line
        . ' (' . $TOKEN . ')  on line ' . $this->line
        . '. Expected: ' . print_r($expect, true));
#line 1506 "lib/Artifex/Parser.php"
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
