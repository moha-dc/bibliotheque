<?php
//début de la ssession
session_start();

//seul l'admin peut accéder à cette page 
if (!isset($_SESSION['admin_id'])) {
    //redirection vers la page de connexion
    header('Location: ../login.php');
    exit();
}
require_once __DIR__ . '/../config/database.php';

$page_title = 'Tableau de bord administrateur';

// Requête SQL pour récupérer tous les abonnés avec leurs statistiques d'emprunts
$sql_abonnes = "SELECT a.id_abonne,a.civilite,a.nom,a.prenom,a.email,count(e.id_emprunt) as total_emprunts, SUM(case WHEN e.date_rendu IS NULL THEN 1 ELSE 0 END) as emprunts_en_cours
FROM emprunt e, abonne a 
WHERE e.id_abonne = a.id_abonne
GROUP BY a.id_abonne, a.civilite,a.nom,a.prenom,a.email
ORDER BY emprunts_en_cours DESC, a.nom ASC;";

//préparer et exécuter la reuqête ! 
$pdo = getDbConnection();
$reqPreparee = $pdo->prepare($sql_abonnes);
$reqPreparee->execute();

//on récupère toutes les résultats dans un tableau associatif
$abonnes = $reqPreparee->fetchAll();


include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/nav.php';


?>

