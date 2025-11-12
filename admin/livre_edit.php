<?php
//début de la session
session_start();
//seul l'admin peut accéder à cette page 
if (!isset($_SESSION['admin_id'])) {
      //redirection vers la page de connexion
      header('Location: ../login.php');
      exit();
}
require_once __DIR__ . '/../config/database.php';

//vérifier que l'ID du livre est présent
if (!isset($_GET['id']) || empty($_GET['id'])) {
      //redirection avec un message d'erreur
      header('location:livres.php?livre=inexistant');
      exit;
}

$id_livre = (int) $_GET['id'];

//connexion à la base de données
$pdo = getDbConnection();

//vérifier si le livre existe et récupérer ses informations
$sql = "SELECT id_livre, titre, auteur, couverture FROM livre WHERE id_livre = :id_livre LIMIT 1";
$check_book = $pdo->prepare($sql);
$check_book->bindParam(':id_livre', $id_livre, PDO::PARAM_INT);
$check_book->execute();
$livre = $check_book->fetch();

//si le livre n'existe pas, on redirige avec un message d'erreur
if (!$livre) {
      header('location:livres.php?livre=inexistant');
      exit;
}

//initialisation des variables avec les valeurs actuelles du livre
$erreurs = [];
$titre = $livre['titre'];
$auteur = $livre['auteur'];
$couverture = $livre['couverture'] ?? '';

//traitement du formulaire si soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      //récupération et nettoyage des données
      $titre = trim($_POST['titre'] ?? '');
      $auteur = trim($_POST['auteur'] ?? '');
      $couverture = trim($_POST['couverture'] ?? '');
      
      //validation des données
      if (empty($titre)) {
            $erreurs[] = "Le titre est obligatoire";
      } elseif (strlen($titre) > 30) {
            $erreurs[] = "Le titre ne doit pas dépasser 30 caractères";
      }
      
      if (empty($auteur)) {
            $erreurs[] = "L'auteur est obligatoire";
      } elseif (strlen($auteur) > 25) {
            $erreurs[] = "L'auteur ne doit pas dépasser 25 caractères";
      }
      
      if (!empty($couverture) && strlen($couverture) > 100) {
            $erreurs[] = "L'URL de la couverture ne doit pas dépasser 100 caractères";
      }
      
      //si pas d'erreurs, on met à jour le livre
      if (empty($erreurs)) {
            //requête de mise à jour
            $sql_update = "UPDATE livre 
                          SET titre = :titre, auteur = :auteur, couverture = :couverture 
                          WHERE id_livre = :id_livre";
            
            //préparer la requête
            $book_update = $pdo->prepare($sql_update);
            
            //lier les paramètres
            $book_update->bindParam(':titre', $titre, PDO::PARAM_STR);
            $book_update->bindParam(':auteur', $auteur, PDO::PARAM_STR);
            $book_update->bindParam(':couverture', $couverture, PDO::PARAM_STR);
            $book_update->bindParam(':id_livre', $id_livre, PDO::PARAM_INT);
            
            //exécuter la requête
            $book_update->execute();
            
            //redirection avec message de succès
            header('location:livres.php?livre=modifie');
            exit;
      }
}

include __DIR__ . '/../includes/header.php'; 
include __DIR__ . '/../includes/nav.php'; 
?>

    <!-- Contenu principal de la page -->
    <main class="container mx-auto px-4 py-8 flex-grow" role="main">
        <div class="max-w-2xl mx-auto">
            <!-- En-tête de la page -->
            <header class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    Modifier un livre
                </h1>
                <p class="text-gray-600">
                    Livre ID : <?php echo htmlspecialchars($id_livre); ?>
                </p>
            </header>

            <!-- Formulaire de modification -->
            <section class="bg-white rounded-lg shadow-md p-8">
                <!-- Affichage des erreurs -->
                <?php if (!empty($erreurs)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p class="font-bold">Erreur(s) :</p>
                    <ul class="list-disc list-inside mt-2">
                        <?php foreach ($erreurs as $erreur): ?>
                            <li><?php echo htmlspecialchars($erreur); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <!-- Formulaire -->
                <form method="POST" action="" novalidate>
                    <!-- Champ Titre -->
                    <div class="mb-6">
                        <label for="titre" class="block text-gray-700 font-semibold mb-2">
                            Titre du livre <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="titre"
                            name="titre"
                            required
                            maxlength="30"
                            value="<?php echo htmlspecialchars($titre); ?>"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Ex: Les Misérables">
                        <p class="text-gray-500 text-sm mt-1">Maximum 30 caractères</p>
                    </div>

                    <!-- Champ Auteur -->
                    <div class="mb-6">
                        <label for="auteur" class="block text-gray-700 font-semibold mb-2">
                            Auteur <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="auteur"
                            name="auteur"
                            required
                            maxlength="25"
                            value="<?php echo htmlspecialchars($auteur); ?>"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Ex: VICTOR HUGO">
                        <p class="text-gray-500 text-sm mt-1">Maximum 25 caractères</p>
                    </div>

                    <!-- Champ Couverture -->
                    <div class="mb-6">
                        <label for="couverture" class="block text-gray-700 font-semibold mb-2">
                            URL de la couverture
                        </label>
                        <input
                            type="text"
                            id="couverture"
                            name="couverture"
                            maxlength="100"
                            value="<?php echo htmlspecialchars($couverture); ?>"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Ex: images/couvertures/les_miserables.jpg">
                        <p class="text-gray-500 text-sm mt-1">Optionnel - Chemin relatif vers l'image de couverture</p>
                    </div>

                    <!-- Légende des champs obligatoires -->
                    <p class="text-gray-600 text-sm mb-6">
                        <span class="text-red-500">*</span> Champs obligatoires
                    </p>

                    <!-- Boutons d'action -->
                    <div class="flex justify-between items-center">
                        <!-- Bouton Annuler -->
                        <a href="livres.php" class="text-gray-600 hover:text-gray-800 transition font-medium">
                            ← Annuler
                        </a>

                        <!-- Bouton Enregistrer -->
                        <button
                            type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition shadow">
                            ✓ Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </main>

<?php 
include __DIR__ . '/../includes/footer.php'; 
?>