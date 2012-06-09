<?php
namespace Artifex\Runtime;

use Artifex\Runtime;

class Expr_If extends Base {
    public function Execute(Runtime $vm)
    {
        $value = $this->args[0]->getValue($vm);
        if ($value instanceof Base) {
            $value = $value->getValue($vm);
        }

        if ($value) {
            $vm->execStmts($this->args[1]);
        } else if (isset($this->args[2])) {
            if (is_array($this->args[2])) {
                /* else */
                $vm->execStmts($this->args[2]);
            } else {
                /* else if */
                $this->args[2]->execute($vm);
            }
        }
    }
}
