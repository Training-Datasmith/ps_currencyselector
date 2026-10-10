<?php

require_once __DIR__ . '/PsCurrencyselectorTestCase.php';

class PsCurrencyselectorWidgetTest extends PsCurrencyselectorTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->seedDefaultCurrencies();
        Context::getInstance()->language->id = 7;
        Context::getInstance()->currency->id = 2;
        LinkStub::$url = 'http://127.0.0.1/fr/';
    }

    public function testWidgetVariablesShapeAndCurrentCurrency()
    {
        $vars = $this->module->getWidgetVariables('displayNav', array());

        $this->assertInternalType('array', $vars);
        $this->assertArrayHasKey('currencies', $vars);
        $this->assertArrayHasKey('current_currency', $vars);
        $this->assertCount(2, $vars['currencies']);

        $eur = $vars['currencies'][0];
        $usd = $vars['currencies'][1];

        $this->assertFalse($eur['current']);
        $this->assertTrue($usd['current']);
        $this->assertSame($usd, $vars['current_currency']);

        $this->assertSame('EUR', $eur['iso_code']);
        $this->assertSame('USD', $usd['iso_code']);
        $this->assertSame(1, $eur['id']);
        $this->assertSame(2, $usd['id']);
        $this->assertSame(1.1, $usd['conversion_rate']);

        $currentCount = 0;
        foreach ($vars['currencies'] as $row) {
            if ($row['current'] === true) {
                ++$currentCount;
            }
        }
        $this->assertSame(1, $currentCount);
    }

    public function testSignIsCopiedWhenPresenterOmitsIt()
    {
        Currency::setCurrencies(array(
            Currency::make(1, 'EUR', 'Euro', 1, '€'),
            Currency::make(2, 'USD', 'US Dollar', 1.1, '$'),
            Currency::make(3, 'ABC', 'Abc', 1, ''),
        ));

        $vars = $this->module->getWidgetVariables('displayNav', array());

        $this->assertSame('€', $vars['currencies'][0]['sign']);
        $this->assertSame('$', $vars['currencies'][1]['sign']);
        $this->assertArrayHasKey('sign', $vars['currencies'][2]);
        $this->assertSame('', $vars['currencies'][2]['sign']);
    }

    public function testCurrencyOrderFollowsGetCurrencies()
    {
        Currency::setCurrencies(array(
            Currency::make(2, 'USD', 'US Dollar', 1.1, '$'),
            Currency::make(1, 'EUR', 'Euro', 1, '€'),
        ));

        $vars = $this->module->getWidgetVariables('displayNav', array());

        $this->assertSame('USD', $vars['currencies'][0]['iso_code']);
        $this->assertSame('EUR', $vars['currencies'][1]['iso_code']);
    }

    public function testGetCurrenciesAskedForActiveObjects()
    {
        $this->module->getWidgetVariables('displayNav', array());

        $this->assertInternalType('array', Currency::$lastGetCurrenciesArgs);
        $this->assertCount(3, Currency::$lastGetCurrenciesArgs);
        $this->assertTrue(Currency::$lastGetCurrenciesArgs[0]);
        $this->assertTrue(Currency::$lastGetCurrenciesArgs[1]);
        $this->assertFalse(Currency::$lastGetCurrenciesArgs[2]);
    }

    public function testLanguageIdIsPassedToLanguageLink()
    {
        $this->module->getWidgetVariables('displayNav', array());

        $this->assertSame(7, LinkStub::$lastLanguageId);

        $vars = $this->module->getWidgetVariables('displayNav', array());
        $parsed = parse_url($vars['currencies'][0]['url']);
        $this->assertInternalType('array', $parsed);
        $this->assertSame('127.0.0.1', $parsed['host']);
    }

    public function testImplementsWidgetInterfaceSignature()
    {
        $ref = new ReflectionClass('Ps_Currencyselector');

        $this->assertTrue($ref->isSubclassOf('Module'));
        $this->assertTrue($ref->implementsInterface('PrestaShop\\PrestaShop\\Core\\Module\\WidgetInterface'));

        foreach (array('getWidgetVariables', 'renderWidget') as $methodName) {
            $method = $ref->getMethod($methodName);
            $this->assertTrue($method->isPublic());
            $this->assertCount(2, $method->getParameters());
            $this->assertTrue($method->getParameters()[1]->isArray());
        }
    }

    public function testRenderWidgetRendersTemplateWhenSellingInSeveralCurrencies()
    {
        Configuration::$catalogMode = false;
        Currency::$multiCurrencyActivated = true;

        $result = $this->module->renderWidget('displayNav', array());

        $this->assertSame('rendered:module:ps_currencyselector/ps_currencyselector.tpl', $result);
        $this->assertArrayHasKey('currencies', $this->module->smarty->assigns);
        $this->assertArrayHasKey('current_currency', $this->module->smarty->assigns);
        $this->assertTrue($this->module->smarty->assigns['current_currency']['current']);
        $this->assertFalse($this->module->smarty->assigns['currencies'][0]['current']);
    }

    public function testRenderWidgetFalseInCatalogMode()
    {
        Configuration::$catalogMode = true;
        Currency::$throwOnGetCurrencies = true;

        $result = $this->module->renderWidget('displayNav', array());

        $this->assertFalse($result);
        $this->assertCount(0, $this->module->smarty->assigns);
        $this->assertFalse(Module::$fetchWasCalled);
    }

    public function testRenderWidgetFalseWhenSingleCurrency()
    {
        Configuration::$catalogMode = false;
        Currency::$multiCurrencyActivated = false;
        Currency::$throwOnGetCurrencies = true;

        $result = $this->module->renderWidget('displayNav', array());

        $this->assertFalse($result);
        $this->assertCount(0, $this->module->smarty->assigns);
        $this->assertFalse(Module::$fetchWasCalled);
    }

    public function testRenderWidgetFalseWhenCatalogModeEvenIfMultiCurrency()
    {
        Configuration::$catalogMode = true;
        Currency::$multiCurrencyActivated = true;
        Currency::$throwOnGetCurrencies = true;

        $result = $this->module->renderWidget('displayNav', array());

        $this->assertFalse($result);
    }
}
