<?php
// On appelle tous les traitements avant affichage et création de fonctions
require_once __DIR__ . '/include/initialisation.php';

unset($_SESSION['utilisateur']); // coupe la session

header('Location: site.php'); // redirection sur la page d'accueil
die;