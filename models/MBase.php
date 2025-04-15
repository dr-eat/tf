<?php
/**
 * MBase - Base object
 *
 * PHP version 8.2
 *
 * @author    Aleksandr Jermakovich <dreat@dreat.net>
 * @copyright 2025
 */
class MBase {
    public int $id = 0;

    public int $created_dt = 0;

    public App $app;

    protected array $fields = ['created_dt'];
    protected array $db_fields = [];

    protected string $table = '';

    public function __construct($id = 0)
    {
        $this->app = App::get();
        $class_name = get_called_class();
        $this->table = strtolower(substr($class_name, 1)) . 's';
        if (empty($id)) {
            return;
        }
        $this->loadObject($id);
    }

    /**
     * Load info from the database and populate it to the object
     * @param int $id Item id
     * @return void
     */
    public function loadObject(int $id): void
    {
        $row = $this->app->db->getRow("SELECT * FROM `$this->table` WHERE id = ?", [$id]);
        $this->populateObject($row);
    }

    /**
     * Create new database record based on current object representation
     *
     * @return int
     */
    public function create(): int
    {
        $row = [];
        foreach ($this->db_fields as $field) {
            $row[$field] = $this->$field;
        }
        $row['created_dt'] = time();
        $sql = "INSERT INTO $this->table (" . implode(', ', array_keys($row)) .
                ') VALUES (' . str_repeat('?,', count($row) - 1) . '?)';
        $res = $this->app->db->query($sql, array_values($row));
        if ($res) {
            $this->id = $this->app->db->getLastInsertId();
        }
        return $res ? $this->id : 0;
    }

    /**
     * Replace database record based on current object representation
     *
     * @return int
     */
    public function replace(): int
    {
        $row = [];
        foreach ($this->db_fields as $field) {
            $row[$field] = $this->$field;
        }
        $row['created_dt'] = time();
        $sql = "REPLACE INTO $this->table (" . implode(', ', array_keys($row)) .
            ') VALUES (' . str_repeat('?,', count($row) - 1) . '?)';
        $res = $this->app->db->query($sql, array_values($row));
        if ($res) {
            $this->id = $this->app->db->getLastInsertId();
        }
        return $res ? $this->id : 0;
    }

    /**
     * Update database record
     *
     * @return bool
     */
    public function update(): bool
    {
        // to use on production: NEED TO UPDATE CODE TO UPDATE ONLY CHANGED FIELDS!
        $row = [];
        foreach ($this->db_fields as $field) {
            $row[$field] = $this->$field;
        }
        $sql = "UPDATE $this->table SET " . implode(' = ?, ', array_keys($row)) . ' = ? WHERE id = ?';
        $row[] = $this->id;
        return (bool)$this->app->db->query($sql, array_values($row));
    }

    /**
     * Delete record from the database
     *
     * @return bool
     */
    public function delete(): bool
    {
        $sql = "DELETE FROM $this->table WHERE id = ? LIMIT 1";
        $row[] = $this->id;
        return (bool)$this->app->db->query($sql, array_values($row));
    }

    /**
     * Populate object info
     *
     * @param array $row Database record
     * @return void
     */
    public function populateObject(array $row): void
    {
        if (empty($row)) {
            return;
        }
        foreach ($row as $key => $value) {
            $this->$key = $value;
        }
    }
}