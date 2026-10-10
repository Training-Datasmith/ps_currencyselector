<?php

$phar = getenv('PHPUNIT_PHAR') ? getenv('PHPUNIT_PHAR') : '/tmp/phpunit-4.8.36.phar';
if (!is_readable($phar)) {
    fwrite(STDERR, "PHPUnit PHAR not found at {$phar}\n");
    exit(1);
}

require $phar;

require __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/PsCurrencyselectorTestCase.php';

$testClasses = array(
    'PsCurrencyselectorConstructorTest',
    'PsCurrencyselectorInstallTest',
    'PsCurrencyselectorWidgetTest',
    'PsCurrencyselectorUrlTest',
    'DirectoryGuardTest',
    'CurrencySelectorTemplateTest',
);

foreach ($testClasses as $className) {
    $file = __DIR__ . '/' . $className . '.php';
    if (is_readable($file)) {
        require_once $file;
    }
}

$methods = array();
foreach ($testClasses as $className) {
    if (!class_exists($className)) {
        fwrite(STDERR, "Missing test class {$className}\n");
        exit(1);
    }

    $ref = new ReflectionClass($className);
    foreach ($ref->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
        if (strpos($method->getName(), 'test') === 0) {
            $methods[] = array($className, $method->getName());
        }
    }
}

$seed = 20261008;
srand($seed);
shuffle($methods);

echo "Shuffle seed: {$seed}\n";

$suite = new PHPUnit_Framework_TestSuite('ShuffledSuite');
foreach ($methods as $pair) {
    $suite->addTest(new $pair[0]($pair[1]));
}

$runner = new PHPUnit_TextUI_TestRunner();
$result = $runner->doRun($suite, array(), true);

exit($result->wasSuccessful() ? 0 : 1);
