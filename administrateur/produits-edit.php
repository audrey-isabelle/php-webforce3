<?php
/*faire un formulaire d'édition de produit
- nom : input text - obligatoire
- description : textarea , obligatoire, 50 caractères max, unique
- reference : input text - obligatoire
- prix : input text -,obligatoire
- categorie : select - obligatoire
Si le formulaire est bien rempli : INSERT en BDD et redirection vers la liste avec message de confirmation,
sinon messages d'erreurs et champs pré-remplis avec les valeurs saisies
Adapter la page pr la modif :
	avoir 1 bouton ds a page de liste qui pointe vers cette page en passant l'id du produit dans l'url
	si on a un produit dans l'url sans retour de post, faire une requête select pour
    pré-remplir le formulaire
	- apdater le traitement pour faire un update au lieu d'un insert si on a un id dans l'url
	- adapter la vérification de l'unicité de la référence pour exclure la référence du produit que l'on modifie de la requête
*/


require_once __DIR__ . '/../include/initialisation.php';
adminSecurity(); // sécurity d'accès

$errors = [];

$nom = $description = $reference = $prix = $categorieId = $photoActuelle = '';

if (!empty($_POST)) {
	sanitizePost();
	extract($_POST);
	$categorieId = $_POST['categorie']; // entre crochet c'est le name dans l'input

	if (empty($_POST['nom'])) {
		$errors[] = "Le nom est obligatoire";
	}
	if (empty($_POST['description'])) {
		$errors[] = "La description est obligatoire";
	}
	if (empty($_POST['reference'])) {
		$errors[] = "La référence est obligatoire";
	} elseif (strlen($_POST['reference']) > 50) { // si il fait plus de 50 caractères il affiche le message d'erreur
		$errors[] = 'La référence ne doit pas faire plus de 50 caractères';
	} else { // on compte pour voir si la réf existe ou pas en BDD si il est = à 0 c'est non sinon !=0
		$query = 'SELECT count(*) FROM produit WHERE reference = :reference';

		if (isset($_GET['id'])) {
			// modification, on exclut de la vérification le produit que l'on est en train de modifier
			$query .= ' AND id != ' . $_GET['id'];
		}
		$stmt = $pdo->prepare($query);
		$stmt->bindValue(':reference', $_POST['reference']);
		$stmt->execute();
		$nb = $stmt->fetchColumn();

		if ($nb !=0) { // si l'email existe déjà le message d'erreurs s'enclenche
			$errors[] = "Il existe déjà un produit avec cette référence";
		}
	}
	if (empty($_POST['prix'])) {
		$errors[] = "Le prix est obligatoire";
	}
	if (empty($_POST['categorie'])) {
		$errors[] = "La catégorie est obligatoire";
	}
	// si une image a été téléchargée
	if (!empty($_FILES['photo']['tmp_name'])) { // si l'image différent de vide
		// $_FILE['photo']['size'] = le poids du fichier en octets
		if ($_FILES['photo']['size'] > 1000000) {
			$errors[] = 'La photo ne doit pas faire plus de 1Mo';
		}
		$allowedMineTypes = [
			'image/png',
			'image/jpeg',
			'image/gif'
		];
		// si les types de photo ne correspond pas au tableau c-a-d où les extensions des fichiers sont précisés on envoit un message d'erreur
		if (!in_array($_FILES['photo']['type'], $allowedMineTypes)) {
			$errors[] = 'La photo ne doit être une image GIF, JPG ou PNG';
		}
	}
	if (empty($errors)) {

		if (!empty($_FILES['photo']['tmp_name'])) { // va le chercher dans le fichier tmp extension dans ce dossier en .tmp
			$originalName = $_FILES['photo']['name'];
			// on retrouve l'extension du fichier original à partir de son nom (ex: .png pour mon_fichier.png)
			$extension = substr($originalName, strripos($originalName, '.'));
			// le nom que va avoir le fichier dans le répertoire photo
			// nom de la photo est la réf du produit (pas avec le nom d'origine)
			$nomPhoto = $_POST['reference'] . $extension;
			// En modification, si le produit avait déjà une photo (ancienne photo) on la supprime de la BDD
			// lorsqu'on modifie un produit et qu'on veut changer la photo, l'ancienne photo sera supprimé automatiquement
			if (!empty($photoActuelle)) {
				unlink(PHOTO_DIR . $photoActuelle);
			}
			// enregistrement du fichier dans le répertoire photo
			move_uploaded_file($_FILES['photo']['tmp_name'], PHOTO_DIR . $nomPhoto);
		} else {
			//$nomPhoto = '';
			$nomPhoto = $photoActuelle;
		}
		// avant l'insertion on s'occupe de la modification et cela permet de récuperer au BDD sans faire de reflesh avec la flèche
		if (isset($_GET['id'])) { // Pour l'update
			//requête :
			$query = <<<EOS
UPDATE produit SET
	nom = :nom,
	description = :description,
	reference = :reference,
	prix = :prix,
	categorie_id = :categorie_id,
	photo = :photo
WHERE id = :id
EOS;

			$stmt = $pdo->prepare($query);
			$stmt->bindValue(':nom', $_POST['nom']);
			$stmt->bindValue(':description', $_POST['description']);
			$stmt->bindValue(':reference', $_POST['reference']);
			$stmt->bindValue(':prix', $_POST['prix']);
			$stmt->bindValue(':categorie_id', $_POST['categorie']);
			$stmt->bindValue(':photo', $nomPhoto);
			$stmt->bindValue(':id', $_GET['id']);
			$stmt->execute();
		} else {
		$query = <<<EOS
INSERT INTO produit(
	nom,
	description,
	reference,
	prix,
	categorie_id,
	photo
) VALUES (
	:nom,
	:description,
	:reference,
	:prix,
	:categorie_id,
	:photo
)
EOS;
			$stmt = $pdo->prepare($query);
			$stmt->bindValue(':nom', $_POST['nom']); // le name du post est le name du form
			$stmt->bindValue(':description', $_POST['description']);
			$stmt->bindValue(':reference', $_POST['reference']);
			$stmt->bindValue(':prix', $_POST['prix']);
			$stmt->bindValue(':categorie_id', $_POST['categorie']);
			$stmt->bindValue(':photo', $nomPhoto);
			$stmt->execute();
		}
		setFlashMessage('Le produit est enregistré');
		header('Location: produits.php');
		die;
	}
}elseif (isset($_GET['id'])) { // pour pré-remplir les inputs pour la modif,récupération des infos dans l'url et prendre les données dans la BDD pour les champs du formulaire
	// pré-remplissage du formulaire à partir de la BDD (bouton modifier)
	// en modification, si on n'a pas de retour de formulaire
	// on va chercher la catégorie en BDD pour l'affichage
	// "SELECT * FROM produit WHERE id = " . $_GET['id'];
	$query = 'SELECT * FROM produit WHERE id = ' . $_GET['id'];
	//echo $query;
	$stmt = $pdo->query($query);
	$produit = $stmt->fetch();
	// extract($produit) ou et rajouter juste $categorieId = $produit['categorie_id']; ou :
	$nom = $produit['nom']; // on peut faire aussi un extract
	$description = $produit['description'];
	$reference = $produit['reference'];
	$prix = $produit['prix'];
	$categorieId = $produit['categorie_id'];
	$photoActuelle = $produit['photo'];
}


