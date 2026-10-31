<?php
require_once 'config.php';

$mot_de_passe_admin = "ADMIN_DIVINTICK_2026";

session_start();
if (isset($_GET['logout'])) { session_destroy(); header("Location: dashboard.php"); exit; }
if (isset($_POST['password'])) {
    if ($_POST['password'] === $mot_de_passe_admin) { $_SESSION['admin'] = true; }
    else { $erreur = "Mot de passe incorrect !"; }
}

if (!isset($_SESSION['admin'])) {
?>
<!DOCTYPE html>
<html lang="fr"><head><meta charset="UTF-8"><title>DivinTick - Connexion Admin</title>
<script src="https://jsdelivr.net"></script></head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-sm text-center">
        <h1 class="text-2xl font-black text-slate-800 mb-2">🔐 Administration</h1>
        <p class="text-sm text-slate-400 mb-6">Entrez le mot de passe pour accéder à DivinTick</p>
        <?php if(isset($erreur)) echo "<p class='text-red-500 text-sm mb-4 font-semibold'>$erreur</p>"; ?>
        <form method="POST" class="space-y-4">
            <input type="password" name="password" placeholder="Mot de passe secret" class="w-full p-3 border border-slate-200 rounded-xl text-center focus:outline-none focus:border-blue-600" required>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition-colors cursor-pointer">Se connecter</button>
        </form>
    </div>
</body></html>
<?php exit; }

