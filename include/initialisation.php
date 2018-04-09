<?php

//initialise la session
session_start();

// les CONSTANTES
define('RACINE_WEB', '/PHP2/SITE/');
define('PHOTO_WEB', RACINE_WEB . 'photo/'); // le chemin dans l'url par rapport à localhost pour chercher le dossier image
// sous xampp, $_SERVER['DOCUMENT_ROOT'] vaut  C:\xampp\htdocs
// pour chercher le dossier photo dans xampp
define('PHOTO_DIR', $_SERVER['DOCUMENT_ROOT'] . '/PHP2/SITE/photo/');
define('PHOTO_DEFAULT', 'https://dummyimage.com/600x400/000ecc/ffffff&text=Pas+d\'image'); // photo par défaut quand il n'y a pas de photo

require_once __DIR__ . '/connexion.php';

require_once __DIR__ . '/fonctions.php';










