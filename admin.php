<?php

declare(strict_types=1);

session_start();
require_once 'database/database.php';
require_once 'flash.php';
require_once 'app/Enums/Role.php';
require_once 'app/helpers.php';


checkAdmin();

$pageTitle = 'Administration';
render('admin/admin', [
    'pageTitle' => $pageTitle
], 'admin-layout');

