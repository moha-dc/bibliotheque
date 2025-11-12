<?php
// Démarrer la session
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['abonne_id'])) {
    header('Location: login.php');
    exit;
}

// Récupérer les infos de l'abonné depuis la session
$prenom = htmlspecialchars($_SESSION['abonne_prenom']);
$nom    = htmlspecialchars($_SESSION['abonne_nom']);
$email  = htmlspecialchars($_SESSION['abonne_email']);

// Titre de la page
$page_title = "Espace abonné - $prenom $nom";

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';
?>

<main class="container mx-auto px-4 py-8 flex-grow" role="main">
    <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-md p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-4 text-center">
            Bienvenue, <?= $prenom; ?> 👋
        </h1>
        <p class="text-gray-600 text-center mb-6">
            Voici votre espace personnel.
        </p>

        <div class="space-y-4">
            <div class="p-4 bg-gray-50 rounded-lg">
                <h2 class="text-xl font-semibold text-gray-700 mb-2">Vos informations</h2>
                <ul class="text-gray-600">
                    <li><strong>Nom :</strong> <?= $nom; ?></li>
                    <li><strong>Prénom :</strong> <?= $prenom; ?></li>
                    <li><strong>Email :</strong> <?= $email; ?></li>
                </ul>
            </div>

            <div class="p-4 bg-gray-50 rounded-lg">
                <h2 class="text-xl font-semibold text-gray-700 mb-2">Actions disponibles</h2>
                <ul class="list-disc ml-6 text-gray-600 space-y-1">
                    <li><a href="catalogue.php" class="text-blue-600 hover:underline">📚 Consulter le catalogue</a></li>
                    <li><a href="profil.php" class="text-blue-600 hover:underline">🧑‍💼 Modifier mon profil</a></li>
                    <li><a href="deconnexion.php" class="text-red-600 hover:underline">🚪 Se déconnecter</a></li>
                </ul>
            </div>
        </div>
    </div>
</main>

<?php
include __DIR__ . '/includes/footer.php';
?>
