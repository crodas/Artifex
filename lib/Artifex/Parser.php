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
    \Artifex\Runtime\Whitespace,
    \Artifex\Runtime\Term,
    \Artifex\Runtime\DefFunction,
    \Artifex\Runtime\Variable;
#line 148 "lib/Artifex/Parser.php"

// declare_class is output here
#line 51 "lib/Artifex/Parser.y"
class Artifex_Parser #line 153 "lib/Artifex/Parser.php"
{
/* First off, code is included which follows the "include_class" declaration
** in the input file. */
#line 52 "lib/Artifex/Parser.y"

    public $body = array();
#line 161 "lib/Artifex/Parser.php"

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
    const T_FOREACH                      = 27;
    const T_LPARENT                      = 28;
    const T_AS                           = 29;
    const T_RPARENT                      = 30;
    const T_END                          = 31;
    const T_DOUBLE_ARROW                 = 32;
    const T_FUNCTION                     = 33;
    const T_ALPHA                        = 34;
    const T_IF                           = 35;
    const T_ELSE                         = 36;
    const T_AT                           = 37;
    const T_DOLLAR                       = 38;
    const T_SUBSCR_OPEN                  = 39;
    const T_SUBSCR_CLOSE                 = 40;
    const T_TRUE                         = 41;
    const T_FALSE                        = 42;
    const T_NUMBER                       = 43;
    const T_CURLY_CLOSE                  = 44;
    const T_COLON                        = 45;
    const YY_NO_ACTION = 177;
    const YY_ACCEPT_ACTION = 176;
    const YY_ERROR_ACTION = 175;

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
    const YY_SZ_ACTTAB = 455;
static public $yy_action = array(
 /*     0 */    16,   11,   22,   15,   15,   32,   15,   15,   15,   15,
 /*    10 */    15,    8,   13,   13,   14,   14,   14,   12,   12,   29,
 /*    20 */    75,   16,   11,   20,   15,   15,   37,   15,   15,   15,
 /*    30 */    15,   15,    8,   13,   13,   14,   14,   14,   12,   12,
 /*    40 */   107,   93,   16,   11,   18,   15,   15,   38,   15,   15,
 /*    50 */    15,   15,   15,    8,   13,   13,   14,   14,   14,   12,
 /*    60 */    12,   23,  114,   16,   11,    9,   15,   15,  101,   15,
 /*    70 */    15,   15,   15,   15,    8,   13,   13,   14,   14,   14,
 /*    80 */    12,   12,   15,   15,    3,   15,   15,   15,   15,   15,
 /*    90 */     8,   13,   13,   14,   14,   14,   12,   12,    5,   98,
 /*   100 */    71,   73,   83,   84,   16,   11,   58,   15,   15,   66,
 /*   110 */    15,   15,   15,   15,   15,    8,   13,   13,   14,   14,
 /*   120 */    14,   12,   12,   11,   99,   15,   15,  111,   15,   15,
 /*   130 */    15,   15,   15,    8,   13,   13,   14,   14,   14,   12,
 /*   140 */    12,   30,   28,   41,   21,   31,   92,    2,   86,   88,
 /*   150 */    68,  105,   30,   19,   91,   78,   80,   69,   77,   34,
 /*   160 */     1,   32,   76,   97,  176,   27,   10,   70,   94,  112,
 /*   170 */   108,   46,   79,    5,  102,   33,   32,    4,   26,   95,
 /*   180 */    96,  104,   92,   69,   86,   88,   68,   32,    4,   17,
 /*   190 */   103,   39,   80,   69,   77,    3,   92,   32,   86,   88,
 /*   200 */    68,    5,   40,   36,   87,   90,   80,   69,   77,   35,
 /*   210 */    92,   32,   86,   88,   68,   12,   12,   60,   82,   25,
 /*   220 */    80,   69,   77,    6,   92,   32,   86,   88,   68,   30,
 /*   230 */   110,    7,  115,   24,   80,   69,   77,   74,   92,   32,
 /*   240 */    86,   88,   68,   78,  123,  123,   17,  123,   80,   69,
 /*   250 */    77,   97,  123,   32,   81,   72,  123,   62,  109,  123,
 /*   260 */   113,  123,  116,   85,  123,    4,  123,   95,   96,  104,
 /*   270 */    13,   13,   14,   14,   14,   12,   12,   81,   72,  123,
 /*   280 */    62,  109,  123,  123,  123,  116,   89,   70,   94,  112,
 /*   290 */   123,   50,   81,   72,  102,   62,  109,   65,  123,  123,
 /*   300 */   116,   70,   94,  112,   61,   46,  123,  123,  102,  123,
 /*   310 */    70,   94,  112,   57,   46,  123,  123,  102,   70,   94,
 /*   320 */   112,   63,   46,  123,  123,  102,  123,   14,   14,   14,
 /*   330 */    12,   12,  123,   70,   94,  112,  123,   45,  123,  123,
 /*   340 */   102,   70,   94,  112,  123,   54,  123,  112,  102,   70,
 /*   350 */    94,  112,   67,  100,   64,  112,  102,   70,   94,  112,
 /*   360 */    67,   53,  106,  123,  102,   70,   94,  112,  123,   44,
 /*   370 */   123,  123,  102,   70,   94,  112,  123,   47,  123,  123,
 /*   380 */   102,   70,   94,  112,  123,   59,  123,  123,  102,  123,
 /*   390 */    70,   94,  112,  123,   52,  123,  123,  102,   70,   94,
 /*   400 */   112,  123,   51,  123,  123,  102,   70,   94,  112,  123,
 /*   410 */    43,  123,  123,  102,   70,   94,  112,  123,   42,  123,
 /*   420 */   123,  102,   70,   94,  112,  123,   48,  123,  123,  102,
 /*   430 */    70,   94,  112,  123,   56,  123,  123,  102,   70,   94,
 /*   440 */   112,  123,   49,  123,  123,  102,  123,   70,   94,  112,
 /*   450 */   123,   55,  123,  123,  102,
    );
    static public $yy_lookahead = array(
 /*     0 */     4,    5,    1,    7,    8,   38,   10,   11,   12,   13,
 /*    10 */    14,   15,   16,   17,   18,   19,   20,   21,   22,    1,
 /*    20 */    52,    4,    5,   28,    7,    8,   30,   10,   11,   12,
 /*    30 */    13,   14,   15,   16,   17,   18,   19,   20,   21,   22,
 /*    40 */    34,   40,    4,    5,   45,    7,    8,   30,   10,   11,
 /*    50 */    12,   13,   14,   15,   16,   17,   18,   19,   20,   21,
 /*    60 */    22,   48,   44,    4,    5,    9,    7,    8,   30,   10,
 /*    70 */    11,   12,   13,   14,   15,   16,   17,   18,   19,   20,
 /*    80 */    21,   22,    7,    8,   28,   10,   11,   12,   13,   14,
 /*    90 */    15,   16,   17,   18,   19,   20,   21,   22,    1,   40,
 /*   100 */    51,   52,   53,   54,    4,    5,   60,    7,    8,   35,
 /*   110 */    10,   11,   12,   13,   14,   15,   16,   17,   18,   19,
 /*   120 */    20,   21,   22,    5,   52,    7,    8,   30,   10,   11,
 /*   130 */    12,   13,   14,   15,   16,   17,   18,   19,   20,   21,
 /*   140 */    22,    3,   28,   30,    6,   32,   23,   28,   25,   26,
 /*   150 */    27,   43,    3,   28,   31,   17,   33,   34,   35,   36,
 /*   160 */    28,   38,   34,   25,   47,   48,   28,   52,   53,   54,
 /*   170 */    55,   56,   34,    1,   59,   37,   38,   39,   48,   41,
 /*   180 */    42,   43,   23,   34,   25,   26,   27,   38,   39,   39,
 /*   190 */    31,   30,   33,   34,   35,   28,   23,   38,   25,   26,
 /*   200 */    27,    1,   30,   29,   31,   24,   33,   34,   35,    2,
 /*   210 */    23,   38,   25,   26,   27,   21,   22,   52,   31,   48,
 /*   220 */    33,   34,   35,   48,   23,   38,   25,   26,   27,    3,
 /*   230 */    30,   48,   31,   48,   33,   34,   35,   60,   23,   38,
 /*   240 */    25,   26,   27,   17,   63,   63,   39,   63,   33,   34,
 /*   250 */    35,   25,   63,   38,   49,   50,   63,   52,   53,   63,
 /*   260 */    34,   63,   57,   58,   63,   39,   63,   41,   42,   43,
 /*   270 */    16,   17,   18,   19,   20,   21,   22,   49,   50,   63,
 /*   280 */    52,   53,   63,   63,   63,   57,   58,   52,   53,   54,
 /*   290 */    63,   56,   49,   50,   59,   52,   53,   62,   63,   63,
 /*   300 */    57,   52,   53,   54,   55,   56,   63,   63,   59,   63,
 /*   310 */    52,   53,   54,   55,   56,   63,   63,   59,   52,   53,
 /*   320 */    54,   55,   56,   63,   63,   59,   63,   18,   19,   20,
 /*   330 */    21,   22,   63,   52,   53,   54,   63,   56,   63,   63,
 /*   340 */    59,   52,   53,   54,   63,   56,   63,   54,   59,   52,
 /*   350 */    53,   54,   59,   56,   61,   54,   59,   52,   53,   54,
 /*   360 */    59,   56,   61,   63,   59,   52,   53,   54,   63,   56,
 /*   370 */    63,   63,   59,   52,   53,   54,   63,   56,   63,   63,
 /*   380 */    59,   52,   53,   54,   63,   56,   63,   63,   59,   63,
 /*   390 */    52,   53,   54,   63,   56,   63,   63,   59,   52,   53,
 /*   400 */    54,   63,   56,   63,   63,   59,   52,   53,   54,   63,
 /*   410 */    56,   63,   63,   59,   52,   53,   54,   63,   56,   63,
 /*   420 */    63,   59,   52,   53,   54,   63,   56,   63,   63,   59,
 /*   430 */    52,   53,   54,   63,   56,   63,   63,   59,   52,   53,
 /*   440 */    54,   63,   56,   63,   63,   59,   63,   52,   53,   54,
 /*   450 */    63,   56,   63,   63,   59,
);
    const YY_SHIFT_USE_DFLT = -34;
    const YY_SHIFT_MAX = 80;
    static public $yy_shift_ofst = array(
 /*     0 */   -34,  138,  138,  138,  138,  138,  123,  123,  138,  138,
 /*    10 */   138,  138,  138,  138,  138,  138,  138,  138,  138,  138,
 /*    20 */   138,  138,  138,  173,  187,  159,  201,  215,  149,  226,
 /*    30 */   226,  -33,    6,  -33,   74,    6,  -33,  -34,  -34,  -34,
 /*    40 */   -34,  -34,   38,   59,   -4,   17,  100,  100,  100,  100,
 /*    50 */   100,  118,   75,   75,   75,  254,  309,   97,  207,  194,
 /*    60 */   113,  200,   56,  172,   18,    1,   -5,   -1,  114,  119,
 /*    70 */   167,  174,  181,  167,  150,  161,  132,  125,  108,  119,
 /*    80 */   128,
);
    const YY_REDUCE_USE_DFLT = -33;
    const YY_REDUCE_MAX = 41;
    static public $yy_reduce_ofst = array(
 /*     0 */   117,  266,  249,  258,  235,  115,  205,  228,  395,  370,
 /*    10 */   362,  305,  297,  378,  329,  338,  346,  354,  321,  313,
 /*    20 */   281,  289,  386,  243,  243,  243,  243,  243,   49,  301,
 /*    30 */   293,  -32,   46,   72,   13,  177,  165,  175,  183,  185,
 /*    40 */   171,  130,
);
    static public $yyExpectedTokens = array(
        /* 0 */ array(),
        /* 1 */ array(3, 6, 17, 25, 28, 34, 37, 38, 39, 41, 42, 43, ),
        /* 2 */ array(3, 6, 17, 25, 28, 34, 37, 38, 39, 41, 42, 43, ),
        /* 3 */ array(3, 6, 17, 25, 28, 34, 37, 38, 39, 41, 42, 43, ),
        /* 4 */ array(3, 6, 17, 25, 28, 34, 37, 38, 39, 41, 42, 43, ),
        /* 5 */ array(3, 6, 17, 25, 28, 34, 37, 38, 39, 41, 42, 43, ),
        /* 6 */ array(23, 25, 26, 27, 31, 33, 34, 35, 36, 38, ),
        /* 7 */ array(23, 25, 26, 27, 31, 33, 34, 35, 36, 38, ),
        /* 8 */ array(3, 6, 17, 25, 28, 34, 37, 38, 39, 41, 42, 43, ),
        /* 9 */ array(3, 6, 17, 25, 28, 34, 37, 38, 39, 41, 42, 43, ),
        /* 10 */ array(3, 6, 17, 25, 28, 34, 37, 38, 39, 41, 42, 43, ),
        /* 11 */ array(3, 6, 17, 25, 28, 34, 37, 38, 39, 41, 42, 43, ),
        /* 12 */ array(3, 6, 17, 25, 28, 34, 37, 38, 39, 41, 42, 43, ),
        /* 13 */ array(3, 6, 17, 25, 28, 34, 37, 38, 39, 41, 42, 43, ),
        /* 14 */ array(3, 6, 17, 25, 28, 34, 37, 38, 39, 41, 42, 43, ),
        /* 15 */ array(3, 6, 17, 25, 28, 34, 37, 38, 39, 41, 42, 43, ),
        /* 16 */ array(3, 6, 17, 25, 28, 34, 37, 38, 39, 41, 42, 43, ),
        /* 17 */ array(3, 6, 17, 25, 28, 34, 37, 38, 39, 41, 42, 43, ),
        /* 18 */ array(3, 6, 17, 25, 28, 34, 37, 38, 39, 41, 42, 43, ),
        /* 19 */ array(3, 6, 17, 25, 28, 34, 37, 38, 39, 41, 42, 43, ),
        /* 20 */ array(3, 6, 17, 25, 28, 34, 37, 38, 39, 41, 42, 43, ),
        /* 21 */ array(3, 6, 17, 25, 28, 34, 37, 38, 39, 41, 42, 43, ),
        /* 22 */ array(3, 6, 17, 25, 28, 34, 37, 38, 39, 41, 42, 43, ),
        /* 23 */ array(23, 25, 26, 27, 31, 33, 34, 35, 38, ),
        /* 24 */ array(23, 25, 26, 27, 31, 33, 34, 35, 38, ),
        /* 25 */ array(23, 25, 26, 27, 31, 33, 34, 35, 38, ),
        /* 26 */ array(23, 25, 26, 27, 31, 33, 34, 35, 38, ),
        /* 27 */ array(23, 25, 26, 27, 33, 34, 35, 38, ),
        /* 28 */ array(3, 34, 38, 39, ),
        /* 29 */ array(3, 17, 25, 34, 39, 41, 42, 43, ),
        /* 30 */ array(3, 17, 25, 34, 39, 41, 42, 43, ),
        /* 31 */ array(38, ),
        /* 32 */ array(34, ),
        /* 33 */ array(38, ),
        /* 34 */ array(35, ),
        /* 35 */ array(34, ),
        /* 36 */ array(38, ),
        /* 37 */ array(),
        /* 38 */ array(),
        /* 39 */ array(),
        /* 40 */ array(),
        /* 41 */ array(),
        /* 42 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 30, ),
        /* 43 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 40, ),
        /* 44 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 30, ),
        /* 45 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 30, ),
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
        /* 57 */ array(1, 30, ),
        /* 58 */ array(2, 39, ),
        /* 59 */ array(21, 22, ),
        /* 60 */ array(30, 32, ),
        /* 61 */ array(1, 30, ),
        /* 62 */ array(9, 28, ),
        /* 63 */ array(1, 30, ),
        /* 64 */ array(1, 44, ),
        /* 65 */ array(1, 40, ),
        /* 66 */ array(28, ),
        /* 67 */ array(45, ),
        /* 68 */ array(28, ),
        /* 69 */ array(28, ),
        /* 70 */ array(28, ),
        /* 71 */ array(29, ),
        /* 72 */ array(24, ),
        /* 73 */ array(28, ),
        /* 74 */ array(39, ),
        /* 75 */ array(30, ),
        /* 76 */ array(28, ),
        /* 77 */ array(28, ),
        /* 78 */ array(43, ),
        /* 79 */ array(28, ),
        /* 80 */ array(34, ),
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
        /* 112 */ array(),
        /* 113 */ array(),
        /* 114 */ array(),
        /* 115 */ array(),
        /* 116 */ array(),
);
    static public $yy_default = array(
 /*     0 */   119,  155,  155,  155,  174,  155,  175,  175,  175,  175,
 /*    10 */   175,  175,  175,  175,  175,  175,  175,  175,  175,  175,
 /*    20 */   175,  175,  175,  175,  175,  175,  175,  117,  175,  171,
 /*    30 */   171,  175,  175,  175,  119,  175,  175,  119,  119,  119,
 /*    40 */   119,  119,  175,  175,  175,  175,  154,  170,  131,  172,
 /*    50 */   173,  141,  144,  142,  140,  148,  143,  175,  156,  145,
 /*    60 */   175,  175,  175,  175,  175,  175,  175,  175,  175,  175,
 /*    70 */   149,  175,  121,  127,  157,  175,  175,  175,  175,  160,
 /*    80 */   175,  118,  126,  128,  129,  136,  123,  138,  124,  137,
 /*    90 */   122,  139,  120,  168,  152,  161,  162,  163,  158,  151,
 /*   100 */   146,  147,  150,  130,  164,  165,  169,  159,  153,  132,
 /*   110 */   133,  134,  166,  160,  167,  125,  135,
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
    const YYNOCODE = 64;
    const YYSTACKDEPTH = 100;
    const YYNSTATE = 117;
    const YYNRULE = 58;
    const YYERRORSYMBOL = 46;
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
  'T_NEW_LINE',    'T_STRING',      'T_WHITESPACE',  'T_FOREACH',   
  'T_LPARENT',     'T_AS',          'T_RPARENT',     'T_END',       
  'T_DOUBLE_ARROW',  'T_FUNCTION',    'T_ALPHA',       'T_IF',        
  'T_ELSE',        'T_AT',          'T_DOLLAR',      'T_SUBSCR_OPEN',
  'T_SUBSCR_CLOSE',  'T_TRUE',        'T_FALSE',       'T_NUMBER',    
  'T_CURLY_CLOSE',  'T_COLON',       'error',         'start',       
  'body',          'line',          'code',          'foreach_source',
  'variable',      'fnc_call',      'json',          'args',        
  'expr',          'if',            'else_if',       'term',        
  'var',           'json_obj',      'json_arr',    
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
 /*   8 */ "code ::= T_FOREACH T_LPARENT foreach_source T_AS variable T_RPARENT body T_END",
 /*   9 */ "code ::= T_FOREACH T_LPARENT foreach_source T_AS variable T_DOUBLE_ARROW variable T_RPARENT body T_END",
 /*  10 */ "foreach_source ::= variable",
 /*  11 */ "foreach_source ::= fnc_call",
 /*  12 */ "foreach_source ::= json",
 /*  13 */ "code ::= T_FUNCTION T_ALPHA T_LPARENT args T_RPARENT body T_END",
 /*  14 */ "code ::= variable T_ASSIGN expr",
 /*  15 */ "code ::= fnc_call",
 /*  16 */ "fnc_call ::= T_ALPHA T_LPARENT args T_RPARENT",
 /*  17 */ "fnc_call ::= variable T_LPARENT args T_RPARENT",
 /*  18 */ "code ::= if",
 /*  19 */ "if ::= T_IF T_LPARENT expr T_RPARENT body else_if",
 /*  20 */ "else_if ::= T_ELSE T_IF T_LPARENT expr T_RPARENT body else_if",
 /*  21 */ "else_if ::= T_ELSE body T_END",
 /*  22 */ "else_if ::= T_END",
 /*  23 */ "expr ::= T_NOT expr",
 /*  24 */ "expr ::= expr T_AND expr",
 /*  25 */ "expr ::= expr T_OR expr",
 /*  26 */ "expr ::= expr T_PLUS|T_MINUS expr",
 /*  27 */ "expr ::= expr T_EQ|T_NE|T_GT|T_GE|T_LT|T_LE|T_IN expr",
 /*  28 */ "expr ::= expr T_TIMES|T_DIV|T_MOD expr",
 /*  29 */ "expr ::= expr T_BITWISE|T_PIPE expr",
 /*  30 */ "expr ::= T_LPARENT expr T_RPARENT",
 /*  31 */ "expr ::= expr T_DOT expr",
 /*  32 */ "expr ::= variable",
 /*  33 */ "expr ::= term",
 /*  34 */ "expr ::= T_AT variable",
 /*  35 */ "expr ::= fnc_call",
 /*  36 */ "args ::= args T_COMMA args",
 /*  37 */ "args ::= expr",
 /*  38 */ "args ::=",
 /*  39 */ "variable ::= T_DOLLAR var",
 /*  40 */ "var ::= var T_OBJ var",
 /*  41 */ "var ::= var T_SUBSCR_OPEN expr T_SUBSCR_CLOSE",
 /*  42 */ "var ::= T_ALPHA",
 /*  43 */ "term ::= T_ALPHA",
 /*  44 */ "term ::= T_TRUE",
 /*  45 */ "term ::= T_FALSE",
 /*  46 */ "term ::= T_STRING",
 /*  47 */ "term ::= T_NUMBER",
 /*  48 */ "term ::= T_MINUS T_NUMBER",
 /*  49 */ "term ::= json",
 /*  50 */ "json ::= T_CURLY_OPEN json_obj T_CURLY_CLOSE",
 /*  51 */ "json ::= T_SUBSCR_OPEN json_arr T_SUBSCR_CLOSE",
 /*  52 */ "json_obj ::= json_obj T_COMMA json_obj",
 /*  53 */ "json_obj ::= term T_COLON expr",
 /*  54 */ "json_obj ::=",
 /*  55 */ "json_arr ::= json_arr T_COMMA expr",
 /*  56 */ "json_arr ::= expr",
 /*  57 */ "json_arr ::=",
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
  array( 'lhs' => 47, 'rhs' => 1 ),
  array( 'lhs' => 48, 'rhs' => 2 ),
  array( 'lhs' => 48, 'rhs' => 0 ),
  array( 'lhs' => 49, 'rhs' => 1 ),
  array( 'lhs' => 49, 'rhs' => 1 ),
  array( 'lhs' => 49, 'rhs' => 2 ),
  array( 'lhs' => 49, 'rhs' => 1 ),
  array( 'lhs' => 49, 'rhs' => 1 ),
  array( 'lhs' => 50, 'rhs' => 8 ),
  array( 'lhs' => 50, 'rhs' => 10 ),
  array( 'lhs' => 51, 'rhs' => 1 ),
  array( 'lhs' => 51, 'rhs' => 1 ),
  array( 'lhs' => 51, 'rhs' => 1 ),
  array( 'lhs' => 50, 'rhs' => 7 ),
  array( 'lhs' => 50, 'rhs' => 3 ),
  array( 'lhs' => 50, 'rhs' => 1 ),
  array( 'lhs' => 53, 'rhs' => 4 ),
  array( 'lhs' => 53, 'rhs' => 4 ),
  array( 'lhs' => 50, 'rhs' => 1 ),
  array( 'lhs' => 57, 'rhs' => 6 ),
  array( 'lhs' => 58, 'rhs' => 7 ),
  array( 'lhs' => 58, 'rhs' => 3 ),
  array( 'lhs' => 58, 'rhs' => 1 ),
  array( 'lhs' => 56, 'rhs' => 2 ),
  array( 'lhs' => 56, 'rhs' => 3 ),
  array( 'lhs' => 56, 'rhs' => 3 ),
  array( 'lhs' => 56, 'rhs' => 3 ),
  array( 'lhs' => 56, 'rhs' => 3 ),
  array( 'lhs' => 56, 'rhs' => 3 ),
  array( 'lhs' => 56, 'rhs' => 3 ),
  array( 'lhs' => 56, 'rhs' => 3 ),
  array( 'lhs' => 56, 'rhs' => 3 ),
  array( 'lhs' => 56, 'rhs' => 1 ),
  array( 'lhs' => 56, 'rhs' => 1 ),
  array( 'lhs' => 56, 'rhs' => 2 ),
  array( 'lhs' => 56, 'rhs' => 1 ),
  array( 'lhs' => 55, 'rhs' => 3 ),
  array( 'lhs' => 55, 'rhs' => 1 ),
  array( 'lhs' => 55, 'rhs' => 0 ),
  array( 'lhs' => 52, 'rhs' => 2 ),
  array( 'lhs' => 60, 'rhs' => 3 ),
  array( 'lhs' => 60, 'rhs' => 4 ),
  array( 'lhs' => 60, 'rhs' => 1 ),
  array( 'lhs' => 59, 'rhs' => 1 ),
  array( 'lhs' => 59, 'rhs' => 1 ),
  array( 'lhs' => 59, 'rhs' => 1 ),
  array( 'lhs' => 59, 'rhs' => 1 ),
  array( 'lhs' => 59, 'rhs' => 1 ),
  array( 'lhs' => 59, 'rhs' => 2 ),
  array( 'lhs' => 59, 'rhs' => 1 ),
  array( 'lhs' => 54, 'rhs' => 3 ),
  array( 'lhs' => 54, 'rhs' => 3 ),
  array( 'lhs' => 61, 'rhs' => 3 ),
  array( 'lhs' => 61, 'rhs' => 3 ),
  array( 'lhs' => 61, 'rhs' => 0 ),
  array( 'lhs' => 62, 'rhs' => 3 ),
  array( 'lhs' => 62, 'rhs' => 1 ),
  array( 'lhs' => 62, 'rhs' => 0 ),
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
        38 => 2,
        54 => 2,
        57 => 2,
        3 => 3,
        6 => 3,
        4 => 4,
        10 => 4,
        11 => 4,
        15 => 4,
        18 => 4,
        32 => 4,
        35 => 4,
        46 => 4,
        49 => 4,
        5 => 5,
        51 => 5,
        7 => 7,
        8 => 8,
        9 => 9,
        12 => 12,
        13 => 13,
        14 => 14,
        16 => 16,
        17 => 16,
        19 => 19,
        20 => 20,
        21 => 21,
        22 => 22,
        23 => 23,
        24 => 24,
        25 => 24,
        26 => 26,
        28 => 26,
        29 => 26,
        27 => 27,
        30 => 30,
        31 => 31,
        33 => 33,
        34 => 34,
        36 => 36,
        40 => 36,
        52 => 36,
        37 => 37,
        56 => 37,
        39 => 39,
        41 => 41,
        42 => 42,
        43 => 43,
        44 => 44,
        45 => 45,
        47 => 47,
        48 => 48,
        50 => 50,
        53 => 53,
        55 => 55,
    );
    /* Beginning here are the reduction cases.  A typical example
    ** follows:
    **  #line <lineno> <grammarfile>
    **   function yy_r0($yymsp){ ... }           // User supplied code
    **  #line <lineno> <thisfile>
    */
#line 80 "lib/Artifex/Parser.y"
    function yy_r0(){ $this->body = $this->yystack[$this->yyidx + 0]->minor;     }
#line 1205 "lib/Artifex/Parser.php"
#line 82 "lib/Artifex/Parser.y"
    function yy_r1(){
    $last = end($this->yystack[$this->yyidx + -1]->minor);
    if ($last) {
        $last->setNext($this->yystack[$this->yyidx + 0]->minor);
        $this->yystack[$this->yyidx + 0]->minor->setPrev($last);
    }
    $this->yystack[$this->yyidx + -1]->minor[] = $this->yystack[$this->yyidx + 0]->minor; 
    $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor; 
    }
#line 1216 "lib/Artifex/Parser.php"
#line 91 "lib/Artifex/Parser.y"
    function yy_r2(){ $this->_retvalue = array();     }
#line 1219 "lib/Artifex/Parser.php"
#line 93 "lib/Artifex/Parser.y"
    function yy_r3(){ $this->_retvalue = new RawString($this->yystack[$this->yyidx + 0]->minor);     }
#line 1222 "lib/Artifex/Parser.php"
#line 94 "lib/Artifex/Parser.y"
    function yy_r4(){ $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;     }
#line 1225 "lib/Artifex/Parser.php"
#line 95 "lib/Artifex/Parser.y"
    function yy_r5(){ $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor;     }
#line 1228 "lib/Artifex/Parser.php"
#line 97 "lib/Artifex/Parser.y"
    function yy_r7(){ $this->_retvalue = new Whitespace($this->yystack[$this->yyidx + 0]->minor);     }
#line 1231 "lib/Artifex/Parser.php"
#line 100 "lib/Artifex/Parser.y"
    function yy_r8(){ 
    $this->_retvalue = new Expr_Foreach($this->yystack[$this->yyidx + -5]->minor, $this->yystack[$this->yyidx + -3]->minor, NULL, $this->yystack[$this->yyidx + -1]->minor); 
    $this->_retvalue->setChild($this->yystack[$this->yyidx + -1]->minor);
    }
#line 1237 "lib/Artifex/Parser.php"
#line 105 "lib/Artifex/Parser.y"
    function yy_r9(){
    $this->_retvalue = new Expr_Foreach($this->yystack[$this->yyidx + -7]->minor, $this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -5]->minor, $this->yystack[$this->yyidx + -1]->minor);
    $this->_retvalue->setChild($this->yystack[$this->yyidx + -1]->minor);
    }