// --- LOGIQUE D'IMPORTATION DIRECTE DU CSV ---
$message_import = "";
if (isset($_FILES['csv_file'])) {
    if ($_FILES['csv_file']['error'] == 0) {
        $filename = $_FILES['csv_file']['tmp_name'];
        $file = fopen($filename, 'r');
        fgetcsv($file, 0, ',');
        $count = 0;
        while (($data = fgetcsv($file, 0, ',')) !== FALSE) {
            if (isset($data[0]) && isset($data[1]) && isset($data[2])) {
                $username = trim($data[0]);
                $password = trim($data[1]);
                $profil   = trim($data[2]);
               
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
        $message_import = "🎉 Succès : $count tickets ont été importés avec succès !";
    } else { $message_import = "❌ Erreur lors du transfert du fichier."; }
}

// --- CALCULS ET STATISTIQUES ---
$tarifs = [
    '1-Go' => 100, '3-Go' => 200, '9-Go' => 500, '20-Go' => 1000,
    '01-Heure' => 100, '3-Heures' => 200, '8-Heures' => 500, '24-Heures' => 1000,
    'Illimite-24H' => 300, 'Illimite-1Semaine' => 1000, 'Illimite-1Mois' => 4500
];

$ventes_req = $db->query("SELECT profil, COUNT(*) as total FROM tickets WHERE statut = 'vendu' GROUP BY profil");
$chiffre_affaire = 0;
while($v = $ventes_req->fetch(PDO::FETCH_ASSOC)) {
    if(isset($tarifs[$v['profil']])) { $chiffre_affaire += ($v['total'] * $tarifs[$v['profil']]); }
}

$stats_req = $db->query("SELECT profil,
    SUM(CASE WHEN statut = 'disponible' THEN 1 ELSE 0 END) as dispo,
    SUM(CASE WHEN statut = 'vendu' THEN 1 ELSE 0 END) as vendu
    FROM tickets GROUP BY profil ORDER BY profil ASC");
$stats = $stats_req->fetchAll(PDO::FETCH_ASSOC);

$liste_complete_forfaits = [];
foreach($tarifs as $nom_prof => $prix_prof) {
    $liste_complete_forfaits[$nom_prof] = ['dispo' => 0, 'vendu' => 0, 'prix' => $prix_prof];
}
foreach($stats as $s) {
    if(isset($liste_complete_forfaits[$s['profil']])) {
        $liste_complete_forfaits[$s['profil']]['dispo'] = $s['dispo'];
        $liste_complete_forfaits[$s['profil']]['vendu'] = $s['vendu'];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>DivinTick - Vos Tarifs & Stocks</title>
    <script src="https://jsdelivr.net"></script>
</head>
<body class="bg-slate-900 min-h-screen text-slate-100 antialiased p-4 sm:p-6">

    <div class="max-w-6xl mx-auto space-y-6">
       
        <div class="flex justify-between items-center bg-slate-800 p-6 rounded-2xl border border-slate-700">
            <div>
                <h1 class="text-xl font-black tracking-wide text-white">📋 Mes tarifs</h1>
                <p class="text-xs text-slate-400 mt-1">DivinTick &rsaquo; Gestion des stocks en direct</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="bg-blue-600/20 border border-blue-500/30 px-4 py-2 rounded-xl text-right">
                    <span class="text-[10px] text-blue-400 block font-bold uppercase tracking-wider">Solde vendable</span>
                    <span class="text-emerald-400 font-mono font-black text-sm"><?php echo number_format($chiffre_affaire, 0, ',', ' '); ?> F CFA</span>
                </div>
                <a href="dashboard.php?logout=1" class="bg-red-500/10 hover:bg-red-500/20 border border-red-500/30 text-red-400 px-3 py-1.5 rounded-xl text-xs font-bold transition-colors">Sortir</a>
            </div>
        </div>

        <?php if(!empty($message_import)): ?>
            <div class="bg-blue-600/10 border border-blue-500/30 text-blue-400 p-4 rounded-xl text-sm font-semibold">
                <?php echo $message_import; ?>
            </div>
        <?php endif; ?>

        <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="font-bold text-white text-sm">📥 Recharger des tickets</h3>
                <p class="text-xs text-slate-400 mt-0.5">Glissez le CSV de votre MikroTik pour alimenter automatiquement les cartes.</p>
            </div>
            <form method="POST" enctype="multipart/form-data" class="flex items-center gap-3 w-full md:w-auto">
                <input type="file" name="csv_file" accept=".csv" class="text-xs text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer" required>
                <button type="submit" class="bg-slate-700 hover:bg-slate-600 text-white font-bold py-1.5 px-4 rounded-xl text-xs border border-slate-600 transition-colors cursor-pointer">Importer</button>
            </form>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <?php foreach($liste_complete_forfaits as $nom_forfait => $f): ?>
            <div class="bg-slate-800 rounded-2xl border border-slate-700 p-5 flex flex-col justify-between relative shadow-lg overflow-hidden group hover:border-blue-500/50 transition-all">
               
                <div class="absolute top-4 right-4 bg-blue-600/20 border border-blue-500/30 text-blue-400 text-xs font-black px-3 py-1 rounded-full font-mono">
                    ● <?php echo $f['prix']; ?> FCFA
                </div>

                <div class="space-y-4">
                    <div>
                        <h2 class="text-lg font-black tracking-tight text-white"><?php echo htmlspecialchars($nom_forfait); ?></h2>
                        <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">⚡ DIVIN CYBER CAFÉ</span>
                    </div>

                    <div class="flex items-center justify-between bg-slate-900/50 p-3 rounded-xl border border-slate-700/50">
                        <span class="text-xs text-slate-400 flex items-center gap-1.5">📂 Tickets disponibles</span>
                        <span class="font-mono font-black text-xl <?php echo ($f['dispo'] == 0) ? 'text-red-500' : (($f['dispo'] <= 5) ? 'text-amber-500' : 'text-blue-400'); ?>">
                            <?php echo $f['dispo']; ?>
                        </span>
                    </div>

                    <div class="text-[11px] bg-slate-900/30 border border-slate-700/30 p-2.5 rounded-xl space-y-1">
                        <span class="text-slate-500 font-bold block">🔗 Lien de vente lié :</span>
                        <code class="text-blue-400 block break-all select-all font-mono">https://onrender.com<?php echo urlencode($nom_forfait); ?></code>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 mt-5 pt-4 border-t border-slate-700/50 text-center text-xs font-bold">
                    <div class="bg-slate-700/40 border border-slate-600/50 py-2 rounded-xl text-slate-300">
                        Vendus : <span class="font-mono text-white"><?php echo $f['vendu']; ?></span>
                    </div>
                    <?php if($f['dispo'] == 0): ?>
                        <div class="bg-red-500/10 border border-red-500/30 py-2 rounded-xl text-red-400 text-[10px] uppercase font-bold flex items-center justify-center animate-pulse">Vide</div>
                    <?php else: ?>
                        <div class="bg-emerald-500/10 border border-emerald-500/30 py-2 rounded-xl text-emerald-400 text-[10px] uppercase font-bold flex items-center justify-center">Actif</div>
                    <?php endif; ?>
                </div>

            </div>
            <?php endforeach; ?>
        </div>

    </div>
</body>
</html>
