<?php

//démarrer la session : 
session_start();
//appel à la bdd : 
require_once __DIR__ . '/config/database.php';

//si l'utilisateur est déjà connecté, le rediriger
if (isset($_SESSION['admin_id'])) {
    header('Location: admin/dashboard.php');
    exit;
}

if (isset($_SESSION['abonne_id'])) {
    header('Location: abonne/mon-compte.php');
    exit;
}

//page title
$page_title = 'Inscription';

//variables d'affichage
$error = "";
$success = "";

//traitement du formulaire d'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    extract($_POST);
    
    // Validation du login
    if (empty(trim($login))) {
        $error .= "<p>Le login est obligatoire.</p>";
    } elseif (iconv_strlen(trim($login)) < 3) {
        $error .= "<p>Le login doit contenir au moins 3 caractères.</p>";
    }
    
    // Validation du nom
    if (empty(trim($nom))) {
        $error .= "<p>Le nom est obligatoire.</p>";
    }
    
    // Validation du prénom
    if (empty(trim($prenom))) {
        $error .= "<p>Le prénom est obligatoire.</p>";
    }
    
    // Validation de l'email
    if (empty(trim($email))) {
        $error .= "<p>L'email est obligatoire.</p>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error .= "<p>Format d'email invalide.</p>";
    }
    
    // Validation de l'adresse
    if (empty(trim($adresse))) {
        $error .= "<p>L'adresse est obligatoire.</p>";
    }
    
    // Validation du téléphone (optionnel mais si fourni, doit être valide)
    if (!empty(trim($telephone)) && !preg_match('/^[0-9]{10}$/', preg_replace('/[\s\-\.]/', '', $telephone))) {
        $error .= "<p>Le format du téléphone est invalide (10 chiffres attendus).</p>";
    }
    
    // Validation du mot de passe
    if (empty(trim($mot_de_passe))) {
        $error .= "<p>Le mot de passe est obligatoire.</p>";
    } elseif (iconv_strlen(trim($mot_de_passe)) < 5) {
        $error .= "<p>Le mot de passe doit contenir au moins 5 caractères.</p>";
    }
    
    // Validation de la confirmation du mot de passe
    if (empty(trim($mot_de_passe_confirm))) {
        $error .= "<p>Veuillez confirmer votre mot de passe.</p>";
    } elseif ($mot_de_passe !== $mot_de_passe_confirm) {
        $error .= "<p>Les mots de passe ne correspondent pas.</p>";
    }
    
    // Si pas d'erreur, vérifier l'unicité et insérer en base
    if (empty($error)) {
        $pdo = getDbConnection();
        
        // Vérifier si le login existe déjà
        $check_login = $pdo->prepare("SELECT id_abonne FROM abonne WHERE login = :login LIMIT 1");
        $check_login->bindParam(':login', $login, PDO::PARAM_STR);
        $check_login->execute();
        
        if ($check_login->rowCount() > 0) {
            $error .= "<p>Ce login est déjà utilisé.</p>";
        }
        
        // Vérifier si l'email existe déjà
        $check_email = $pdo->prepare("SELECT id_abonne FROM abonne WHERE email = :email LIMIT 1");
        $check_email->bindParam(':email', $email, PDO::PARAM_STR);
        $check_email->execute();
        
        if ($check_email->rowCount() > 0) {
            $error .= "<p>Cet email est déjà utilisé.</p>";
        }
        
        // Si tout est OK, procéder à l'inscription
        if (empty($error)) {
            // Hasher le mot de passe
            $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
            
            // Préparer la requête d'insertion
            $sql = "INSERT INTO abonne (login, mot_de_passe, nom, prenom, email, adresse, telephone, statut, date_inscription) 
                    VALUES (:login, :mot_de_passe, :nom, :prenom, :email, :adresse, :telephone, 'actif', NOW())";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':login', $login, PDO::PARAM_STR);
            $stmt->bindParam(':mot_de_passe', $mot_de_passe_hash, PDO::PARAM_STR);
            $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
            $stmt->bindParam(':prenom', $prenom, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':adresse', $adresse, PDO::PARAM_STR);
            $stmt->bindParam(':telephone', $telephone, PDO::PARAM_STR);
            
            if ($stmt->execute()) {
                $success = "Votre compte a été créé avec succès ! Vous pouvez maintenant vous connecter.";
                // Réinitialiser les champs du formulaire
                $_POST = [];
            } else {
                $error .= "<p>Une erreur est survenue lors de l'inscription. Veuillez réessayer.</p>";
            }
        }
    }
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';

