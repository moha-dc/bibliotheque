<?php
// Démarrer la session
session_start();

// Appel à la base de données
require_once __DIR__ . '/config/database.php';

// Si l'utilisateur est déjà connecté, le rediriger
if (isset($_SESSION['admin_id']) || isset($_SESSION['abonne_id'])) {
    header('Location: index.php');
    exit;
}

// Titre de la page
$page_title = 'Inscription';

// Variables d'affichage
$error = "";
$success = "";

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer et nettoyer les données
    $civilite = $_POST['civilite'] ?? '';
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $mot_de_passe_confirm = $_POST['mot_de_passe_confirm'] ?? '';

    // Validation du formulaire
    if (empty($civilite)) {
        $error .= "<p>La civilité est obligatoire.</p>";
    }
    if (empty($nom)) {
        $error .= "<p>Le nom est obligatoire.</p>";
    }
    if (empty($prenom)) {
        $error .= "<p>Le prénom est obligatoire.</p>";
    }
    if (empty($email)) {
        $error .= "<p>L'email est obligatoire.</p>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error .= "<p>Format d'email invalide.</p>";
    }
    if (empty($mot_de_passe)) {
        $error .= "<p>Le mot de passe est obligatoire.</p>";
    } elseif (strlen(trim($mot_de_passe)) < 5) {
        $error .= "<p>Le mot de passe doit contenir au moins 5 caractères.</p>";
    }
    if ($mot_de_passe !== $mot_de_passe_confirm) {
        $error .= "<p>Les mots de passe ne correspondent pas.</p>";
    }

    // Si pas d'erreur, vérifier l'unicité de l'email et insérer en base
    if (empty($error)) {
        $pdo = getDbConnection();

        // Vérifier si l'email existe déjà
        $check_email = $pdo->prepare("SELECT id_abonne FROM abonne WHERE email = :email LIMIT 1");
        $check_email->bindParam(':email', $email);
        $check_email->execute();

        if ($check_email->rowCount() > 0) {
            $error .= "<p>Cet email est déjà utilisé.</p>";
        } else {
            // Hasher le mot de passe
            $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

            // Préparer l'insertion
            $sql = "INSERT INTO abonne (civilite, nom, prenom, email, mot_de_passe)
                    VALUES (:civilite, :nom, :prenom, :email, :mot_de_passe)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':civilite', $civilite);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':prenom', $prenom);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':mot_de_passe', $mot_de_passe_hash);

            if ($stmt->execute()) {
                $success = "Votre compte a été créé avec succès ! Vous pouvez maintenant vous connecter.";
                $_POST = []; // Réinitialiser le formulaire
            } else {
                $error .= "<p>Une erreur est survenue lors de l'inscription. Veuillez réessayer.</p>";
            }
        }
    }
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';
?>

<main class="container mx-auto px-4 py-8 flex-grow" role="main">
    <div class="max-w-md mx-auto">
        <header class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Créer un compte</h1>
            <p class="text-gray-600">Rejoignez notre bibliothèque et profitez de nos services</p>
        </header>

        <section class="bg-white rounded-lg shadow-md p-8">
            <?php if (!empty($success)): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p class="font-bold">Succès</p>
                    <p><?= $success; ?></p>
                    <a href="/bibliotheque/login.php" class="underline font-semibold mt-2 inline-block">
                        Cliquez ici pour vous connecter
                    </a>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p class="font-bold">Erreur</p>
                    <div><?= $error; ?></div>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= $_SERVER['PHP_SELF']; ?>" novalidate>
                <!-- Civilité -->
                <div class="mb-4">
                    <label for="civilite" class="block text-gray-700 font-semibold mb-2">Civilité <span class="text-red-500">*</span></label>
                    <select id="civilite" name="civilite" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Sélectionnez</option>
                        <option value="M" <?= (isset($_POST['civilite']) && $_POST['civilite']=='M') ? 'selected':''; ?>>M.</option>
                        <option value="Mme" <?= (isset($_POST['civilite']) && $_POST['civilite']=='Mme') ? 'selected':''; ?>>Mme</option>
                        <option value="Mlle" <?= (isset($_POST['civilite']) && $_POST['civilite']=='Mlle') ? 'selected':''; ?>>Mlle</option>
                    </select>
                </div>

                <!-- Nom -->
                <div class="mb-4">
                    <label for="nom" class="block text-gray-700 font-semibold mb-2">Nom <span class="text-red-500">*</span></label>
                    <input type="text" id="nom" name="nom" required value="<?= $_POST['nom'] ?? ""; ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Prénom -->
                <div class="mb-4">
                    <label for="prenom" class="block text-gray-700 font-semibold mb-2">Prénom <span class="text-red-500">*</span></label>
                    <input type="text" id="prenom" name="prenom" required value="<?= $_POST['prenom'] ?? ""; ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 font-semibold mb-2">Email <span class="text-red-500">*</span></label>
                    <input type="email" id="email" name="email" required value="<?= $_POST['email'] ?? ""; ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Mot de passe -->
                <div class="mb-4">
                    <label for="mot_de_passe" class="block text-gray-700 font-semibold mb-2">Mot de passe <span class="text-red-500">*</span></label>
                    <input type="password" id="mot_de_passe" name="mot_de_passe" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Confirmation mot de passe -->
                <div class="mb-4">
                    <label for="mot_de_passe_confirm" class="block text-gray-700 font-semibold mb-2">Confirmer le mot de passe <span class="text-red-500">*</span></label>
                    <input type="password" id="mot_de_passe_confirm" name="mot_de_passe_confirm" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <p class="text-sm text-gray-600 mt-2"><span class="text-red-500">*</span> Champs obligatoires</p>

                <button type="submit"
                        class="w-full mt-6 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition">
                    Créer mon compte
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="/bibliotheque/login.php" class="text-blue-600 hover:text-blue-800 transition">Déjà un compte ? Se connecter</a>
            </div>
        </section>
    </div>
</main>

<?php
include __DIR__ . '/includes/footer.php';
?>
