<?php
/**
 * CBase - base controllers logic
 *
 * PHP version 8.2
 *
 * @author    Aleksandr Jermakovich <dreat@dreat.net>
 * @copyright 2025
 */
class CBase {
    /**
     * Get variable passed in $_GET or $_POST array
     *
     * @param string $field
     * @param mixed $default
     * @return mixed|null
     */
    public function getVar(string$field, mixed $default = NULL): mixed
    {
        return $_POST[$field] ?? ($_GET[$field] ?? $default);
    }
}