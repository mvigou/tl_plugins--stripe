<?php
if(!$GLOBALS['domain']) exit;
if(!@$GLOBALS['content']['titre']) $GLOBALS['content']['titre'] = $GLOBALS['content']['title'];
?>

<!--top part, pic et description-->
<section class="mw960p mod center mtm mbl">

	<?php h1('title', 'mbn tc')?> 

	<article>

		<!--pic produit-->
		<figure class="mtm mbm">
			<?php media('visuel', array('size' => '450', 'dir' => 'produits'))?>
			<?php txt('visuel-description', array('class' => 'inline-block', 'tag' => 'figcation'))?>
		</figure>

		<div class="h2-like"><?php span('prix'); ?> €</div>

		<?h2('ss-titre')?>

		<?php txt('texte')?>

		<div>
			<a href="javascript:void(0);" class="bt acheter" title="Ajouter au panier" data-id="<?=$id?>">Ajouter au panier</a>
		</div>

	</article>
	
</section>

<!-- fonctions panier -->
<script src="/theme/<?=$GLOBALS['theme']?>/panier<?=$GLOBALS['min']?>.js"></script>