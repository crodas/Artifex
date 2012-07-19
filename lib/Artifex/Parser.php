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
    const T_WHITESPACE                   = 25;
    const T_FOREACH                      = 26;
    const T_LPARENT                      = 27;
    const T_AS                           = 28;
    const T_RPARENT                      = 29;
    const T_END                          = 30;
    const T_DOUBLE_ARROW                 = 31;
    const T_FUNCTION                     = 32;
    const T_ALPHA                        = 33;
    const T_IF                           = 34;
    const T_ELSE                         = 35;
    const T_AT                           = 36;
    const T_DOLLAR                       = 37;
    const T_SUBSCR_OPEN                  = 38;
    const T_SUBSCR_CLOSE                 = 39;
    const T_TRUE                         = 40;
    const T_FALSE                        = 41;
    const T_STRING                       = 42;
    const T_NUMBER                       = 43;
    const T_CURLY_CLOSE                  = 44;
    const T_COLON                        = 45;
    const YY_NO_ACTION = 172;
    const YY_ACCEPT_ACTION = 171;
    const YY_ERROR_ACTION = 170;

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
    const YY_SZ_ACTTAB = 442;
static public $yy_action = array(
 /*     0 */    13,   18,    3,   21,   21,   16,   21,   21,   21,   21,
 /*    10 */    21,   11,   17,   17,   14,   14,   14,    8,    8,   36,
 /*    20 */    13,   18,    9,   21,   21,   80,   21,   21,   21,   21,
 /*    30 */    21,   11,   17,   17,   14,   14,   14,    8,    8,   29,
 /*    40 */    13,   18,   78,   21,   21,   38,   21,   21,   21,   21,
 /*    50 */    21,   11,   17,   17,   14,   14,   14,    8,    8,   21,
 /*    60 */    21,   20,   21,   21,   21,   21,   21,   11,   17,   17,
 /*    70 */    14,   14,   14,    8,    8,  102,   70,   71,   89,   90,
 /*    80 */    13,   18,  109,   21,   21,   19,   21,   21,   21,   21,
 /*    90 */    21,   11,   17,   17,   14,   14,   14,    8,    8,  103,
 /*   100 */    13,   18,    5,   21,   21,   41,   21,   21,   21,   21,
 /*   110 */    21,   11,   17,   17,   14,   14,   14,    8,    8,   18,
 /*   120 */    30,   21,   21,   10,   21,   21,   21,   21,   21,   11,
 /*   130 */    17,   17,   14,   14,   14,    8,    8,   40,   87,   34,
 /*   140 */    93,   75,  171,   27,   22,  101,   86,   79,   76,   68,
 /*   150 */    77,   35,   32,   33,   32,    4,    1,  111,   88,   94,
 /*   160 */    81,    2,   67,  107,   83,   63,   46,   83,   87,   82,
 /*   170 */    93,   75,   66,   28,   60,   96,   37,   79,   76,   68,
 /*   180 */    31,   87,   32,   93,   75,   30,   15,    9,   91,   99,
 /*   190 */    79,   76,   68,   72,   87,   32,   93,   75,   30,    8,
 /*   200 */     8,  105,   69,   79,   76,   68,    2,   87,   32,   93,
 /*   210 */    75,    2,   25,   98,   84,  108,   79,   76,   68,   62,
 /*   220 */     4,   32,  111,   88,   94,   81,   32,   87,   76,   93,
 /*   230 */    75,   24,   32,    4,   39,   23,   79,   76,   68,   95,
 /*   240 */    12,   32,   17,   17,   14,   14,   14,    8,    8,    6,
 /*   250 */    74,   92,   73,   65,   64,  106,    7,   26,    3,  112,
 /*   260 */    97,  121,  121,  110,   92,   73,  121,   64,  106,  121,
 /*   270 */   121,  121,  112,   85,   67,  107,   83,  121,   49,   92,
 /*   280 */    73,   82,   64,  106,   57,  121,  121,  112,  121,   67,
 /*   290 */   107,   83,   58,   46,  121,  121,   82,   67,  107,   83,
 /*   300 */    61,   46,  121,  121,   82,   67,  107,   83,  100,   46,
 /*   310 */   121,  121,   82,  121,  121,   14,   14,   14,    8,    8,
 /*   320 */    67,  107,   83,  121,   44,  121,  121,   82,   67,  107,
 /*   330 */    83,  121,   59,  121,   83,   82,   67,  107,   83,   66,
 /*   340 */    51,  104,  121,   82,   67,  107,   83,  121,   50,  121,
 /*   350 */   121,   82,   67,  107,   83,  121,   45,  121,  121,   82,
 /*   360 */    67,  107,   83,  121,   56,  121,  121,   82,   67,  107,
 /*   370 */    83,  121,   47,  121,  121,   82,  121,   67,  107,   83,
 /*   380 */   121,   48,  121,  121,   82,   67,  107,   83,  121,   52,
 /*   390 */   121,  121,   82,   67,  107,   83,  121,   55,  121,  121,
 /*   400 */    82,   67,  107,   83,  121,   54,  121,  121,   82,   67,
 /*   410 */   107,   83,  121,   43,  121,  121,   82,   67,  107,   83,
 /*   420 */   121,   53,  121,  121,   82,   67,  107,   83,  121,   42,
 /*   430 */   121,  121,   82,  121,   67,  107,   83,  121,  113,  121,
 /*   440 */   121,   82,
    );
    static public $yy_lookahead = array(
 /*     0 */     4,    5,   27,    7,    8,   27,   10,   11,   12,   13,
 /*    10 */    14,   15,   16,   17,   18,   19,   20,   21,   22,   28,
 /*    20 */     4,    5,   38,    7,    8,   29,   10,   11,   12,   13,
 /*    30 */    14,   15,   16,   17,   18,   19,   20,   21,   22,    1,
 /*    40 */     4,    5,   33,    7,    8,   29,   10,   11,   12,   13,
 /*    50 */    14,   15,   16,   17,   18,   19,   20,   21,   22,    7,
 /*    60 */     8,    1,   10,   11,   12,   13,   14,   15,   16,   17,
 /*    70 */    18,   19,   20,   21,   22,   39,   51,   52,   53,   54,
 /*    80 */     4,    5,   44,    7,    8,   45,   10,   11,   12,   13,
 /*    90 */    14,   15,   16,   17,   18,   19,   20,   21,   22,   39,
 /*   100 */     4,    5,   27,    7,    8,   29,   10,   11,   12,   13,
 /*   110 */    14,   15,   16,   17,   18,   19,   20,   21,   22,    5,
 /*   120 */     3,    7,    8,    6,   10,   11,   12,   13,   14,   15,
 /*   130 */    16,   17,   18,   19,   20,   21,   22,   29,   23,   31,
 /*   140 */    25,   26,   47,   48,   27,   30,   24,   32,   33,   34,
 /*   150 */    33,    2,   37,   36,   37,   38,   27,   40,   41,   42,
 /*   160 */    43,    1,   52,   53,   54,   55,   56,   54,   23,   59,
 /*   170 */    25,   26,   59,   27,   61,   30,   29,   32,   33,   34,
 /*   180 */    35,   23,   37,   25,   26,    3,   27,   38,   30,   29,
 /*   190 */    32,   33,   34,   52,   23,   37,   25,   26,    3,   21,
 /*   200 */    22,   30,   60,   32,   33,   34,    1,   23,   37,   25,
 /*   210 */    26,    1,   48,   33,   30,   33,   32,   33,   34,   52,
 /*   220 */    38,   37,   40,   41,   42,   43,   37,   23,   33,   25,
 /*   230 */    26,   48,   37,   38,   29,   48,   32,   33,   34,   29,
 /*   240 */     9,   37,   16,   17,   18,   19,   20,   21,   22,   48,
 /*   250 */    34,   49,   50,   60,   52,   53,   48,   48,   27,   57,
 /*   260 */    58,   63,   63,   52,   49,   50,   63,   52,   53,   63,
 /*   270 */    63,   63,   57,   58,   52,   53,   54,   63,   56,   49,
 /*   280 */    50,   59,   52,   53,   62,   63,   63,   57,   63,   52,
 /*   290 */    53,   54,   55,   56,   63,   63,   59,   52,   53,   54,
 /*   300 */    55,   56,   63,   63,   59,   52,   53,   54,   55,   56,
 /*   310 */    63,   63,   59,   63,   63,   18,   19,   20,   21,   22,
 /*   320 */    52,   53,   54,   63,   56,   63,   63,   59,   52,   53,
 /*   330 */    54,   63,   56,   63,   54,   59,   52,   53,   54,   59,
 /*   340 */    56,   61,   63,   59,   52,   53,   54,   63,   56,   63,
 /*   350 */    63,   59,   52,   53,   54,   63,   56,   63,   63,   59,
 /*   360 */    52,   53,   54,   63,   56,   63,   63,   59,   52,   53,
 /*   370 */    54,   63,   56,   63,   63,   59,   63,   52,   53,   54,
 /*   380 */    63,   56,   63,   63,   59,   52,   53,   54,   63,   56,
 /*   390 */    63,   63,   59,   52,   53,   54,   63,   56,   63,   63,
 /*   400 */    59,   52,   53,   54,   63,   56,   63,   63,   59,   52,
 /*   410 */    53,   54,   63,   56,   63,   63,   59,   52,   53,   54,
 /*   420 */    63,   56,   63,   63,   59,   52,   53,   54,   63,   56,
 /*   430 */    63,   63,   59,   63,   52,   53,   54,   63,   56,   63,
 /*   440 */    63,   59,
);
    const YY_SHIFT_USE_DFLT = -26;
    const YY_SHIFT_MAX = 79;
    static public $yy_shift_ofst = array(
 /*     0 */   -26,  117,  117,  117,  117,  117,  145,  145,  117,  117,
 /*    10 */   117,  117,  117,  117,  117,  117,  117,  117,  117,  117,
 /*    20 */   117,  117,  117,  158,  171,  115,  184,  204,  195,  182,
 /*    30 */   182,  216,  180,  189,  189,  180,  189,  -26,  -26,  -26,
 /*    40 */   -26,  -26,   36,   -4,   76,   16,   96,   96,   96,   96,
 /*    50 */    96,  114,   52,   52,   52,  226,  297,   60,  160,  178,
 /*    60 */    38,  210,  108,  205,  231,  149,   40,  -25,  -22,  -16,
 /*    70 */    -9,  -25,  147,  122,  159,  146,  129,  129,   75,    9,
);
    const YY_REDUCE_USE_DFLT = -1;
    const YY_REDUCE_MAX = 41;
    static public $yy_reduce_ofst = array(
 /*     0 */    95,  237,  253,  245,  222,  110,  202,  215,  382,  373,
 /*    10 */   349,  341,  292,  284,  276,  268,  300,  308,  333,  325,
 /*    20 */   316,  365,  357,  230,  230,  230,  230,  230,   25,  280,
 /*    30 */   113,  209,  193,  211,  141,  142,  167,  187,  201,  183,
 /*    40 */   164,  208,
);
    static public $yyExpectedTokens = array(
        /* 0 */ array(),
        /* 1 */ array(3, 6, 27, 33, 36, 37, 38, 40, 41, 42, 43, ),
        /* 2 */ array(3, 6, 27, 33, 36, 37, 38, 40, 41, 42, 43, ),
        /* 3 */ array(3, 6, 27, 33, 36, 37, 38, 40, 41, 42, 43, ),
        /* 4 */ array(3, 6, 27, 33, 36, 37, 38, 40, 41, 42, 43, ),
        /* 5 */ array(3, 6, 27, 33, 36, 37, 38, 40, 41, 42, 43, ),
        /* 6 */ array(23, 25, 26, 30, 32, 33, 34, 35, 37, ),
        /* 7 */ array(23, 25, 26, 30, 32, 33, 34, 35, 37, ),
        /* 8 */ array(3, 6, 27, 33, 36, 37, 38, 40, 41, 42, 43, ),
        /* 9 */ array(3, 6, 27, 33, 36, 37, 38, 40, 41, 42, 43, ),
        /* 10 */ array(3, 6, 27, 33, 36, 37, 38, 40, 41, 42, 43, ),
        /* 11 */ array(3, 6, 27, 33, 36, 37, 38, 40, 41, 42, 43, ),
        /* 12 */ array(3, 6, 27, 33, 36, 37, 38, 40, 41, 42, 43, ),
        /* 13 */ array(3, 6, 27, 33, 36, 37, 38, 40, 41, 42, 43, ),
        /* 14 */ array(3, 6, 27, 33, 36, 37, 38, 40, 41, 42, 43, ),
        /* 15 */ array(3, 6, 27, 33, 36, 37, 38, 40, 41, 42, 43, ),
        /* 16 */ array(3, 6, 27, 33, 36, 37, 38, 40, 41, 42, 43, ),
        /* 17 */ array(3, 6, 27, 33, 36, 37, 38, 40, 41, 42, 43, ),
        /* 18 */ array(3, 6, 27, 33, 36, 37, 38, 40, 41, 42, 43, ),
        /* 19 */ array(3, 6, 27, 33, 36, 37, 38, 40, 41, 42, 43, ),
        /* 20 */ array(3, 6, 27, 33, 36, 37, 38, 40, 41, 42, 43, ),
        /* 21 */ array(3, 6, 27, 33, 36, 37, 38, 40, 41, 42, 43, ),
        /* 22 */ array(3, 6, 27, 33, 36, 37, 38, 40, 41, 42, 43, ),
        /* 23 */ array(23, 25, 26, 30, 32, 33, 34, 37, ),
        /* 24 */ array(23, 25, 26, 30, 32, 33, 34, 37, ),
        /* 25 */ array(23, 25, 26, 30, 32, 33, 34, 37, ),
        /* 26 */ array(23, 25, 26, 30, 32, 33, 34, 37, ),
        /* 27 */ array(23, 25, 26, 32, 33, 34, 37, ),
        /* 28 */ array(3, 33, 37, 38, ),
        /* 29 */ array(3, 33, 38, 40, 41, 42, 43, ),
        /* 30 */ array(3, 33, 38, 40, 41, 42, 43, ),
        /* 31 */ array(34, ),
        /* 32 */ array(33, ),
        /* 33 */ array(37, ),
        /* 34 */ array(37, ),
        /* 35 */ array(33, ),
        /* 36 */ array(37, ),
        /* 37 */ array(),
        /* 38 */ array(),
        /* 39 */ array(),
        /* 40 */ array(),
        /* 41 */ array(),
        /* 42 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 39, ),
        /* 43 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 29, ),
        /* 44 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 29, ),
        /* 45 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 29, ),
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
        /* 57 */ array(1, 39, ),
        /* 58 */ array(1, 29, ),
        /* 59 */ array(21, 22, ),
        /* 60 */ array(1, 44, ),
        /* 61 */ array(1, 29, ),
        /* 62 */ array(29, 31, ),
        /* 63 */ array(1, 29, ),
        /* 64 */ array(9, 27, ),
        /* 65 */ array(2, 38, ),
        /* 66 */ array(45, ),
        /* 67 */ array(27, ),
        /* 68 */ array(27, ),
        /* 69 */ array(38, ),
        /* 70 */ array(28, ),
        /* 71 */ array(27, ),
        /* 72 */ array(29, ),
        /* 73 */ array(24, ),
        /* 74 */ array(27, ),
        /* 75 */ array(27, ),
        /* 76 */ array(27, ),
        /* 77 */ array(27, ),
        /* 78 */ array(27, ),
        /* 79 */ array(33, ),
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
        /* 112 */ array(),
        /* 113 */ array(),
);
    static public $yy_default = array(
 /*     0 */   116,  151,  151,  151,  169,  151,  170,  170,  170,  170,
 /*    10 */   170,  170,  170,  170,  170,  170,  170,  170,  170,  170,
 /*    20 */   170,  170,  170,  170,  170,  170,  170,  114,  170,  166,
 /*    30 */   166,  116,  170,  170,  170,  170,  170,  116,  116,  116,
 /*    40 */   116,  116,  170,  170,  170,  170,  150,  167,  165,  168,
 /*    50 */   127,  137,  138,  140,  136,  144,  139,  170,  170,  141,
 /*    60 */   170,  170,  170,  170,  170,  152,  170,  145,  170,  153,
 /*    70 */   170,  123,  170,  118,  170,  170,  170,  156,  170,  170,
 /*    80 */   143,  160,  146,  161,  134,  133,  119,  117,  158,  124,
 /*    90 */   125,  122,  115,  120,  159,  130,  135,  132,  155,  129,
 /*   100 */   149,  121,  154,  163,  164,  126,  128,  148,  156,  162,
 /*   110 */   147,  157,  131,  142,
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
    const YYNSTATE = 114;
    const YYNRULE = 56;
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
  'T_NEW_LINE',    'T_WHITESPACE',  'T_FOREACH',     'T_LPARENT',   
  'T_AS',          'T_RPARENT',     'T_END',         'T_DOUBLE_ARROW',
  'T_FUNCTION',    'T_ALPHA',       'T_IF',          'T_ELSE',      
  'T_AT',          'T_DOLLAR',      'T_SUBSCR_OPEN',  'T_SUBSCR_CLOSE',
  'T_TRUE',        'T_FALSE',       'T_STRING',      'T_NUMBER',    
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
 /*   6 */ "line ::= T_WHITESPACE",
 /*   7 */ "code ::= T_FOREACH T_LPARENT foreach_source T_AS variable T_RPARENT body T_END",
 /*   8 */ "code ::= T_FOREACH T_LPARENT foreach_source T_AS variable T_DOUBLE_ARROW variable T_RPARENT body T_END",
 /*   9 */ "foreach_source ::= variable",
 /*  10 */ "foreach_source ::= fnc_call",
 /*  11 */ "foreach_source ::= json",
 /*  12 */ "code ::= T_FUNCTION T_ALPHA T_LPARENT args T_RPARENT body T_END",
 /*  13 */ "code ::= variable T_ASSIGN expr",
 /*  14 */ "code ::= fnc_call",
 /*  15 */ "fnc_call ::= T_ALPHA T_LPARENT args T_RPARENT",
 /*  16 */ "fnc_call ::= variable T_LPARENT args T_RPARENT",
 /*  17 */ "code ::= if",
 /*  18 */ "if ::= T_IF T_LPARENT expr T_RPARENT body else_if",
 /*  19 */ "else_if ::= T_ELSE T_IF T_LPARENT expr T_RPARENT body else_if",
 /*  20 */ "else_if ::= T_ELSE body T_END",
 /*  21 */ "else_if ::= T_END",
 /*  22 */ "expr ::= T_NOT expr",
 /*  23 */ "expr ::= expr T_AND expr",
 /*  24 */ "expr ::= expr T_OR expr",
 /*  25 */ "expr ::= expr T_PLUS|T_MINUS expr",
 /*  26 */ "expr ::= expr T_EQ|T_NE|T_GT|T_GE|T_LT|T_LE|T_IN expr",
 /*  27 */ "expr ::= expr T_TIMES|T_DIV|T_MOD expr",
 /*  28 */ "expr ::= expr T_BITWISE|T_PIPE expr",
 /*  29 */ "expr ::= T_LPARENT expr T_RPARENT",
 /*  30 */ "expr ::= expr T_DOT expr",
 /*  31 */ "expr ::= variable",
 /*  32 */ "expr ::= term",
 /*  33 */ "expr ::= T_AT variable",
 /*  34 */ "expr ::= fnc_call",
 /*  35 */ "args ::= args T_COMMA args",
 /*  36 */ "args ::= expr",
 /*  37 */ "args ::=",
 /*  38 */ "variable ::= T_DOLLAR var",
 /*  39 */ "var ::= var T_OBJ var",
 /*  40 */ "var ::= var T_SUBSCR_OPEN expr T_SUBSCR_CLOSE",
 /*  41 */ "var ::= T_ALPHA",
 /*  42 */ "term ::= T_ALPHA",
 /*  43 */ "term ::= T_TRUE",
 /*  44 */ "term ::= T_FALSE",
 /*  45 */ "term ::= T_STRING",
 /*  46 */ "term ::= T_NUMBER",
 /*  47 */ "term ::= json",
 /*  48 */ "json ::= T_CURLY_OPEN json_obj T_CURLY_CLOSE",
 /*  49 */ "json ::= T_SUBSCR_OPEN json_arr T_SUBSCR_CLOSE",
 /*  50 */ "json_obj ::= json_obj T_COMMA json_obj",
 /*  51 */ "json_obj ::= term T_COLON expr",
 /*  52 */ "json_obj ::=",
 /*  53 */ "json_arr ::= json_arr T_COMMA expr",
 /*  54 */ "json_arr ::= expr",
 /*  55 */ "json_arr ::=",
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
        37 => 2,
        52 => 2,
        55 => 2,
        3 => 3,
        4 => 4,
        9 => 4,
        10 => 4,
        14 => 4,
        17 => 4,
        31 => 4,
        34 => 4,
        45 => 4,
        47 => 4,
        5 => 5,
        49 => 5,
        6 => 6,
        7 => 7,
        8 => 8,
        11 => 11,
        12 => 12,
        13 => 13,
        15 => 15,
        16 => 15,
        18 => 18,
        19 => 19,
        20 => 20,
        21 => 21,
        22 => 22,
        23 => 23,
        24 => 23,
        25 => 25,
        27 => 25,
        28 => 25,
        26 => 26,
        29 => 29,
        30 => 30,
        32 => 32,
        33 => 33,
        35 => 35,
        39 => 35,
        50 => 35,
        36 => 36,
        54 => 36,
        38 => 38,
        40 => 40,
        41 => 41,
        42 => 42,
        43 => 43,
        44 => 44,
        46 => 46,
        48 => 48,
        51 => 51,
        53 => 53,
    );
    /* Beginning here are the reduction cases.  A typical example
    ** follows:
    **  #line <lineno> <grammarfile>
    **   function yy_r0($yymsp){ ... }           // User supplied code
    **  #line <lineno> <thisfile>
    */
