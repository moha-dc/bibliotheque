<?php
// Démarrer la session
session_start();

// Appel à la BDD
require_once __DIR__ . '/../config/database.php';

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['abonne_id'])) {
    header('Location: /bibliotheque/login.php');
    exit;
}

// Page title
$page_title = 'Réserver un livre';

// Variables d'affichage
$error = "";
$success = "";
$livres_disponibles = [];

// Récupérer la connexion à la BDD
$pdo = getDbConnection();

// Traitement de la réservation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reserver'])) {
    $id_livre = $_POST['id_livre'] ?? null;
    $id_abonne = $_SESSION['abonne_id'];
    
    if (empty($id_livre)) {
        $error = "Veuillez sélectionner un livre.";
    } else {
        // Vérifier que le livre existe et est disponible
        $check_livre = $pdo->prepare("SELECT * FROM livre WHERE id_livre = :id_livre AND statut = 'disponible' LIMIT 1");
        $check_livre->bindParam(':id_livre', $id_livre, PDO::PARAM_INT);
        $check_livre->execute();
        
        if ($check_livre->rowCount() === 0) {
            $error = "Ce livre n'est plus disponible.";
        } else {
            // Vérifier que l'abonné n'a pas déjà réservé ce livre
            $check_reservation = $pdo->prepare("SELECT * FROM reservation WHERE id_abonne = :id_abonne AND id_livre = :id_livre AND statut IN ('en_attente', 'confirmee') LIMIT 1");
            $check_reservation->bindParam(':id_abonne', $id_abonne, PDO::PARAM_INT);
            $check_reservation->bindParam(':id_livre', $id_livre, PDO::PARAM_INT);
            $check_reservation->execute();
            
            if ($check_reservation->rowCount() > 0) {
                $error = "Vous avez déjà une réservation en cours pour ce livre.";
            } else {
                // Vérifier que l'abonné n'a pas déjà emprunté ce livre actuellement
                $check_emprunt = $pdo->prepare("SELECT * FROM emprunt WHERE id_abonne = :id_abonne AND id_livre = :id_livre AND date_retour_reel IS NULL LIMIT 1");
                $check_emprunt->bindParam(':id_abonne', $id_abonne, PDO::PARAM_INT);
                $check_emprunt->bindParam(':id_livre', $id_livre, PDO::PARAM_INT);
                $check_emprunt->execute();
                
                if ($check_emprunt->rowCount() > 0) {
                    $error = "Vous avez déjà emprunté ce livre actuellement.";
                } else {
                    // Créer la réservation
                    $sql_reservation = "INSERT INTO reservation (id_abonne, id_livre, date_reservation, statut) 
                                       VALUES (:id_abonne, :id_livre, NOW(), 'en_attente')";
                    $stmt = $pdo->prepare($sql_reservation);
                    $stmt->bindParam(':id_abonne', $id_abonne, PDO::PARAM_INT);
                    $stmt->bindParam(':id_livre', $id_livre, PDO::PARAM_INT);
                    
                    if ($stmt->execute()) {
                        // Mettre à jour le statut du livre
                        $update_livre = $pdo->prepare("UPDATE livre SET statut = 'reserve' WHERE id_livre = :id_livre");
                        $update_livre->bindParam(':id_livre', $id_livre, PDO::PARAM_INT);
                        $update_livre->execute();
                        
                        $success = "Votre réservation a été enregistrée avec succès ! Vous serez notifié lorsque le livre sera prêt à être récupéré.";
                    } else {
                        $error = "Une erreur est survenue lors de la réservation. Veuillez réessayer.";
                    }
                }
            }
        }
    }
}

// Récupérer tous les livres disponibles
try {
    $sql_livres = "SELECT l.*, a.nom as auteur_nom, a.prenom as auteur_prenom, 
                   c.nom_categorie 
                   FROM livre l 
                   LEFT JOIN auteur a ON l.id_auteur = a.id_auteur 
                   LEFT JOIN categorie c ON l.id_categorie = c.id_categorie 
                   WHERE l.statut = 'disponible' 
                   ORDER BY l.titre ASC";
    $stmt_livres = $pdo->query($sql_livres);
    $livres_disponibles = $stmt_livres->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Erreur lors du chargement des livres disponibles.";
}

