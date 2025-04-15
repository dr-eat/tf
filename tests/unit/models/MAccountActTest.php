<?php

namespace models;
use BaseTest;
use MAccount;
use MAccountAct;
require_once('../tests/BaseTest.php');
/**
 * MAccountAct - Account actions
 *
 * PHP version 8.2
 *
 * @author Aleksandr Jermakovich <dreat@dreat.net>
 * @copyright 2025
 */
class MAccountActTest extends BaseTest
{
    public function testListByClientId()
    {
	    $this->app->db->query('delete from accounts', []);
        $maccount = new MAccount();
        $maccount->name = 'test ut1';
        $maccount->client_id = 1;
        $maccount->currency = 'TRY';
        $ins1 = $maccount->create();
        $maccount = new MAccount();
        $maccount->name = 'test ut2';
        $maccount->client_id = 1;
        $maccount->currency = 'PLY';
        $ins2 = $maccount->create();

        $maccount_act = new MAccountAct();

        $res = $maccount_act->listByClientId(0);
        $this->assertEmpty($res, print_r($res, true));

        $res = $maccount_act->listByClientId(1);
        $this->assertSame(2, count($res), print_r([$ins1, $ins2, $res], true));
        $this->assertSame('TRY', $res[0]['currency'], print_r([$ins1, $ins2, $res], true));
    }
}