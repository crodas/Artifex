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
    const YY_NO_ACTION = 175;
    const YY_ACCEPT_ACTION = 174;
    const YY_ERROR_ACTION = 173;

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
    const YY_SZ_ACTTAB = 436;
static public $yy_action = array(
 /*     0 */    12,   14,   30,    9,    9,   28,    9,    9,    9,    9,
 /*    10 */     9,   18,   15,   15,   16,   16,   16,   21,   21,    9,
 /*    20 */     9,   10,    9,    9,    9,    9,    9,   18,   15,   15,
 /*    30 */    16,   16,   16,   21,   21,  112,   73,   67,   88,   89,
 /*    40 */    12,   14,    1,    9,    9,  115,    9,    9,    9,    9,
 /*    50 */     9,   18,   15,   15,   16,   16,   16,   21,   21,  101,
 /*    60 */    12,   14,   35,    9,    9,   41,    9,    9,    9,    9,
 /*    70 */     9,   18,   15,   15,   16,   16,   16,   21,   21,    5,
 /*    80 */    12,   14,  104,    9,    9,  108,    9,    9,    9,    9,
 /*    90 */     9,   18,   15,   15,   16,   16,   16,   21,   21,    4,
 /*   100 */    12,   14,   93,    9,    9,   39,    9,    9,    9,    9,
 /*   110 */     9,   18,   15,   15,   16,   16,   16,   21,   21,   14,
 /*   120 */     3,    9,    9,   32,    9,    9,    9,    9,    9,   18,
 /*   130 */    15,   15,   16,   16,   16,   21,   21,   29,  174,   27,
 /*   140 */    20,   75,   38,   91,   36,   92,   78,   66,   40,   29,
 /*   150 */    86,   77,   80,   70,   68,   33,  114,   31,   59,   11,
 /*   160 */    31,   19,   76,  105,  103,   62,   48,   79,  106,  110,
 /*   170 */    34,   31,    2,   25,   99,   98,   96,  100,   91,   70,
 /*   180 */    92,   78,   29,   31,    2,   90,   69,   80,   70,   68,
 /*   190 */   103,   91,   31,   92,   78,   71,   77,   97,   85,   65,
 /*   200 */    80,   70,   68,   72,   91,   31,   92,   78,    3,    3,
 /*   210 */    23,   81,  102,   80,   70,   68,   22,    2,   31,   99,
 /*   220 */    98,   96,  100,    6,   91,  103,   92,   78,    8,   11,
 /*   230 */    71,   82,   64,   80,   70,   68,   95,   83,   31,   15,
 /*   240 */    15,   16,   16,   16,   21,   21,    5,   37,   91,   17,
 /*   250 */    92,   78,   87,   74,   24,   57,  111,   80,   70,   68,
 /*   260 */   107,   84,   31,    7,   76,  105,  103,   58,   48,   13,
 /*   270 */   122,  110,   76,  105,  103,   61,   48,   21,   21,  110,
 /*   280 */    76,  105,  103,  113,   48,   26,  122,  110,   76,  105,
 /*   290 */   103,  122,   49,   87,   74,  110,   57,  111,   60,  122,
 /*   300 */   122,  107,   94,   76,  105,  103,  122,   53,  122,  122,
 /*   310 */   110,   76,  105,  103,  122,   42,  122,  122,  110,   76,
 /*   320 */   105,  103,  122,   52,  122,  122,  110,   76,  105,  103,
 /*   330 */   122,   44,   87,   74,  110,   57,  111,  122,  122,  122,
 /*   340 */   107,  122,   16,   16,   16,   21,   21,   76,  105,  103,
 /*   350 */   122,   51,  122,  122,  110,   76,  105,  103,  122,   47,
 /*   360 */   122,  122,  110,   76,  105,  103,  122,  109,  122,  122,
 /*   370 */   110,   76,  105,  103,  122,   56,  122,  122,  110,   76,
 /*   380 */   105,  103,  122,   43,  122,  122,  110,   76,  105,  103,
 /*   390 */   122,   46,  122,  122,  110,   76,  105,  103,  122,   50,
 /*   400 */   122,  122,  110,  122,   76,  105,  103,  122,   55,  122,
 /*   410 */   122,  110,   76,  105,  103,  122,   45,  122,  122,  110,
 /*   420 */    76,  105,  103,  122,   54,  122,  122,  110,   76,  105,
 /*   430 */   103,  122,   63,  122,  122,  110,
    );
    static public $yy_lookahead = array(
 /*     0 */     4,    5,    1,    7,    8,   27,   10,   11,   12,   13,
 /*    10 */    14,   15,   16,   17,   18,   19,   20,   21,   22,    7,
 /*    20 */     8,    1,   10,   11,   12,   13,   14,   15,   16,   17,
 /*    30 */    18,   19,   20,   21,   22,   39,   51,   52,   53,   54,
 /*    40 */     4,    5,   27,    7,    8,   44,   10,   11,   12,   13,
 /*    50 */    14,   15,   16,   17,   18,   19,   20,   21,   22,   39,
 /*    60 */     4,    5,   28,    7,    8,   29,   10,   11,   12,   13,
 /*    70 */    14,   15,   16,   17,   18,   19,   20,   21,   22,   27,
 /*    80 */     4,    5,   43,    7,    8,   29,   10,   11,   12,   13,
 /*    90 */    14,   15,   16,   17,   18,   19,   20,   21,   22,   27,
 /*   100 */     4,    5,   24,    7,    8,   29,   10,   11,   12,   13,
 /*   110 */    14,   15,   16,   17,   18,   19,   20,   21,   22,    5,
 /*   120 */     1,    7,    8,    2,   10,   11,   12,   13,   14,   15,
 /*   130 */    16,   17,   18,   19,   20,   21,   22,    3,   47,   48,
 /*   140 */     6,   33,   29,   23,   31,   25,   26,   52,   29,    3,
 /*   150 */    30,   17,   32,   33,   34,   35,   52,   37,   52,   38,
 /*   160 */    37,   27,   52,   53,   54,   55,   56,   33,   33,   59,
 /*   170 */    36,   37,   38,   48,   40,   41,   42,   43,   23,   33,
 /*   180 */    25,   26,    3,   37,   38,   30,   34,   32,   33,   34,
 /*   190 */    54,   23,   37,   25,   26,   59,   17,   61,   30,   60,
 /*   200 */    32,   33,   34,   60,   23,   37,   25,   26,    1,    1,
 /*   210 */    48,   30,   33,   32,   33,   34,   27,   38,   37,   40,
 /*   220 */    41,   42,   43,   48,   23,   54,   25,   26,    9,   38,
 /*   230 */    59,   30,   61,   32,   33,   34,   29,   29,   37,   16,
 /*   240 */    17,   18,   19,   20,   21,   22,   27,   29,   23,   27,
 /*   250 */    25,   26,   49,   50,   48,   52,   53,   32,   33,   34,
 /*   260 */    57,   58,   37,   48,   52,   53,   54,   55,   56,   45,
 /*   270 */    63,   59,   52,   53,   54,   55,   56,   21,   22,   59,
 /*   280 */    52,   53,   54,   55,   56,   48,   63,   59,   52,   53,
 /*   290 */    54,   63,   56,   49,   50,   59,   52,   53,   62,   63,
 /*   300 */    63,   57,   58,   52,   53,   54,   63,   56,   63,   63,
 /*   310 */    59,   52,   53,   54,   63,   56,   63,   63,   59,   52,
 /*   320 */    53,   54,   63,   56,   63,   63,   59,   52,   53,   54,
 /*   330 */    63,   56,   49,   50,   59,   52,   53,   63,   63,   63,
 /*   340 */    57,   63,   18,   19,   20,   21,   22,   52,   53,   54,
 /*   350 */    63,   56,   63,   63,   59,   52,   53,   54,   63,   56,
 /*   360 */    63,   63,   59,   52,   53,   54,   63,   56,   63,   63,
 /*   370 */    59,   52,   53,   54,   63,   56,   63,   63,   59,   52,
 /*   380 */    53,   54,   63,   56,   63,   63,   59,   52,   53,   54,
 /*   390 */    63,   56,   63,   63,   59,   52,   53,   54,   63,   56,
 /*   400 */    63,   63,   59,   63,   52,   53,   54,   63,   56,   63,
 /*   410 */    63,   59,   52,   53,   54,   63,   56,   63,   63,   59,
 /*   420 */    52,   53,   54,   63,   56,   63,   63,   59,   52,   53,
 /*   430 */    54,   63,   56,   63,   63,   59,
);
    const YY_SHIFT_USE_DFLT = -23;
    const YY_SHIFT_MAX = 80;
    static public $yy_shift_ofst = array(
 /*     0 */   -23,  134,  134,  134,  134,  134,  120,  120,  134,  134,
 /*    10 */   134,  134,  134,  134,  134,  134,  134,  134,  134,  134,
 /*    20 */   134,  134,  134,  155,  168,  201,  181,  225,  146,  179,
 /*    30 */   179,  135,  135,  152,  123,  123,  123,  -23,  -23,  -23,
 /*    40 */   -23,  -23,   56,   76,   -4,   36,   96,   96,   96,   96,
 /*    50 */    96,  114,   12,   12,   12,  223,  324,  219,  119,  113,
 /*    60 */    20,  208,  207,  256,    1,  121,  218,   52,  222,  189,
 /*    70 */    72,  224,  191,   34,   78,   15,   52,   39,  -22,   72,
 /*    80 */   108,
);
    const YY_REDUCE_USE_DFLT = -16;
    const YY_REDUCE_MAX = 41;
    static public $yy_reduce_ofst = array(
 /*     0 */    91,  212,  236,  228,  220,  110,  203,  244,  303,  368,
 /*    10 */   343,  275,  295,  335,  251,  319,  376,  360,  352,  259,
 /*    20 */   267,  311,  327,  283,  283,  283,  283,  283,  -15,  171,
 /*    30 */   136,  139,  143,  125,  104,  106,   95,  162,  237,  215,
 /*    40 */   206,  175,
);
    static public $yyExpectedTokens = array(
        /* 0 */ array(),
        /* 1 */ array(3, 6, 17, 27, 33, 36, 37, 38, 40, 41, 42, 43, ),
        /* 2 */ array(3, 6, 17, 27, 33, 36, 37, 38, 40, 41, 42, 43, ),
        /* 3 */ array(3, 6, 17, 27, 33, 36, 37, 38, 40, 41, 42, 43, ),
        /* 4 */ array(3, 6, 17, 27, 33, 36, 37, 38, 40, 41, 42, 43, ),
        /* 5 */ array(3, 6, 17, 27, 33, 36, 37, 38, 40, 41, 42, 43, ),
        /* 6 */ array(23, 25, 26, 30, 32, 33, 34, 35, 37, ),
        /* 7 */ array(23, 25, 26, 30, 32, 33, 34, 35, 37, ),
        /* 8 */ array(3, 6, 17, 27, 33, 36, 37, 38, 40, 41, 42, 43, ),
        /* 9 */ array(3, 6, 17, 27, 33, 36, 37, 38, 40, 41, 42, 43, ),
        /* 10 */ array(3, 6, 17, 27, 33, 36, 37, 38, 40, 41, 42, 43, ),
        /* 11 */ array(3, 6, 17, 27, 33, 36, 37, 38, 40, 41, 42, 43, ),
        /* 12 */ array(3, 6, 17, 27, 33, 36, 37, 38, 40, 41, 42, 43, ),
        /* 13 */ array(3, 6, 17, 27, 33, 36, 37, 38, 40, 41, 42, 43, ),
        /* 14 */ array(3, 6, 17, 27, 33, 36, 37, 38, 40, 41, 42, 43, ),
        /* 15 */ array(3, 6, 17, 27, 33, 36, 37, 38, 40, 41, 42, 43, ),
        /* 16 */ array(3, 6, 17, 27, 33, 36, 37, 38, 40, 41, 42, 43, ),
        /* 17 */ array(3, 6, 17, 27, 33, 36, 37, 38, 40, 41, 42, 43, ),
        /* 18 */ array(3, 6, 17, 27, 33, 36, 37, 38, 40, 41, 42, 43, ),
        /* 19 */ array(3, 6, 17, 27, 33, 36, 37, 38, 40, 41, 42, 43, ),
        /* 20 */ array(3, 6, 17, 27, 33, 36, 37, 38, 40, 41, 42, 43, ),
        /* 21 */ array(3, 6, 17, 27, 33, 36, 37, 38, 40, 41, 42, 43, ),
        /* 22 */ array(3, 6, 17, 27, 33, 36, 37, 38, 40, 41, 42, 43, ),
        /* 23 */ array(23, 25, 26, 30, 32, 33, 34, 37, ),
        /* 24 */ array(23, 25, 26, 30, 32, 33, 34, 37, ),
        /* 25 */ array(23, 25, 26, 30, 32, 33, 34, 37, ),
        /* 26 */ array(23, 25, 26, 30, 32, 33, 34, 37, ),
        /* 27 */ array(23, 25, 26, 32, 33, 34, 37, ),
        /* 28 */ array(3, 33, 37, 38, ),
        /* 29 */ array(3, 17, 33, 38, 40, 41, 42, 43, ),
        /* 30 */ array(3, 17, 33, 38, 40, 41, 42, 43, ),
        /* 31 */ array(33, ),
        /* 32 */ array(33, ),
        /* 33 */ array(34, ),
        /* 34 */ array(37, ),
        /* 35 */ array(37, ),
        /* 36 */ array(37, ),
        /* 37 */ array(),
        /* 38 */ array(),
        /* 39 */ array(),
        /* 40 */ array(),
        /* 41 */ array(),
        /* 42 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 29, ),
        /* 43 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 29, ),
        /* 44 */ array(4, 5, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 39, ),
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
        /* 57 */ array(9, 27, ),
        /* 58 */ array(1, 29, ),
        /* 59 */ array(29, 31, ),
        /* 60 */ array(1, 39, ),
        /* 61 */ array(1, 29, ),
        /* 62 */ array(1, 29, ),
        /* 63 */ array(21, 22, ),
        /* 64 */ array(1, 44, ),
        /* 65 */ array(2, 38, ),
        /* 66 */ array(29, ),
        /* 67 */ array(27, ),
        /* 68 */ array(27, ),
        /* 69 */ array(27, ),
        /* 70 */ array(27, ),
        /* 71 */ array(45, ),
        /* 72 */ array(38, ),
        /* 73 */ array(28, ),
        /* 74 */ array(24, ),
        /* 75 */ array(27, ),
        /* 76 */ array(27, ),
        /* 77 */ array(43, ),
        /* 78 */ array(27, ),
        /* 79 */ array(27, ),
        /* 80 */ array(33, ),
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
);
    static public $yy_default = array(
 /*     0 */   118,  153,  172,  153,  153,  153,  173,  173,  173,  173,
 /*    10 */   173,  173,  173,  173,  173,  173,  173,  173,  173,  173,
 /*    20 */   173,  173,  173,  173,  173,  173,  173,  116,  173,  169,
 /*    30 */   169,  173,  173,  118,  173,  173,  173,  118,  118,  118,
 /*    40 */   118,  118,  173,  173,  173,  173,  168,  129,  152,  171,
 /*    50 */   170,  139,  138,  140,  142,  146,  141,  173,  173,  173,
 /*    60 */   173,  173,  173,  143,  173,  154,  173,  125,  173,  173,
 /*    70 */   173,  173,  155,  173,  120,  173,  147,  173,  173,  158,
 /*    80 */   173,  123,  136,  131,  134,  128,  137,  117,  126,  127,
 /*    90 */   124,  119,  122,  121,  135,  132,  161,  167,  160,  159,
 /*   100 */   162,  166,  158,  164,  163,  150,  157,  133,  145,  144,
 /*   110 */   148,  130,  156,  151,  149,  165,
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
    const YYNSTATE = 116;
    const YYNRULE = 57;
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
 /*  47 */ "term ::= T_MINUS T_NUMBER",
 /*  48 */ "term ::= json",
 /*  49 */ "json ::= T_CURLY_OPEN json_obj T_CURLY_CLOSE",
 /*  50 */ "json ::= T_SUBSCR_OPEN json_arr T_SUBSCR_CLOSE",
 /*  51 */ "json_obj ::= json_obj T_COMMA json_obj",
 /*  52 */ "json_obj ::= term T_COLON expr",
 /*  53 */ "json_obj ::=",
 /*  54 */ "json_arr ::= json_arr T_COMMA expr",
 /*  55 */ "json_arr ::= expr",
 /*  56 */ "json_arr ::=",
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
        37 => 2,
        53 => 2,
        56 => 2,
        3 => 3,
        4 => 4,
        9 => 4,
        10 => 4,
        14 => 4,
        17 => 4,
        31 => 4,
        34 => 4,
        45 => 4,
        48 => 4,
        5 => 5,
        50 => 5,
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
        51 => 35,
        36 => 36,
        55 => 36,
        38 => 38,
        40 => 40,
        41 => 41,
        42 => 42,
        43 => 43,
        44 => 44,
        46 => 46,
        47 => 47,
        49 => 49,
        52 => 52,
        54 => 54,
    );
    /* Beginning here are the reduction cases.  A typical example
    ** follows:
    **  #line <lineno> <grammarfile>
    **   function yy_r0($yymsp){ ... }           // User supplied code
    **  #line <lineno> <thisfile>
    */
