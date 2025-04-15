<?php

/**
 * MDb - MySQL connection logic
 *
 * PHP version 8.2
 *
 * @author Aleksandr Jermakovich <dreat@dreat.net>
 * @copyright 2025
 */
class MDb {
    protected mysqli $_link;
    public string $error;
    /**
     * Connect to MySQL
     */
    public function connect(): void
    {
        mysqli_report(MYSQLI_REPORT_OFF);
        $this->_link = new mysqli(App::get()->config->db_host, App::get()->config->db_user, App::get()->config->db_pass, App::get()->config->db_name, App::get()->config->db_port);
        if ($this->_link->connect_errno) {
            $this->error = $this->_link->connect_errno;
            App::get()->log("Failed to connect to MySQL: " . $this->_link->connect_error, true);
        }
        $this->_link->set_charset('utf8');
    }

    /**
     * Disconnect from MySQL
     *
     * @return bool
     */
    public function close(): bool
    {
        return $this->_link->close();
    }

    /**
     * Execute Query on MySQL
     *
     * @param string $sql The query
     * @param array $params An optional list array with as many elements as there are bound parameters in the SQL statement being executed
     * @return mysqli_result|bool
     */
    public function query(string $sql, array $params = []): mysqli_result|bool
    {
        // echo $sql . "\n";
        $result = $this->_link->execute_query($sql, $params);
        if ($this->_link->error) {
            $this->error = $this->_link->error;
            App::get()->log("Failed to execute: $sql : " . print_r($params, 1) . $this->error);
        }
        else {
            $this->error = '';
        }
        return $result;
    }

    /**
     * @return int
     */
    public function getLastInsertId(): int
    {
        return $this->_link->insert_id;
    }

    /**
     * Get first column of first row of specified query
     *
     * @param string $sql The query
     * @param array $params An optional list array with as many elements as there are bound parameters in the SQL statement being executed
     * @param string $fieldname Column to be fetched
     * @return string
     */
    public function getOne(string $sql, array $params = [], string $fieldname = ''): string
    {
        $result = $this->query($sql, $params);
        if (!$result) {
            return '';
        }
        $row = $fieldname ? $result->fetch_assoc() : $result->fetch_row();
        return $row[$fieldname ?: 0] ?? '';
    }

    /**
     * Get first row of specified query
     *
     * @param string $sql The query
     * @param array $params An optional list array with as many elements as there are bound parameters in the SQL statement being executed
     * @return array
     */
    public function getRow(string $sql, array $params): array
    {
        $result = $this->query($sql, $params);
        return $result ? $result->fetch_assoc() ?? [] : [];
    }

    /**
     * Get first row of specified query
     *
     * @param string $sql The query
     * @param array $params An optional list array with as many elements as there are bound parameters in the SQL statement being executed
     * @return array
     */
    public function getAll(string $sql, array $params): array
    {
        $result = $this->query($sql, $params);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Starts a transaction
     *
     * @return bool
     */
    public function beginTransaction(): bool
    {
        return $this->_link->begin_transaction();
    }

    /**
     * Commits the current transaction
     *
     * @return bool
     */
    public function commitTransaction(): bool
    {
        if (!$this->_link->commit()) {
            $this->_link->rollback();
            return false;
        }
        return true;
    }

    /**
     * Rolls back current transaction
     *
     * @return bool
     */
    public function rollbackTransaction(): bool
    {
        return $this->_link->rollback();
    }

    /**
     * A string containing the queries to be executed. Multiple queries must be separated by a semicolon.
     *
     * @param string $sqls
     * @return bool
     */
    public function applyMigration(string $sqls): bool
    {
        return $this->_link->multi_query($sqls);
    }
}