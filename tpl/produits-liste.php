<?php if(!$GLOBALS['domain']) exit; ?>

<section id="basket-list" class="mw960p center">

	<?
		$_GET['mode']='liste-produits';
		include('theme/'.$GLOBALS['theme'].'/panier.ajax.php');
	?>

</section>

<!-- fonctions panier -->
<script src="/theme/<?=$GLOBALS['theme']?>/panier<?=$GLOBALS['min']?>.js"></script>