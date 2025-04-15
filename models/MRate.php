<?php
/**
 * MRate - handle exchange rate logic
 *
 * PHP version 8.2
 *
 * @author    Aleksandr Jermakovich <dreat@dreat.net>
 * @copyright 2025
 */
class MRate extends MBase {
    public string $currency_from = '';
    public string $currency_to = '';
    public float $rate = 1;

    protected array $db_fields = ['currency_from', 'currency_to', 'rate'];
}