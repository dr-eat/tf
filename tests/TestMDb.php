<?php
class TestMDb extends MDb {
    public function beginTransaction(): bool
    {
        return true;
    }

    /**
     * Commits the current transaction
     *
     * @return bool
     */
    public function commitTransaction(): bool
    {
        return true;
    }

    /**
     * Rolls back current transaction
     *
     * @return bool
     */
    public function rollbackTransaction(): bool
    {
        return true;
    }

    public function beginTestTransaction(): bool
    {
        return $this->_link->begin_transaction();
    }

    /**
     * Commits the current transaction
     *
     * @return bool
     */
    public function commitTestTransaction(): bool
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
    public function rollbackTestTransaction(): bool
    {
        return $this->_link->rollback();
    }

    public function createSavepoint(string $name): bool
    {
        return $this->_link->query("SAVEPOINT `$name`");
    }
    public function rollbackSavepoint(string $name): bool
    {
        return $this->_link->query("ROLLBACK TO SAVEPOINT `$name`");
    }
}
