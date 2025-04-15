<?php
require_once '../config/config.php';
require_once '../tests/TestConfig.php';
require_once '../models/MDb.php';
require_once '../models/App.php';
require_once '../models/MBase.php';
require_once '../models/MActions.php';
require_once '../models/MAccount.php';
require_once '../models/MAccountAct.php';
require_once '../models/MClient.php';

require_once '../controllers/CBase.php';
require_once '../controllers/CAccount.php';
require_once '../controllers/CClient.php';
require_once '../controllers/CRate.php';
require_once '../controllers/CTransaction.php';

class BaseTest extends PHPUnit\Framework\TestCase
{
    public static ?App $_app = null;
    protected ?App $app = null;
    private $savepoint = '';

    protected function setUp(): void
    {
        parent::setUp();
        if (empty(self::$_app) || self::$_app::$env != App::ENV_TEST)
        {
            self::initApp();
        }
        $this->app = &self::$_app;
        $class_name_parts = explode('\\', get_class($this));
        if (!$this->savepoint) {
            $this->savepoint = array_pop($class_name_parts);
            $this->app->db->createSavepoint($this->savepoint);
        }
        $_SESSION = [];
        $_COOKIE = [];
        $_POST = [];
        $_GET = [];
        $_FILES = [];
        $_SERVER['argv'] = [];

    }

    /**
     * initApp
     *
     * @return void
     */
    public static function initApp()
    {
        App::$env = App::ENV_TEST;
        self::$_app = App::get(true);
        self::$_app->config = new TestConfig();
        self::$_app->db->beginTestTransaction();
        set_time_limit(100);
    }

    /**
     * tearDown
     *
     * @return void
     */
    protected function tearDown(): void
    {
        $this->app->db->rollbackSavepoint($this->savepoint);
        parent::tearDown();
    }

    /**
     * Set up before class
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        set_time_limit(100);
        self::initApp();
    }

    /**
     * tearDownAfterClass Method
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        self::$_app->db->rollbackTestTransaction();
        self::$_app = null;
    }

    /**
     * Reflection
     *
     * @param mixed $object
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public static function callMethod($object, $method, $parameters = [])
    {
        try
        {
            $class_name = get_class($object);
            $reflection = new ReflectionClass($class_name);
        }
        catch (ReflectionException $e)
        {
            throw new Exception($e->getMessage());
        }
        $method = $reflection->getMethod($method);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }
}