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
#line 2 "Artifex/Parser.y"

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
    \Artifex\Runtime\Variable;
#line 146 "Artifex/Parser.php"

// declare_class is output here
#line 49 "Artifex/Parser.y"
class Artifex_Parser #line 151 "Artifex/Parser.php"
{
/* First off, code is included which follows the "include_class" declaration
** in the input file. */
#line 50 "Artifex/Parser.y"

    public $body = array();
#line 159 "Artifex/Parser.php"

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
    const T_START                        = 24;
    const T_FOREACH                      = 25;
    const T_LPARENT                      = 26;
    const T_AS                           = 27;
    const T_RPARENT                      = 28;
    const T_END                          = 29;
    const T_DOUBLE_ARROW                 = 30;
    const T_IF                           = 31;
    const T_ELSE                         = 32;
    const T_AT                           = 33;
    const T_ALPHA                        = 34;
    const T_DOLLAR                       = 35;
    const T_CURLY_CLOSE                  = 36;
    const T_TRUE                         = 37;
    const T_FALSE                        = 38;
    const T_STRING                       = 39;
    const T_NUMBER                       = 40;
    const T_SUBSCR_OPEN                  = 41;
    const T_SUBSCR_CLOSE                 = 42;
    const T_COLON                        = 43;
    const YY_NO_ACTION = 157;
    const YY_ACCEPT_ACTION = 156;
    const YY_ERROR_ACTION = 155;

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
    const YY_SZ_ACTTAB = 402;
static public $yy_action = array(
 /*     0 */    15,    6,    1,    9,    9,   35,    9,    9,    9,    9,
 /*    10 */     9,   12,    5,    5,   10,   10,   10,   13,   13,   63,
 /*    20 */    73,   74,   89,   26,   66,  156,   25,   19,   72,   80,
 /*    30 */    71,   31,   84,   68,   32,   13,   13,   15,    6,   29,
 /*    40 */     9,    9,   28,    9,    9,    9,    9,    9,   12,    5,
 /*    50 */     5,   10,   10,   10,   13,   13,   15,    6,    3,    9,
 /*    60 */     9,   37,    9,    9,    9,    9,    9,   12,    5,    5,
 /*    70 */    10,   10,   10,   13,   13,   15,    6,   98,    9,    9,
 /*    80 */    38,    9,    9,    9,    9,    9,   12,    5,    5,   10,
 /*    90 */    10,   10,   13,   13,   15,    6,    4,    9,    9,   86,
 /*   100 */     9,    9,    9,    9,    9,   12,    5,    5,   10,   10,
 /*   110 */    10,   13,   13,    6,   16,    9,    9,   17,    9,    9,
 /*   120 */     9,    9,    9,   12,    5,    5,   10,   10,   10,   13,
 /*   130 */    13,    9,    9,    7,    9,    9,    9,    9,    9,   12,
 /*   140 */     5,    5,   10,   10,   10,   13,   13,   27,   33,   14,
 /*   150 */     8,    5,    5,   10,   10,   10,   13,   13,   94,  103,
 /*   160 */    89,   26,   66,   32,   65,   27,   91,   59,   71,   36,
 /*   170 */    11,   68,   32,   39,   18,   34,   27,   30,   70,   32,
 /*   180 */    20,   93,  100,  101,  102,    2,   89,   26,   66,   23,
 /*   190 */    83,    4,   75,   21,   71,   24,   99,   68,   32,   93,
 /*   200 */   100,  101,  102,    2,  103,   89,   26,   66,   32,   65,
 /*   210 */     1,   77,   95,   71,    2,   64,   68,   32,   89,   26,
 /*   220 */    66,   67,  103,   45,   85,   57,   71,   82,   61,   68,
 /*   230 */    32,   69,   67,  103,   47,   85,   96,   97,   82,   81,
 /*   240 */    22,   55,   56,  112,  112,  112,   67,  103,   45,   85,
 /*   250 */   112,  112,   82,   58,  112,   67,  103,   45,   85,  112,
 /*   260 */   112,   82,   92,   67,  103,   49,   85,  112,  112,   82,
 /*   270 */    67,  103,   41,   85,  112,  112,   82,   67,  103,   42,
 /*   280 */    85,  112,  112,   82,   10,   10,   10,   13,   13,  112,
 /*   290 */   112,  112,   67,  103,   87,   85,  112,  112,   82,   67,
 /*   300 */   103,   40,   85,  112,  112,   82,   67,  103,   54,   85,
 /*   310 */   112,  112,   82,   67,  103,   43,   85,  112,  112,   82,
 /*   320 */    67,  103,   62,   85,  112,   88,   82,   60,  112,  112,
 /*   330 */   104,   76,   78,   67,  103,   50,   85,  112,  112,   82,
 /*   340 */    88,  112,   60,  112,  112,  104,   76,   79,   67,  103,
 /*   350 */    53,   85,  112,  112,   82,   67,  103,   46,   85,  112,
 /*   360 */   112,   82,   67,  103,   44,   85,  112,  112,   82,   67,
 /*   370 */   103,   51,   85,  112,  112,   82,   67,  103,   48,   85,
 /*   380 */   112,  112,   82,   67,  103,   52,   85,  112,   88,   82,
 /*   390 */    60,  112,  112,  104,   76,   90,  112,   60,  112,  112,
 /*   400 */   104,   76,
    );
    static public $yy_lookahead = array(
 /*     0 */     4,    5,    1,    7,    8,   27,   10,   11,   12,   13,
 /*    10 */    14,   15,   16,   17,   18,   19,   20,   21,   22,   48,
 /*    20 */    49,   50,   23,   24,   25,   45,   46,   43,   29,   28,
 /*    30 */    31,   32,   36,   34,   35,   21,   22,    4,    5,   26,
 /*    40 */     7,    8,    1,   10,   11,   12,   13,   14,   15,   16,
 /*    50 */    17,   18,   19,   20,   21,   22,    4,    5,   26,    7,
 /*    60 */     8,   28,   10,   11,   12,   13,   14,   15,   16,   17,
 /*    70 */    18,   19,   20,   21,   22,    4,    5,   36,    7,    8,
 /*    80 */    28,   10,   11,   12,   13,   14,   15,   16,   17,   18,
 /*    90 */    19,   20,   21,   22,    4,    5,   26,    7,    8,   28,
 /*   100 */    10,   11,   12,   13,   14,   15,   16,   17,   18,   19,
 /*   110 */    20,   21,   22,    5,   26,    7,    8,    1,   10,   11,
 /*   120 */    12,   13,   14,   15,   16,   17,   18,   19,   20,   21,
 /*   130 */    22,    7,    8,   26,   10,   11,   12,   13,   14,   15,
 /*   140 */    16,   17,   18,   19,   20,   21,   22,    3,    2,    3,
 /*   150 */     6,   16,   17,   18,   19,   20,   21,   22,   42,   50,
 /*   160 */    23,   24,   25,   35,   55,    3,   29,   58,   31,   28,
 /*   170 */    26,   34,   35,   28,    9,   30,    3,   33,   34,   35,
 /*   180 */    46,   37,   38,   39,   40,   41,   23,   24,   25,   46,
 /*   190 */    57,   26,   29,   46,   31,   46,   34,   34,   35,   37,
 /*   200 */    38,   39,   40,   41,   50,   23,   24,   25,   35,   55,
 /*   210 */     1,   29,   58,   31,   41,   49,   34,   35,   23,   24,
 /*   220 */    25,   49,   50,   51,   52,   49,   31,   55,   56,   34,
 /*   230 */    35,   31,   49,   50,   51,   52,   34,   28,   55,   49,
 /*   240 */    46,   57,   59,   60,   60,   60,   49,   50,   51,   52,
 /*   250 */    60,   60,   55,   56,   60,   49,   50,   51,   52,   60,
 /*   260 */    60,   55,   56,   49,   50,   51,   52,   60,   60,   55,
 /*   270 */    49,   50,   51,   52,   60,   60,   55,   49,   50,   51,
 /*   280 */    52,   60,   60,   55,   18,   19,   20,   21,   22,   60,
 /*   290 */    60,   60,   49,   50,   51,   52,   60,   60,   55,   49,
 /*   300 */    50,   51,   52,   60,   60,   55,   49,   50,   51,   52,
 /*   310 */    60,   60,   55,   49,   50,   51,   52,   60,   60,   55,
 /*   320 */    49,   50,   51,   52,   60,   47,   55,   49,   60,   60,
 /*   330 */    52,   53,   54,   49,   50,   51,   52,   60,   60,   55,
 /*   340 */    47,   60,   49,   60,   60,   52,   53,   54,   49,   50,
 /*   350 */    51,   52,   60,   60,   55,   49,   50,   51,   52,   60,
 /*   360 */    60,   55,   49,   50,   51,   52,   60,   60,   55,   49,
 /*   370 */    50,   51,   52,   60,   60,   55,   49,   50,   51,   52,
 /*   380 */    60,   60,   55,   49,   50,   51,   52,   60,   47,   55,
 /*   390 */    49,   60,   60,   52,   53,   47,   60,   49,   60,   60,
 /*   400 */    52,   53,
);
    const YY_SHIFT_USE_DFLT = -23;
    const YY_SHIFT_MAX = 71;
    static public $yy_shift_ofst = array(
 /*     0 */   -23,  144,  144,  144,  144,  144,  144,  144,  144,  144,
 /*    10 */   144,  144,  144,  144,  144,  144,  144,  144,  144,  144,
 /*    20 */    -1,   -1,  182,  137,  163,  195,  195,  162,  162,  173,
 /*    30 */   128,  200,  202,  202,  128,  128,  -23,  -23,  -23,  -23,
 /*    40 */    -4,   71,   52,   33,   90,   90,   90,   90,   90,  108,
 /*    50 */   124,  124,  124,  135,  266,  146,  116,  145,  209,   41,
 /*    60 */   165,    1,   14,  -22,  141,  -16,   13,   70,   32,   88,
 /*    70 */    32,  107,
);
    const YY_REDUCE_USE_DFLT = -30;
    const YY_REDUCE_MAX = 39;
    static public $yy_reduce_ofst = array(
 /*     0 */   -20,  206,  183,  172,  197,  257,  284,  228,  334,  320,
 /*    10 */   271,  221,  299,  243,  250,  214,  264,  327,  306,  313,
 /*    20 */   293,  278,  341,  341,  341,  341,  348,  109,  154,  -29,
 /*    30 */   190,  194,  184,  133,  166,  176,  149,  147,  134,  143,
);
    static public $yyExpectedTokens = array(
        /* 0 */ array(),
        /* 1 */ array(3, 6, 26, 33, 34, 35, 37, 38, 39, 40, 41, ),
        /* 2 */ array(3, 6, 26, 33, 34, 35, 37, 38, 39, 40, 41, ),
        /* 3 */ array(3, 6, 26, 33, 34, 35, 37, 38, 39, 40, 41, ),
        /* 4 */ array(3, 6, 26, 33, 34, 35, 37, 38, 39, 40, 41, ),
        /* 5 */ array(3, 6, 26, 33, 34, 35, 37, 38, 39, 40, 41, ),
        /* 6 */ array(3, 6, 26, 33, 34, 35, 37, 38, 39, 40, 41, ),
        /* 7 */ array(3, 6, 26, 33, 34, 35, 37, 38, 39, 40, 41, ),
        /* 8 */ array(3, 6, 26, 33, 34, 35, 37, 38, 39, 40, 41, ),
        /* 9 */ array(3, 6, 26, 33, 34, 35, 37, 38, 39, 40, 41, ),
        /* 10 */ array(3, 6, 26, 33, 34, 35, 37, 38, 39, 40, 41, ),
        /* 11 */ array(3, 6, 26, 33, 34, 35, 37, 38, 39, 40, 41, ),
        /* 12 */ array(3, 6, 26, 33, 34, 35, 37, 38, 39, 40, 41, ),
        /* 13 */ array(3, 6, 26, 33, 34, 35, 37, 38, 39, 40, 41, ),
        /* 14 */ array(3, 6, 26, 33, 34, 35, 37, 38, 39, 40, 41, ),
        /* 15 */ array(3, 6, 26, 33, 34, 35, 37, 38, 39, 40, 41, ),
        /* 16 */ array(3, 6, 26, 33, 34, 35, 37, 38, 39, 40, 41, ),
        /* 17 */ array(3, 6, 26, 33, 34, 35, 37, 38, 39, 40, 41, ),
        /* 18 */ array(3, 6, 26, 33, 34, 35, 37, 38, 39, 40, 41, ),
        /* 19 */ array(3, 6, 26, 33, 34, 35, 37, 38, 39, 40, 41, ),
        /* 20 */ array(23, 24, 25, 29, 31, 32, 34, 35, ),
        /* 21 */ array(23, 24, 25, 29, 31, 32, 34, 35, ),
        /* 22 */ array(23, 24, 25, 29, 31, 34, 35, ),
        /* 23 */ array(23, 24, 25, 29, 31, 34, 35, ),
        /* 24 */ array(23, 24, 25, 29, 31, 34, 35, ),
        /* 25 */ array(23, 24, 25, 31, 34, 35, ),
        /* 26 */ array(23, 24, 25, 31, 34, 35, ),
        /* 27 */ array(3, 34, 37, 38, 39, 40, 41, ),
        /* 28 */ array(3, 34, 37, 38, 39, 40, 41, ),
        /* 29 */ array(3, 35, 41, ),
        /* 30 */ array(35, ),
        /* 31 */ array(31, ),
        /* 32 */ array(34, ),
        /* 33 */ array(34, ),
        /* 34 */ array(35, ),
        /* 35 */ array(35, ),
        /* 36 */ array(),
        /* 37 */ array(),
        /* 38 */ array(),
        /* 39 */ array(),
        /* 40 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 36, ),
        /* 41 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 28, ),
        /* 42 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 28, ),
        /* 43 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 28, ),
        /* 44 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, ),
        /* 45 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, ),
        /* 46 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, ),
        /* 47 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, ),
        /* 48 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, ),
        /* 49 */ array(5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, ),
        /* 50 */ array(7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, ),
        /* 51 */ array(7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, ),
        /* 52 */ array(7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, ),
        /* 53 */ array(16, 17, 18, 19, 20, 21, 22, ),
        /* 54 */ array(18, 19, 20, 21, 22, ),
        /* 55 */ array(2, 3, ),
        /* 56 */ array(1, 42, ),
        /* 57 */ array(28, 30, ),
        /* 58 */ array(1, 28, ),
        /* 59 */ array(1, 36, ),
        /* 60 */ array(9, 26, ),
        /* 61 */ array(1, 28, ),
        /* 62 */ array(21, 22, ),
        /* 63 */ array(27, ),
        /* 64 */ array(28, ),
        /* 65 */ array(43, ),
        /* 66 */ array(26, ),
        /* 67 */ array(26, ),
        /* 68 */ array(26, ),
        /* 69 */ array(26, ),
        /* 70 */ array(26, ),
        /* 71 */ array(26, ),
        /* 72 */ array(),
        /* 73 */ array(),
        /* 74 */ array(),
        /* 75 */ array(),
        /* 76 */ array(),
        /* 77 */ array(),
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
);
    static public $yy_default = array(
 /*     0 */   107,  138,  155,  138,  138,  155,  155,  155,  155,  155,
 /*    10 */   155,  155,  155,  155,  155,  155,  155,  155,  155,  155,
 /*    20 */   155,  155,  155,  155,  155,  105,  155,  155,  155,  155,
 /*    30 */   155,  107,  155,  155,  155,  155,  107,  107,  107,  107,
 /*    40 */   155,  155,  155,  155,  152,  137,  114,  154,  153,  122,
 /*    50 */   123,  125,  121,  129,  124,  139,  155,  155,  155,  155,
 /*    60 */   155,  155,  126,  155,  155,  155,  155,  130,  155,  155,
 /*    70 */   143,  155,  120,  112,  113,  111,  116,  119,  118,  117,
 /*    80 */   134,  132,  131,  140,  141,  133,  128,  127,  106,  108,
 /*    90 */   109,  110,  136,  144,  150,  151,  142,  135,  149,  143,
 /*   100 */   145,  146,  147,  148,  115,
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
    const YYNOCODE = 61;
    const YYSTACKDEPTH = 100;
    const YYNSTATE = 105;
    const YYNRULE = 50;
    const YYERRORSYMBOL = 44;
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
  'T_START',       'T_FOREACH',     'T_LPARENT',     'T_AS',        
  'T_RPARENT',     'T_END',         'T_DOUBLE_ARROW',  'T_IF',        
  'T_ELSE',        'T_AT',          'T_ALPHA',       'T_DOLLAR',    
  'T_CURLY_CLOSE',  'T_TRUE',        'T_FALSE',       'T_STRING',    
  'T_NUMBER',      'T_SUBSCR_OPEN',  'T_SUBSCR_CLOSE',  'T_COLON',     
  'error',         'start',         'body',          'code',        
  'foreach_source',  'variable',      'json',          'expr',        
  'fnc_call',      'if',            'else_if',       'term',        
  'args',          'var',           'json_obj',      'json_arr',    
    );

