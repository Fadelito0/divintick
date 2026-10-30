<?php
require_once 'config.php';

try {
    // 1. Calcul du revenu total
    $res_revenu = $db->query("SELECT COUNT(*) * 100 as total FROM tickets WHERE statut = 'vendu'"); // Version simplifiée
    // Note : Pour un calcul précis par forfait, il faudrait une colonne 'prix' dans la BDD.
   
    // 2. Compter le nombre total de tickets vendus ce mois-ci
    $res_vendus = $db->query("SELECT COUNT(*) FROM tickets WHERE statut = 'vendu'");
    $total_vendus = $res_vendus->fetchColumn();

    // 3. Compter le nombre de tickets encore disponibles en stock
    $res_stock = $db->query("SELECT COUNT(*) FROM tickets WHERE statut = 'disponible'");
    $total_stock = $res_stock->fetchColumn();
   
} catch (Exception $e) {
    die("Erreur de statistiques : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DivinTick - Dashboard Administrateur</title>
    <script src="https://jsdelivr.net"></script>
</head>
<body class="bg-slate-900 text-slate-100 antialiased min-h-screen font-sans">

    <div class="flex flex-col md:flex-row min-h-screen">
       
        <!-- BARRE LATERALE DE NAVIGATION (Comme sur l'image) -->
        <aside class="w-full md:w-64 bg-slate-950 p-6 space-y-6 shadow-xl">
            <div class="text-xl font-black tracking-wider text-blue-500">🚀 DIVINTICK</div>
            <div class="text-xs text-slate-500 uppercase tracking-widest font-bold">Principal</div>
            <nav class="space-y-2">
                <a href="dashboard.php" class="block bg-blue-600 text-white px-4 py-2.5 rounded-xl font-medium text-sm transition-all">📊 Dashboard</a>
                <a href="#" class="block text-slate-400 hover:bg-slate-900 hover:text-white px-4 py-2.5 rounded-xl font-medium text-sm transition-all">📶 Routeurs</a>
                <a href="#" class="block text-slate-400 hover:bg-slate-900 hover:text-white px-4 py-2.5 rounded-xl font-medium text-sm transition-all">🎟 Gestion Tickets</a>
            </nav>
        </aside>

        <!-- ZONE DU CONTENU PRINCIPAL -->
        <main class="flex-1 p-6 md:p-10 space-y-8">
           
            <!-- EN-TETE DU DASHBOARD -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b border-slate-800 pb-4">
                <div>
                    <h1 class="text-3xl font-extrabold tracking-tight">Tableau de Bord</h1>
                    <p class="text-sm text-slate-400 mt-1">Suivi en direct de l'activité de votre Wi-Fi Zone</p>
                </div>
                <div class="bg-slate-950 text-xs px-4 py-2 rounded-lg border border-slate-800 text-slate-400 mt-4 md:mt-0">
                     <?php echo date('d-m-Y H:i'); ?>
                </div>
            </div>

            <!-- LES BLOCS DE STATISTIQUES (Identiques aux tuiles bleues de votre exemple) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
               
                <!-- Tuile 1 : Revenus -->
                <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800 shadow-sm relative overflow-hidden">
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Revenus Estimés</div>
                    <div class="text-3xl font-black text-emerald-400 mt-2"><?php echo $total_vendus * 100; ?> F CFA</div>
                    <p class="text-[10px] text-slate-500 mt-1">Calculé sur la base des tickets livrés</p>
                </div>

                <!-- Tuile 2 : Tickets Vendus -->
                <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800 shadow-sm">
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tickets Vendus (Total)</div>
                    <div class="text-3xl font-black text-blue-400 mt-2"><?php echo $total_vendus; ?></div>
                    <p class="text-[10px] text-slate-500 mt-1">Transactions validées par FedaPay</p>
                </div>

                <!-- Tuile 3 : Stock restant -->
                <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800 shadow-sm">
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tickets en Stock (Disponibles)</div>
                    <div class="text-3xl font-black <?php echo ($total_stock < 10) ? 'text-red-400' : 'text-purple-400'; ?> mt-2">
                        <?php echo $total_stock; ?>
                    </div>
                    <p class="text-[10px] text-slate-500 mt-1">Pensez à en rajouter depuis Winbox si le stock est rouge</p>
                </div>

            </div>

            <!-- ZONE INFERIEURE : LISTE DES DERNIÈRES VENTES -->
            <div class="bg-slate-950 border border-slate-800 rounded-2xl p-6 shadow-sm">
                <h3 class="text-lg font-bold mb-4">Dernières transactions en direct</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-400">
                        <thead class="text-xs uppercase bg-slate-900 text-slate-500">
                            <tr>
                                <th class="p-3">ID</th>
                                <th class="p-3">Forfait / Profil</th>
                                <th class="p-3">Code Utilisateur</th>
                                <th class="p-3">Date de Vente</th>
                                <th class="p-3">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-900">
                            <?php
                            // Récupération des 5 dernières ventes pour l'affichage du tableau
                            $ventes = $db->query("SELECT * FROM tickets WHERE statut = 'vendu' ORDER BY date_vente DESC LIMIT 5")->fetchAll();
                            if(count($ventes) > 0):
                                foreach($ventes as $v):
                            ?>
                            <tr class="hover:bg-slate-900/50 transition-colors">
                                <td class="p-3 font-mono text-xs"><?php echo $v['id']; ?></td>
                                <td class="p-3 font-semibold text-slate-200"><?php echo htmlspecialchars($v['profil']); ?></td>
                                <td class="p-3 font-mono text-blue-400"><?php echo htmlspecialchars($v['username']); ?></td>
                                <td class="p-3 text-xs"><?php echo $v['date_vente']; ?></td>
                                <td class="p-3"><span class="bg-emerald-500/10 text-emerald-400 text-[10px] px-2 py-0.5 rounded-full font-bold">Validé</span></td>
                            </tr>
                            <?php
                                endforeach;
                            else:
                            ?>
                            <tr>
                                <td colspan="5" class="p-4 text-center text-slate-600 italic">Aucune vente enregistrée pour le moment.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

</body>
</html>
