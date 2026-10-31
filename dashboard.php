<?php
require_once 'config.php';

// SÉCURITÉ : Mot de passe administrateur
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
       
        // On saute la première ligne (en-tête)
        fgetcsv($file, 0, ',');
       
        $count = 0;
        while (($data = fgetcsv($file, 0, ',')) !== FALSE) {
            if (isset($data[0]) && isset($data[1]) && isset($data[2])) {
                $username = trim($data[0]);
                $password = trim($data[1]);
                $profil   = trim($data[2]); // Prend '1-Go', '01-Heure', etc.
               
                // Vérification anti-doublon
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
    } else {
        $message_import = "❌ Erreur lors du transfert du fichier.";
    }
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

$historique_req = $db->query("SELECT profil, username, date_vente FROM tickets WHERE statut = 'vendu' ORDER BY date_vente DESC LIMIT 10");
$historique = $historique_req->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>DivinTick - Tableau de Bord</title>
    <script src="https://jsdelivr.net"></script>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800 antialiased p-4 sm:p-6">

    <div class="max-w-4xl mx-auto space-y-6">
       
        <!-- En-tête -->
        <div class="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div>
                <h1 class="text-2xl font-black text-slate-900">📊 Tableau de Bord</h1>
                <p class="text-xs text-slate-400">Gestion de vos stocks et ventes Divin Cyber Café</p>
            </div>
            <a href="dashboard.php?logout=1" class="bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-xl text-xs font-bold transition-colors">Déconnexion</a>
        </div>

        <?php if(!empty($message_import)): ?>
            <div class="bg-emerald-100 border border-emerald-300 text-emerald-800 p-4 rounded-xl text-sm font-semibold">
                <?php echo $message_import; ?>
            </div>
        <?php endif; ?>

        <!-- Zone de Téléchargement CSV Direct -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <h2 class="text-sm uppercase font-bold text-slate-400 tracking-wider mb-3">📥 Ajouter de nouveaux tickets (CSV)</h2>
            <form method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-4 items-center">
                <input type="file" name="csv_file" accept=".csv" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer" required>
                <button type="submit" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-xl text-xs transition-colors cursor-pointer whitespace-nowrap">
                    Importer le fichier
                </button>
            </form>
        </div>

        <!-- Chiffre d'Affaires -->
        <div class="bg-blue-600 p-6 rounded-2xl shadow-md text-white text-center sm:text-left">
            <p class="text-blue-100 text-xs font-bold uppercase tracking-wider">Chiffre d'Affaires Estimé</p>
            <p class="text-4xl font-black mt-1"><?php echo number_format($chiffre_affaire, 0, ',', ' '); ?> <span class="text-xl">F CFA</span></p>
        </div>

        <!-- Grille des Stocks -->
        <div>
            <h2 class="text-sm uppercase font-bold text-slate-400 tracking-wider mb-3 px-1">📦 État de vos Stocks de Tickets</h2>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-400 text-[10px] uppercase font-bold border-b border-slate-100">
                            <th class="p-4">Profil Forfait</th>
                            <th class="p-4 text-center">En Stock (Dispo)</th>
                            <th class="p-4 text-center">Vendus</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 text-sm">
                        <?php foreach($stats as $s): ?>
                        <tr>
                            <td class="p-4 font-bold text-slate-700"><?php echo htmlspecialchars($s['profil']); ?></td>
                            <td class="p-4 text-center font-mono font-bold <?php echo ($s['dispo'] <= 5) ? 'text-red-600 bg-red-50' : 'text-emerald-600'; ?>">
                                <?php echo $s['dispo']; ?>
                            </td>
                            <td class="p-4 text-center font-mono text-slate-400"><?php echo $s['vendu']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>
</html>