    /**
     * For tracing reduce actions, the names of all rules are required.
     * @var array
     */
    static public $yyRuleName = array(
 /*   0 */ "start ::= body",
 /*   1 */ "body ::= body code",
 /*   2 */ "body ::=",
 /*   3 */ "code ::= T_RAW_STRING",
 /*   4 */ "code ::= T_START code",
 /*   5 */ "code ::= T_FOREACH T_LPARENT foreach_source T_AS variable T_RPARENT body T_END",
 /*   6 */ "code ::= T_FOREACH T_LPARENT foreach_source T_AS variable T_DOUBLE_ARROW variable T_RPARENT body T_END",
 /*   7 */ "foreach_source ::= variable",
 /*   8 */ "foreach_source ::= json",
 /*   9 */ "code ::= variable T_ASSIGN expr",
 /*  10 */ "code ::= fnc_call",
 /*  11 */ "code ::= if",
 /*  12 */ "if ::= T_IF T_LPARENT expr T_RPARENT body else_if",
 /*  13 */ "else_if ::= T_ELSE T_IF T_LPARENT expr T_RPARENT body else_if",
 /*  14 */ "else_if ::= T_ELSE body T_END",
 /*  15 */ "else_if ::= T_END",
 /*  16 */ "expr ::= T_NOT expr",
 /*  17 */ "expr ::= expr T_AND expr",
 /*  18 */ "expr ::= expr T_OR expr",
 /*  19 */ "expr ::= expr T_PLUS|T_MINUS expr",
 /*  20 */ "expr ::= expr T_EQ|T_NE|T_GT|T_GE|T_LT|T_LE|T_IN expr",
 /*  21 */ "expr ::= expr T_TIMES|T_DIV|T_MOD expr",
 /*  22 */ "expr ::= expr T_BITWISE|T_PIPE expr",
 /*  23 */ "expr ::= T_LPARENT expr T_RPARENT",
 /*  24 */ "expr ::= expr T_DOT expr",
 /*  25 */ "expr ::= variable",
 /*  26 */ "expr ::= term",
 /*  27 */ "expr ::= T_AT variable",
 /*  28 */ "expr ::= fnc_call",
 /*  29 */ "fnc_call ::= T_ALPHA T_LPARENT args T_RPARENT",
 /*  30 */ "fnc_call ::= variable T_LPARENT args T_RPARENT",
 /*  31 */ "args ::= args T_COMMA args",
 /*  32 */ "args ::= expr",
 /*  33 */ "args ::=",
 /*  34 */ "variable ::= T_DOLLAR var",
 /*  35 */ "var ::= var T_OBJ var",
 /*  36 */ "var ::= var T_CURLY_OPEN expr T_CURLY_CLOSE",
 /*  37 */ "var ::= T_ALPHA",
 /*  38 */ "term ::= T_ALPHA",
 /*  39 */ "term ::= T_TRUE",
 /*  40 */ "term ::= T_FALSE",
 /*  41 */ "term ::= T_STRING",
 /*  42 */ "term ::= T_NUMBER",
 /*  43 */ "term ::= json",
 /*  44 */ "json ::= T_CURLY_OPEN json_obj T_CURLY_CLOSE",
 /*  45 */ "json ::= T_SUBSCR_OPEN json_arr T_SUBSCR_CLOSE",
 /*  46 */ "json_obj ::= json_obj T_COMMA json_obj",
 /*  47 */ "json_obj ::= term T_COLON expr",
 /*  48 */ "json_arr ::= json_arr T_COMMA expr",
 /*  49 */ "json_arr ::= expr",
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
  array( 'lhs' => 45, 'rhs' => 1 ),
  array( 'lhs' => 46, 'rhs' => 2 ),
  array( 'lhs' => 46, 'rhs' => 0 ),
  array( 'lhs' => 47, 'rhs' => 1 ),
  array( 'lhs' => 47, 'rhs' => 2 ),
  array( 'lhs' => 47, 'rhs' => 8 ),
  array( 'lhs' => 47, 'rhs' => 10 ),
  array( 'lhs' => 48, 'rhs' => 1 ),
  array( 'lhs' => 48, 'rhs' => 1 ),
  array( 'lhs' => 47, 'rhs' => 3 ),
  array( 'lhs' => 47, 'rhs' => 1 ),
  array( 'lhs' => 47, 'rhs' => 1 ),
  array( 'lhs' => 53, 'rhs' => 6 ),
  array( 'lhs' => 54, 'rhs' => 7 ),
  array( 'lhs' => 54, 'rhs' => 3 ),
  array( 'lhs' => 54, 'rhs' => 1 ),
  array( 'lhs' => 51, 'rhs' => 2 ),
  array( 'lhs' => 51, 'rhs' => 3 ),
  array( 'lhs' => 51, 'rhs' => 3 ),
  array( 'lhs' => 51, 'rhs' => 3 ),
  array( 'lhs' => 51, 'rhs' => 3 ),
  array( 'lhs' => 51, 'rhs' => 3 ),
  array( 'lhs' => 51, 'rhs' => 3 ),
  array( 'lhs' => 51, 'rhs' => 3 ),
  array( 'lhs' => 51, 'rhs' => 3 ),
  array( 'lhs' => 51, 'rhs' => 1 ),
  array( 'lhs' => 51, 'rhs' => 1 ),
  array( 'lhs' => 51, 'rhs' => 2 ),
  array( 'lhs' => 51, 'rhs' => 1 ),
  array( 'lhs' => 52, 'rhs' => 4 ),
  array( 'lhs' => 52, 'rhs' => 4 ),
  array( 'lhs' => 56, 'rhs' => 3 ),
  array( 'lhs' => 56, 'rhs' => 1 ),
  array( 'lhs' => 56, 'rhs' => 0 ),
  array( 'lhs' => 49, 'rhs' => 2 ),
  array( 'lhs' => 57, 'rhs' => 3 ),
  array( 'lhs' => 57, 'rhs' => 4 ),
  array( 'lhs' => 57, 'rhs' => 1 ),
  array( 'lhs' => 55, 'rhs' => 1 ),
  array( 'lhs' => 55, 'rhs' => 1 ),
  array( 'lhs' => 55, 'rhs' => 1 ),
  array( 'lhs' => 55, 'rhs' => 1 ),
  array( 'lhs' => 55, 'rhs' => 1 ),
  array( 'lhs' => 55, 'rhs' => 1 ),
  array( 'lhs' => 50, 'rhs' => 3 ),
  array( 'lhs' => 50, 'rhs' => 3 ),
  array( 'lhs' => 58, 'rhs' => 3 ),
  array( 'lhs' => 58, 'rhs' => 3 ),
  array( 'lhs' => 59, 'rhs' => 3 ),
  array( 'lhs' => 59, 'rhs' => 1 ),
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
        33 => 2,
        3 => 3,
        4 => 4,
        7 => 4,
        10 => 4,
        11 => 4,
        25 => 4,
        28 => 4,
        41 => 4,
        43 => 4,
        5 => 5,
        6 => 6,
        8 => 8,
        9 => 9,
        12 => 12,
        13 => 13,
        14 => 14,
        15 => 15,
        16 => 16,
        17 => 17,
        18 => 17,
        19 => 19,
        21 => 19,
        22 => 19,
        20 => 20,
        23 => 23,
        24 => 24,
        26 => 26,
        27 => 27,
        29 => 29,
        30 => 29,
        31 => 31,
        35 => 31,
        46 => 31,
        32 => 32,
        49 => 32,
        34 => 34,
        36 => 36,
        37 => 37,
        38 => 38,
        39 => 39,
        40 => 40,
        42 => 42,
        44 => 44,
        45 => 45,
        47 => 47,
        48 => 48,
    );
    /* Beginning here are the reduction cases.  A typical example
    ** follows:
    **  #line <lineno> <grammarfile>
    **   function yy_r0($yymsp){ ... }           // User supplied code
    **  #line <lineno> <thisfile>
    */
#line 76 "Artifex/Parser.y"
    function yy_r0(){ $this->body = $this->yystack[$this->yyidx + 0]->minor;     }
#line 1151 "Artifex/Parser.php"
#line 78 "Artifex/Parser.y"
    function yy_r1(){ $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor; $this->_retvalue[] = $this->yystack[$this->yyidx + 0]->minor;     }
#line 1154 "Artifex/Parser.php"
#line 79 "Artifex/Parser.y"
    function yy_r2(){ $this->_retvalue = array();     }
#line 1157 "Artifex/Parser.php"
#line 81 "Artifex/Parser.y"
    function yy_r3(){ $this->_retvalue = new RawString($this->yystack[$this->yyidx + 0]->minor);     }
#line 1160 "Artifex/Parser.php"
#line 82 "Artifex/Parser.y"
    function yy_r4(){ $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;     }
#line 1163 "Artifex/Parser.php"
#line 84 "Artifex/Parser.y"
    function yy_r5(){ 
    $this->_retvalue = new Expr_Foreach($this->yystack[$this->yyidx + -5]->minor, $this->yystack[$this->yyidx + -3]->minor, NULL, $this->yystack[$this->yyidx + -1]->minor); 
    }
#line 1168 "Artifex/Parser.php"
#line 88 "Artifex/Parser.y"
    function yy_r6(){
    $this->_retvalue = new Expr_Foreach($this->yystack[$this->yyidx + -7]->minor, $this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -5]->minor, $this->yystack[$this->yyidx + -1]->minor);
    }
