<section>

	<?php
	// Si on n'a pas les droits d'édition des articles on affiche uniquement ceux actifs
	if(!@$_SESSION['auth']['edit-product']) $sql_state = "AND state='active'"; else $sql_state = "";
	// Navigation par page
	$num_pp = 9;

	if(isset($GLOBALS['filter']['page'])) $page = (int)$GLOBALS['filter']['page']; else $page = 1;

	$start = ($page * $num_pp) - $num_pp;

	// Construction de la requete
	$sql ="SELECT SQL_CALC_FOUND_ROWS ".$tc.".id, ".$tc.".* FROM ".$tc;

	// Si filtre tag
	if(isset($tag))
	$sql.=" RIGHT JOIN ".$tt."
	ON
	(
		".$tt.".id = ".$tc.".id AND
		".$tt.".zone = 'categorie' AND
		".$tt.".encode = '".$tag."'
	)";

	$sql.=" WHERE (".$tc.".type='product') AND ".$tc.".lang='".$lang."' ".$sql_state."
	ORDER BY ".$tc.".date_insert DESC
	LIMIT ".$start.", ".$num_pp;

	$sel_fiche = $connect->query($sql);

	$num_total = $connect->query("SELECT FOUND_ROWS()")->fetch_row()[0];// Nombre total de fiches

	?>

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

		<?php
		while($res_fiche = $sel_fiche->fetch_assoc())
		{

			// Affichage du message pour dire si l'article est invisible ou pas
			if($res_fiche['state'] != "active") $state = " <span class='deactivate pat'>".__("Article d&eacute;sactiv&eacute;")."</span>";
			else $state = "";

			$content_fiche = json_decode($res_fiche['content'], true);

			// Création de la miniature du visuel
			$new_width = 300;// Largeur max
			//$new_height = 200; // hauteur max
			/*if(!isset($content_fiche['visuel'])) $visuel = './theme/'.$GLOBALS['theme'].'/media/visuel-absent-300x200.jpg';
			else
			{
				$visuel = $content_fiche['visuel'];

				// Crée une minature si l'image est trop grande
				$visuel_clean = parse_url($visuel, PHP_URL_PATH);

				list($source_width, $source_height) = getimagesize($visuel_clean);// Taille de l'image

				// Image a resize ?
				if($source_width > $new_width)
				{
					// Nom de l'image resize pour voir si elle existe
					if(!isset($new_height)) $new_height = round($new_width * $source_height / $source_width);// Nouvelle hauteur
					preg_match("/(-[0-9]+x[0-9]+)\./", $visuel_clean, $matches);// Pour modifier le nom de l'image avec la nouvelle taille

					if(isset($matches[1])) $new_name = str_replace($matches[1], "-".$new_width."x".$new_height, $visuel_clean);
					else
					{// Cas d'une image pas forcement redimentionner lors de l'upload initial
						$pathinfo = pathinfo($visuel_clean);
						$new_name = $pathinfo['dirname'].'/'.$pathinfo['filename']."-".$new_width."x".$new_height.$pathinfo['extension'];
					}

					// La nouvelle image existe déjà ?
					if(file_exists($new_name)) $visuel = $new_name;							
					else $visuel = resize($visuel_clean, $new_width, $new_height, null, 'crop');
				}
			}
			*/
			?>
			<tr class="liste-produits--details">
			<!-- La liste des produits (présentation rapide)-->
				<td class="pys pxs">
					<h2 class="big mts mbs">
						<a href="<?=$res_fiche['url']?>" title="voir le détail du produit : <?=$res_fiche['title'];?>">
							<?=$res_fiche['title']?>
						</a>
					</h2>
				</td>

				<td class="pys pxs">description</td>

				<td class="pys pxs">
					<?=$content_fiche['prix']?> €
				</td>

				<td class="pys pxs w200p tr">
					<button class="bt acheter" title="Ajouter au panier" data-id="<?=$res_fiche['id']?>">Ajouter au panier</button>
				</td>

			</tr>

		<?php
		}
		?>

		</tbody>

	</table>

</section>

<!-- fonctions panier -->
<script src="/theme/<?=$GLOBALS['theme']?>/panier<?=$GLOBALS['min']?>.js"></script>