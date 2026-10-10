<?php

require_once __DIR__ . '/PsCurrencyselectorTestCase.php';

class PsCurrencyselectorInstallTest extends PsCurrencyselectorTestCase
{
    public function testInstallRegistersCurrencySaveHook()
    {
        Module::$parentInstallReturn = true;
        Module::$registerHookReturn = true;

        $result = $this->module->install();

        $this->assertTrue(is_bool($result));
        $this->assertTrue($result);
        $this->assertInternalType('array', Module::$registeredHooks);
        $this->assertCount(1, Module::$registeredHooks);
        $this->assertSame('actionAdminCurrenciesControllerSaveAfter', Module::$registeredHooks[0]);
    }

    public function testInstallFalseWhenParentInstallFails()
    {
        Module::$parentInstallReturn = false;
        Module::$registerHookReturn = true;

        $result = $this->module->install();

        $this->assertTrue(is_bool($result));
        $this->assertFalse($result);
        $this->assertCount(0, Module::$registeredHooks);
    }

    public function testInstallFalseWhenHookRegistrationFails()
    {
        Module::$parentInstallReturn = true;
        Module::$registerHookReturn = false;

        $result = $this->module->install();

        $this->assertTrue(is_bool($result));
        $this->assertFalse($result);
        $this->assertCount(1, Module::$registeredHooks);
        $this->assertSame('actionAdminCurrenciesControllerSaveAfter', Module::$registeredHooks[0]);
    }
}