#line 80 "lib/Artifex/Parser.y"
    function yy_r0(){ $this->body = $this->yystack[$this->yyidx + 0]->minor;     }
#line 1193 "lib/Artifex/Parser.php"
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
#line 1204 "lib/Artifex/Parser.php"
#line 91 "lib/Artifex/Parser.y"
    function yy_r2(){ $this->_retvalue = array();     }
#line 1207 "lib/Artifex/Parser.php"
#line 93 "lib/Artifex/Parser.y"
    function yy_r3(){ $this->_retvalue = new RawString($this->yystack[$this->yyidx + 0]->minor);     }
#line 1210 "lib/Artifex/Parser.php"
#line 94 "lib/Artifex/Parser.y"
    function yy_r4(){ $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;     }
#line 1213 "lib/Artifex/Parser.php"
#line 95 "lib/Artifex/Parser.y"
    function yy_r5(){ $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor;     }
#line 1216 "lib/Artifex/Parser.php"
#line 96 "lib/Artifex/Parser.y"
    function yy_r6(){ $this->_retvalue = new Whitespace($this->yystack[$this->yyidx + 0]->minor);     }
#line 1219 "lib/Artifex/Parser.php"
#line 99 "lib/Artifex/Parser.y"
    function yy_r7(){ 
    $this->_retvalue = new Expr_Foreach($this->yystack[$this->yyidx + -5]->minor, $this->yystack[$this->yyidx + -3]->minor, NULL, $this->yystack[$this->yyidx + -1]->minor); 
    }
