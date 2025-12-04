<?php
// Helper script to generate bcrypt hashes for supervisor passwords.
// Open in your browser at: http://localhost/attachment_portal/hash_passwords.php

$passwords = ['Bosco123', 'Michael123', 'Jeremiah123'];

header('Content-Type: text/plain');

foreach ($passwords as $pwd) {
    echo $pwd . ' => ' . password_hash($pwd, PASSWORD_BCRYPT) . PHP_EOL;
}


