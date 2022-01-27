<?php if(!$GLOBALS['domain']) exit;?>

<header class="clearfix mbm">

	<section class="relative">

		<nav aria-label="<?php _e("Quick access")?>"><a href="#main" class="acces-rapide">Aller au contenu</a></nav>

		<article class="inbl ptm pxm">
			<p class="big"><?span('description','h1')?></p>
			<aside class="small"><?span('en-savoir-plus')?></aside>
		</article>

		<nav class="mtm mbm mxs" aria-label="<?php _e("Browsing menu")?>">

			<ul>
				<?php
				// Extraction du menu
				foreach($GLOBALS['nav'] as $cle => $val)
				{
					// Menu sélectionné si page en cours ou article (actu)
					if(get_url() == $val['href'] or (@$res['type'] == "article" and $val['href'] == "actualites"))
						$selected = " selected";
					else
						$selected = "";

					echo"<li><a href=\"".make_url($val['href'], array("domaine" => true))."\"".($val['id']?" id='".$val['id']."'":"")."".($val['target']?" target='".$val['target']."'":"")." class='".$selected."'>".$val['text']."</a></li>";
				}
				?>
			</ul>

		</nav>

	</section>

	<section id="nav-panier" class="mxs">

		<? 
			//on calcule le volume du panier 
			$id_elem = @array_column($_SESSION['panier'], 'qte');
			$quantite = @array_sum($id_elem);
		?>
		<a href="<?=$GLOBALS['home']?>panier" class="inbl tc">	
			<i id="mon-panier" title="mon panier" class="fa fa-basket" data-quantite="<?if(@$quantite) echo $quantite; else echo '0';?>" aria-hidden="true"></i>
			<div>Mon panier</div>
		</a>

	</section>

</header>
 
