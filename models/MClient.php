<?php
/**
 * MClient - Client object
 *
 * PHP version 8.2
 *
 * @author    Aleksandr Jermakovich <dreat@dreat.net>
 * @copyright 2025
 */
class MClient extends MBase {
    const STATUS_NEW = 'new';
    const STATUS_ACTIVE = 'active';
    const STATUS_BLOCKED = 'blocked';

    public string $login = '';
    public string $password = '';
    public string $name = '';
    public string $email = '';
    public string $status = '';

    protected array $db_fields = ['login', 'password', 'name', 'email', 'status'];

    /**
     * Create new client
     * @return int
     */
    public function create(): int
    {
        $this->status = self::STATUS_NEW;
        return parent::create();
    }
    /**
     * Get id of authenticated client
     *
     * @param string $login Submitted login
     * @param string $password Submitted password
     * @return int
     */
    public static function getAuthId(string $login, string $password): int
    {
        if (empty($login) || empty($password)) {
            return 0;
        }
        $sql = "SELECT id FROM clients WHERE login = ? and password = ? and status = ?";
        return App::get()->db->getOne($sql, [$login, $password, self::STATUS_ACTIVE]) ?: 0;
    }
}