// pour construire la liste déroulante des catégories :
$query = 'SELECT * FROM categorie';
$stmt = $pdo->query($query);
$categories = $stmt->fetchAll();

include __DIR__ . '/../layout/top.php';

if (!empty($errors)) :
?>

<h1>Edition produits</h1>


<!-- en dehors de php prise en sandwish -->
	<div class="alert alert-danger">
		<h4 class="alert-heading">Le formulaire contient des erreurs</h4>
		<?= implode('<br>', $errors); // implode transforme un tableau en chaîne de caractères ?= équivaut au "echo" ?>
	</div>
<?php
	endif;
?>

<!--
	L'attribut enctype est obligatoire pour un formulaire qui contient un téléchargement de fichier
-->
<form method="post" enctype="multipart/form-data">
	<div class="form-group">
		<label>Nom</label>
		<input type="text" name="nom" class="form-control" value="<?= $nom; ?>">
	</div>
	<div class="form-group">
		<label>Description</label>
		<textarea name="description" class="form-control"><?= $description ?></textarea>
	</div>
	<div class="form-group">
		<label>Référence</label>
		<input type="text" name="reference" class="form-control" value="<?= $reference; ?>">
	</div>
	<div class="form-group">
		<label>Prix</label>
		<input type="text" name="prix" class="form-control" value="<?= $prix; ?>">
	</div>
	<div class="form-group">
		<label>Catégorie</label>
		<select name="categorie" class="form-control">
			<option value=""></option>
			<?php
				foreach ($categories as $categorie) :
					$selected = ($categorie['id'] == $categorieId)
						? 'selected'
						: ''
					;
			?>
				<option value="<?= $categorie['id']; ?>"<?= $selected; ?>><?= $categorie['nom']; ?></option>

			<?php
			endforeach;
			?>
		</select>
	</div>
	<div class="form-group">
		<label>Photo</label>
		<input type="file" name="photo">
	</div>
	<?php
		if (!empty($photoActuelle)) :
			echo '<p>Actuellement :<br><img src="' . PHOTO_WEB . $photoActuelle . '" height="150px"</p>';
		endif;
	?>
	<!-- champs cachés pour la photo avec sa valeur actuelle 
		avec cette input invisible, il permet de garder le nom de la photo même en modifiant la photo pour une autre
	-->
	<input type="hidden" name="photoActuelle" value="<?= $photoActuelle; ?>">
	<div class="form-btn-group text-right">
		<button type="submit" class="btn btn-primary">Enregistrer</button>
		<a class="btn btn-secondary" href="produits.php">Retour</a>
	</div>
</form>

<?php
include __DIR__ . '/../layout/bottom.php';
?>