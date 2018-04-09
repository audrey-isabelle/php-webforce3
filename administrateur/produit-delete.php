<?php
require_once __DIR__ . '/../include/initialisation.php';
adminSecurity(); // sécurity d'accès

// requete pour récuperer la photo pour arriver après à la supprimer
$query = 'SELECT photo FROM produit WHERE id = ' . $_GET['id'];
$stmt = $pdo->query($query);
$photo = $stmt->fetchColumn();

// on supprime l'image du produit dans le répertoire photo s'il en a une
if (!empty($photo)) {
	unlink(PHOTO_DIR . $photoActuelle);
}


$query = 'DELETE FROM produit WHERE id = ' . $_GET['id'];
$pdo->exec($query);

setFlashMessage('Le produit est supprimé');

header('Location: produits.php');
die;