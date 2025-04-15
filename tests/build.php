<?php
require_once '../../htdocs/tf/config/config.php';
require_once '../../htdocs/tf/models/MDb.php';
require_once '../../htdocs/tf/models/App.php';
require_once '../../htdocs/tf/models/MBase.php';
require_once '../../htdocs/tf/models/MActions.php';
require_once '../../htdocs/tf/models/MAccount.php';
require_once '../../htdocs/tf/models/MAccountAct.php';
require_once '../../htdocs/tf/tests/BaseTest.php';

$db = App::get()->db;
$files = scandir('../tests');
foreach ($files as $file) {

}
