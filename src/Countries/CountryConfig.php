<?php

namespace PlacetoPay\PaymentMethod\Countries;

use PlacetoPay\PaymentMethod\Constants\Environment;

abstract class CountryConfig implements CountryConfigInterface
{
    public static function resolve(string $countryCode): bool
    {
        return true;
    }

    public static function getEndpoints(): array
    {
        return [
            Environment::DEV => 'https://dev.placetopay.com/redirection',
            Environment::TEST => 'https://checkout-test.placetopay.com',
            Environment::PROD => 'https://checkout.placetopay.com',
        ];
    }
}