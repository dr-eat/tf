<?php
/**
 * MTransaction - transaction object
 *
 * PHP version 8.2
 *
 * @author    Aleksandr Jermakovich <dreat@dreat.net>
 * @copyright 2025
 */
class MTransactionAct extends MActions {
    /**
     * List all transaction for the specified account
     * @param int $account_id
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function listByAccount(int $account_id, int $limit = 0, int $offset = 0): array
    {
        $sql = "SELECT * FROM transactions WHERE account_id_from = ? OR account_id_to = ? ORDER BY created_dt,id DESC LIMIT ? OFFSET ?";
        return $this->app->db->getAll($sql, [$account_id, $account_id, $limit, $offset]);
    }

    /**
     * Process transactions with status `new`
     * @return void
     */
    public function processTransactions(): void
    {
        $sql = "SELECT * FROM transactions WHERE status = ?";
        $trans = $this->app->db->getAll($sql, [MTransaction::STATUS_NEW]);
        foreach ($trans as $tran) {
            $mtran = new MTransaction($tran['id']);
            $mtran->process();
        }
    }
}