#line 1173 "Artifex/Parser.php"
#line 93 "Artifex/Parser.y"
    function yy_r8(){ $this->_retvalue = new Term($this->yystack[$this->yyidx + 0]->minor);     }
#line 1176 "Artifex/Parser.php"
#line 95 "Artifex/Parser.y"
    function yy_r9(){ $this->_retvalue = new Assign($this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1179 "Artifex/Parser.php"
#line 100 "Artifex/Parser.y"
    function yy_r12(){
    $this->_retvalue = new Expr_If($this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + 0]->minor);
    }
#line 1184 "Artifex/Parser.php"
#line 104 "Artifex/Parser.y"
    function yy_r13(){ 
    $this->_retvalue = new Expr_If($this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + 0]->minor); 
    }
#line 1189 "Artifex/Parser.php"
#line 107 "Artifex/Parser.y"
    function yy_r14(){ 
    $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor; 
    }
#line 1194 "Artifex/Parser.php"
#line 110 "Artifex/Parser.y"
    function yy_r15(){ $this->_retvalue = NULL;     }
#line 1197 "Artifex/Parser.php"
#line 112 "Artifex/Parser.y"
    function yy_r16(){ $this->_retvalue = new Expr('not', $this->yystack[$this->yyidx + 0]->minor);     }
