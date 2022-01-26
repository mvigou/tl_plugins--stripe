<?php
if(!$GLOBALS['domain']) exit;
if(!@$GLOBALS['content']['titre']) $GLOBALS['content']['titre'] = $GLOBALS['content']['title'];
?>

<!--top part, pic et description-->
<section class="mw960p mod center mtm mbl">

	<?
		$_GET['mode']='fiche-produit';
		include('theme/'.$GLOBALS['theme'].'/panier.ajax.php');
	?>
	
</section>

<!-- fonctions panier -->
<script src="/theme/<?=$GLOBALS['theme']?>/panier<?=$GLOBALS['min']?>.js"></script>