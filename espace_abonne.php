<?php
session_start();
require_once __DIR__ . '/config/database.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['abonne_id'])) {
    header('Location: login.php');
    exit;
}

$pdo = getDbConnection();

$id_abonne = $_SESSION['abonne_id'];
$prenom    = htmlspecialchars($_SESSION['abonne_prenom']);
$nom       = htmlspecialchars($_SESSION['abonne_nom']);
$email     = htmlspecialchars($_SESSION['abonne_email']);

$message = "";

// --- 1️⃣ Gérer un emprunt ---
if (isset($_GET['emprunter'])) {
    $id_livre = (int)$_GET['emprunter'];

    // Vérifier si le livre est disponible
    $check = $pdo->prepare("SELECT * FROM livre WHERE id_livre = :id_livre AND disponible = 1");
    $check->execute([':id_livre' => $id_livre]);

    if ($check->rowCount() === 1) {
        // Insérer un nouvel emprunt
        $insert = $pdo->prepare("INSERT INTO emprunt (id_abonne, id_livre, date_sortie) VALUES (:id_abonne, :id_livre, NOW())");
        $insert->execute([':id_abonne' => $id_abonne, ':id_livre' => $id_livre]);

        // Mettre à jour la disponibilité du livre
        $pdo->prepare("UPDATE livre SET disponible = 0 WHERE id_livre = :id_livre")
            ->execute([':id_livre' => $id_livre]);

        $message = "<p class='text-green-600 font-semibold'>✅ Livre emprunté avec succès !</p>";
    } else {
        $message = "<p class='text-red-600 font-semibold'>❌ Ce livre n’est plus disponible.</p>";
    }
}

// --- 2️⃣ Gérer un retour ---
if (isset($_GET['rendre'])) {
    $id_emprunt = (int)$_GET['rendre'];

    // Récupérer le livre lié à l'emprunt
    $getLivre = $pdo->prepare("SELECT id_livre FROM emprunt WHERE id_emprunt = :id_emprunt AND id_abonne = :id_abonne AND date_rendu IS NULL");
    $getLivre->execute([':id_emprunt' => $id_emprunt, ':id_abonne' => $id_abonne]);
    $emprunt = $getLivre->fetch(PDO::FETCH_ASSOC);

    if ($emprunt) {
        // Mettre à jour la date de retour
        $pdo->prepare("UPDATE emprunt SET date_rendu = NOW() WHERE id_emprunt = :id_emprunt")
            ->execute([':id_emprunt' => $id_emprunt]);

        // Rendre le livre disponible à nouveau
        $pdo->prepare("UPDATE livre SET disponible = 1 WHERE id_livre = :id_livre")
            ->execute([':id_livre' => $emprunt['id_livre']]);

        $message = "<p class='text-green-600 font-semibold'>📚 Livre rendu avec succès ! Merci.</p>";
    } else {
        $message = "<p class='text-red-600 font-semibold'>⚠ Impossible de rendre ce livre.</p>";
    }
}

// --- 3️⃣ Récupérer tous les livres ---
$livres = $pdo->query("SELECT * FROM livre ORDER BY titre ASC")->fetchAll(PDO::FETCH_ASSOC);

// --- 4️⃣ Récupérer les emprunts de l'abonné ---
$sql_emprunts = "SELECT e.id_emprunt, e.date_sortie, e.date_rendu, l.titre, l.auteur
                 FROM emprunt e
                 JOIN livre l ON e.id_livre = l.id_livre
                 WHERE e.id_abonne = :id_abonne
                 ORDER BY e.date_sortie DESC";
$stmt = $pdo->prepare($sql_emprunts);
$stmt->execute([':id_abonne' => $id_abonne]);
$emprunts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = "Espace abonné - $prenom $nom";

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';
?>

