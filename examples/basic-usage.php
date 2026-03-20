<?php

declare(strict_types=1);

/**
 * Example: Working with the ps_currencyselector PrestaShop module.
 *
 * ps_currencyselector renders a currency switcher dropdown in the header or
 * navigation. When a customer selects a currency, prices across the store
 * update using the configured exchange rates.
 *
 * This file documents common usage patterns.
 */

// --- Widget invocation in Smarty/Twig template ---
// {widget name="ps_currencyselector" hook="displayNav1"}

// --- Getting available currencies programmatically ---
// $currencies = Currency::getCurrencies(object: false, active: true);
//
// foreach ($currencies as $currency) {
//     echo $currency['name'] . ' (' . $currency['iso_code'] . '): ' . $currency['symbol'] . "\n";
// }
// Output example:
//   Euro (EUR): €
//   US Dollar (USD): $
//   British Pound (GBP): £

// --- Getting the current currency ---
// $context  = Context::getContext();
// $currency = $context->currency;
// echo "Current: {$currency->name} ({$currency->iso_code})\n";

// --- Programmatically switching currency ---
// Currency switching happens via the module's front controller action.
// The customer selects from the dropdown; a redirect updates the session:
//   GET /module/ps_currencyselector/select?id_currency=2

// --- Exchange rate update ---
// PrestaShop can auto-update exchange rates via a cron job:
//   php bin/console prestashop:update:exchange-rate
//
// Or configure automatic updates in:
//   International > Currencies > Update currency rates

// --- Back Office configuration ---
// Manage currencies at: International > Currencies
//   - Add/remove active currencies
//   - Set conversion rates (manual or automatic)
//   - Set default currency
