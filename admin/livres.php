<?php
require_once __DIR__ . '/../config/database.php';

// Récupération de la connexion à la base de données
$pdo = getDbConnection();

// Requête SQL pour récupérer tous les livres avec leur statut et l'abonné qui les a empruntés
$sql = "SELECT 
            l.id_livre,
            l.titre,
            l.auteur,
            l.couverture,
            CASE WHEN e.id_emprunt IS NOT NULL THEN 'emprunte' ELSE 'disponible' END as statut,
            a.nom as abonne_nom,
            a.prenom as abonne_prenom
        FROM livre l 
        LEFT JOIN (
            SELECT id_livre, id_emprunt, id_abonne 
            FROM emprunt 
            WHERE date_rendu IS NULL
        ) e ON l.id_livre = e.id_livre
        LEFT JOIN abonne a ON e.id_abonne = a.id_abonne
        ORDER BY l.titre ASC";

// Préparation et exécution de la requête
$reqPreparee = $pdo->prepare($sql);
$reqPreparee->execute();

// Récupération de tous les résultats dans un tableau associatif
$livres = $reqPreparee->fetchAll();

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/nav.php';
?>
<!-- Contenu principal de la page -->
<main class="container mx-auto px-4 py-8 flex-grow" role="main">
    <!-- En-tête de la page -->
    <header class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-4xl font-bold text-gray-800 mb-2">
                Gestion des livres
            </h1>
            <p class="text-gray-600">
                Total : <?= count($livres) ?> livre(s)
            </p>
        </div>

        <!-- Bouton pour ajouter un nouveau livre -->
        <a href="livre_add.php" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition shadow">
            ➕ Ajouter un livre
        </a>
    </header>

    <!-- Section de la liste des livres -->
    <section aria-label="Liste des livres">

        <?php if (empty($livres)): ?>
            <!-- Message si aucun livre n'est disponible -->
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
                <p class="font-bold">Aucun livre</p>
                <p>La bibliothèque ne contient actuellement aucun livre.</p>
            </div>
        <?php else: ?>
            <!-- Tableau des livres -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Titre
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Auteur
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Statut
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Emprunté par
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($livres as $livre): ?>
                            <tr class="hover:bg-gray-50">
                                <!-- ID du livre -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?= $livre['id_livre'] ?>
                                </td>

                                <!-- Titre du livre -->
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    <?= htmlspecialchars($livre['titre']) ?>
                                </td>

                                <!-- Auteur du livre -->
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <?= htmlspecialchars($livre['auteur']) ?>
                                </td>

                                <!-- Statut du livre -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <?php if ($livre['statut'] === 'disponible'): ?>
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-semibold">
                                            ✓ Disponible
                                        </span>
                                    <?php else: ?>
                                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-semibold">
                                            ✗ En prêt
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Abonné ayant emprunté le livre -->
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <?php if ($livre['statut'] === 'emprunte' && !empty($livre['abonne_nom'])): ?>
                                        <?= htmlspecialchars($livre['abonne_prenom'] . ' ' . $livre['abonne_nom']) ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                    <!-- Bouton modifier -->
                                    <a href="livre_edit.php?id=<?= $livre['id_livre'] ?>"
                                        class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded transition font-medium">
                                        ✏️ Modifier
                                    </a>

                                    <!-- Bouton supprimer -->
                                    <a href="livre_delete.php?id=<?= $livre['id_livre'] ?>"
                                        class="inline-block bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded transition font-medium">
                                        🗑️ Supprimer
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </section>
</main>
<!-- Pied de page -->
<?php include __DIR__ . '/../includes/footer.php'; ?>