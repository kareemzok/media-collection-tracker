<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

session_start();
session_unset();
session_destroy();

redirect(BASE_URL . "/auth/login.php");