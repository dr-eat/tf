<?php
declare(strict_types=1);


/**
 * CAccount - handle Account logic
 *
 * PHP version 8.2
 *
 * @author    Aleksandr Jermakovich <dreat@dreat.net>
 * @copyright 2025
 */

namespace controllers;

use BaseTest;
use CAccount;
use MAccount;
require_once('../tests/BaseTest.php');


class CAccountTest extends BaseTest
{
    public function testList()
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

        $_GET['client_id'] = 0;
        $account = new CAccount();
        $res = $account->list();
        $this->assertEmpty($res);

        $_GET['client_id'] = 1;
        $account = new CAccount();
        $res = $account->list();
        $this->assertSame(2, count($res), print_r([$ins1, $ins2, $res], true));
        $this->assertSame('TRY', $res[0]['currency'], print_r([$ins1, $ins2, $res], true));
    }
}