#line 80 "lib/Artifex/Parser.y"
    function yy_r0(){ $this->body = $this->yystack[$this->yyidx + 0]->minor;     }
#line 1197 "lib/Artifex/Parser.php"
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
#line 1208 "lib/Artifex/Parser.php"
#line 91 "lib/Artifex/Parser.y"
    function yy_r2(){ $this->_retvalue = array();     }
#line 1211 "lib/Artifex/Parser.php"
#line 93 "lib/Artifex/Parser.y"
    function yy_r3(){ $this->_retvalue = new RawString($this->yystack[$this->yyidx + 0]->minor);     }
#line 1214 "lib/Artifex/Parser.php"
#line 94 "lib/Artifex/Parser.y"
    function yy_r4(){ $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;     }
#line 1217 "lib/Artifex/Parser.php"
#line 95 "lib/Artifex/Parser.y"
    function yy_r5(){ $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor;     }
#line 1220 "lib/Artifex/Parser.php"
#line 96 "lib/Artifex/Parser.y"
    function yy_r6(){ $this->_retvalue = new Whitespace($this->yystack[$this->yyidx + 0]->minor);     }
#line 1223 "lib/Artifex/Parser.php"
#line 99 "lib/Artifex/Parser.y"
    function yy_r7(){ 
    $this->_retvalue = new Expr_Foreach($this->yystack[$this->yyidx + -5]->minor, $this->yystack[$this->yyidx + -3]->minor, NULL, $this->yystack[$this->yyidx + -1]->minor); 
    $this->_retvalue->setChild($this->yystack[$this->yyidx + -1]->minor);
    }
