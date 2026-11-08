<?php
require __DIR__ . '/../src/bootstrap.php';

session_start_once();
$_SESSION = [];
session_destroy();
redirect('index.php');
