<?php

abstract class PsCurrencyselectorTestCase extends PHPUnit_Framework_TestCase
{
    /** @var Ps_Currencyselector */
    protected $module;

    protected function setUp()
    {
        ps_currencyselector_load_module();
        PrestaShopTestState::resetAll();
        $this->module = new Ps_Currencyselector();
    }

    protected function seedDefaultCurrencies()
    {
        Currency::setCurrencies(array(
            Currency::make(1, 'EUR', 'Euro', 1, '€'),
            Currency::make(2, 'USD', 'US Dollar', 1.1, '$'),
        ));
    }
}
