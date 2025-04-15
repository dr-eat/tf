<?php

namespace models;
use BaseTest;
use MClient;
require_once('../tests/BaseTest.php');
/**
 * MClient - Client object
 *
 * PHP version 8.2
 *
 * @author    Aleksandr Jermakovich <dreat@dreat.net>
 * @copyright 2025
 */
class MClientTest extends BaseTest
{
    public function testCreate(): void
    {
        $mclient = new MClient();
        $mclient->login = 'test_ut1';
        $mclient->password = md5($mclient->login);
        $mclient->name = 'Test Ut1';
        $mclient->email = 'test@test.com';
        $client_id = $mclient->create();
        $mclient_test = new MClient($client_id);
        $fields = ['login', 'password', 'name', 'email', 'status'];
        foreach($fields as $field) {
            $this->assertSame($mclient->$field, $mclient_test->$field, "Field: $field");
        }
        $this->assertSame($mclient->status, \MClient::STATUS_NEW, "Field: status");
    }
}