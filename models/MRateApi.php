<?php
/**
 * MRateApi - get exchange rate from the exchangerate.host
 *
 * PHP version 8.2
 *
 * @author    Aleksandr Jermakovich <dreat@dreat.net>
 * @copyright 2025
 */
class MRateApi extends MBase {
    /**
     * Load and save exchange rates from exchangerate.host
     * @param string $currency
     * @return int
     */
    public function getExchangeRates(string $currency): int
    {
        if (empty($currency)) {
            return 0;
        }
        $url = 'https://api.exchangerate.host/live?access_key=' . App::get()->config->exchangerate_api_key . '&source=' . $currency;
        $data = $this->getApiData($url);
        if (empty($data)) {
            return 0;
        }
        $rates = json_decode($data, true);
        if (empty($rates['quotes']) || !is_array($rates['quotes'])) {
            return 0;
        }
        return $this->save($rates['quotes']);
    }

    /**
     * Save exchange rates to database
     * @param array $rates
     * @return int
     */
    public function save(array $rates): int
    {
        if (empty($rates)) {
            return 0;
        }
        $inserted = 0;
        foreach ($rates as $currencies => $rate) {
            if (strlen($currencies) != 6) {
                continue;
            }
            [$currency_from, $currency_to] = str_split($currencies, 3);
            if (empty($currency_from) || empty($currency_to)) {
                continue;
            }
            $mrate = new MRate();
            $mrate->currency_from = $currency_from;
            $mrate->currency_to = $currency_to;
            $mrate->rate = $rate;
            $inserted += $mrate->replace() ? 1 : 0;
        }
        return $inserted;
    }

    /**
     * Connect to 3rd party service and get information
     * @param string $url
     * @return string
     */
    protected function getApiData(string $url): string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:135.0) Gecko/20100101 Firefox/135.0");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

}