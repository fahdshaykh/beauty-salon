<?php

function currency_symbol()
{
    if (auth()->check()) {
        return auth()->user()->companySetting->currency_symbol ?? '$';
    }
    return '$';
}

function currency_code()
{
    if (auth()->check()) {
        return auth()->user()->companySetting->currency_code ?? 'USD';
    }
    return 'USD';
}

function money($amount, $withSymbol = true)
{
    $symbol = currency_symbol();
    $formatted = number_format($amount, 2);
    return $withSymbol ? $symbol . $formatted : $formatted;
}