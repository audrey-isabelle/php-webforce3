<?php

require_once __DIR__ . '/include/initialisation.php';

$email = '';

if (!empty($_POST)) {
	sanitizePost();
	extract($_POST);

	if (empty($_POST['email'])) {
		$errors[] = "L'email est obligatoire";
	}
	if (empty($_POST['mdp'])) {
		$errors[] = "Le mot de passe est obligatoire";
	}
	if (empty($errors)) { // pas de message d'erreurs (vide) donc on rentre dans cette condition
		$query = 'SELECT * FROM utilisateur WHERE email = :email';
		$stmt = $pdo->prepare($query);
		$stmt->bindValue(':email', $_POST['email']);
		$stmt->execute();

		$utilisateur = $stmt->fetch(); // on récupère le résultat avec un fetch qui nous retourne un tableau associatif

		// si on a un utilisateur en BDD avec l'email saisi
		if(!empty($utilisateur)) { // vérifier le mot de passe, les données avant la connexion
			//si le mdp saisi correspond au mdp encrypté en BDD
			if (password_verify($_POST['mdp'], $utilisateur['mdp'])) {
				//connecter un utilisateur, c'est l'enregistrer en session
				$_SESSION['utilisateur'] = $utilisateur;

				// après être connecter, redirection vers la page d'accueil
				header('Location: site.php');
				die;
			} // condition n°1, on vérifie si l'email de l'utilisateur est dans la BDD

		}// condtion n°2, vérifie que le mdp correspond bientôt bien à l'email de l'utilisateur (une correspondance entre les deux)

		// si on ne rentre pas dans les deux conditions, on déclenche ce message d'erreur
		$errors[] = 'Identifiant ou mot de passe incorrect';
	}
}




include __DIR__ . '/layout/top.php';

 if (!empty($errors)) :
?>

	<div class="alert alert-danger">
		<h4 class="alert-heading">Le formulaire contient des erreurs</h4>
		<?= implode('<br>', $errors); // implode transforme un tableau en chaîne de caractères ?= équivaut au "echo" ?>
	</div>
<?php
	endif;
?>

<h1>Connexion</h1>

<form method="post">
	<div class="form-group">
		<div class="form-group">
		<label>Email</label>
		<input type="text" name="email" value="<?= $email ?>" class="form-control">
	</div>
	<div class="form-group">
		<label>Mot de passe</label>
		<input type="password" name="mdp" class="form-control">
	</div>
	<div class="form-btn-group text-right">
		<button type="submit" class="btn btn-primary">Se connecter</button>
	</div>	
</form>


<?php
include __DIR__ . '/layout/bottom.php';
?>












