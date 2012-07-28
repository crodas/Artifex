Artifex
=======

Artifex is a [pre-processor](http://en.wikipedia.org/wiki/Preprocessor) for PHP. It aims to generate PHP code in a very simple and intuitive way.

Syntax
------

Artifex syntax is heavily inspired by C preprocessors, but instead of starting with `#` (which is a comment in PHP), they starts with `#*`

```C
#* $foo = $foo + 1
```

Artifex also support multiline in a convinient way.

```C
#*
# $foo = $foo + 2
# $bar = $foo + $foo->xxx();
```

In order to replace a value, it must be surrounded by `__`, for instance:

```php
<?php
#* $foo = rand()
function getRandomOnce() {
  return __foo__;
}

#* $foo = {'foo': 'bar'}
function getArray() {
  return __@foo__;
}
```

The syntax is heavily inspired by PHP, although there are some differences:
  1. The semicolon is not needed (they are optional). 
  2. Any string which is not assigned will be printed out. `#* "hello there"` is equal to `#* print("hello there")`
  3. Curly Brackets are not valid, instead there is a generic `end` token to tell where a block ends
  4. Variables that are prepend with a `@` caracter means that we want to representate its value (same as calling `var_exports`).
  5. It supports json.

Iterators
---------

So far Artifex support foreach

```php
<?php

class __foo__ {
  #* foreach(['foo', 'bar'] as $id => $name)
  function get__name__() {
    return "I'm a the getter of __id__ => __name__";
  }
  #* end
}
```

