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

if (!isset($_GET['id']) || empty($_GET['id'])) {
      //redirection avec un message d'erreur
      header('location:livres.php?livre=inexistant');
      exit;
}

$id_livre = (int) $_GET['id'];
//connexion à la base de données
$pdo = getDbConnection();

//vérifier si le livre existe : 
$sql = "SELECT id_livre,titre FROM livre WHERE id_livre = :id_livre LIMIT 1";

//préparer et exécuter la requête
$check_book = $pdo->prepare($sql);
$check_book->bindParam(':id_livre', $id_livre, PDO::PARAM_INT);
$check_book->execute();

//récupère le livre
$livre = $check_book->fetch();

//si le livre n'existe pas, on redigire avcec un message d'erreur : 
if (!$livre) {
      header('location:bibliotheque/admin/livre.php?livre=inexistant');
      exit;
}


//requête de suppression : 
$sql_delete = "DELETE FROM livre WHERE id_livre = :id_livre";
//méthode sur la préparation : 
$book_delete = $pdo->prepare($sql_delete);
//lier le paramètre : 
$book_delete->bindParam(':id_livre', $id_livre, PDO::PARAM_INT);
//exécuter la requête :
$book_delete->execute();

header('location:livres.php?livre=supprime');
exit;