#line 1243 "lib/Artifex/Parser.php"
#line 112 "lib/Artifex/Parser.y"
    function yy_r12(){ $this->_retvalue = new Term($this->yystack[$this->yyidx + 0]->minor);     }
#line 1246 "lib/Artifex/Parser.php"
#line 116 "lib/Artifex/Parser.y"
    function yy_r13(){
    $this->_retvalue = new DefFunction($this->yystack[$this->yyidx + -5]->minor, $this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -1]->minor);
    $this->_retvalue->setChild($this->yystack[$this->yyidx + -1]->minor);
    }
#line 1252 "lib/Artifex/Parser.php"
#line 123 "lib/Artifex/Parser.y"
    function yy_r14(){ $this->_retvalue = new Assign($this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1255 "lib/Artifex/Parser.php"
#line 128 "lib/Artifex/Parser.y"
    function yy_r16(){ 
    $this->_retvalue = new Exec($this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -1]->minor);
    }
#line 1260 "lib/Artifex/Parser.php"
#line 139 "lib/Artifex/Parser.y"
    function yy_r19(){
    $this->_retvalue = new Expr_If($this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + 0]->minor);
    $this->_retvalue->setChild($this->yystack[$this->yyidx + -1]->minor);
    }
#line 1266 "lib/Artifex/Parser.php"
#line 144 "lib/Artifex/Parser.y"
    function yy_r20(){ 
    $this->_retvalue = new Expr_If($this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + 0]->minor); 
    $this->_retvalue->setChild($this->yystack[$this->yyidx + -1]->minor);
    }
