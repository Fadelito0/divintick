<?php
require_once 'config.php';

// Vérification de la présence du fichier
if (!file_exists('tickets.csv')) {
    die("Erreur : Le fichier tickets.csv est introuvable sur le serveur.");
}

$file = fopen('tickets.csv', 'r');

// On saute la toute première ligne (l'en-tête Username,Password...)
fgetcsv($file, 0, ',');

$count = 0;

// On lit le fichier ligne par ligne
while (($data = fgetcsv($file, 0, ',')) !== FALSE) {
    // $data[0] = Username, $data[1] = Password, $data[2] = Profile
    if (!empty($data[0]) && !empty($data[1]) && !empty($data[2])) {
        $username = $data[0];
        $password = $data[1];
        $profil   = $data[2]; // Prend automatiquement '1-Go', '01-Heure', etc.
       
        // Sécurité : On vérifie si ce ticket n'existe pas déjà pour éviter les doublons
        $check = $db->prepare("SELECT id FROM tickets WHERE username = ? AND password = ?");
        $check->execute([$username, $password]);
       
        if (!$check->fetch()) {
            // Insertion du ticket dans la base de données de Render
            $req = $db->prepare("INSERT INTO tickets (profil, username, password, statut) VALUES (?, ?, ?, 'disponible')");
            $req->execute([$profil, $username, $password]);
            $count++;
        }
    }
}

fclose($file);

echo "<div style='font-family:sans-serif; text-align:center; margin-top:50px;'>";
echo "<h2 style='color:#10b981;'>🎉 Importation Réussie !</h2>";
echo "<p style='font-size:18px;'><strong>$count</strong> nouveaux tickets ont été ajoutés à votre base de données DivinTick.</p>";
echo "<a href='index.php' style='display:inline-block; background:#000; color:#fff; padding:10px 20px; text-decoration:none; border-radius:5px;'>Retour à l'accueil</a>";
echo "</div>";
?>
