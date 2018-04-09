<?php
require_once __DIR__ . '/../include/initialisation.php';
adminSecurity(); // sécurity d'accès

$query = 'DELETE FROM categorie WHERE id = ' . $_GET['id'];
$pdo->exec($query);

setFlashMessage('La catégorie est supprimée');

header('Location: categories.php');
die;