// Récupérer les réservations en cours de l'abonné
try {
    $sql_mes_reservations = "SELECT r.*, l.titre, l.isbn, 
                             a.nom as auteur_nom, a.prenom as auteur_prenom 
                             FROM reservation r 
                             INNER JOIN livre l ON r.id_livre = l.id_livre 
                             LEFT JOIN auteur a ON l.id_auteur = a.id_auteur 
                             WHERE r.id_abonne = :id_abonne 
                             AND r.statut IN ('en_attente', 'confirmee') 
                             ORDER BY r.date_reservation DESC";
    $stmt_reservations = $pdo->prepare($sql_mes_reservations);
    $stmt_reservations->bindParam(':id_abonne', $_SESSION['abonne_id'], PDO::PARAM_INT);
    $stmt_reservations->execute();
    $mes_reservations = $stmt_reservations->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $mes_reservations = [];
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/nav.php';
?>

<!-- Contenu principal de la page -->
<main class="container mx-auto px-4 py-8 flex-grow" role="main">
    <div class="max-w-7xl mx-auto">
        <!-- En-tête de la page -->
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                Réserver un livre
            </h1>
            <p class="text-gray-600">
                Parcourez les livres disponibles et réservez celui de votre choix
            </p>
        </header>

        <!-- Affichage du succès -->
        <?php if (!empty($success)): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p class="font-bold">Succès</p>
                <p><?= htmlspecialchars($success); ?></p>
            </div>
        <?php endif; ?>

        <!-- Affichage des erreurs -->
        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <p class="font-bold">Erreur</p>
                <p><?= htmlspecialchars($error); ?></p>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Section principale : Livres disponibles -->
            <div class="lg:col-span-2">
                <section class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-6">
                        Livres disponibles (<?= count($livres_disponibles); ?>)
                    </h2>

                    <?php if (empty($livres_disponibles)): ?>
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <h3 class="mt-2 text-lg font-medium text-gray-900">Aucun livre disponible</h3>
                            <p class="mt-1 text-gray-500">Tous les livres sont actuellement empruntés ou réservés.</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($livres_disponibles as $livre): ?>
                                <article class="border border-gray-200 rounded-lg p-4 hover:shadow-lg transition">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h3 class="text-xl font-semibold text-gray-800 mb-2">
                                                <?= htmlspecialchars($livre['titre']); ?>
                                            </h3>
                                            
                                            <div class="text-sm text-gray-600 space-y-1 mb-3">
                                                <?php if (!empty($livre['auteur_nom'])): ?>
                                                    <p>
                                                        <span class="font-medium">Auteur :</span>
                                                        <?= htmlspecialchars($livre['auteur_prenom'] . ' ' . $livre['auteur_nom']); ?>
                                                    </p>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($livre['isbn'])): ?>
                                                    <p>
                                                        <span class="font-medium">ISBN :</span>
                                                        <?= htmlspecialchars($livre['isbn']); ?>
                                                    </p>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($livre['nom_categorie'])): ?>
                                                    <p>
                                                        <span class="font-medium">Catégorie :</span>
                                                        <?= htmlspecialchars($livre['nom_categorie']); ?>
                                                    </p>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($livre['annee_publication'])): ?>
                                                    <p>
                                                        <span class="font-medium">Année :</span>
                                                        <?= htmlspecialchars($livre['annee_publication']); ?>
                                                    </p>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <span class="inline-block px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                                Disponible
                                            </span>
                                        </div>
                                        
                                        <div class="ml-4">
                                            <form method="POST" action="<?= $_SERVER['PHP_SELF']; ?>">
                                                <input type="hidden" name="id_livre" value="<?= $livre['id_livre']; ?>">
                                                <button
                                                    type="submit"
                                                    name="reserver"
                                                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                                                    Réserver
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>
            </div>

            <!-- Section latérale : Mes réservations en cours -->
            <div class="lg:col-span-1">
                <aside class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">
                        Mes réservations en cours
                    </h2>

                    <?php if (empty($mes_reservations)): ?>
                        <div class="text-center py-8">
                            <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">Aucune réservation en cours</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php foreach ($mes_reservations as $reservation): ?>
                                <div class="border border-gray-200 rounded-lg p-3">
                                    <h4 class="font-semibold text-gray-800 text-sm mb-1">
                                        <?= htmlspecialchars($reservation['titre']); ?>
                                    </h4>
                                    <p class="text-xs text-gray-600 mb-2">
                                        <?= htmlspecialchars($reservation['auteur_prenom'] . ' ' . $reservation['auteur_nom']); ?>
                                    </p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs <?= $reservation['statut'] === 'confirmee' ? 'text-green-600' : 'text-yellow-600'; ?>">
                                            <?= $reservation['statut'] === 'confirmee' ? '✓ Confirmée' : '⏳ En attente'; ?>
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            <?= date('d/m/Y', strtotime($reservation['date_reservation'])); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <a href="mes-reservations.php" class="block text-center text-blue-600 hover:text-blue-800 font-semibold text-sm transition">
                            Voir toutes mes réservations →
                        </a>
                    </div>
                </aside>
            </div>
        </div>

        <!-- Lien de retour -->
        <div class="mt-8">
            <a href="mon-compte.php" class="text-gray-600 hover:text-gray-800 transition">
                ← Retour à mon compte
            </a>
        </div>
    </div>
</main>

<?php
include __DIR__ . '/../includes/footer.php';
?>