?>

<!-- Contenu principal de la page -->
<main class="container mx-auto px-4 py-8 flex-grow" role="main">
    <div class="max-w-2xl mx-auto">
        <!-- Titre de la page -->
        <header class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                Créer un compte
            </h1>
            <p class="text-gray-600">
                Rejoignez notre bibliothèque et profitez de tous nos services
            </p>
        </header>

        <!-- Formulaire d'inscription -->
        <section class="bg-white rounded-lg shadow-md p-8">
            <!-- Affichage du succès -->
            <?php if (!empty($success)): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p class="font-bold">Succès</p>
                    <p><?= $success; ?></p>
                    <a href="/bibliotheque/login.php" class="underline font-semibold mt-2 inline-block">
                        Cliquez ici pour vous connecter
                    </a>
                </div>
            <?php endif; ?>

            <!-- Affichage des erreurs -->
            <?php if (!empty($error)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p class="font-bold">Erreur</p>
                    <div><?= $error; ?></div>
                </div>
            <?php endif; ?>

            <!-- Formulaire -->
            <form method="POST" action="<?= $_SERVER['PHP_SELF']; ?>" novalidate>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Champ Login -->
                    <div>
                        <label for="login" class="block text-gray-700 font-semibold mb-2">
                            Login <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="login"
                            name="login"
                            required
                            autocomplete="username"
                            value="<?= $_POST['login'] ?? ""; ?>"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Votre identifiant unique">
                    </div>

                    <!-- Champ Email -->
                    <div>
                        <label for="email" class="block text-gray-700 font-semibold mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            required
                            autocomplete="email"
                            value="<?= $_POST['email'] ?? ""; ?>"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="votre@email.com">
                    </div>

                    <!-- Champ Nom -->
                    <div>
                        <label for="nom" class="block text-gray-700 font-semibold mb-2">
                            Nom <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="nom"
                            name="nom"
                            required
                            autocomplete="family-name"
                            value="<?= $_POST['nom'] ?? ""; ?>"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Votre nom">
                    </div>

                    <!-- Champ Prénom -->
                    <div>
                        <label for="prenom" class="block text-gray-700 font-semibold mb-2">
                            Prénom <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="prenom"
                            name="prenom"
                            required
                            autocomplete="given-name"
                            value="<?= $_POST['prenom'] ?? ""; ?>"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Votre prénom">
                    </div>

                    <!-- Champ Adresse (pleine largeur) -->
                    <div class="md:col-span-2">
                        <label for="adresse" class="block text-gray-700 font-semibold mb-2">
                            Adresse <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="adresse"
                            name="adresse"
                            required
                            autocomplete="street-address"
                            value="<?= $_POST['adresse'] ?? ""; ?>"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Votre adresse complète">
                    </div>

                    <!-- Champ Téléphone -->
                    <div class="md:col-span-2">
                        <label for="telephone" class="block text-gray-700 font-semibold mb-2">
                            Téléphone
                        </label>
                        <input
                            type="tel"
                            id="telephone"
                            name="telephone"
                            autocomplete="tel"
                            value="<?= $_POST['telephone'] ?? ""; ?>"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="06 12 34 56 78">
                    </div>

                    <!-- Champ Mot de passe -->
                    <div>
                        <label for="mot_de_passe" class="block text-gray-700 font-semibold mb-2">
                            Mot de passe <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="password"
                            id="mot_de_passe"
                            name="mot_de_passe"
                            required
                            autocomplete="new-password"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Minimum 5 caractères">
                    </div>

                    <!-- Champ Confirmation mot de passe -->
                    <div>
                        <label for="mot_de_passe_confirm" class="block text-gray-700 font-semibold mb-2">
                            Confirmer le mot de passe <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="password"
                            id="mot_de_passe_confirm"
                            name="mot_de_passe_confirm"
                            required
                            autocomplete="new-password"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Retapez votre mot de passe">
                    </div>
                </div>

                <!-- Note sur les champs obligatoires -->
                <p class="text-sm text-gray-600 mt-4">
                    <span class="text-red-500">*</span> Champs obligatoires
                </p>

                <!-- Bouton de soumission -->
                <button
                    type="submit"
                    class="w-full mt-6 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition">
                    Créer mon compte
                </button>
            </form>

            <!-- Liens supplémentaires -->
            <div class="mt-6 space-y-3 text-center">
                <div>
                    <a href="/bibliotheque/login.php" class="text-blue-600 hover:text-blue-800 transition">
                        Déjà un compte ? Se connecter
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