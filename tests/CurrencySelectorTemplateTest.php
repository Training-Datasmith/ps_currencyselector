<?php

require_once __DIR__ . '/PsCurrencyselectorTestCase.php';

class CurrencySelectorTemplateTest extends PsCurrencyselectorTestCase
{
    public function testTemplateReferencesWidgetKeys()
    {
        $templatePath = dirname(__DIR__) . '/ps_currencyselector.tpl';
        $this->assertTrue(is_readable($templatePath));

        $contents = file_get_contents($templatePath);
        $this->assertInternalType('string', $contents);
        $this->assertNotSame('', $contents);

        $required = array(
            '$current_currency.iso_code',
            '$current_currency.sign',
            '$currencies',
            '$currency.url',
            '$currency.current',
            '$currency.iso_code',
            '$currency.sign',
        );

        foreach ($required as $needle) {
            $this->assertNotFalse(strpos($contents, $needle), 'Missing template key reference: ' . $needle);
        }
    }
}
