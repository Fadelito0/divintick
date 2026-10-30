<?php
require_once 'config.php';

$user_code = null;
$pass_code = null;
$erreur = null;

if (isset($_GET['id']) && isset($_GET['profil'])) {
    $profil_choisi = $_GET['profil'];

    try {
        // Recherche d'un ticket disponible pour le profil sélectionné
        $req = $db->prepare("SELECT * FROM tickets WHERE profil = ? AND statut = 'disponible' LIMIT 1");
        $req->execute([$profil_choisi]);
        $ticket = $req->fetch();

        if ($ticket) {
            // Mise à jour immédiate pour verrouiller le ticket vendu
            $update = $db->prepare("UPDATE tickets SET statut = 'vendu', date_vente = NOW() WHERE id = ?");
            $update->execute([$ticket['id']]);
            $user_code = $ticket['username'];
            $pass_code = $ticket['password'];
        } else {
            $erreur = "Rupture de stock : Il n'y a plus de tickets disponibles pour le forfait '" . htmlspecialchars($profil_choisi) . "' dans notre base. Veuillez avertir le gérant.";
        }
    } catch (Exception $e) {
        $erreur = "Une erreur technique interne est survenue.";
    }
} else {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DivinTick - Vos Accès</title>
    <script src="https://jsdelivr.net"></script>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4 text-slate-800 antialiased">

    <div class="bg-white p-8 rounded-3xl shadow-xl w-full max-w-md text-center border border-slate-100">
        <?php if ($user_code): ?>
            <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">✓</div>
            <h1 class="text-2xl font-extrabold text-slate-900 mb-1">Paiement Réussi !</h1>
            <p class="text-sm text-slate-500 mb-6">Merci pour votre confiance. Voici vos accès chez Divin Cyber Café :</p>

            <div class="bg-slate-50 border border-slate-200 p-5 rounded-2xl mb-6 text-left space-y-3">
                <div>
                    <span class="text-xs uppercase font-bold text-slate-400 tracking-wider">Identifiant (Username)</span>
                    <div class="text-2xl font-mono font-black text-blue-600 select-all"><?php echo htmlspecialchars($user_code); ?></div>
                </div>
                <div class="border-t border-slate-200 pt-2">
                    <span class="text-xs uppercase font-bold text-slate-400 tracking-wider">Mot de passe (Password)</span>
                    <div class="text-2xl font-mono font-black text-blue-600 select-all"><?php echo htmlspecialchars($pass_code); ?></div>
                </div>
            </div>
            <p class="text-xs text-slate-400 mb-6">ℹ️ Utilisez ces identifiants uniques sur notre portail de connexion Wi-Fi Zone.</p>
            <a href="index.php" class="inline-block bg-slate-900 text-white text-sm font-semibold py-3 px-6 rounded-xl w-full">Retour à l'accueil</a>
        <?php else: ?>
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">✕</div>
            <h1 class="text-2xl font-extrabold text-slate-900 mb-2">Erreur</h1>
            <p class="text-sm text-red-600 bg-red-50 p-4 rounded-xl mb-6"><?php echo $erreur; ?></p>
            <a href="index.php" class="inline-block bg-blue-600 text-white text-sm font-semibold py-3 px-6 rounded-xl w-full">Réessayer</a>
        <?php endif; ?>
    </div>

</body>
</html>