<?php
/**
 * CAccount - handle Account logic
 *
 * PHP version 8.2
 *
 * @author    Aleksandr Jermakovich <dreat@dreat.net>
 * @copyright 2025
 */
require_once '../models/MAccount.php';
require_once '../models/MAccountAct.php';

class CAccount extends CBase {

    /**
     * Prepare list of Accounts for the specified client
     * GET: client_id
     * @return array
     */
    public function list(): array
    {
        $id = $this->getVar('client_id');
        if (empty($id)) {
            return [];
        }
        $mclient = new MAccountAct();
        return $mclient->listByClientId($id);
    }
}