#line 1224 "lib/Artifex/Parser.php"
#line 103 "lib/Artifex/Parser.y"
    function yy_r8(){
    $this->_retvalue = new Expr_Foreach($this->yystack[$this->yyidx + -7]->minor, $this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -5]->minor, $this->yystack[$this->yyidx + -1]->minor);
    }
#line 1229 "lib/Artifex/Parser.php"
#line 109 "lib/Artifex/Parser.y"
    function yy_r11(){ $this->_retvalue = new Term($this->yystack[$this->yyidx + 0]->minor);     }
#line 1232 "lib/Artifex/Parser.php"
#line 113 "lib/Artifex/Parser.y"
    function yy_r12(){
    $this->_retvalue = new DefFunction($this->yystack[$this->yyidx + -5]->minor, $this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -1]->minor);
    }
#line 1237 "lib/Artifex/Parser.php"
#line 119 "lib/Artifex/Parser.y"
    function yy_r13(){ $this->_retvalue = new Assign($this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1240 "lib/Artifex/Parser.php"
#line 124 "lib/Artifex/Parser.y"
    function yy_r15(){ 
    $this->_retvalue = new Exec($this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -1]->minor);
    }
#line 1245 "lib/Artifex/Parser.php"
#line 135 "lib/Artifex/Parser.y"
    function yy_r18(){
    $this->_retvalue = new Expr_If($this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + 0]->minor);
    }
