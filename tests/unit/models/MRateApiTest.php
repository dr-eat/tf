<?php

namespace models;
use BaseTest;
use MRateAct;
use MRateApi;
require_once('../tests/BaseTest.php');
/**
 * MExchageRateApi - get exchange rate from the exchangerate.host
 *
 * PHP version 8.2
 *
 * @author    Aleksandr Jermakovich <dreat@dreat.net>
 * @copyright 2025
 */
class MRateApiTest extends BaseTest
{
    /**
     * @param array $rates
     * @return int
     */
    public function testSave(): void
    {
        $rates = [ "EURAE" => 4.04,
            'USDEUR' => 0.95,
            'EURPLN' => 4];
        $mexchage_rate_api = new MRateApiMock();
        $cnt = $mexchage_rate_api->save($rates);
        $this->assertSame(2, $cnt, 'Wrong saved count');
        $mrate_act = new MRateAct();
        $this->assertSame(0.95, $mrate_act->get('USD', 'EUR'));
        $this->assertSame(4.0, $mrate_act->get('EUR', 'PLN'));
    }

    /**
     * @param string $currency
     * @return int
     */
    public function testGetExchangeRates(): void
    {
        $mexchage_rate_api = new MRateApiMock();
        $cnt = $mexchage_rate_api->getExchangeRates('');
        $this->assertEmpty($cnt, 'Empty test failed');

        $mexchage_rate_api->api_return_data = '';
        $cnt = $mexchage_rate_api->getExchangeRates('EUR');
        $this->assertEmpty($cnt, 'Empty response test failed');

        $mexchage_rate_api = new MRateApiMock();
        $cnt = $mexchage_rate_api->getExchangeRates('EUR');
        $this->assertSame(169, $cnt);
    }
}

class MRateApiMock extends MRateApi {
    public string $api_return_data = '{"success":true,"terms":"https:\/\/currencylayer.com\/terms","privacy":"https:\/\/currencylayer.com\/privacy","timestamp":1744186084,"source":"EUR","quotes":{"EURAED":4.043945,"EURAFN":78.219221,"EURALL":98.848466,"EURAMD":426.173947,"EURANG":1.970976,"EURAOA":1009.0416,"EURARS":1184.218987,"EURAUD":1.829426,"EURAWG":1.983133,"EURAZN":1.871766,"EURBAM":1.954754,"EURBBD":2.20525,"EURBDT":132.699011,"EURBGN":1.948172,"EURBHD":0.414929,"EURBIF":3246.522196,"EURBMD":1.100976,"EURBND":1.475317,"EURBOB":7.546997,"EURBRL":6.671807,"EURBSD":1.092131,"EURBTC":1.4205427e-5,"EURBTN":94.143781,"EURBWP":15.415963,"EURBYN":3.574204,"EURBYR":21579.129899,"EURBZD":2.193856,"EURCAD":1.563898,"EURCDF":3162.003108,"EURCHF":0.93065,"EURCLF":0.028728,"EURCLP":1102.440325,"EURCNY":8.080393,"EURCNH":8.115723,"EURCOP":4870.993135,"EURCRC":560.70768,"EURCUC":1.100976,"EURCUP":29.175864,"EURCVE":110.206544,"EURCZK":25.164983,"EURDJF":194.488605,"EURDKK":7.467501,"EURDOP":68.514281,"EURDZD":146.970398,"EUREGP":56.611787,"EURERN":16.51464,"EURETB":144.012066,"EURFJD":2.569454,"EURFKP":0.865013,"EURGBP":0.860765,"EURGEL":3.033159,"EURGGP":0.865013,"EURGHS":16.92902,"EURGIP":0.865013,"EURGMD":78.71395,"EURGNF":9452.07224,"EURGTQ":8.423455,"EURGYD":228.498799,"EURHKD":8.543695,"EURHNL":27.944178,"EURHRK":7.529026,"EURHTG":142.914195,"EURHUF":408.429622,"EURIDR":18592.62236,"EURILS":4.178821,"EURIMP":0.865013,"EURINR":95.256994,"EURIQD":1430.754088,"EURIRR":46364.863764,"EURISK":144.9108,"EURJEP":0.865013,"EURJMD":172.466953,"EURJOD":0.780482,"EURJPY":160.145217,"EURKES":142.521356,"EURKGS":95.84778,"EURKHR":4370.681683,"EURKMF":494.882015,"EURKPW":990.852517,"EURKRW":1627.137922,"EURKWD":0.338825,"EURKYD":0.910117,"EURKZT":565.719928,"EURLAK":23658.450976,"EURLBP":97858.493448,"EURLKR":326.556786,"EURLRD":218.42918,"EURLSL":21.271017,"EURLTL":3.250896,"EURLVL":0.665969,"EURLYD":6.073833,"EURMAD":10.437264,"EURMDL":19.391714,"EURMGA":5112.265112,"EURMKD":61.529248,"EURMMK":2311.372267,"EURMNT":3864.170508,"EURMOP":8.740843,"EURMRU":43.236459,"EURMUR":49.427715,"EURMVR":16.966388,"EURMWK":1893.786888,"EURMXN":22.918709,"EURMYR":4.946725,"EURMZN":70.356506,"EURNAD":21.271017,"EURNGN":1712.545916,"EURNIO":40.189413,"EURNOK":12.079744,"EURNPR":150.633468,"EURNZD":1.980584,"EUROMR":0.423879,"EURPAB":1.092131,"EURPEN":4.057785,"EURPGK":4.509649,"EURPHP":63.167947,"EURPKR":306.57507,"EURPLN":4.282164,"EURPYG":8756.434905,"EURQAR":3.981188,"EURRON":4.977734,"EURRSD":117.181275,"EURRUB":94.960869,"EURRWF":1546.279815,"EURSAR":4.134136,"EURSBD":9.163703,"EURSCR":15.931645,"EURSDG":661.137711,"EURSEK":11.015772,"EURSGD":1.484352,"EURSHP":0.865194,"EURSLE":25.057948,"EURSLL":23086.917743,"EURSOS":624.163259,"EURSRD":40.574333,"EURSTD":22787.980654,"EURSVC":9.556931,"EURSYP":14314.403569,"EURSZL":21.256918,"EURTHB":38.12021,"EURTJS":11.866136,"EURTMT":3.864426,"EURTND":3.366714,"EURTOP":2.578594,"EURTRY":41.837639,"EURTTD":7.407071,"EURTWD":36.328361,"EURTZS":2927.214473,"EURUAH":44.988733,"EURUGX":4053.849738,"EURUSD":1.100976,"EURUYU":46.464299,"EURUZS":14161.809802,"EURVES":80.664031,"EURVND":28663.910557,"EURVUV":137.687456,"EURWST":3.130307,"EURXAF":655.609249,"EURXAG":0.03641,"EURXAU":0.000362,"EURXCD":2.975443,"EURXDR":0.815364,"EURXOF":655.6152,"EURXPF":119.203268,"EURYER":270.151989,"EURZAR":21.648546,"EURZMK":9910.111693,"EURZMW":30.444128,"EURZWL":354.513828}}';
    protected function getApiData(string $url): string
    {
        return $this->api_return_data;
    }
}