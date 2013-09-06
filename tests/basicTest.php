<?php
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

class basicTest extends \phpunit_framework_testcase
{

    // loop {{{
    public function testLoop() {
        $output = Artifex::execute(<<<EOF
#* foreach ([1,2,3,4,5] as \$id => \$foo)
    hola __id__ __foo__
#* end
EOF
        );
        $this->assertEquals($output, "    hola 0 1
    hola 1 2
    hola 2 3
    hola 3 4
    hola 4 5
");
        
    }
    // }}}

    // loop key - value {{{
    public function testLoopKeyValue() {
        $output = Artifex::execute(<<<EOF
#* foreach ({foo:1,bar:2} as \$id => \$foo)
    hola __id__ __foo__
#* end
EOF
        );
        $this->assertEquals($output, "    hola foo 1
    hola bar 2
");
        
    }
    // }}}

    // testReplace {{{
    public function testReplace() {
        $methods = array();
        for ($i=0; $i < 10; $i++) {
            $methods[] = 'x' . uniqid(true);
        }
        $output = Artifex::execute(<<<'EOF'
#* foreach ([1,2,3,4,5] as $foo)
class class__foo__ {
    #* foreach ($methods as $method) *# public function set__method__() {
    }
    #* end
}
#* end
EOF
        , compact('methods'));
        $namespace = safe_eval($output);
        foreach (range(1,5) as $id) { 
            $class = $namespace . '\\' . 'class' . $id;
            $this->assertTrue(class_exists($class));
            foreach ($methods as $method) {
                $this->assertTrue(is_callable($class, $method));
            }
        }
    }
    // }}}

    // testSave {{{
    public function testSave() 
    {
        $file = "/tmp/foo";
        $content = uniqid(true);
        $this->assertTrue(Artifex::save($file, $content));
        $this->assertEquals($content, file_get_contents($file));
        $this->assertEquals($content, file_get_contents($file));
    }
    // }}}

    // if {{{
    public function testIf() {
        $vm = Artifex::compile('
        #* function artifex ($n)
        #* if ($n == 1)
            1
        #* else if ($n == 2)
            2
        #* else
            don\'t know
        #* end end
        ');
        $fnc = $vm->getFunction('artifex');
        $this->assertEquals('1', trim($fnc(1)));
        $this->assertEquals('2', trim($fnc(2)));
        $this->assertEquals("don't know", trim($fnc(99)));
    }
    // }}}

    // exec {{{
    public function testFunctionCall() {
        $this->assertEquals(time(), Artifex::execute('#* print(time())'));
    }
    // }}}

    // concat {{{
    public function testStringConcat() {
        $this->assertEquals('foo bar test', Artifex::execute('#* print("foo ". "bar" . " test")'));
    }
    // }}}

    // expr {{{
    public function testExpr() {
        $expected = (5+4/(4-3)*4)+1;
        $a = 5;
        $b = 4;
        $args = compact('expected', 'a', 'b');
        $code = '#* if ($a+$b/($b-3)*$b >= $expected) print("foo ". "bar" . " test") end';
        $this->assertEquals('foo bar test', Artifex::execute($code), $args);
        $code = '#* if ($a+$b/($b-3)*$b+1 > $expected) print("foo ". "bar" . " test") end';
        $this->assertEquals('foo bar test', Artifex::execute($code), $args);
        $code = '#* if ((99%3) == 0 && true) print("foo ". "bar" . " test") end';
        $this->assertEquals('foo bar test', Artifex::execute($code), $args);
        $code = '#* if (true != false) print("foo ". "bar" . " test") end';
        $this->assertEquals('foo bar test', Artifex::execute($code), $args);
        $code = '#* if (not true == false) print("foo ". "bar" . " test") end';
        $this->assertEquals('foo bar test', Artifex::execute($code), $args);
        $code = '#* if (is_array([1,2,3])) print("foo ". "bar" . " test") end';
        $this->assertEquals('foo bar test', Artifex::execute($code), $args);
    }
    // }}}
        
    // missing variable {{{
    /**
     *  @expectedException \RuntimeException
     */
    public function testMissingVariable() {
        $output = Artifex::execute(<<<'EOF'
#* foreach ([1,2,3,4,5] as $foo)
class class__foo__ {
    #* foreach ($methods as $method) 
    public function set__method__() {
    }
    #* end
}
#* end
EOF
        );
    }
    // }}}

    // define function {{{
    public function testDefineFunction() {
        $vm = Artifex::compile(<<<'EOF'
#* function defineClass($foo, $methods)
class __foo__ {
    #* foreach ($methods as $method) 
    public function set__method__() {
    }
    #* end
}
#* end
EOF
        );

        $function = $vm->getFunction("defineClass");
        $code = $function('foo', array(1,2));
        
        $namespace = safe_eval($code);
        $class     = $namespace . '\\foo';
        $this->assertTrue(class_exists($class));
        $this->assertTrue(is_callable($class, 'set1'));
        $this->assertTrue(is_callable($class, 'set2'));
    }
    // }}}

    // local defined function call {{{
    public function testDefineExecLocalFunction() {
        $namespace = safe_eval(Artifex::execute(<<<'EOF'
#* foo("artifex", "rulz")
#* function foo($foo, $method) 
    class __foo__ {
        function __method__() {
        }
    }
#* end
EOF
        ));

        $this->assertTrue(class_exists($namespace . '\\artifex'));
        $this->assertTrue(is_callable($namespace . '\\artifex', 'rulz'));
    }
    // }}}

    // scope of functions {{{
    public function testDefineScope() {
        $vm = Artifex::compile(<<<'EOF'
#* $foo = "foo"
#* $methods = [1, 2]
#* function defineClass($foo, $methods)
class __foo__ {
    #* foreach ($methods as $method) 
    public function set__method__() {
    }
    #* end
}
#* end
EOF
        );
        $function = $vm->getFunction("defineClass");
        try {
            /* it fails because the main 
               scope haven't run yet and the variables
               $foo and $methods doens't exists yet */
            $code = $function();
            $this->assertTrue(false);
        } catch (\RuntimeException $e) {
            $this->assertTrue(true);
        }


        $vm->run();
        $code = $function();
        
        $namespace = safe_eval($code);
        $class     = $namespace . '\\foo';
        $this->assertTrue(class_exists($class));
        $this->assertTrue(is_callable($class, 'set1'));
        $this->assertTrue(is_callable($class, 'set2'));
    }
    // }}}

}
