<?php if(!$GLOBALS['domain']) exit; ?>

<header class="mw960p center exclude">

	<?h1('baseline')?>
	<?txt('texte')?>

</header>

<!-- liste des produits -->
<section id="<?=make_url(__('Items'))?>">

	<?var_dump($_SESSION)?>

	<?@include_once('theme/'.$GLOBALS['theme'].'/plugin/stripe/produits.php');?>

</section>

<!-- panier -->
<footer id="<?=make_url(__('Cart'))?>" class="mw960p center exclude">

	<?@include_once('theme/'.$GLOBALS['theme'].'/plugin/stripe/panier.php');?>

</footer>