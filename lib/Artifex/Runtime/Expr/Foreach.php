<?php
namespace Artifex\Runtime;

use Artifex\Runtime;

class Expr_Foreach extends Base
{
    protected $source;
    protected $value;
    protected $key;
    protected $body;

    public function __construct($source, Variable $value, Variable $key = NULL,  Array $body)
    {
        foreach (array('source', 'key', 'value', 'body') as $var) {
            $this->$var = $$var;
        }
    }

    public function execute(Runtime $vm)
    {
        foreach (array('source', 'key', 'value', 'body') as $var) {
            $$var = $this->$var;
        }

        if ($source instanceof Variable) {
            /* it's a json definition */
            $val = $vm->get($source);
            if (is_null($val)) {
                throw new \RuntimeException("Cannot find variable " . $source );
            }
            $source = $val;
        }

        foreach ($vm->getValue($source) as $zkey => $zvalue) {
            if ($key) {
                $vm->define($key, new Term($zkey));
            }
            $vm->define($value, $zvalue);
            $vm->execStmts($body);
        }
    }
}
