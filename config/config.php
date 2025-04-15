<?php
class Config {
    public string $admin_mail = 'dreat@dreat.net';
    public string $db_name = 'tf';
    public string $db_user = 'root';
    public string $db_pass = 'root';
    public string $db_host = 'localhost';
    public int $db_port = 3306;

    public string $exchangerate_api_key = '169c4a605b782aa395005dc4bbd59046';

    public int $transaction_list_limit = 10;

    public array $exchange_currencies = ['usd', 'eur'];

    public bool $transaction_process_immediate = true;

}
