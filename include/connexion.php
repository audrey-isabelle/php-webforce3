<?php
$pdo = new PDO(
	'mysql:host=localhost;dbname=boutique', // chaîne de connexion
	'root', // nom utilisateur
	'root', // mot de passe
	[ // tableau d'options
		// 1er option pour gérer les erreurs, géré par des warning
		PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, // Gestion des erreurs
		PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', // gestion utf8 mysql
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // résultats en tableau associatif uniquement (pour pas avoir des doublons clés numériques [0] et clés "string" ex : [id_employes], avec cette option on aura que les clés "string"). Cette option permet de faire le tableau associatif automatiquement à chaque fois quand a besoin d'un fetch.
	]
);