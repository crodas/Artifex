<?php
/**
 *  Autoloader function generated by crodas/Autoloader
 *
 *  https://github.com/crodas/Autoloader
 *
 *  This is a generated file, do not modify it.
 */


spl_autoload_register(function ($class) {
    /*
        This array has a map of (class => file)
    */

    // classes {{{
    static $classes = array (
  'artifex' => '/../Artifex.php',
  'artifex\\runtime\\base' => '/Runtime/Base.php',
  'artifex\\runtime\\raw' => '/Runtime/Raw.php',
  'artifex\\runtime\\variable' => '/Runtime/Variable.php',
  'artifex\\runtime\\exec' => '/Runtime/Exec.php',
  'artifex\\runtime\\term' => '/Runtime/Term.php',
  'artifex\\runtime\\rawstring' => '/Runtime/RawString.php',
  'artifex\\runtime\\expr' => '/Runtime/Expr.php',
  'artifex\\runtime\\deffunction' => '/Runtime/DefFunction.php',
  'artifex\\runtime\\concat' => '/Runtime/Concat.php',
  'artifex\\runtime\\assign' => '/Runtime/Assign.php',
  'artifex\\runtime\\expr_if' => '/Runtime/Expr/If.php',
  'artifex\\runtime\\expr_foreach' => '/Runtime/Expr/Foreach.php',
  'artifex\\runtime\\whitespace' => '/Runtime/Whitespace.php',
  'artifex\\tokenizer' => '/Tokenizer.php',
  'artifex_yytoken' => '/Parser.php',
  'artifex_yystackentry' => '/Parser.php',
  'artifex_parser' => '/Parser.php',
  'artifex\\util\\phptokens' => '/Util/PHPTokens.php',
  'artifex\\runtime' => '/Runtime.php',
);
    // }}}

    // deps {{{
    static $deps    = array (
  'artifex\\runtime\\raw' => 
  array (
    0 => 'artifex\\runtime\\base',
  ),
  'artifex\\runtime\\variable' => 
  array (
    0 => 'artifex\\runtime\\base',
  ),
  'artifex\\runtime\\exec' => 
  array (
    0 => 'artifex\\runtime\\base',
  ),
  'artifex\\runtime\\term' => 
  array (
    0 => 'artifex\\runtime\\base',
  ),
  'artifex\\runtime\\rawstring' => 
  array (
    0 => 'artifex\\runtime\\base',
    1 => 'artifex\\runtime\\raw',
  ),
  'artifex\\runtime\\expr' => 
  array (
    0 => 'artifex\\runtime\\base',
  ),
  'artifex\\runtime\\deffunction' => 
  array (
    0 => 'artifex\\runtime\\base',
  ),
  'artifex\\runtime\\concat' => 
  array (
    0 => 'artifex\\runtime\\base',
  ),
  'artifex\\runtime\\assign' => 
  array (
    0 => 'artifex\\runtime\\base',
  ),
  'artifex\\runtime\\expr_if' => 
  array (
    0 => 'artifex\\runtime\\base',
  ),
  'artifex\\runtime\\expr_foreach' => 
  array (
    0 => 'artifex\\runtime\\base',
  ),
  'artifex\\runtime\\whitespace' => 
  array (
    0 => 'artifex\\runtime\\base',
    1 => 'artifex\\runtime\\raw',
  ),
);
    // }}}

    $class = strtolower($class);
    if (isset($classes[$class])) {
        if (!empty($deps[$class])) {
            foreach ($deps[$class] as $zclass) {
                if (!class_exists($zclass, false)) {
                    require __DIR__  . $classes[$zclass];
                }
            }
        }

        if (!class_exists($class, false)) {
            require __DIR__  . $classes[$class];
        }
        return true;
    }

    return false;
});


