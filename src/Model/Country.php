<?php

namespace App\Model;

class Country
{
    const EU_COUNTRY_CODES = array(
        'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'EL',
        'ES', 'FI', 'FR', 'GR', 'HR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV',
        'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK'
    );

    public function __construct(
        protected string $isoCode2,
        protected string $name,
    ) {
        if (\strlen($isoCode2) != 2) {
            throw new \RuntimeException('Wrong ISO Alpha2 code '.$isoCode2);
        }
    }

    public function isEU(): bool
    {
        return (in_array($this->isoCode2, self::EU_COUNTRY_CODES));
    }
}