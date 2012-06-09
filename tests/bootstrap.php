<?php
require "lib/Artifex.php";
Artifex::registerAutoloader();

function safe_eval($code) {
    $namespace = "Artifex\\test\\x" . uniqid(true);
    eval ("namespace $namespace ;\n " . $code);
    return $namespace;
}