#line 1272 "lib/Artifex/Parser.php"
#line 148 "lib/Artifex/Parser.y"
    function yy_r21(){ 
    $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor; 
    }
#line 1277 "lib/Artifex/Parser.php"
#line 151 "lib/Artifex/Parser.y"
    function yy_r22(){ $this->_retvalue = NULL;     }
#line 1280 "lib/Artifex/Parser.php"
#line 155 "lib/Artifex/Parser.y"
    function yy_r23(){ $this->_retvalue = new Expr('not', $this->yystack[$this->yyidx + 0]->minor);     }
#line 1283 "lib/Artifex/Parser.php"
#line 156 "lib/Artifex/Parser.y"
    function yy_r24(){ $this->_retvalue = new Expr(strtolower(@$this->yystack[$this->yyidx + -1]->minor), $this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1286 "lib/Artifex/Parser.php"
#line 158 "lib/Artifex/Parser.y"
    function yy_r26(){ $this->_retvalue = new Expr(@$this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1289 "lib/Artifex/Parser.php"
#line 159 "lib/Artifex/Parser.y"
    function yy_r27(){ $this->_retvalue = new Expr(trim(@$this->yystack[$this->yyidx + -1]->minor), $this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1292 "lib/Artifex/Parser.php"
#line 162 "lib/Artifex/Parser.y"
    function yy_r30(){ $this->_retvalue = new Expr($this->yystack[$this->yyidx + -1]->minor);     }
#line 1295 "lib/Artifex/Parser.php"
#line 163 "lib/Artifex/Parser.y"
    function yy_r31(){ $this->_retvalue = new Concat($this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1298 "lib/Artifex/Parser.php"
#line 165 "lib/Artifex/Parser.y"
    function yy_r33(){  $this->_retvalue = new Term($this->yystack[$this->yyidx + 0]->minor);     }
#line 1301 "lib/Artifex/Parser.php"
#line 166 "lib/Artifex/Parser.y"
    function yy_r34(){ 
    $this->_retvalue = new Exec('var_export', array($this->yystack[$this->yyidx + 0]->minor, new Term(true))); 
    }
#line 1306 "lib/Artifex/Parser.php"
#line 173 "lib/Artifex/Parser.y"
    function yy_r36(){ $this->_retvalue = array_merge($this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1309 "lib/Artifex/Parser.php"
#line 174 "lib/Artifex/Parser.y"
    function yy_r37(){ $this->_retvalue = array($this->yystack[$this->yyidx + 0]->minor);     }
#line 1312 "lib/Artifex/Parser.php"
#line 179 "lib/Artifex/Parser.y"
    function yy_r39(){ $this->_retvalue = new Variable($this->yystack[$this->yyidx + 0]->minor);     }
#line 1315 "lib/Artifex/Parser.php"
#line 182 "lib/Artifex/Parser.y"
    function yy_r41(){ $this->_retvalue = $this->yystack[$this->yyidx + -3]->minor ; $this->_retvalue[] = $this->yystack[$this->yyidx + -1]->minor;     }
#line 1318 "lib/Artifex/Parser.php"
#line 183 "lib/Artifex/Parser.y"
    function yy_r42(){ $this->_retvalue = array($this->yystack[$this->yyidx + 0]->minor);    }
#line 1321 "lib/Artifex/Parser.php"
#line 187 "lib/Artifex/Parser.y"
    function yy_r43(){ $this->_retvalue = trim($this->yystack[$this->yyidx + 0]->minor);     }
#line 1324 "lib/Artifex/Parser.php"
#line 188 "lib/Artifex/Parser.y"
    function yy_r44(){ $this->_retvalue = TRUE;     }
#line 1327 "lib/Artifex/Parser.php"
#line 189 "lib/Artifex/Parser.y"
    function yy_r45(){ $this->_retvalue = FALSE;     }
#line 1330 "lib/Artifex/Parser.php"
#line 191 "lib/Artifex/Parser.y"
    function yy_r47(){ $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor + 0;     }
#line 1333 "lib/Artifex/Parser.php"
#line 192 "lib/Artifex/Parser.y"
    function yy_r48(){ $this->_retvalue = -1 * ($this->yystack[$this->yyidx + 0]->minor + 0);     }
#line 1336 "lib/Artifex/Parser.php"
#line 197 "lib/Artifex/Parser.y"
    function yy_r50(){ $this->_retvalue  = $this->yystack[$this->yyidx + -1]->minor;     }
#line 1339 "lib/Artifex/Parser.php"
#line 201 "lib/Artifex/Parser.y"
    function yy_r53(){ $this->_retvalue = array($this->yystack[$this->yyidx + -2]->minor => $this->yystack[$this->yyidx + 0]->minor);     }
#line 1342 "lib/Artifex/Parser.php"
#line 204 "lib/Artifex/Parser.y"
    function yy_r55(){ $this->_retvalue = $this->yystack[$this->yyidx + -2]->minor; $this->_retvalue[] = $this->yystack[$this->yyidx + 0]->minor;     }
#line 1345 "lib/Artifex/Parser.php"

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
#line 56 "lib/Artifex/Parser.y"

    $expect = array();
    foreach ($this->yy_get_expected_tokens($yymajor) as $token) {
        $expect[] = self::$yyTokenName[$token];
    }
    throw new Exception('Unexpected ' . $this->tokenName($yymajor) .  ' in line ' . $this->line
        . ' (' . $TOKEN . ') '
        . ' Expected: ' . print_r($expect, true));
#line 1467 "lib/Artifex/Parser.php"
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
