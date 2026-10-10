<?php

require_once __DIR__ . '/PsCurrencyselectorTestCase.php';

class DirectoryGuardTest extends PsCurrencyselectorTestCase
{
    private function runGuardScript($scriptPath)
    {
        $phpBinary = getenv('PHP_BINARY');
        if (!$phpBinary) {
            $phpBinary = 'php';
        }

        $wrapper = tempnam(sys_get_temp_dir(), 'guard');
        $sentinel = 'GUARD_SENTINEL_SHOULD_NOT_APPEAR';
        $code = '<?php require ' . var_export($scriptPath, true) . '; echo ' . var_export($sentinel, true) . ';';
        file_put_contents($wrapper, $code);

        $descriptorSpec = array(
            0 => array('pipe', 'r'),
            1 => array('pipe', 'w'),
            2 => array('pipe', 'w'),
        );

        $process = proc_open(
            escapeshellarg($phpBinary) . ' ' . escapeshellarg($wrapper),
            $descriptorSpec,
            $pipes,
            dirname(__DIR__)
        );

        $this->assertInternalType('resource', $process);

        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);
        unlink($wrapper);

        return array($exitCode, $stdout, $stderr);
    }

    public function testRootIndexExitsBeforeSentinel()
    {
        list($exitCode, $stdout, $stderr) = $this->runGuardScript(dirname(__DIR__) . '/index.php');

        $this->assertSame(0, $exitCode);
        $this->assertSame('', $stdout);
        $this->assertSame('', $stderr);
    }

    public function testTranslationsIndexExitsBeforeSentinel()
    {
        list($exitCode, $stdout, $stderr) = $this->runGuardScript(dirname(__DIR__) . '/translations/index.php');

        $this->assertSame(0, $exitCode);
        $this->assertSame('', $stdout);
        $this->assertSame('', $stderr);
    }

    public function testModuleFileExitsWhenPsVersionMissing()
    {
        $phpBinary = getenv('PHP_BINARY') ? getenv('PHP_BINARY') : 'php';
        $modulePath = dirname(__DIR__) . '/ps_currencyselector.php';
        $sentinel = 'GUARD_SENTINEL_SHOULD_NOT_APPEAR';

        $descriptorSpec = array(
            0 => array('pipe', 'r'),
            1 => array('pipe', 'w'),
            2 => array('pipe', 'w'),
        );

        $cmd = escapeshellarg($phpBinary) . ' -r ' . escapeshellarg('require ' . var_export($modulePath, true) . '; echo ' . var_export($sentinel, true) . ';');

        $process = proc_open($cmd, $descriptorSpec, $pipes, dirname(__DIR__));
        $this->assertInternalType('resource', $process);

        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);

        $this->assertSame(0, $exitCode);
        $this->assertSame('', $stdout);
        $this->assertSame('', $stderr);
    }
}
