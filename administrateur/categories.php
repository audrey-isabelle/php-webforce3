<?php
// On appelle tous les traitements avant affichage et création de fonctions
require_once __DIR__ . '/../include/initialisation.php';
adminSecurity(); // sécurity d'accès

// lister les catégories dans un tableau HTML

// Le requêtage ici
$query = 'SELECT * FROM categorie';
$stmt = $pdo->query($query);

$categories = $stmt->fetchAll();

include __DIR__ . '/../layout/top.php';
?>

<h1>Gestion catégories</h1>

<p>
	<a class="btn btn-info" href="categorie-edit.php">Ajouter une catégorie</a>
</p>

<!-- Le tableau HTML ici -->
<table class="table">
	<tr>
		<th>Id</th>
		<th>Nom</th>
		<th width="250px"></th>
	</tr>
	<?php
	/*
		// une boucle pour avoir un tr avec 2 td pour chaque catégorie
		foreach ($categories as $article) { // :
			echo '<tr>' . '<td>' . $article['id'] . '</td>' . '<td>' . $article['nom'] . '</td>' . '</tr>' ;
		} // endforeach;
	*/
		foreach ($categories as $article) :
		?>
			<tr>
				<td><?= $article['id']; ?></td>
				<td><?= $article['nom']; ?></td>
				<td>
					<a class="btn btn-info" href="categorie-edit.php?id=<?= $article['id']; ?>">
					Modifier
					</a>
					<a class="btn btn-danger" href="categorie-delete.php?id=<?= $article['id']; ?>">
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