#line 1229 "lib/Artifex/Parser.php"
#line 104 "lib/Artifex/Parser.y"
    function yy_r8(){
    $this->_retvalue = new Expr_Foreach($this->yystack[$this->yyidx + -7]->minor, $this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -5]->minor, $this->yystack[$this->yyidx + -1]->minor);
    $this->_retvalue->setChild($this->yystack[$this->yyidx + -1]->minor);
    }
#line 1235 "lib/Artifex/Parser.php"
#line 111 "lib/Artifex/Parser.y"
    function yy_r11(){ $this->_retvalue = new Term($this->yystack[$this->yyidx + 0]->minor);     }
#line 1238 "lib/Artifex/Parser.php"
#line 115 "lib/Artifex/Parser.y"
    function yy_r12(){
    $this->_retvalue = new DefFunction($this->yystack[$this->yyidx + -5]->minor, $this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -1]->minor);
    $this->_retvalue->setChild($this->yystack[$this->yyidx + -1]->minor);
    }
#line 1244 "lib/Artifex/Parser.php"
#line 122 "lib/Artifex/Parser.y"
    function yy_r13(){ $this->_retvalue = new Assign($this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1247 "lib/Artifex/Parser.php"
#line 127 "lib/Artifex/Parser.y"
    function yy_r15(){ 
    $this->_retvalue = new Exec($this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -1]->minor);
    }
#line 1252 "lib/Artifex/Parser.php"
#line 138 "lib/Artifex/Parser.y"
    function yy_r18(){
    $this->_retvalue = new Expr_If($this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + 0]->minor);
    $this->_retvalue->setChild($this->yystack[$this->yyidx + -1]->minor);
    }