<!-- Contenu principal de la page -->
<main class="container mx-auto px-4 py-8 flex-grow" role="main">
    <!-- Titre de la page -->
    <header class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">
            Tableau de bord administrateur
        </h1>
        <p class="text-gray-600">
            Gestion des abonnés et suivi des emprunts
        </p>
    </header>

    <!-- Section des statistiques -->
    <section class="mb-8" aria-label="Statistiques">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Nombre total d'abonnés -->
            <article class="bg-blue-100 rounded-lg p-6 shadow">
                <h2 class="text-lg font-semibold text-blue-800 mb-2">Total d'abonnés</h2>
                <p class="text-4xl font-bold text-blue-600"><?= sizeof($abonnes) ?></p>
            </article>

            <!-- Nombre d'emprunts en cours -->
            <article class="bg-orange-100 rounded-lg p-6 shadow">
                <h2 class="text-lg font-semibold text-orange-800 mb-2">Emprunts en cours</h2>
                <p class="text-4xl font-bold text-orange-600">
                    <?php
                    $total_emprunts = 0;
                    foreach ($abonnes as $abonne) {
                        $total_emprunts += $abonne['emprunts_en_cours'];
                    }
                    echo $total_emprunts;
                    ?>
                </p>
            </article>

            <!-- Nombre d'abonnés avec retards -->
            <article class="bg-red-100 rounded-lg p-6 shadow">
                <h2 class="text-lg font-semibold text-red-800 mb-2">Abonnés avec retards</h2>
                <p class="text-4xl font-bold text-red-600">4</p>
            </article>
        </div>
    </section>

    <!-- Section de la liste des abonnés -->
    <section aria-label="Liste des abonnés">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">
            Liste des abonnés
        </h2>

        <?php if (empty($abonnes)): ?>
            <!-- Message si aucun abonné n'existe -->
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
                <p class="font-bold">Aucun abonné</p>
                <p>La bibliothèque n'a actuellement aucun abonné enregistré.</p>
            </div>
        <?php else : ?>
            <!-- Tableau des abonnés -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Civilité
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nom
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Prénom
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total emprunts
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                En cours
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($abonnes as $abonne): ?>
                            <!-- Ligne de l'abonné (en rouge si emprunts non rendus) -->
                            <tr class="<?= ($abonne['emprunts_en_cours'] > 0) ? 'bg-red-50 hover:bg-red-100' : 'hover:bg-gray-50'; ?>">
                                <!-- ID de l'abonné -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?= $abonne['id_abonne']; ?>
                                </td>

                                <!-- Civilité -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= $abonne['civilite']; ?>
                                </td>

                                <!-- Nom -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?= $abonne['nom']; ?>
                                </td>

                                <!-- Prénom -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= $abonne['prenom']; ?>
                                </td>

                                <!-- Email -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <a href="mailto:<?= $abonne['email']; ?>" class="text-blue-600 hover:text-blue-800">
                                        <?= $abonne['email']; ?>
                                    </a>
                                </td>

                                <!-- Nombre total d'emprunts -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-semibold">
                                        <?= $abonne['total_emprunts']; ?>
                                    </span>
                                </td>

                                <!-- Nombre d'emprunts en cours -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php if ($abonne['emprunts_en_cours'] > 0): ?>
                                        <span class="bg-red-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                                            ⚠ <?= $abonne['emprunts_en_cours']; ?> non rendu(s)
                                        </span>
                                    <?php else: ?>
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-semibold">
                                            ✓ Aucun
                                        </span>
                                    <?php endif; ?>

                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="?voir_historique=true&id_abonne=<?= $abonne['id_abonne']; ?>#historique" class="text-blue-600 hover:text-blue-800 font-medium">
                                        Voir historique
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
    <?php
    // Affichage de l'historique des emprunts d'un abonné si demandé
    if (isset($_GET['voir_historique']) && $_GET['voir_historique'] === 'true' && isset($_GET['id_abonne'])) {
        $id_abonne = intval($_GET['id_abonne']);
        //echo gettype($_GET['id_abonne']); => integer

        $sql_historique = "SELECT e.id_emprunt,e.date_sortie,e.date_rendu,l.titre,l.auteur,l.id_livre,(CASE WHEN e.date_rendu IS NULL THEN DATEDIFF(NOW(),e.date_sortie) ELSE DATEDIFF(e.date_rendu,e.date_sortie) END) as duree_jours FROM emprunt e, livre l WHERE e.id_livre = l.id_livre AND e.id_abonne = :id_abonne;";
        //préparer et exécuter la requête !
        $historique = $pdo->prepare($sql_historique);
        $historique->bindParam(':id_abonne', $id_abonne, PDO::PARAM_INT);
        $historique->execute();

        //on récupère tous les résultats dans un tableau associatif
        $emprunts = $historique->fetchAll();
        // echo '<pre>';
        // print_r($emprunts);
        // echo '</pre>';

        //récupérer les infos de l'abonné   
        $abonne_info = null;
        foreach ($abonnes as $abonne) {
            if ($abonne['id_abonne'] === $id_abonne) {
                $abonne_info = $abonne;
                break;
            }
        }
        // echo '<pre>';
        // print_r($abonne_info);
        // echo '</pre>';
    }

    ?>
    <section class="mt-8 bg-blue-50 rounded-lg p-6" aria-label="Historique des emprunts">
        <header class="mb-4 flex justify-between items-center">
            <div id="historique">
                <h2 class="text-2xl font-bold text-gray-800">
                    Historique des emprunts
                </h2>
                <?php if (isset($abonne_info)): ?>
                    <p class="text-gray-600 mt-1">
                        <?= $abonne_info['civilite'] . ' ' . $abonne_info['nom'] . ' ' . $abonne_info['prenom']; ?> (<?= $abonne_info['email']; ?>)
                    </p>
                <?php endif; ?>
            </div>
            <a href="?" class="text-blue-600 hover:text-blue-800 font-medium">
                ✕ Fermer
            </a>
        </header>

        <?php if (empty($emprunts)): ?>
            <!-- Message si aucun emprunt -->
            <p class="text-gray-600">Cet abonné n'a effectué aucun emprunt.</p>
        <?php else: ?>
            <!-- Tableau de l'historique -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID Emprunt
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Livre
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date de sortie
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date de retour
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Durée
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Statut
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($emprunts as $emprunt): ?>
                            <tr class="hover:bg-gray-50">
                                <!-- ID de l'emprunt -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #<?= $emprunt['id_emprunt']; ?>
                                </td>

                                <!-- Informations du livre -->
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="font-medium"><?= $emprunt['titre']; ?></div>
                                    <div class="text-gray-500 text-xs"><?= $emprunt['auteur']; ?></div>
                                </td>

                                <!-- Date de sortie -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= $emprunt['date_sortie']; ?>
                                </td>

                                <!-- Date de retour -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= $emprunt['date_rendu']; ?>
                                </td>

                                <!-- Durée de l'emprunt -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= $emprunt['duree_jours']; ?> jour(s)
                                </td>

                                <!-- Statut -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <?php if ($emprunt['date_rendu']): ?>
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-semibold">
                                            ✓ Rendu
                                        </span>
                                    <?php else: ?>
                                        <span class="bg-red-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                                            ⚠ En cours
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php
include __DIR__ . '/../includes/footer.php';
?>