#line 1250 "lib/Artifex/Parser.php"
#line 139 "lib/Artifex/Parser.y"
    function yy_r19(){ 
    $this->_retvalue = new Expr_If($this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + 0]->minor); 
    }
#line 1255 "lib/Artifex/Parser.php"
#line 142 "lib/Artifex/Parser.y"
    function yy_r20(){ 
    $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor; 
    }
#line 1260 "lib/Artifex/Parser.php"
#line 145 "lib/Artifex/Parser.y"
    function yy_r21(){ $this->_retvalue = NULL;     }
#line 1263 "lib/Artifex/Parser.php"
#line 149 "lib/Artifex/Parser.y"
    function yy_r22(){ $this->_retvalue = new Expr('not', $this->yystack[$this->yyidx + 0]->minor);     }
#line 1266 "lib/Artifex/Parser.php"
#line 150 "lib/Artifex/Parser.y"
    function yy_r23(){ $this->_retvalue = new Expr(strtolower(@$this->yystack[$this->yyidx + -1]->minor), $this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1269 "lib/Artifex/Parser.php"
#line 152 "lib/Artifex/Parser.y"
    function yy_r25(){ $this->_retvalue = new Expr(@$this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1272 "lib/Artifex/Parser.php"
#line 153 "lib/Artifex/Parser.y"
    function yy_r26(){ $this->_retvalue = new Expr(trim(@$this->yystack[$this->yyidx + -1]->minor), $this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1275 "lib/Artifex/Parser.php"
#line 156 "lib/Artifex/Parser.y"
    function yy_r29(){ $this->_retvalue = new Expr($this->yystack[$this->yyidx + -1]->minor);     }
#line 1278 "lib/Artifex/Parser.php"
#line 157 "lib/Artifex/Parser.y"
    function yy_r30(){ $this->_retvalue = new Concat($this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1281 "lib/Artifex/Parser.php"
#line 159 "lib/Artifex/Parser.y"
    function yy_r32(){  $this->_retvalue = new Term($this->yystack[$this->yyidx + 0]->minor);     }
#line 1284 "lib/Artifex/Parser.php"
#line 160 "lib/Artifex/Parser.y"
    function yy_r33(){ 
    $this->_retvalue = new Exec('var_export', array($this->yystack[$this->yyidx + 0]->minor, new Term(true))); 
    }
#line 1289 "lib/Artifex/Parser.php"
#line 167 "lib/Artifex/Parser.y"
    function yy_r35(){ $this->_retvalue = array_merge($this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1292 "lib/Artifex/Parser.php"
#line 168 "lib/Artifex/Parser.y"
    function yy_r36(){ $this->_retvalue = array($this->yystack[$this->yyidx + 0]->minor);     }
#line 1295 "lib/Artifex/Parser.php"
#line 173 "lib/Artifex/Parser.y"
    function yy_r38(){ $this->_retvalue = new Variable($this->yystack[$this->yyidx + 0]->minor);     }
#line 1298 "lib/Artifex/Parser.php"
#line 176 "lib/Artifex/Parser.y"
    function yy_r40(){ $this->_retvalue = $this->yystack[$this->yyidx + -3]->minor ; $this->_retvalue[] = $this->yystack[$this->yyidx + -1]->minor;     }
#line 1301 "lib/Artifex/Parser.php"
#line 177 "lib/Artifex/Parser.y"
    function yy_r41(){ $this->_retvalue = array($this->yystack[$this->yyidx + 0]->minor);    }
#line 1304 "lib/Artifex/Parser.php"
#line 181 "lib/Artifex/Parser.y"
    function yy_r42(){ $this->_retvalue = trim($this->yystack[$this->yyidx + 0]->minor);     }
#line 1307 "lib/Artifex/Parser.php"
#line 182 "lib/Artifex/Parser.y"
    function yy_r43(){ $this->_retvalue = TRUE;     }
#line 1310 "lib/Artifex/Parser.php"
#line 183 "lib/Artifex/Parser.y"
    function yy_r44(){ $this->_retvalue = FALSE;     }
#line 1313 "lib/Artifex/Parser.php"
#line 185 "lib/Artifex/Parser.y"
    function yy_r46(){ $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor + 0;     }
#line 1316 "lib/Artifex/Parser.php"
#line 190 "lib/Artifex/Parser.y"
    function yy_r48(){ $this->_retvalue  = $this->yystack[$this->yyidx + -1]->minor;     }
#line 1319 "lib/Artifex/Parser.php"
#line 194 "lib/Artifex/Parser.y"
    function yy_r51(){ $this->_retvalue = array($this->yystack[$this->yyidx + -2]->minor => $this->yystack[$this->yyidx + 0]->minor);     }
#line 1322 "lib/Artifex/Parser.php"
#line 197 "lib/Artifex/Parser.y"
    function yy_r53(){ $this->_retvalue = $this->yystack[$this->yyidx + -2]->minor; $this->_retvalue[] = $this->yystack[$this->yyidx + 0]->minor;     }
#line 1325 "lib/Artifex/Parser.php"

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
#line 1447 "lib/Artifex/Parser.php"
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
