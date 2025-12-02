<?php
// Quick script to generate the correct password hash for admin
// Run this file to get the hash, then update database.sql

$password = 'TomatoPotato01!';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password: $password\n";
echo "Hash: $hash\n";
echo "\nUse this hash in your database.sql file or run this SQL query:\n";
echo "UPDATE users SET password = '$hash' WHERE username = 'admin';\n";
?>
