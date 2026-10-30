
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DivinTick - Vos Tickets WiFi</title>
    <script src="https://jsdelivr.net"></script>
    <script src="https://fedapay.com"></script>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800 antialiased p-4">

    <header class="bg-blue-600 text-white text-center py-6 px-4 shadow-md rounded-2xl max-w-md mx-auto mb-6">
        <h1 class="text-2xl font-black tracking-tight">⚡ DIVIN CYBER CAFÉ</h1>
        <p class="text-blue-100 mt-1 text-xs">Achetez vos forfaits Wi-Fi via MTN, Moov ou Celtiis</p>
    </header>

    <main class="max-w-md mx-auto space-y-6">

        <!-- CATEGORIE : FORFAITS VOLUME -->
        <div>
            <h2 class="text-sm uppercase font-bold text-slate-400 tracking-wider mb-2 px-1">📊 Forfaits Volume</h2>
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                    <div><h3 class="font-bold">1 Go <span class="text-xs text-slate-400 font-normal">(Valide 24H)</span></h3></div>
                    <button id="pay-v1" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1.5 px-4 rounded-xl text-xs cursor-pointer">100 F</button>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                    <div><h3 class="font-bold">3 Go <span class="text-xs text-slate-400 font-normal">(Valide 48H)</span></h3></div>
                    <button id="pay-v3" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1.5 px-4 rounded-xl text-xs cursor-pointer">200 F</button>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                    <div><h3 class="font-bold">9 Go <span class="text-xs text-slate-400 font-normal">(Valide 5 Jours)</span></h3></div>
                    <button id="pay-v9" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1.5 px-4 rounded-xl text-xs cursor-pointer">500 F</button>
                </div>
                <div class="flex justify-between items-center py-2">
                    <div><h3 class="font-bold">20 Go <span class="text-xs text-slate-400 font-normal">(Valide 10 Jours)</span></h3></div>
                    <button id="pay-v20" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1.5 px-4 rounded-xl text-xs cursor-pointer">1000 F</button>
                </div>
            </div>
        </div>

        <!-- CATEGORIE : FORFAITS HORAIRES -->
        <div>
            <h2 class="text-sm uppercase font-bold text-slate-400 tracking-wider mb-2 px-1">⏱ Forfaits Horaires</h2>
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                    <div><h3 class="font-bold">1 Heure <span class="text-xs text-slate-400 font-normal">(Valide 24H)</span></h3></div>
                    <button id="pay-h1" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1.5 px-4 rounded-xl text-xs cursor-pointer">100 F</button>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                    <div><h3 class="font-bold">3 Heures <span class="text-xs text-slate-400 font-normal">(Valide 48H)</span></h3></div>
                    <button id="pay-h3" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1.5 px-4 rounded-xl text-xs cursor-pointer">200 F</button>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                    <div><h3 class="font-bold">8 Heures <span class="text-xs text-slate-400 font-normal">(Valide 5 Jours)</span></h3></div>
                    <button id="pay-h8" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1.5 px-4 rounded-xl text-xs cursor-pointer">500 F</button>
                </div>
                <div class="flex justify-between items-center py-2">
                    <div><h3 class="font-bold">24 Heures <span class="text-xs text-slate-400 font-normal">(Valide 10 Jours)</span></h3></div>
                    <button id="pay-h24" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1.5 px-4 rounded-xl text-xs cursor-pointer">1000 F</button>
                </div>
            </div>
        </div>

        <!-- CATEGORIE : FORFAITS ILLIMITÉS -->
        <div>
            <h2 class="text-sm uppercase font-bold text-slate-400 tracking-wider mb-2 px-1">🚀 Forfaits Illimités</h2>
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                    <div><h3 class="font-bold">Illimité 24H</h3></div>
                    <button id="pay-i24h" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1.5 px-4 rounded-xl text-xs cursor-pointer">300 F</button>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                    <div><h3 class="font-bold">Illimité 1 Semaine</h3></div>
                    <button id="pay-i1s" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1.5 px-4 rounded-xl text-xs cursor-pointer">1000 F</button>
                </div>
                <div class="flex justify-between items-center py-2">
                    <div><h3 class="font-bold">Illimité 1 Mois</h3></div>
                    <button id="pay-i1m" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1.5 px-4 rounded-xl text-xs cursor-pointer">4500 F</button>
                </div>
            </div>
        </div>

    </main>

    <footer class="text-center py-6 text-xs text-slate-400">
        &copy; <?php echo date('Y'); ?> DivinTick. Tous droits réservés.
    </footer>

    <script>
        const key = 'pk_live_OviqJ-sNT949ElpSqVztJ7IT';
        const url = "http://gt.tc";

        // Association des boutons aux vrais profils de votre base de données
        const forfaits = [
            { id: '#pay-v1', amt: 100, desc: 'Forfait Volume 1-Go', prof: '1-Go' },
            { id: '#pay-v3', amt: 200, desc: 'Forfait Volume 3-Go', prof: '3-Go' },
            { id: '#pay-v9', amt: 500, desc: 'Forfait Volume 9-Go', prof: '9-Go' },
            { id: '#pay-v20', amt: 1000, desc: 'Forfait Volume 20-Go', prof: '20-Go' },
            { id: '#pay-h1', amt: 100, desc: 'Forfait Horaire 1-Heure', prof: '01-Heure' },
            { id: '#pay-h3', amt: 200, desc: 'Forfait Horaire 3-Heures', prof: '3-Heures' },
            { id: '#pay-h8', amt: 500, desc: 'Forfait Horaire 8-Heures', prof: '8-Heures' },
            { id: '#pay-h24', amt: 1000, desc: 'Forfait Horaire 24-Heures', prof: '24-Heures' },
            { id: '#pay-i24h', amt: 300, desc: 'Forfait Illimite 24H', prof: 'Illimite-24H' },
            { id: '#pay-i1s', amt: 1000, desc: 'Forfait Illimite 1Semaine', prof: 'Illimite-1Semaine' },
            { id: '#pay-i1m', amt: 4500, desc: 'Forfait Illimite 1Mois', prof: 'Illimite-1Mois' }
        ];

        forfaits.forEach(f => {
            FedaPay.init(f.id, {
                public_key: key,
                transaction: { amount: f.amt, description: f.desc },
                onComplete: function(data) {
                    if(data.status === 'approved') {
                        window.location.href = url + "?id=" + data.transaction.id + "&profil=" + f.prof;
                    }
                }
            });
        });
    </script>
</body>
</html>
