<?php
require 'connect.php';
$db = new PDO(DNS, LOGIN, PASSWORD, $options);

$sql = 'SELECT * FROM ni_afficher';
$statement = $db->query($sql);
$results = $statement->fetchAll();
foreach ($results as $row) {
    echo 'ID: ' . $row['id'] . ' - Name: ' . $row['name'] . '<br>';
}
?>