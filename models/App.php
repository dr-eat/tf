<?php
require_once '../tests/TestConfig.php';
require_once '../tests/TestMDb.php';
/**
 * Main class with core functionality
 */
class App {
    const ENV_PROD = 2;
    const ENV_DEV = 1;
    const ENV_TEST = 0;
    protected static ?App $app = null;
    public static int $env = self::ENV_DEV;
    public MDb $db;
    public Config $config;
    public int $client_id = 0;

    /**
     * Perform initialization
     * @return void
     */
    protected static function init(): void
    {
        self::$app = new App();
        self::$app->config = self::$env == self::ENV_TEST ? new TestConfig() : new Config();
        self::$app->db = self::$env == self::ENV_TEST ? new TestMDb() : new MDb();
        self::$app->db->connect();
    }
    /**
     * Return instance of App class
     * @return App
     */
    public static function get($re_init = false): App
    {
        if (!self::$app || $re_init) {
            self::init();
        }
        return self::$app;
    }

    /**
     * Record error/debug info
     *
     * @param string $message Information
     * @return void
     */
    public function log(string $message) {
        echo $message . "\n";
    }
}