<?

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
}
