<?php
/**
 * MRate - handle exchange rate logic
 *
 * PHP version 8.2
 *
 * @author    Aleksandr Jermakovich <dreat@dreat.net>
 * @copyright 2025
 */
class MRateAct extends MBase {
    protected string $intermediate = 'EUR';

    /**
     * Get exchange rate
     *
     * @param string $currency_from
     * @param string $currency_to
     * @return float|int
     */
    public function get(string $currency_from, string $currency_to): float|int
    {
        if ($currency_from == $currency_to) {
            return 1;
        }
        if (empty($currency_from) || empty($currency_to)) {
            return 0;
        }
        $sql = "SELECT rate FROM rates WHERE currency_from = ? AND currency_to = ?";
        $rate = $this->app->db->getOne($sql, [$currency_from, $currency_to]);
        if ($rate) {
            return (float)$rate;
        }
        $rate = $this->app->db->getOne($sql, [$currency_to, $currency_from]);
        if ($rate) {
            return 1 / $rate;
        }
        return $this->calc($currency_from, $currency_to);
    }

    /**
     * Calculate exchange rate using intermediate currency
     *
     * @param string $currency_from
     * @param string $currency_to
     * @return float|int
     */
    protected function calc(string $currency_from, string $currency_to): float|int
    {
        if ($currency_from == $this->intermediate || $currency_to == $this->intermediate) {
            return 0;
        }
        $rate1 = $this->get($currency_from, $this->intermediate);
        $rate2 = $this->get($this->intermediate, $currency_to);
        if (!$rate1 || !$rate2) {
            return 0;
        }
        return $rate1 * $rate2;
    }
}