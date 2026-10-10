<?php

namespace PrestaShop\PrestaShop\Core\Module {

interface WidgetInterface
{
    public function getWidgetVariables($hookName, array $configuration);

    public function renderWidget($hookName, array $configuration);
}

}

namespace PrestaShop\PrestaShop\Adapter\Presenter\Object {

class ObjectPresenter
{
    public function present($object)
    {
        return array(
            'id' => $object->id,
            'name' => $object->name,
            'iso_code' => $object->iso_code,
            'conversion_rate' => $object->conversion_rate,
        );
    }
}

}

namespace {

class SmartyStub
{
    public $assigns = array();

    public function assign($vars)
    {
        if (is_array($vars)) {
            $this->assigns = array_merge($this->assigns, $vars);
        }
    }
}

class LinkStub
{
    public static $lastLanguageId = null;
    public static $url = 'http://127.0.0.1/fr/';

    public function getLanguageLink($idLang)
    {
        self::$lastLanguageId = $idLang;

        return self::$url;
    }

    public static function resetStatics()
    {
        self::$lastLanguageId = null;
        self::$url = 'http://127.0.0.1/fr/';
    }
}

class LanguageContext
{
    public $id = 7;
}

class CurrencyContext
{
    public $id = 2;
}

class Context
{
    /** @var Context */
    private static $instance;

    /** @var LanguageContext */
    public $language;

    /** @var CurrencyContext */
    public $currency;

    /** @var LinkStub */
    public $link;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
            self::$instance->language = new LanguageContext();
            self::$instance->currency = new CurrencyContext();
            self::$instance->link = new LinkStub();
        }

        return self::$instance;
    }

    public static function resetStatics()
    {
        self::$instance = null;
        LinkStub::resetStatics();
    }
}

class Configuration
{
    public static $catalogMode = false;

    public static function isCatalogMode()
    {
        return self::$catalogMode;
    }

    public static function resetStatics()
    {
        self::$catalogMode = false;
    }
}

class Currency
{
    public $id;
    public $name;
    public $iso_code;
    public $conversion_rate;
    public $sign;

    /** @var array */
    private static $currencies = array();

    /** @var array|null */
    public static $lastGetCurrenciesArgs = null;

    public static $multiCurrencyActivated = true;

    public static $throwOnGetCurrencies = false;

    public static function getCurrencies($object = false, $active = true, $groupBy = false)
    {
        if (self::$throwOnGetCurrencies) {
            throw new \Exception('Currency::getCurrencies should not be called');
        }

        self::$lastGetCurrenciesArgs = array($object, $active, $groupBy);

        return self::$currencies;
    }

    public static function isMultiCurrencyActivated($idShop = null)
    {
        return self::$multiCurrencyActivated;
    }

    public static function setCurrencies(array $currencies)
    {
        self::$currencies = $currencies;
    }

    public static function make($id, $iso, $name, $rate, $sign)
    {
        $c = new self();
        $c->id = $id;
        $c->iso_code = $iso;
        $c->name = $name;
        $c->conversion_rate = $rate;
        $c->sign = $sign;

        return $c;
    }

    public static function resetStatics()
    {
        self::$currencies = array();
        self::$lastGetCurrenciesArgs = null;
        self::$multiCurrencyActivated = true;
        self::$throwOnGetCurrencies = false;
    }
}

class Module
{
    public $name;
    public $tab;
    public $author;
    public $version;
    public $need_instance;
    public $displayName;
    public $description;
    public $ps_versions_compliancy = array();

    /** @var Context */
    public $context;

    /** @var SmartyStub */
    public $smarty;

    public static $parentInstallReturn = true;
    public static $registerHookReturn = true;
    /** @var array */
    public static $registeredHooks = array();
    public static $fetchWasCalled = false;

    public function __construct()
    {
        $this->context = Context::getInstance();
        $this->smarty = new SmartyStub();
    }

    public function trans($id, array $parameters = array(), $domain = null, $locale = null)
    {
        return $id;
    }

    public function install()
    {
        return self::$parentInstallReturn;
    }

    public function registerHook($hookName)
    {
        self::$registeredHooks[] = $hookName;

        return self::$registerHookReturn;
    }

    public function fetch($templatePath, $cache_id = null, $compile_id = null)
    {
        self::$fetchWasCalled = true;

        return 'rendered:' . $templatePath;
    }

    public static function resetStatics()
    {
        self::$parentInstallReturn = true;
        self::$registerHookReturn = true;
        self::$registeredHooks = array();
        self::$fetchWasCalled = false;
    }
}

class PrestaShopTestState
{
    public static function resetAll()
    {
        Module::resetStatics();
        Context::resetStatics();
        Configuration::resetStatics();
        Currency::resetStatics();
    }
}

}
