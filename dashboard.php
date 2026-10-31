<?php
require_once 'config.php';

// SÉCURITÉ : Créez votre mot de passe administrateur ici
$mot_de_passe_admin = "ADMIN_DIVINTICK_2026";

// Système de connexion très simple
session_start();
if (isset($_GET['logout'])) { session_destroy(); header("Location: dashboard.php"); exit; }
if (isset($_POST['password'])) {
    if ($_POST['password'] === $mot_de_passe_admin) { $_SESSION['admin'] = true; }
    else { $erreur = "Mot de passe incorrect !"; }
}

// Si l'admin n'est pas connecté, on affiche la page de connexion
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

// --- CODE DE L'ADMINISTRATION SI CONNECTÉ ---

// 1. Calcul du chiffre d'affaires total approximatif
// (On associe chaque profil vendu à son prix réel pour calculer vos gains)
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

// 2. Récupération des statistiques des tickets (Disponibles vs Vendus)
$stats_req = $db->query("SELECT profil,
    SUM(CASE WHEN statut = 'disponible' THEN 1 ELSE 0 END) as dispo,
    SUM(CASE WHEN statut = 'vendu' THEN 1 ELSE 0 END) as vendu
    FROM tickets GROUP BY profil ORDER BY profil ASC");
$stats = $stats_req->fetchAll(PDO::FETCH_ASSOC);

// 3. Récupération des 10 dernières ventes
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

        <!-- Carte Chiffre d'Affaires -->
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
                            <th class="p-4 text-right">Alerte</th>
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
                            <td class="p-4 text-right">
                                <?php if($s['dispo'] <= 5): ?>
                                    <span class="text-[10px] font-bold bg-red-100 text-red-700 px-2.5 py-1 rounded-full animate-pulse">🚨 Recharger !</span>
                                <?php else: ?>
                                    <span class="text-[10px] font-bold bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-full">OK</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Historique des 10 dernières ventes -->
        <div>
            <h2 class="text-sm uppercase font-bold text-slate-400 tracking-wider mb-3 px-1">🕒 10 Dernières Ventes Automatiques</h2>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 space-y-3">
                <?php if(empty($historique)): ?>
                    <p class="text-center text-sm text-slate-400 py-4">Aucune vente enregistrée pour le moment.</p>
                <?php else: ?>
                    <?php foreach($historique as $h): ?>
                    <div class="flex justify-between items-center py-2 border-b border-slate-50 last:border-0 text-xs sm:text-sm">
                        <div>
                            <span class="font-bold text-slate-800"><?php echo htmlspecialchars($h['profil']); ?></span>
                            <span class="text-slate-400 text-xs ml-2">Client : <span class="font-mono text-blue-600"><?php echo htmlspecialchars($h['username']); ?></span></span>
                        </div>
                        <span class="text-slate-400 text-xs font-mono"><?php echo date('d/m H:i', strtotime($h['date_vente'])); ?></span>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>

</body>
</html>
