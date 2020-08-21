<?php

namespace App\Service\Import\Parser;

interface ParserInterface
{
    public function parse($fileInfo, string $currencyTo);
}
