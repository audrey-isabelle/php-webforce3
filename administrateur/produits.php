<?php
// faire la page qui liste les produits dans un tableau HTML
// tous les champs sauf la description
// bonus :
// afficher le nom de la catégorie au lieu de son id

// On appelle tous les traitements avant affichage et création de fonctions
require_once __DIR__ . '/../include/initialisation.php';
adminSecurity(); // sécurity d'accès

// Le requêtage ici
// ma requête
// permet de renommer la colonne sans écraser l'autre du même nom
/*$query = 'SELECT c.nom AS categorie_nom, p.nom, p.reference, p.prix, p.id;
FROM categorie c, produit p
WHERE c.id=p.categorie_id';*/

// p et c sont les alias des tables produit et catégorie
//categorie_nom est l'alias du champ nom de la table catégorie
// p.* veut dire tous les champs de la table produit
$query = <<<EOS
SELECT p.*, c.nom AS categorie_nom
FROM produit p
JOIN categorie c ON p.categorie_id = c.id
EOS;
$stmt = $pdo->query($query);

$produits = $stmt->fetchAll();

include __DIR__ . '/../layout/top.php';
?>

<h1>Gestion produits</h1>

<p>
	<a class="btn btn-info" href="produits-edit.php">Ajouter un produit</a>
</p>

<!-- Le tableau HTML ici -->
<table class="table">
	<tr>
		<th>Id</th>
		<th>Nom</th>
		<th>Réference</th>
		<th>Prix</th>
		<th>Catégorie</th>

		<th width="250px"></th>
	</tr>
	<?php
	
		foreach ($produits as $produit) :
	?>
			<tr>
				<td><?= $produit['id']; ?></td>
				<td><?= $produit['nom']; ?></td>
				<td><?= $produit['reference']; ?></td>
				<td><?= prixFr($produit['prix']); ?></td>
				<td><?= $produit['categorie_nom']; ?></td> <!--grâce au "AS" je peux renommer la colonne -->
				<td>
					<a class="btn btn-info" href="produits-edit.php?id=<?= $produit['id']; ?>">
					Modifier
					</a>
					<a class="btn btn-danger" href="produit-delete.php?id=<?= $produit['id']; ?>">
					Supprimer
					</a>
				</td>
			</tr>
		<?php
		endforeach;
	?>
</table>

<?php
include __DIR__ . '/../layout/bottom.php';
?>