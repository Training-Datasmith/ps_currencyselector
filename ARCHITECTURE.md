# Architecture: ps_currencyselector

## Purpose

A PrestaShop front-office module that adds a currency switcher widget, allowing customers
to select their preferred display currency. Updates prices across the store in real time.

## Directory Structure

```
ps_currencyselector.php   # Main module class (WidgetInterface)
views/templates/           # Smarty/Twig templates for currency dropdown
translations/              # Translation files
tests/                     # PHPStan and unit tests
```

## Key Design Decisions

Retrieves all active currencies from PrestaShop's `Currency` model. The currency switch
is handled via a POST form submission that updates the session currency and redirects
back to the current page. Implements `WidgetInterface` for placement in the header or
other positions.

## Extension Points

Override template in theme for custom dropdown styling.
