<?php
require_once '../config/config.php';
require_once '../models/App.php';
require_once '../models/MDb.php';

$envs = [App::ENV_DEV, App::ENV_TEST];

foreach ($envs as $env) {
    App::$env = $env;
    $db = App::get(true)->db;
    $last_id = $db->getOne('SELECT MAX(id) FROM migrations') ?: 0;
    /*
     // can be implemeted this way:
    do {
        $num = $last_id +1;
        $files = scandir('../db_migrations');
        foreach ($files as $file) {
            $file_num = explode('--', $file)[0];
            if ($file_num == $num) {
                $sqls = file_get_contents('../db_migrations/' . $file);
                $imported = $db->applyMigration($sqls);
            }
        }
    } while ($imported);*/
    $files = scandir('../db_migrations');
    foreach ($files as $file) {
        $imported = false;
        $num = explode('--', $file)[0];
        if ($num <= $last_id) {
            continue;
        }
        $sqls = file_get_contents('../db_migrations/' . $file);
        $db->beginTransaction();
        $db->applyMigration($sqls);
        $sql = "INSERT INTO migrations (id, created_dt) VALUES (?, ?);";
        $db->query($sql, [ltrim($num, '0'), time()]);
        if (!$db->commitTransaction()) {
            die("Migration $num failed!");
        }
        $imported = true;
    }
}

