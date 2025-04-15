<?php
/**
 * MAccountAct - Account actions
 *
 * PHP version 8.2
 *
 * @author Aleksandr Jermakovich <dreat@dreat.net>
 * @copyright 2025
 */
class MAccountAct extends MActions {

    /**
     * Get list of Accounts for the specified client
     *
     * @param int $client_id Client ID
     * @return array
     */
    public function listByClientId(int $client_id): array
    {
        return App::get()->db->getAll("SELECT * FROM accounts WHERE client_id = ?", [$client_id]);
    }
}