#line 1200 "Artifex/Parser.php"
#line 113 "Artifex/Parser.y"
    function yy_r17(){ $this->_retvalue = new Expr(strtolower(@$this->yystack[$this->yyidx + -1]->minor), $this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1203 "Artifex/Parser.php"
#line 115 "Artifex/Parser.y"
    function yy_r19(){ $this->_retvalue = new Expr(@$this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1206 "Artifex/Parser.php"
#line 116 "Artifex/Parser.y"
    function yy_r20(){ $this->_retvalue = new Expr(trim(@$this->yystack[$this->yyidx + -1]->minor), $this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1209 "Artifex/Parser.php"
#line 119 "Artifex/Parser.y"
    function yy_r23(){ $this->_retvalue = new Expr($this->yystack[$this->yyidx + -1]->minor);     }
#line 1212 "Artifex/Parser.php"
#line 120 "Artifex/Parser.y"
    function yy_r24(){ $this->_retvalue = new Concat($this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1215 "Artifex/Parser.php"
#line 122 "Artifex/Parser.y"
    function yy_r26(){  $this->_retvalue = new Term($this->yystack[$this->yyidx + 0]->minor);     }
#line 1218 "Artifex/Parser.php"
#line 123 "Artifex/Parser.y"
    function yy_r27(){ 
    $this->_retvalue = new Exec('var_export', array($this->yystack[$this->yyidx + 0]->minor, new Term(true))); 
    }
#line 1223 "Artifex/Parser.php"
#line 129 "Artifex/Parser.y"
    function yy_r29(){ 
    $this->_retvalue = new Exec($this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -1]->minor);
    }
#line 1228 "Artifex/Parser.php"
#line 138 "Artifex/Parser.y"
    function yy_r31(){ $this->_retvalue = array_merge($this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1231 "Artifex/Parser.php"
#line 139 "Artifex/Parser.y"
    function yy_r32(){ $this->_retvalue = array($this->yystack[$this->yyidx + 0]->minor);     }
#line 1234 "Artifex/Parser.php"
#line 142 "Artifex/Parser.y"
    function yy_r34(){ $this->_retvalue = new Variable($this->yystack[$this->yyidx + 0]->minor);     }
#line 1237 "Artifex/Parser.php"
#line 145 "Artifex/Parser.y"
    function yy_r36(){ $this->_retvalue = $this->yystack[$this->yyidx + -3]->minor ; $this->_retvalue[] = $this->yystack[$this->yyidx + -1]->minor;     }
#line 1240 "Artifex/Parser.php"
#line 146 "Artifex/Parser.y"
    function yy_r37(){ $this->_retvalue = array($this->yystack[$this->yyidx + 0]->minor);    }
#line 1243 "Artifex/Parser.php"
#line 148 "Artifex/Parser.y"
    function yy_r38(){ $this->_retvalue = trim($this->yystack[$this->yyidx + 0]->minor);     }
#line 1246 "Artifex/Parser.php"
#line 149 "Artifex/Parser.y"
    function yy_r39(){ $this->_retvalue = TRUE;     }
#line 1249 "Artifex/Parser.php"
#line 150 "Artifex/Parser.y"
    function yy_r40(){ $this->_retvalue = FALSE;     }
#line 1252 "Artifex/Parser.php"
#line 152 "Artifex/Parser.y"
    function yy_r42(){ $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor + 0;     }
#line 1255 "Artifex/Parser.php"
#line 156 "Artifex/Parser.y"
    function yy_r44(){ $this->_retvalue  = $this->yystack[$this->yyidx + -1]->minor;     }
#line 1258 "Artifex/Parser.php"
#line 157 "Artifex/Parser.y"
    function yy_r45(){ $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor;     }
#line 1261 "Artifex/Parser.php"
#line 160 "Artifex/Parser.y"
    function yy_r47(){ $this->_retvalue = array($this->yystack[$this->yyidx + -2]->minor => $this->yystack[$this->yyidx + 0]->minor);     }
#line 1264 "Artifex/Parser.php"
#line 162 "Artifex/Parser.y"
    function yy_r48(){ $this->_retvalue = $this->yystack[$this->yyidx + -2]->minor; $this->_retvalue[] = $this->yystack[$this->yyidx + 0]->minor;     }
#line 1267 "Artifex/Parser.php"

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
#line 54 "Artifex/Parser.y"

    $expect = array();
    foreach ($this->yy_get_expected_tokens($yymajor) as $token) {
        $expect[] = self::$yyTokenName[$token];
    }
    throw new Exception('Unexpected ' . $this->tokenName($yymajor) . '(' . $TOKEN. ')' . print_r($expect, true));
#line 1387 "Artifex/Parser.php"
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
