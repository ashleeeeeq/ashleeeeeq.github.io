<?php
echo "<h2>PHP Extension Check</h2>";
echo "MySQLi: " . (extension_loaded('mysqli') ? '✅ LOADED' : '❌ NOT LOADED') . "<br>";
echo "PDO MySQL: " . (extension_loaded('pdo_mysql') ? '✅ LOADED' : '❌ NOT LOADED') . "<br>";

if (extension_loaded('pdo')) {
    echo "PDO Drivers: ";
    print_r(PDO::getAvailableDrivers());
} else {
    echo "PDO: ❌ NOT LOADED<br>";
}

if (extension_loaded('mysqli')) {
    echo "<br>MySQLi Constants: ";
    echo defined('MYSQLI_STORE_RESULT') ? 'MYSQLI_STORE_RESULT: ✅ DEFINED' : 'MYSQLI_STORE_RESULT: ❌ NOT DEFINED';
}