#line 1258 "lib/Artifex/Parser.php"
#line 143 "lib/Artifex/Parser.y"
    function yy_r19(){ 
    $this->_retvalue = new Expr_If($this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + 0]->minor); 
    $this->_retvalue->setChild($this->yystack[$this->yyidx + -1]->minor);
    }
#line 1264 "lib/Artifex/Parser.php"
#line 147 "lib/Artifex/Parser.y"
    function yy_r20(){ 
    $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor; 
    }
#line 1269 "lib/Artifex/Parser.php"
#line 150 "lib/Artifex/Parser.y"
    function yy_r21(){ $this->_retvalue = NULL;     }
#line 1272 "lib/Artifex/Parser.php"
#line 154 "lib/Artifex/Parser.y"
    function yy_r22(){ $this->_retvalue = new Expr('not', $this->yystack[$this->yyidx + 0]->minor);     }
#line 1275 "lib/Artifex/Parser.php"
#line 155 "lib/Artifex/Parser.y"
    function yy_r23(){ $this->_retvalue = new Expr(strtolower(@$this->yystack[$this->yyidx + -1]->minor), $this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1278 "lib/Artifex/Parser.php"
#line 157 "lib/Artifex/Parser.y"
    function yy_r25(){ $this->_retvalue = new Expr(@$this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1281 "lib/Artifex/Parser.php"
#line 158 "lib/Artifex/Parser.y"
    function yy_r26(){ $this->_retvalue = new Expr(trim(@$this->yystack[$this->yyidx + -1]->minor), $this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1284 "lib/Artifex/Parser.php"
#line 161 "lib/Artifex/Parser.y"
    function yy_r29(){ $this->_retvalue = new Expr($this->yystack[$this->yyidx + -1]->minor);     }
#line 1287 "lib/Artifex/Parser.php"
#line 162 "lib/Artifex/Parser.y"
    function yy_r30(){ $this->_retvalue = new Concat($this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1290 "lib/Artifex/Parser.php"
#line 164 "lib/Artifex/Parser.y"
    function yy_r32(){  $this->_retvalue = new Term($this->yystack[$this->yyidx + 0]->minor);     }
#line 1293 "lib/Artifex/Parser.php"
#line 165 "lib/Artifex/Parser.y"
    function yy_r33(){ 
    $this->_retvalue = new Exec('var_export', array($this->yystack[$this->yyidx + 0]->minor, new Term(true))); 
    }
#line 1298 "lib/Artifex/Parser.php"
#line 172 "lib/Artifex/Parser.y"
    function yy_r35(){ $this->_retvalue = array_merge($this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1301 "lib/Artifex/Parser.php"
#line 173 "lib/Artifex/Parser.y"
    function yy_r36(){ $this->_retvalue = array($this->yystack[$this->yyidx + 0]->minor);     }
#line 1304 "lib/Artifex/Parser.php"
#line 178 "lib/Artifex/Parser.y"
    function yy_r38(){ $this->_retvalue = new Variable($this->yystack[$this->yyidx + 0]->minor);     }
#line 1307 "lib/Artifex/Parser.php"
#line 181 "lib/Artifex/Parser.y"
    function yy_r40(){ $this->_retvalue = $this->yystack[$this->yyidx + -3]->minor ; $this->_retvalue[] = $this->yystack[$this->yyidx + -1]->minor;     }
#line 1310 "lib/Artifex/Parser.php"
#line 182 "lib/Artifex/Parser.y"
    function yy_r41(){ $this->_retvalue = array($this->yystack[$this->yyidx + 0]->minor);    }
#line 1313 "lib/Artifex/Parser.php"
#line 186 "lib/Artifex/Parser.y"
    function yy_r42(){ $this->_retvalue = trim($this->yystack[$this->yyidx + 0]->minor);     }
#line 1316 "lib/Artifex/Parser.php"
#line 187 "lib/Artifex/Parser.y"
    function yy_r43(){ $this->_retvalue = TRUE;     }
#line 1319 "lib/Artifex/Parser.php"
#line 188 "lib/Artifex/Parser.y"
    function yy_r44(){ $this->_retvalue = FALSE;     }
#line 1322 "lib/Artifex/Parser.php"
#line 190 "lib/Artifex/Parser.y"
    function yy_r46(){ $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor + 0;     }
#line 1325 "lib/Artifex/Parser.php"
#line 191 "lib/Artifex/Parser.y"
    function yy_r47(){ $this->_retvalue = -1 * ($this->yystack[$this->yyidx + 0]->minor + 0);     }
#line 1328 "lib/Artifex/Parser.php"
#line 196 "lib/Artifex/Parser.y"
    function yy_r49(){ $this->_retvalue  = $this->yystack[$this->yyidx + -1]->minor;     }
#line 1331 "lib/Artifex/Parser.php"
#line 200 "lib/Artifex/Parser.y"
    function yy_r52(){ $this->_retvalue = array($this->yystack[$this->yyidx + -2]->minor => $this->yystack[$this->yyidx + 0]->minor);     }
#line 1334 "lib/Artifex/Parser.php"
#line 203 "lib/Artifex/Parser.y"
    function yy_r54(){ $this->_retvalue = $this->yystack[$this->yyidx + -2]->minor; $this->_retvalue[] = $this->yystack[$this->yyidx + 0]->minor;     }
#line 1337 "lib/Artifex/Parser.php"

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
#line 1459 "lib/Artifex/Parser.php"
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
