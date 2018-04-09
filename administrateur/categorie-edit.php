<?php

require_once __DIR__ . '/../include/initialisation.php';
adminSecurity(); // sécurity d'accès

$errors = [];
$nom = '';

if (!empty($_POST)) { // si on a des données venant du formulaire
	// "nettoyage" des données venues du formulaire
	sanitizePost();
	// crée des variables à partir d'un tableau (les variables ont les les noms des clés dans le tableau)
	extract($_POST);

	if (empty($_POST['nom'])) { // si le champs du formulaire est vide, il y a un message d'erreur sui s'enclenche
		$errors[] = 'le nom est obligatoire';
	}elseif (strlen($_POST['nom']) > 50) { // si il fait plus de 50 caractères il affiche le message d'erreur
		$errors[] = 'le nom ne doit pas faire plus de 50 caractères';
	}
	// si le formulaire est correctement rempli
	if (empty($errors)) { // si le tableau d'erreurs est vide ($errors = [];) après le passage des tests des champs on peut les stocker/ les enregistre en BDD

		// si le formulaire est correctement rempli
		if (isset($_GET['id'])) { // modification (bouton modifier)
			$query = 'UPDATE categorie SET nom = :nom WHERE id = :id';
			$stmt = $pdo->prepare($query);
			$stmt->bindValue(':nom', $_POST['nom']); // récupère les données en poste pour l'input
			$stmt->bindValue(':id', $_GET['id']); // récupère l'id de la catégorie de vêtement
			$stmt->execute();
		} else { // création 
			//insertion en BDD
			$query = 'INSERT INTO categorie(nom) VALUES(:nom)';
			$stmt = $pdo->prepare($query);
			$stmt->bindValue(':nom', $_POST['nom']);
			$stmt->execute(); // quand on fait une requête préparée on utilise toujours execute() au lieu de exec()
		}
		
		// cette fonction se lance
		// enregistrement d'un message en session
		setFlashMessage('La catégorie est enregistrée');

		// rediger l'utilisateur vers la page categories.php
		// redirection vers la page de liste
		header('Location: categories.php');
		die; //  die arrête l'exécution du script
	}
}elseif (isset($_GET['id'])) { // pour pré-remplir les inputs pour la modif,récupération des infos dans l'url et prendre les données dans la BDD pour les champs du formulaire
	// en modification, si on n'a pas de retour de formulaire
	// on va chercher la catégorie en BDD pour l'affichage
	$query = 'SELECT * FROM categorie WHERE id = ' . $_GET['id'];
	//echo $query;
	$stmt = $pdo->query($query);
	$categorie = $stmt->fetch();

	$nom = $categorie['nom']; // on peut faire aussi un extract
}

include __DIR__ . '/../layout/top.php';
?>

<h1>Edition catégorie</h1>

<?php
 if (!empty($errors)) :
 ?>	
<!-- en dehors de php prise en sandwish -->
	<div class="alert alert-danger">
		<h4 class="alert-heading">Le formulaire contient des erreurs</h4>
		<?= implode('<br>', $errors); // implode transforme un tableau en chaîne de caractères ?= équivaut au "echo" ?>
	</div>
<?php
	endif;
?>

<form method="post">
	<div class="form-group">
		<label>Nom</label>
		<input type="text" name="nom" class="form-control" value="<?= $nom; ?>"> <!-- $nom aura le nom que l'utilisateur aura mis sur renseigner sur l'input "julien" $nom = 'julien' et l'affiche sur l'input le dernier nom saisi -->

	</div>
	<div class="form-btn-group text-right">
		<button type="submit" class="btn btn-primary">Enregistrer</button>
		<a class="btn btn-secondary" href="categories.php">Retour</a>
	</div>
</form>

<?php
include __DIR__ . '/../layout/bottom.php';
?>