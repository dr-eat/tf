<?php
require_once '../config/config.php';
require_once '../models/App.php';
require_once '../models/MDb.php';

$envs = [App::ENV_DEV, App::ENV_TEST];

foreach ($envs as $env) {
    App::$env = $env;
    $db = App::get(true)->db;

    $last_id = $db->getOne('SELECT MAX(id) FROM migrations') ?: 0;
    if (!$last_id) {
        $res = $db->query('CREATE DATABASE IF NOT EXISTS ' . App::get()->config->db_name);
        $db->fetchAll();
        echo App::get()->config->db_name . " created " . ($res ? "with success" : "with failure" ) . "\n";
        $db->query("USE " . App::get()->config->db_name);
        $db->fetchAll();
    }
    $row = $db->getRow("SHOW TABLES LIKE 'clients'", []);
    if (empty($row)) {
        $sqls = file_get_contents('../db_migrations/01--create.sql');
        $db->applyMigration($sqls);
        $sql = "INSERT INTO migrations (id, created_dt) VALUES (?, ?);";
        $db->query($sql, [1, time()]);
        $db->fetchAll();
        $last_id = 1;
    }

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
    $db_name = App::get()->config->db_name;

    echo "$db_name last applied migration nr: $last_id\n";

    $files = scandir('../db_migrations');
    foreach ($files as $file) {
        $imported = false;
        $num = explode('--', $file)[0];
        if (intval($num) <= $last_id) {
            continue;
        }
        $sqls = file_get_contents('../db_migrations/' . $file);
        $db->beginTransaction();
        $db->applyMigration($sqls);
        $sql = "INSERT INTO $db_name.migrations (id, created_dt) VALUES (?, ?)";
        $db->query($sql, [ltrim($num, '0'), time()]);
        $db->fetchAll();
        if (!$db->commitTransaction()) {
            die("Migration $num failed!");
        }
        else
        {
            die("Migration $num applied!");
        }
        $imported = true;
    }

}