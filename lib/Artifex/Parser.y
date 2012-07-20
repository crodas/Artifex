%name Artifex_
%include {
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
}

%declare_class {class Artifex_Parser }
%include_class {
    public $body = array();
}

%syntax_error {
    $expect = array();
    foreach ($this->yy_get_expected_tokens($yymajor) as $token) {
        $expect[] = self::$yyTokenName[$token];
    }
    throw new Exception('Unexpected ' . $this->tokenName($yymajor) .  ' in line ' . $this->line
        . ' (' . $TOKEN . ') '
        . ' Expected: ' . print_r($expect, true));
}


%left T_COMMA.
%left T_OBJ T_CURLY_OPEN.
%left T_AND.
%left T_OR.
%right T_NOT.
%nonassoc T_EQ T_NE T_ASSIGN.
%nonassoc T_GT T_GE T_LT T_LE.
%nonassoc T_IN.
%left T_DOT.
%left T_PLUS T_MINUS.
%left T_TIMES T_DIV T_MOD.
%left T_PIPE T_BITWISE.

start ::= body(A). { $this->body = A; }

body(A) ::= body(B) line(C). {
    $last = end(B);
    if ($last) {
        $last->setNext(C);
        C->setPrev($last);
    }
    B[] = C; 
    A = B; 
}
body(A) ::= . { A = array(); }

line(A) ::= T_RAW_STRING(B). { A = new RawString(B); }
line(A) ::= code(B). { A = B; }
line(A) ::= code(B) T_NEW_LINE. { A = B; }
line(A) ::= T_STRING(B) . { A = new RawString(B); }
line(A) ::= T_WHITESPACE(x). { A = new Whitespace(x); }

/* foreach {{{ */
code(A) ::= T_FOREACH T_LPARENT foreach_source(B) T_AS variable(C) T_RPARENT body(X) T_END. { 
    A = new Expr_Foreach(B, C, NULL, X); 
    A->setChild(X);
}

code(A) ::= T_FOREACH T_LPARENT foreach_source(B) T_AS variable(E) T_DOUBLE_ARROW variable(C) T_RPARENT body(X) T_END . {
    A = new Expr_Foreach(B, C, E, X);
    A->setChild(X);
}

foreach_source(A) ::= variable(B) . { A = B; }
foreach_source(A) ::= fnc_call(B) . { A = B; }
foreach_source(A) ::= json(B) . { A = new Term(B); }
/* }}} */

/* function definition {{{ */
code(A) ::= T_FUNCTION T_ALPHA(B) T_LPARENT args(X) T_RPARENT body(Z) T_END . {
    A = new DefFunction(B, X, Z);
    A->setChild(Z);
}
/* }}} */

/* assign {{{ */
code(A) ::= variable(B) T_ASSIGN expr(C) . { A = new Assign(B, C); }
/* }}} */

/* function call {{{ */
code(A) ::= fnc_call(B) . { A = B; }
fnc_call(A) ::= T_ALPHA(B) T_LPARENT args(X) T_RPARENT . { 
    A = new Exec(B, X);
}

fnc_call(A) ::= variable(B) T_LPARENT args(X) T_RPARENT . { 
    A = new Exec(B, X);
}
/* }}} */

/* if {{{ */
code(A) ::= if(B) . { A = B; }
if(A) ::= T_IF T_LPARENT expr(X) T_RPARENT body(Y) else_if(Z) . {
    A = new Expr_If(X, Y, Z);
    A->setChild(Y);
}

else_if(A) ::= T_ELSE T_IF T_LPARENT expr(X) T_RPARENT body(Y) else_if(Z) . { 
    A = new Expr_If(X, Y, Z); 
    A->setChild(Y);
}
else_if(A) ::= T_ELSE body(X) T_END . { 
    A = X; 
}
else_if(A) ::= T_END . { A = NULL; }
/* }}} */

/* expr {{{ */
expr(A) ::= T_NOT expr(B). { A = new Expr('not', B); }
expr(A) ::= expr(B) T_AND(X)  expr(C).  { A = new Expr(strtolower(@X), B, C); }
expr(A) ::= expr(B) T_OR(X)  expr(C).  { A = new Expr(strtolower(@X), B, C); }
expr(A) ::= expr(B) T_PLUS|T_MINUS(X)  expr(C).  { A = new Expr(@X, B, C); }
expr(A) ::= expr(B) T_EQ|T_NE|T_GT|T_GE|T_LT|T_LE|T_IN(X)  expr(C).  { A = new Expr(trim(@X), B, C); }
expr(A) ::= expr(B) T_TIMES|T_DIV|T_MOD(X)  expr(C).  { A = new Expr(@X, B, C); }
expr(A) ::= expr(B) T_BITWISE|T_PIPE(X)  expr(C).  { A = new Expr(@X, B, C); }
expr(A) ::= T_LPARENT expr(B) T_RPARENT. { A = new Expr(B); }
expr(A) ::= expr(B) T_DOT expr(C) . { A = new Concat(B, C); }
expr(A) ::= variable(X) . { A = X; }
expr(A) ::= term(X) . {  A = new Term(X); }
expr(A) ::= T_AT variable(B) . { 
    A = new Exec('var_export', array(B, new Term(true))); 
}
expr(A) ::= fnc_call(B) . { A = B; }
/* }}} */

/* function arguments {{{ */
args(X) ::= args(A) T_COMMA args(B) . { X = array_merge(A, B); }
args(X) ::= expr(B). { X = array(B); }
args(X) ::= . { X = array(); }
/* }}} */

/* variable {{{ */
variable(A) ::= T_DOLLAR var(B) . { A = new Variable(B); }

var(A) ::= var(B) T_OBJ var(C) . { A = array_merge(B, C); }
var(A) ::= var(B) T_SUBSCR_OPEN expr(C) T_SUBSCR_CLOSE . { A = B ; A[] = C; }
var(A) ::= T_ALPHA(B) . { A = array(B);}
/* }}} */

/* term {{{ */
term(A) ::= T_ALPHA(B)  . { A = trim(B); }
term(A) ::= T_TRUE      . { A = TRUE; }
term(A) ::= T_FALSE     . { A = FALSE; }
term(A) ::= T_STRING(B) . { A = B; }
term(A) ::= T_NUMBER(B) . { A = B + 0; }
term(A) ::= T_MINUS T_NUMBER(B) . { A = -1 * (B + 0); }
term(A) ::= json(B) . { A = B; }
/* }}} */

/* json {{{ */
json(A) ::= T_CURLY_OPEN json_obj(B) T_CURLY_CLOSE. { A  = B; }
json(A) ::= T_SUBSCR_OPEN json_arr(B) T_SUBSCR_CLOSE. { A = B; }

json_obj(A) ::= json_obj(B) T_COMMA json_obj(C). { A = array_merge(B, C); }
json_obj(A) ::= term(B) T_COLON expr(C) . { A = array(B => C); } 
json_obj(A) ::= . { A = array(); } 

json_arr(X) ::= json_arr(A) T_COMMA expr(B) .  { X = A; X[] = B; }
json_arr(A) ::= expr(B). { A = array(B); }
json_arr(A) ::= . { A = array(); }
/* }}} */

