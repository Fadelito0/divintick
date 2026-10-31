<?php
require_once 'config.php';

if (!file_exists('tickets.csv')) {
    die("Erreur : Le fichier tickets.csv est introuvable sur le serveur.");
}

$file = fopen('tickets.csv', 'r');
fgetcsv($file, 0, ','); // Saute l'en-tête du fichier

$count = 0;

while (($data = fgetcsv($file, 0, ',')) !== FALSE) {
    if (!empty($data[0]) && !empty($data[1]) && !empty($data[2])) {
        $username = $data[0];
        $password = $data[1];
        $profil   = $data[2]; // Prend automatiquement '1-Go', '01-Heure', etc.
       
        // Sécurité anti-doublon
        $check = $db->prepare("SELECT id FROM tickets WHERE username = ? AND password = ?");
        $check->execute([$username, $password]);
       
        if (!$check->fetch()) {
            $req = $db->prepare("INSERT INTO tickets (profil, username, password, statut) VALUES (?, ?, ?, 'disponible')");
            $req->execute([$profil, $username, $password]);
            $count++;
        }
    }
}

fclose($file);

echo "<div style='font-family:sans-serif; text-align:center; margin-top:50px;'>";
echo "<h2 style='color:#10b981;'>🎉 Importation Réussie !</h2>";
echo "<p style='font-size:18px;'><strong>$count</strong> tickets insérés avec succès.</p>";
echo "<a href='dashboard.php' style='display:inline-block; background:#000; color:#fff; padding:10px 20px; text-decoration:none; border-radius:5px;'>Aller au Dashboard</a>";
echo "</div>";
?>
