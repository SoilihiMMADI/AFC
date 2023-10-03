 Programme d'actualisation des lignes des tables,  
 cette mise à jour peut prendre plusieurs minutes...
<?php
include("include/fct.inc.SQLServer.php");

/* Modification des paramètres de connexion */

$serveur='sqlsrv:Server=SVRSLAM02';
$bdd='Database=GSB_VALIDE_V2';   		
$user='xx' ;  // Normalement sécurité intégrée ...
$mdp='xx' ;   // Idem pour le mdp ...

// Ajouté en 2018 pour corriger une erreur de connexion :
// "Uncaught PDOException: Invalid handle returned." 
// ... qui apparait avec certaine version du pilote SQL Server pour PHP
// (https://stackoverflow.com/questions/40134878/invalid-handler-returned-php-pdo-sql-server).

$connectionPooling = 'ConnectionPooling=0';

/* fin paramètres*/

$pdo = new PDO($serveur.';'.$bdd . ';' . $connectionPooling, $user, $mdp);
$pdo->query("SET CHARACTER SET utf8"); 

set_time_limit(0);
creationFichesFrais($pdo);
creationFraisForfait($pdo);
creationFraisHorsForfait($pdo);
majFicheFrais($pdo);

?>