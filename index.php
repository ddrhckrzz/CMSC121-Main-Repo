<?php
// Define the socket path dynamically based on where the project is
$socket_path = __DIR__ . '/.data/mysql.sock';

echo "<h1>LAMP Stack Status</h1>";

echo "<h3> PHP is working!</h3>";
echo "Running PHP version: " . phpversion() . "<br>";
$mysqli = new mysqli("localhost", "root", "", "", 0, $socket_path);

if ($mysqli->connect_error) {
    echo "<h3> Database Connection Failed</h3>";
    echo "Error: " . $mysqli->connect_error;
} else {
    echo "<h3> Database Connected Successfully!</h3>";
    echo "Connected to MariaDB via Socket: " . $socket_path;
}

// 3. Show full PHP Info
echo "<hr>";
phpinfo();
?>