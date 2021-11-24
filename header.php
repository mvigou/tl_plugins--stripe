<?php if(!$GLOBALS['domain']) exit;?>


<header>
	<section class="mw960p mod center tc relative">
		<div class="center ptm"><a href="<?=$GLOBALS['home']?>"><?php media('logo', '150')?></a></div>  <!--anciennement logo à 320-->
	</section>

	<section class="mw960p mod center tc relative">
		
		<nav class="mtm mbm">

			<a class="big burger"><span>menu</span></a>

			<ul class="grid up">
				<?php
				// Extraction du menu
				foreach($GLOBALS['nav'] as $cle => $val)
				{
					// Menu sélectionné si page en cours ou article (actu)
					if(get_url() == $val['href'] or (@$res['type'] == "article" and $val['href'] == "actualites"))
						$selected = " selected";
					else
						$selected = "";

					echo"<li><a href=\"".make_url($val['href'], 
					array("domaine" => true))."\"".($val['id']?" id='".$val['id']."'":"")."".($val['target']?" target='".$val['target']."'":"")." class='".$selected."'>".$val['text']."</a></li>";
				}
				?>
			</ul>

		</nav>
		
		<div id="nav-panier">
			<? 
				//on calcule le volume du panier 
				$id_elem = @array_column($_SESSION['panier'], 'qte');
				$quantite = @array_sum($id_elem);
			?>
			<a href="<?=$GLOBALS['home']?>panier">	
				<i id="mon-panier" title="mon panier" class="fa fa-basket" data-quantite="<?if(@$quantite) echo $quantite; else echo '0';?>"></i>
			</a>
		</div>
		
	</section>

</header>
 
