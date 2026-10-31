<?php
// Connexion interne PostgreSQL sur Render pour DivinTick

$internal_url = "postgresql://fadel:KyeVzOyMxKhxGQzbkYc5JPpdzXryXp1a@dpg-dbirg0ks728c7384a6s0-a/wifizone_v28y";

try {
    $dbopts = parse_url($internal_url);
    $host = $dbopts["host"];
    $user = $dbopts["user"];
    $pass = $dbopts["pass"];
    $dbname = ltrim($dbopts["path"], '/');
   
    // Connexion standard stable
    $db = new PDO("pgsql:host=$host;dbname=$dbname", $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // CRÉATION AUTOMATIQUE DE LA TABLE DES TICKETS (Si elle n'existe pas)
    $db->exec("CREATE TABLE IF NOT EXISTS tickets (
        id SERIAL PRIMARY KEY,
        profil VARCHAR(50) NOT NULL,
        username VARCHAR(100) NOT NULL,
        password VARCHAR(100) NOT NULL,
        statut VARCHAR(20) DEFAULT 'disponible',
        date_vente TIMESTAMP NULL
    );");

} catch (PDOException $e) {
    die("Erreur de connexion Render : " . $e->getMessage());
}
?>
