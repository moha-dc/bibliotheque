<?php
// Démarrer la session
session_start();

// Appel à la base de données
require_once __DIR__ . '/config/database.php';

// Si l'utilisateur est déjà connecté, rediriger
if (isset($_SESSION['admin_id'])) {
    header('Location: admin/dashboard.php');
    exit;
}
if (isset($_SESSION['abonne_id'])) {
    header('Location: index.php');
    exit;
}

// Titre de la page
$page_title = 'Page de connexion';

// Variable d'affichage d'erreurs
$error = "";

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    extract($_POST);

    // Vérifier le type d'utilisateur
    $user_type = $_POST['user_type'] ?? 'abonne';

    // Vérifier le format de l'email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error .= "<p>Format d'email invalide.</p>";
    }

    // Vérifier le mot de passe
    if (iconv_strlen(trim($mot_de_passe)) < 5) {
        $error .= "<p>Le mot de passe doit contenir au moins 5 caractères.</p>";
    }

    if (empty($error)) {
        $pdo = getDbConnection();

        if ($user_type === 'admin') {
            // Connexion administrateur
            $sql = "SELECT id_admin, mot_de_passe, nom, prenom, email 
                    FROM administrateur 
                    WHERE email = :email LIMIT 1";
        } else {
            // Connexion abonné
            $sql = "SELECT id_abonne, mot_de_passe, nom, prenom, email 
                    FROM abonne 
                    WHERE email = :email LIMIT 1";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($mot_de_passe, $user['mot_de_passe'])) {
                if ($user_type === 'admin') {
                    // Authentification admin réussie
                    $_SESSION['admin_id'] = $user['id_admin'];
                    $_SESSION['admin_nom'] = $user['nom'];
                    $_SESSION['admin_prenom'] = $user['prenom'];
                    $_SESSION['admin_email'] = $user['email'];

                    // Mettre à jour la date de dernière connexion
                    $update_sql = "UPDATE administrateur SET dernier_acces = NOW() WHERE id_admin = :id_admin";
                    $update = $pdo->prepare($update_sql);
                    $update->bindParam(':id_admin', $user['id_admin'], PDO::PARAM_INT);
                    $update->execute();

                    $_SESSION['message'] = "Bienvenue " . $user['prenom'];
                    header('Location: admin/dashboard.php');
                    exit;
                } else {
                    // Authentification abonné réussie
                    $_SESSION['abonne_id'] = $user['id_abonne'];
                    $_SESSION['abonne_nom'] = $user['nom'];
                    $_SESSION['abonne_prenom'] = $user['prenom'];
                    $_SESSION['abonne_email'] = $user['email'];

                    $_SESSION['message'] = "Bienvenue " . $user['prenom'];
                    header('Location: index.php');
                    exit;
                }
            } else {
                $error .= "<p>Email ou mot de passe incorrect.</p>";
            }
        } else {
            $error .= "<p>Email ou mot de passe incorrect.</p>";
        }
    }
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';
?>

<!-- Contenu principal -->
<main class="container mx-auto px-4 py-8 flex-grow" role="main">
    <div class="max-w-md mx-auto">
        <header class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Connexion</h1>
            <p class="text-gray-600">Connectez-vous à votre compte</p>
        </header>

        <section class="bg-white rounded-lg shadow-md p-8">
            <?php if (!empty($error)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p class="font-bold">Erreur</p>
                    <p><?= $error; ?></p>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= $_SERVER['PHP_SELF']; ?>" novalidate>
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-3">Type de compte</label>
                    <div class="flex gap-4">
                        <label class="flex items-center cursor-pointer">
                            <input
                                type="radio"
                                name="user_type"
                                value="abonne"
                                <?= (!isset($_POST['user_type']) || $_POST['user_type'] === 'abonne') ? 'checked' : ''; ?>
                                class="w-4 h-4 text-blue-600 focus:ring-2 focus:ring-blue-500">
                            <span class="ml-2 text-gray-700">Abonné</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input
                                type="radio"
                                name="user_type"
                                value="admin"
                                <?= (isset($_POST['user_type']) && $_POST['user_type'] === 'admin') ? 'checked' : ''; ?>
                                class="w-4 h-4 text-blue-600 focus:ring-2 focus:ring-blue-500">
                            <span class="ml-2 text-gray-700">Administrateur</span>
                        </label>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="email" class="block text-gray-700 font-semibold mb-2">Email</label>
                    <input
                        type="text"
                        id="email"
                        name="email"
                        required
                        autocomplete="email"
                        value="<?= $_POST['email'] ?? ''; ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Entrez votre email">
                </div>

                <div class="mb-6">
                    <label for="mot_de_passe" class="block text-gray-700 font-semibold mb-2">Mot de passe</label>
                    <input
                        type="password"
                        id="mot_de_passe"
                        name="mot_de_passe"
                        required
                        autocomplete="current-password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Entrez votre mot de passe">
                </div>

                <button
                    type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition">
                    Se connecter
                </button>
            </form>

            <div class="mt-6 space-y-3 text-center">
                <div>
                    <a href="/bibliotheque/inscription.php" class="text-blue-600 hover:text-blue-800 transition">
                        Pas encore de compte ? S'inscrire
                    </a>
                </div>
                <div>
                    <a href="/bibliotheque/index.php" class="text-gray-600 hover:text-gray-800 transition">
                        ← Retour à l'accueil
                    </a>
                </div>
            </div>
        </section>
    </div>
</main>

<?php
include __DIR__ . '/includes/footer.php';
?>
