<?php
/* ATTENTION PAS BESOIN DE REQUETE SQL, récupérer les infos sur $_SESSION DU PANIER
- si le panier est vide : afficher un message
- sinon afficher un tableau HTML avec pour chaque produit du panier :
nom du produit, prix unitaire, la quantité, prix total pour le produit
- faire une fonction  getTotalPanier() qui calcule le montant total du panier et l'utiliser sous le tableau pour afficher le total

- remplacer l'affichage de la quantité par un formulaire avec un :
	1- <input type="number">
	2- un input hidden pour voir l'id du produit dont on modifie la quantité
	3 - un bouton submit
- faire une fonction modifierQuantitePanier() qui met à jour la quantité du produit si la quantité n'est pas 0, ou sinon qui supprime le produit du panier. Sinon appeler cette fonction quand un des formulaire est envoyé
*/
require_once __DIR__ . '/include/initialisation.php';

// récupérer infos de la commander pour le mettre en BDD et confirmer/valider la commander
if (isset($_POST['commander'])) {
	/*
	Enregistrer la commande et son détails en BDD 
	Afficher un message de confirmation vider le panier
	*/

	// Insertion dans la table commande
	$query = <<<EOS
	INSERT INTO commande (
	utilisateur_id,
	montant_total
) VALUES (
	:utilisateur_id,
	:montant_total
)
EOS;

	$stmt = $pdo->prepare($query);
	$stmt->bindValue(':utilisateur_id', $_SESSION['utilisateur']['id']);
	$stmt->bindValue(':montant_total', getTotalPanier());
	$stmt->execute();
	// récupération du numéro de la commande (id)
	$commandeId = $pdo->lastInsertId();

	// On insère les détails de la commande en BDD
	$query = <<<EOS
INSERT INTO detail_commande (
	commande_id,
	produit_id,
	prix,
	quantite
) VALUES (
	:commande_id,
	:produit_id,
	:prix,
	:quantite
)
EOS;

	$stmt = $pdo->prepare($query);
	$stmt->bindValue(':commande_id', $commandeId);

	foreach ($_SESSION['panier'] as $produitId => $produit) {
		$stmt->bindValue(':produit_id', $produitId);
		$stmt->bindValue(':prix', $produit['prix']);
		$stmt->bindValue(':quantite', $produit['quantite']);
		$stmt->execute();
	}
	setFlashMessage('La commande est enregistrée');
	// on vide le panier
	$_SESSION['panier'] = [];
}



// si le buton modifier qté a été cliqué :
if (isset($_POST['modifier-quantite'])) {
	modifierQuantitePanier($_POST['produit-id'], $_POST['quantite']);
	setFlashMessage('La quantité a été modifiée');
}

//$_SESSION['panier'] = [];

include __DIR__ . '/layout/top.php';
?>


<h1>Panier</h1>

<?php
if (empty($_SESSION['panier'])) :
?>
	<div class="alert alert-info">Le panier est vide</div>
<?php
else :
?>
	<table class="table">
		<tr>
		<th>Nom du produit</th>
		<th>Prix unitaire</th>
		<th>Quantité</th>
		<th>Total</th>
		</tr>
	<?php
	foreach ($_SESSION['panier'] as $produitId => $produit) :
	?>	
		<tr>
			<td><?= $produit['nom']; ?></td>
			<td><?= prixFr($produit['prix']); ?></td>
			<!--<td><//?= $produit['quantite']; ?></td>-->
			<td>
			<form method="post" class="form-inline">
				<div class="form-group">
					<input class="form-control col-sm-3" min="0" type="number" name="quantite" value="<?= $produit['quantite']; ?>">
					<input type="hidden" name="produit-id" value="<?= $produitId; ?>">
					<button type="submit" name="modifier-quantite" class="btn btn-primary">Modifier</button>
				</div>
			</form>
			</td>
			<td><?= prixFr($produit['prix'] * $produit['quantite']); ?></td>
		</tr>
	<?php
	endforeach;
	?>
	<tr>
		<th colspan="3">Total commande</th>
		<td><?= prixFr(getTotalPanier()); ?></td>
	</tr>
</table>
	<?php
	if (isUserConnected()) :
	?>
	<form method="post">
		<p class="text-right">
			<button type="submit" class="btn btn-primary" name="commander" >Valider la commande</button>
		</p>
	</form>

	<?php
		else :
	?>

	<div class="alert alert-info">Vous devez vous connecter ou vous inscrire pour valider la commande</div>

	<?php
		endif;
	?>

<?php
endif;
?>





<?php
include __DIR__ . '/layout/bottom.php';
?>