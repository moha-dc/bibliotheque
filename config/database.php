<?php
//desactiver l'affichage des erreurs
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Configuration de la base de données
define('DB_HOST', 'localhost');//hote de la base de données
define('DB_NAME', 'bibliotheque_crud');//nom de la base de données
define('DB_USER', 'root');//utilisateur de la base de données
define('DB_PASS', '');//mot de passe de la base de données
define('DB_CHARSET', 'utf8mb4');//jeu de caractères de la base de données

function getDbConnection(){
    //Data Source Name
    $dns = "mysql:host=" .DB_HOST.";dbname=" .DB_NAME. ";charset=" .DB_CHARSET;
    //Options de la connexion 
    $options=[
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, //activer les exceptions d'erreurs
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,//mode de récupération par défaut
        PDO::ATTR_EMULATE_PREPARES => false, //désactiver l'émulation des requêtes préparées
    ];
    try{
        //Créer une nouvelle connexion PDO
        $pdo = new PDO($dns, DB_USER, DB_PASS, $options);
        return $pdo;
    }catch (PDOException $e){
        die("Erreur de connexion à la base de données : " . $e->getMessage());
    }
}

