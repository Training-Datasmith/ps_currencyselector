<?php

require_once __DIR__ . '/PsCurrencyselectorTestCase.php';

class PsCurrencyselectorConstructorTest extends PsCurrencyselectorTestCase
{
    public function testModuleIdentity()
    {
        $this->assertSame('ps_currencyselector', $this->module->name);
        $this->assertSame('front_office_features', $this->module->tab);
        $this->assertSame('PrestaShop', $this->module->author);
        $this->assertSame('2.1.1', $this->module->version);
        $this->assertSame(0, $this->module->need_instance);
    }

    public function testTranslatedStrings()
    {
        $this->assertSame('Currency block', $this->module->displayName);
        $this->assertSame(
            'Add a currency selector to help visitors from targeted countries purchase on your store.',
            $this->module->description
        );
    }

    public function testVersionCompliancyUsesRuntimeConstant()
    {
        $this->assertSame('1.7.5.0', $this->module->ps_versions_compliancy['min']);
        $this->assertSame('9.9.9-test', $this->module->ps_versions_compliancy['max']);
    }

    public function testConfigXmlNameAndVersionMatchModule()
    {
        $xmlPath = dirname(__DIR__) . '/config.xml';
        $this->assertTrue(is_readable($xmlPath));

        $contents = file_get_contents($xmlPath);
        $this->assertInternalType('string', $contents);
        $this->assertNotSame('', $contents);

        $xml = simplexml_load_string($contents);
        $this->assertInstanceOf('SimpleXMLElement', $xml);

        $this->assertSame('ps_currencyselector', (string) $xml->name);
        $this->assertSame('2.1.1', (string) $xml->version);
        $this->assertSame($this->module->name, (string) $xml->name);
        $this->assertSame($this->module->version, (string) $xml->version);
    }
}
