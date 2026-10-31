<?php
// Connexion interne automatique PostgreSQL sur Render

// postgresql://fadel:KyeVzOyMxKhxGQzbkYc5JPpdzXryXp1a@dpg-dbirg0ks728c7384a6s0-a/wifizone_v28y

try {
    // Extraction automatique des identifiants depuis l'URL de Render
    $dbopts = parse_url($internal_url);
    $host = $dbopts["host"];
    $user = $dbopts["user"];
    $pass = $dbopts["pass"];
    $dbname = ltrim($dbopts["path"],'/');
   
    $db = new PDO("pgsql:host=$host;dbname=$dbname", $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion Render : " . $e->getMessage());
}
?>
