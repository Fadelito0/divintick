<?php
// Connexion interne ultra-rapide PostgreSQL sur Render pour DivinTick

$internal_url = "postgresql://fadel:KyeVzOyMxKhxGQzbkYc5JPpdzXryXp1a@dpg-dbirg0ks728c7384a6s0-a/wifizone_v28y";

try {
    $dbopts = parse_url($internal_url);
    $host = $dbopts["host"];
    $user = $dbopts["user"];
    $pass = $dbopts["pass"];
    $dbname = ltrim($dbopts["path"], '/');
   
    // Optimisation : Ajout d'un timeout de 3 secondes pour ne jamais faire ramer la page
    $db = new PDO("pgsql:host=$host;dbname=$dbname;options='--connect_timeout=3'", $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $db->exec("CREATE TABLE IF NOT EXISTS tickets (
        id SERIAL PRIMARY KEY,
        profil VARCHAR(50) NOT NULL,
        username VARCHAR(100) NOT NULL,
        password VARCHAR(100) NOT NULL,
        statut VARCHAR(20) DEFAULT 'disponible',
        date_vente TIMESTAMP NULL
    );");

} catch (PDOException $e) {
    // Si la bdd met du temps, on n'interrompt pas le site
    $db = null;
}
?>
