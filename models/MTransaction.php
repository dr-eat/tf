<?php
/**
 * MTransaction - transaction object
 *
 * PHP version 8.2
 *
 * @author    Aleksandr Jermakovich <dreat@dreat.net>
 * @copyright 2025
 */
class MTransaction extends MBase {
    const STATUS_NEW = 'new';
    const STATUS_COMPLETED = 'completed';
    const STATUS_DECLINED = 'declined';
    public float $amount = 0;
    public string $currency = '';
    public float $rate = 1;
    public int $account_id_from = 0;
    public int $account_id_to = 0;
    public string $status = self::STATUS_NEW;
    public array $invalid_fields = [];

    protected MAccount $account_to;
    protected MAccount $account_from;

    protected array $db_fields = ['amount', 'currency', 'rate', 'account_id_from', 'account_id_to', 'status'];

    /**
     * Check if current transaction is valid
     * @return bool
     */
    public function isValid(): bool
    {
        $this->invalid_fields = [];
        if ($this->amount < 0) {
            $this->invalid_fields[] = 'amount';
        }
        if (!$this->isNotEmpty()) {
            return false;
        }
        if (!$this->isValidAccounts()) {
            return false;
        }
        if (!$this->isValidAmount()) {
            return false;
        }
        return true;
    }

    /**
     * Check if all fields are filled
     * @return bool
     */
    protected function isNotEmpty(): bool
    {
        $check_empty = ['amount', 'account_id_from', 'account_id_to', 'currency'];
        foreach ($check_empty as $field) {
            if (empty($this->$field)) {
                $this->invalid_fields[] = $field;
            }
        }
        return empty($this->invalid_fields);
    }

    /**
     * Check accounts info and  accordance to specified currency
     * @return bool
     */
    protected function isValidAccounts(): bool
    {
        $this->account_to = new MAccount($this->account_id_to);
        if (empty($this->account_to->id)) {
            $this->invalid_fields[] = 'account_to';
        }
        $this->account_from = new MAccount($this->account_id_from);
        if (empty($this->account_from->id) || $this->account_from->client_id != $this->app->client_id) {
            $this->invalid_fields[] = 'account_from';
        }
        if ($this->account_to->currency != $this->currency) {
            $this->invalid_fields[] = 'currency';
        }
        return empty($this->invalid_fields);
    }

    /**
     * Check if account has enougth balance to perform transaction
     * @return bool
     */
    protected function isValidAmount(): bool
    {
        $mrate_act = new MRateAct();
        if (empty($this->account_from) || $this->account_from->id != $this->account_id_from) {
            $this->account_from = new MAccount($this->account_id_from);
        }
        if (empty($this->account_to) || $this->account_to->id != $this->account_id_to) {
            $this->account_to = new MAccount($this->account_id_to);
        }
        $this->rate = $mrate_act->get($this->account_from->currency, $this->account_to->currency);
        if (!$this->rate) {
            $this->invalid_fields[] = 'rate';
            return false;
        }
        if ($this->amount / $this->rate > $this->account_from->balance) {
            $this->invalid_fields[] = 'amount';
            return false;
        }
        return true;
    }

    /**
     * Create new transaction
     * @return int
     */
    public function create(): int
    {
        $res = parent::create();
        return ($res && App::get()->config->transaction_process_immediate) ? ($this->process() ? $res : 0) : $res;
    }

    /**
     * Process new transaction
     * @return bool
     */
    public function process(): bool
    {
        $res = false;
        $this->account_from = new MAccount($this->account_id_from);
        $this->account_to = new MAccount($this->account_id_to);
        if ($this->isValidAmount()) {
            $this->app->db->beginTransaction();
            $this->account_to->balance += $this->amount;
            $this->account_to->update();
            $this->account_from->balance -= $this->amount / $this->rate;
            $this->account_from->update();
            $this->status = self::STATUS_COMPLETED;
            $this->update();
            $res = $this->app->db->commitTransaction();
        }
        if (!$res) {
            $this->status = self::STATUS_DECLINED;
            $this->update();
        }
        return $res;
    }
}