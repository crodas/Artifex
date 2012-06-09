<?php

class BasicTest extends \phpunit_framework_testcase
{
    public function testLoop() {
        $output = Artifex::execute(<<<EOF
#* foreach ([1,2,3,4,5] as \$foo)
    hola __foo__
#* end
EOF
        );
        $this->assertEquals($output, "    hola 1
    hola 2
    hola 3
    hola 4
    hola 5
");
        
    }

    public function testReplace() {
        $methods = array();
        for ($i=0; $i < 10; $i++) {
            $methods[] = 'x' . uniqid(true);
        }
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

    public function testSave() 
    {
        $file = "/tmp/foo";
        $content = uniqid(true);
        $this->assertTrue(Artifex::save($file, $content));
        $this->assertEquals($content, file_get_contents($file));
        $fp = fopen($file, "a+");
        $this->assertTrue(flock($fp, LOCK_EX | LOCK_NB));
        $this->assertFalse(Artifex::save($file, ""));
        fclose($fp);
        $this->assertEquals($content, file_get_contents($file));
    }

    public function testIf() {
        $vm = Artifex::compile('
        #* function cesar ($n)
        #* if ($n == 1)
            1
        #* else if ($n == 2)
            2
        #* else
            don\'t know
        #* end end
        ');
        $fnc = $vm->getFunction('cesar');
        $this->assertEquals('1', trim($fnc(1)));
        $this->assertEquals('2', trim($fnc(2)));
        $this->assertEquals("don't know", trim($fnc(99)));
    }

    public function testFunctionCall() {
        $this->assertEquals(time(), Artifex::execute('#* print(time())'));
    }

    public function testStringConcat() {
        $this->assertEquals('foo bar test', Artifex::execute('#* print("foo ". "bar" . " test")'));
    }
        

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

    public function testDefineScope() {
        $vm = Artifex::compile(<<<'EOF'
#* $foo = "foo";
#* $methods = [1, 2];
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

}
