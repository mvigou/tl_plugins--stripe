<?php if(!$GLOBALS['domain']) exit; ?>

<section>

	<?h1('titre')?>

	<table class="liste-produits w100 tl">

		<thead>

            <tr class="liste-produits--entete sm:hidden md:tr">
                <th class="up normal pys pxs">Produit</th>
                <th class="up normal pys pxs">Description</th>
                <th class="up normal pys pxs">Prix unitaire</th>
                <th class="up normal pys pxs w250p" title="Boutons d'action" aria-label="Boutons d'action"></th>
            </tr>

			<tr class="liste-produits--entete sm:tr md:hidden">
                <th class="normal pys" colspan="3">Catalogue</th>
            </tr>

        </thead>

		<tbody>

			<?
				$_GET['mode']='liste-produits';
				include('theme/'.$GLOBALS['theme'].'/panier.ajax.php');
			?>

		</tbody>
	</table>

</section>

<!-- fonctions panier -->
<script src="/theme/<?=$GLOBALS['theme']?>/panier<?=$GLOBALS['min']?>.js"></script>