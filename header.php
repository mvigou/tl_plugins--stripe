<?php if(!$GLOBALS['domain']) exit;?>

<style>
	/*-- PANIER --*/
	/* icone du panier */
	#mon-panier {
		position: relative;
		width: 50px;
	}
	#mon-panier.fa-basket::before{
		font-size: 2em;
	}
	#mon-panier::after {
		content: attr(data-quantite);
		display: block;
		font-family: monospace;
		font-size: .9em;
		color: #fff;
		min-width: 10px;
		border-radius: 20px;
		padding: .3rem .4rem;
		background: #CC0000;
		position: absolute;
		top: 0;
		right: 0;
	}

	#mon-panier[data-quantite="0"]::after{
		display: none;
	}

	/* tableau de produit */
	#panier [data-devise]::after {
		content: ' 'attr(data-devise);
	}

	/* #panier th { background-color: #f4f1ef; }  // note : #panier .visuel imgne fonctionne pas avec #panier, mais directement avec table */
	#panier tr { transition: background-color .3s; }
	#panier tr[data-id]:hover { background-color: #f7f9f7; }
	#panier .outofstock { background-color: #e2c6af; }
</style>

<header>

	<section class="mw960p mod center tc relative">

		<div class="center ptm"><a href="<?=$GLOBALS['home']?>"><?php media('logo', '320')?></a></div>

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

					echo"<li><a href=\"".make_url($val['href'], array("domaine" => true))."\"".($val['id']?" id='".$val['id']."'":"")."".($val['target']?" target='".$val['target']."'":"")." class='".$selected."'>".$val['text']."</a></li>";
				}
				?>
			</ul>

		</nav>

		<div>

			<?php
				//on calcule le volume du panier 
				$id_elem = @array_column($_SESSION['panier'], 'qte');
				$quantite = @array_sum($id_elem);
			?>
					
			<a href="panier" class="biggest">	
				<i id="mon-panier" title="mon panier" class="fa fa-shopping-basket" data-quantite="<?php if(@$quantite) echo $quantite; else echo '0';?>"></i>
			</a>
		</div>

	</section>

</header>
