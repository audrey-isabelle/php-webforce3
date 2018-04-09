<?php
// afficher nom de la catégorie dont on a reçu l'id dans l'url en titre de la page 
// lister les produits appartenant à la catégorie avec leur photo s'ils en ont une 
require_once __DIR__ . '/include/initialisation.php';
adminSecurity();

$query = 'SELECT nom FROM categorie WHERE id = ' . $_GET['id'];
$stmt = $pdo->query($query);
$title = $stmt->fetch(); // pas besoin de fetchAll car c'est des tableaux dans des tableaux alors qu'on a juste de pointer directement sur le titre, ce que fait fetch
// code du formateur : $titre = $stmt->fetch();

$nomCategorie = $title['nom'];

$query = 'SELECT * FROM produit WHERE categorie_id = ' . $_GET['id'];
$stmt = $pdo->query($query);
$produits = $stmt->fetchAll(); // on utilise fetchAll quand on a besoin de plusieurs infos dans la BDD

//Marche avec ma version : $infoPhoto = $produits['photo']; // pas obliger le formateur ne l'a pas fait mais c'est bon ce que j'ai fait

//$infoPhoto = $produits['photo']; pas besoin quand on fait un fetchAll
//$infoNom = $produits['nom']; // plus pour un fetch pour afficher un renseignement ou une info en particulier comme un titre par ex
//$infoDescription = $produits['description'];
//$infoPhoto = $produits['prix'];


include __DIR__ . '/layout/top.php';
?>

<h1><?= $title['nom']; ?></h1> <!-- <h1><?= $titre; ?></h1>  pas obliger de mettre le nom entre crochet mais c'est bon ce que j'ai fait-->

<?php
	
		foreach ($produits as $produit) :
		// produit de photo n'est pas vide
		$src = (!empty($produit['photo']))
			? PHOTO_WEB . $produit['photo']
			: PHOTO_DEFAULT
		;
?>

<!-- ma version du code qui marche :
<div class="card float-left col-md-4 offset-md-1" style="width: 18rem;">
<//?php
  echo '<img class="card-img-top" src="' . PHOTO_WEB . $produit['photo'] . '" alt="Image du produit">';
?>

  <div class="card-body">
    <h5 class="card-title text-center"><//?= $produit['nom']; ?></h5>
    <p class="card-text text-center"><//?= $produit['description']; ?></p>
    <p class="card-text text-center"><//?= prixFr($produit['prix']); ?></p>
    mon code:<p class="card-text"><//?= $produit['prix'] . " €"; ?></p>
	mon code:<a href="#" class="btn btn-primary">Panier</a>
	<p class="card-text text-center">
		<a class="btn btn-primary" href="produit.php?id=<//?= $produit['id']; ?>">Voir</a>
	</p>
  </div>
</div>
-->

<div class="col-sm-3">
	<div class="card">
		<img class="card-img-top" src="<?= $src; ?>">
		<div class="card-body">
			<h5 class="card-title text-center"><?= $produit['nom']; ?></h5>
			<p class="card-text text-center">
				<?= prixFr($produit['prix']); ?>
			</p>
			<p class="card-text text-center">
				<a class="btn btn-primary" href="produit.php?id=<?= $produit['id']; ?>">Voir</a>
			</p>
		</div>
	</div>
</div>


<?php
	endforeach;
?>



<?php
include __DIR__ . '/layout/bottom.php';
?>
