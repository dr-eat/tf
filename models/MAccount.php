<?php
/**
 * MAccount - Account object
 *
 * PHP version 8.2
 *
 * @author Aleksandr Jermakovich <dreat@dreat.net>
 * @copyright 2025
 */
class MAccount extends MBase {
    public int $client_id = 0;
    public string $name = '';
    public float $balance = 0;
    public string $currency = '';
    protected array $db_fields = ['client_id', 'name', 'balance', 'currency'];
}