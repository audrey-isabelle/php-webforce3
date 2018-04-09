<?php
function setFlashMessage($message, $type = 'success') // création de flashMessage
{
	$_SESSION['flashMessage'] = [
		'message' => $message,
		'type' => $type
	];
}


function displayFlashMessage() // si il existe la fonction enclenche le flashMessage
{
	if (isset($_SESSION['flashMessage'])) { // isset vaut s'il existe flashMessage
		$message = $_SESSION['flashMessage']['message'];
		$type = ($_SESSION['flashMessage']['type'] == 'error')
		? 'danger' // pour la classe alert-danger du bootstrap "?" vaut "if" $type est remplacé par la valeur "danger"
		: $_SESSION['flashMessage']['type'] // ":" vaut dire "else"
		;

		echo '<div class="alert alert-' . $type . '">'
		. '<h5 class="alert-heading">' . $message . '</h5>'
		. '</div>'
		;

		// suppression du message de la session pour affichage "one shot"
		unset($_SESSION['flashMessage']);
	}
}

// suppression des balises HTML, des caractères, espaces
function sanitizeValue(&$value) // passe par référence (passe par la variable) : la variable va être modifié sa valeur va changer
{
	// trim() supprime les espaces en début et fin de chaîne
	// strip_tags() supprime les balises HTML
	$value = trim(strip_tags($value));
}

function sanitizeArray(array &$array) // typé la variable avec "array" en parenthèse, obligation que se soit un tableau
{
		// applique la fonction sanitizeValue() sur tous les éléments du tableau
		array_walk($array, 'sanitizeValue');
}

function sanitizePost() // nettoyer le tableau
{
	sanitizeArray($_POST);
}

// création d'une fonction pour vérifier que l'utilisateur est bien connecté sur le site (détection de la connexion)
function isUserConnected()
{
	return isset($_SESSION['utilisateur']);
}

// récupérer le nom et prénom de l'utilisateur pour l'afficher dans la session de l'utilisateur
function getUserFullName()
{
	if(isUserConnected()) {
	return $_SESSION['utilisateur']['prenom'] . ' ' . $_SESSION['utilisateur']['nom'];
	}
}

// permet d'accèder à la partie admin et fait apparaitre la barre de nav admin
function isUserAdmin()
{
	return isUserConnected() && $_SESSION['utilisateur']['role'] == 'admin';
}

function adminSecurity()
{
	if (!isUserAdmin()) { // si on est pas connecté du tout sur le site
		if (!isUserConnected()) {	// redirection sur la page connexion
			header('Location: ' . RACINE_WEB . 'connexion.php');
		}else{ // si il tente de se connecter à cette page par l'url le message ce déclenche
			header('HTTP/1.1 403 Forbidden');
			echo "Vous n'avez pas le droit d'accéder à cette page";
		}
		die;
	}
}

function prixFr($prix)
{
	return number_format($prix, 2, ',', ' ') . '€'; // on concatène pas la fonction mais la méthode
}

// formatage en fr de la date
function dateFr($dateSql)
{
	return date('d/m/Y H:i:s', strtotime($dateSql));
}

// pour le panier
// le array dans les parenthèse c'est pour le typer en tableau
function ajoutPanier(array $produit, $quantite)
{
	// initialisation du panier
	if (!isset($_SESSION['panier'])) { // si n'existe pas -> panier vide
		$_SESSION['panier'] = []; // par défaut à l'ouverture de la session utilisateur, le panier n'existe pas on le créé
	}
	// si le produit n'est pas encore dans le panier on l'y ajoute
	if(!isset($_SESSION['panier'][$produit['id']])) {
		$_SESSION['panier'][$produit['id']] = [
			'nom' => $produit['nom'],
			'prix' => $produit['prix'],
			'quantite' => $quantite
		];
	} else {
		// si le produit est déjà dans le panier, on met à jour la quantité
		$_SESSION['panier'][$produit['id']]['quantite'] += $quantite;
	}
}

function getTotalPanier()
{
	$total = 0; // si panier est vide cela nous retourne 0

	if (isset($_SESSION['panier'])) {
		foreach ($_SESSION['panier'] as $produit) {
			$total += $produit['prix'] * $produit['quantite'];
		}
	}
	return $total;
}

function modifierQuantitePanier($produitId, $quantite)
{
	if (isset($_SESSION['panier'][$produitId])) {
		if ($quantite != 0) {
			$_SESSION['panier'][$produitId]['quantite'] = $quantite;	
		}else {
		unset($_SESSION['panier'][$produitId]);
		}
	} 
}