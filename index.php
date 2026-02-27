<?php
// Define the socket path dynamically based on where the project is
$socket_path = __DIR__ . '/.data/mysql.sock';

echo "<h1>LAMP Stack Status</h1>";

echo "<h3> PHP is working!</h3>";
echo "Running PHP version: " . phpversion() . "<br>";
$mysqli = new mysqli("127.0.0.1", "root", "", "lamp_dev", 3306);

if ($mysqli->connect_error) {
    echo "<h3>Database Connection Failed</h3>";
    echo "Error: " . $mysqli->connect_error;
} else {
    echo "<h3>Database Connected Successfully!</h3>";
    echo "Connected to MariaDB via 127.0.0.1 on port 3306";
}

echo "<hr>";
echo "<h2>Table of Contents</h2><br>";
echo "<a href='ass1/index.html'>Go to Assignment 1</a>";
echo "<br><a href='ass2/tmnt.html'>Go to Assignment 2</a>";
echo "<br><a href='ass3/index.html'>Go to Assignment 3</a>";

// 3. Show full PHP Info
echo "<hr>";
phpinfo();
?>