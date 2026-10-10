<?php

require_once __DIR__ . '/PsCurrencyselectorTestCase.php';

class PsCurrencyselectorUrlTest extends PsCurrencyselectorTestCase
{
    private function assertParsedUrl($url, array $expected)
    {
        $parsed = parse_url($url);
        $this->assertInternalType('array', $parsed);

        if (array_key_exists('scheme', $expected)) {
            $this->assertSame($expected['scheme'], $parsed['scheme']);
        }
        if (array_key_exists('host', $expected)) {
            $this->assertSame($expected['host'], $parsed['host']);
        }
        if (array_key_exists('port', $expected)) {
            $this->assertSame($expected['port'], $parsed['port']);
        } elseif (array_key_exists('port_absent', $expected) && $expected['port_absent']) {
            $this->assertFalse(array_key_exists('port', $parsed));
        }
        if (array_key_exists('path', $expected)) {
            $this->assertSame($expected['path'], $parsed['path']);
        }

        $this->assertArrayHasKey('query', $parsed);
        $query = array();
        parse_str($parsed['query'], $query);
        $this->assertInternalType('array', $query);

        foreach ($expected['query'] as $key => $value) {
            $this->assertArrayHasKey($key, $query);
            $this->assertSame($value, $query[$key]);
        }

        if (isset($expected['query_count'])) {
            $this->assertCount($expected['query_count'], $query);
        }

        $this->assertSame(1, substr_count($parsed['query'], 'SubmitCurrency='));
        $this->assertSame(1, substr_count($parsed['query'], 'id_currency='));
    }

    public function testUrlKeepsSchemeHostPortPathAndAddsCurrencyParams()
    {
        LinkStub::$url = 'http://127.0.0.1:8080/en/index.php?ref=campaign&id_product=42';
        $this->seedDefaultCurrencies();

        $vars = $this->module->getWidgetVariables('displayNav', array());
        $this->assertInternalType('array', $vars);
        $this->assertArrayHasKey('currencies', $vars);
        $usd = $vars['currencies'][1];

        $this->assertParsedUrl($usd['url'], array(
            'scheme' => 'http',
            'host' => '127.0.0.1',
            'port' => 8080,
            'path' => '/en/index.php',
            'query' => array(
                'ref' => 'campaign',
                'id_product' => '42',
                'SubmitCurrency' => '1',
                'id_currency' => '2',
            ),
            'query_count' => 4,
        ));
    }

    public function testUrlOmitsPortWhenLanguageLinkHasNone()
    {
        LinkStub::$url = 'https://127.0.0.1/fr/';
        Currency::setCurrencies(array(Currency::make(3, 'GBP', 'Pound', 0.9, '£')));

        $vars = $this->module->getWidgetVariables('displayNav', array());
        $url = $vars['currencies'][0]['url'];

        $this->assertParsedUrl($url, array(
            'scheme' => 'https',
            'host' => '127.0.0.1',
            'port_absent' => true,
            'path' => '/fr/',
            'query' => array(
                'SubmitCurrency' => '1',
                'id_currency' => '3',
            ),
            'query_count' => 2,
        ));
    }

    public function testUrlReplacesExistingCurrencyParamsOnce()
    {
        LinkStub::$url = 'http://127.0.0.1/en/?SubmitCurrency=9&id_currency=9&ref=campaign';
        Currency::setCurrencies(array(Currency::make(3, 'GBP', 'Pound', 0.9, '£')));

        $vars = $this->module->getWidgetVariables('displayNav', array());
        $url = $vars['currencies'][0]['url'];

        $this->assertParsedUrl($url, array(
            'scheme' => 'http',
            'host' => '127.0.0.1',
            'path' => '/en/',
            'query' => array(
                'SubmitCurrency' => '1',
                'id_currency' => '3',
                'ref' => 'campaign',
            ),
            'query_count' => 3,
        ));
    }

    public function testHttpsPathIsPreserved()
    {
        LinkStub::$url = 'https://127.0.0.1/en/2-category';
        Currency::setCurrencies(array(Currency::make(1, 'EUR', 'Euro', 1, '€')));

        $vars = $this->module->getWidgetVariables('displayNav', array());
        $url = $vars['currencies'][0]['url'];

        $parsed = parse_url($url);
        $this->assertInternalType('array', $parsed);
        $this->assertSame('/en/2-category', $parsed['path']);
    }
}
