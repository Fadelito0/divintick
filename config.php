<?php
// Connexion interne PostgreSQL sur Render
$dbname = "wifizone";
$user = "fadel";
$password = "KyeVzOyMxKhxGQzbkYc5JPpdzXryXp1a";

try {
    // Sous Render, si le site et la bdd sont au même endroit, on utilise l'URL interne automatique
    $db = new PDO("pgsql:dbname=$dbname;host=localhost", $user, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