<main class="container mx-auto px-4 py-8 flex-grow" role="main">
    <div class="max-w-5xl mx-auto bg-white rounded-lg shadow-md p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-4 text-center">
            Bienvenue, <?= $prenom; ?> 👋
        </h1>
        <p class="text-gray-600 text-center mb-6">Gérez vos emprunts ici.</p>

        <?php if (!empty($message)): ?>
            <div class="text-center mb-6"><?= $message; ?></div>
        <?php endif; ?>

        <!-- Informations abonné -->
        <div class="bg-gray-50 p-4 rounded-lg mb-8">
            <h2 class="text-xl font-semibold text-gray-700 mb-2">Vos informations</h2>
            <ul class="text-gray-600">
                <li><strong>Nom :</strong> <?= $nom; ?></li>
                <li><strong>Prénom :</strong> <?= $prenom; ?></li>
                <li><strong>Email :</strong> <?= $email; ?></li>
            </ul>
        </div>

        <!-- Section : Catalogue -->
        <div class="bg-gray-50 p-4 rounded-lg mb-8">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">📚 Catalogue des livres</h2>
            <?php if (count($livres) > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-300 rounded-lg">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="py-2 px-4 text-left">Titre</th>
                                <th class="py-2 px-4 text-left">Auteur</th>
                                <th class="py-2 px-4 text-center">Disponibilité</th>
                                <th class="py-2 px-4 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($livres as $livre): ?>
                                <tr class="border-t hover:bg-gray-100 transition">
                                    <td class="py-2 px-4"><?= htmlspecialchars($livre['titre']); ?></td>
                                    <td class="py-2 px-4"><?= htmlspecialchars($livre['auteur']); ?></td>
                                    <td class="py-2 px-4 text-center"> 
                                    </td>
                                    <td class="py-2 px-4 text-center">
                                            <a href="?emprunter=<?= $livre['id_livre']; ?>"
                                               class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg text-sm transition">
                                               Emprunter
                                            </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-gray-500">Aucun livre enregistré pour le moment.</p>
            <?php endif; ?>
        </div>

        <!-- Section : Mes emprunts -->
        <div class="bg-gray-50 p-4 rounded-lg">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">📖 Mes emprunts</h2>
            <?php if (count($emprunts) > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-300 rounded-lg">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="py-2 px-4 text-left">Titre</th>
                                <th class="py-2 px-4 text-left">Auteur</th>
                                <th class="py-2 px-4 text-center">Date d'emprunt</th>
                                <th class="py-2 px-4 text-center">Date de retour</th>
                                <th class="py-2 px-4 text-center">Statut</th>
                                <th class="py-2 px-4 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($emprunts as $emprunt): ?>
                                <tr class="border-t hover:bg-gray-100 transition">
                                    <td class="py-2 px-4"><?= htmlspecialchars($emprunt['titre']); ?></td>
                                    <td class="py-2 px-4"><?= htmlspecialchars($emprunt['auteur']); ?></td>
                                    <td class="py-2 px-4 text-center"><?= $emprunt['date_sortie']; ?></td>
                                    <td class="py-2 px-4 text-center"><?= $emprunt['date_rendu'] ?? '-'; ?></td>
                                    <td class="py-2 px-4 text-center">
                                        <?php if ($emprunt['date_rendu']): ?>
                                            <span class="text-green-600 font-semibold">✓ Rendu</span>
                                        <?php else: ?>
                                            <span class="text-orange-600 font-semibold">En cours</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-2 px-4 text-center">
                                        <?php if (!$emprunt['date_rendu']): ?>
                                            <a href="?rendre=<?= $emprunt['id_emprunt']; ?>"
                                               class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg text-sm transition">
                                               Rendre
                                            </a>
                                        <?php else: ?>
                                            <button disabled class="bg-gray-400 text-white px-3 py-1 rounded-lg text-sm cursor-not-allowed">
                                                Terminé
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-gray-500">Vous n’avez effectué aucun emprunt pour le moment.</p>
            <?php endif; ?>
        </div>

        <div class="mt-8 text-center">
            <a href="deconnexion.php" class="text-red-600 hover:text-red-800 font-semibold">🚪 Se déconnecter</a>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
