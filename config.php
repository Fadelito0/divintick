<?php
// Configuration locale de la base de données de DivinTick

$host = 'localhost';
$dbname = 'if0_43311106_wifizone';
$user = 'if0_43311106';
$password = '96300042Fadel';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données de DivinTick : " . $e->getMessage());
}
?>