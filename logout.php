<?php
require_once __DIR__ . '/includes/auth.php';
logout_user();
set_flash('Logged out successfully.');
redirect('login.php');

