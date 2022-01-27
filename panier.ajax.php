<?php

//------------------------
// MODE APPEL XHR
switch(@$_GET['mode'])
{
	default:	
	break;

	case "liste-produits" :
		require('stripe/init.php');
		\Stripe\Stripe::setApiKey($key);

		// liste des produits actif (non archivés)
		$produits = \Stripe\Product::all(['active' => true]);

		//@todo: vérifier que le produit n'est pas archivé et à bien un tarif !
		foreach($produits->data as $key => $produit) 
		{
			$prix = \Stripe\Price::all(['product' => $produit->id]);

			// si un prix est associé au produit, on affiche le produit
			if(count($prix->data) > 0) {

				$_SESSION[encode($produit->id)] = $produit->id; // sauvegarde de l'id encodé pour récupération du produit

				?>

					<tr class="liste-produits--details">
						<td class="pys pxs">
							<h2 class="big mts mbs">
								<a href="produit/<?=encode($produit->id)?>"><?=$produit->name?></a>
							</h2>
						</td>

						<td class="pys pxs">
						</td>

						<td class="pys pxs">
							<?=@$prix->data[0]->unit_amount_decimal / 100?> €
						</td>

						<td class="pys pxs w250p tr">
							<button class="bt acheter" title="Ajouter au panier" data-id="<?=$produit->id?>">Ajouter au panier</button>
						</td>

					</tr>
				
				<?
			}
		}
	break;

	case "fiche-produit":
		require('stripe/init.php');
		\Stripe\Stripe::setApiKey($key);

		if(!@$_SESSION[array_keys($GLOBALS['filter'])[0]]) {

			print 'produit inconnu/revenir à la liste des produits';
			exit;
		}

		$id = $_SESSION[array_keys($GLOBALS['filter'])[0]];

		$produit = \Stripe\Product::retrieve($id);

		?>
		<article>

			<h1><?=$produit->name?></h1>
			<p><?=$produit->description?></p>

			<button class="bt acheter" title="Ajouter au panier" data-id="<?=$produit->id?>">Ajouter au panier</button>

		</article>
		<?

	break;

    // AJOUT AU PANIER
	// @TODO : revoir la structure du panier pour etre à l'image de stripe (evitera des boucles par la suite)
	case "ajouter": 
		
		include_once("../../config.php");// Variables
		include_once("../../api/function.php");// Fonctions
		include_once("../../api/db.php");// Connexion à la db

		if(@$_GET['id']) {

            header("Content-Type: application/json");

			if (!isset($_SESSION['panier'])){

				// on initialise le panier
				$_SESSION['panier'][0] = array(
						'id' => @$_GET['id'],
						'qte' => 1
					);
			}
			else 
			{
				// on regarde si le produit est déjà dans le panier
				$elem_pos = array_search($_GET['id'],array_column($_SESSION['panier'], 'id'));

				if($elem_pos === false) {
					// on ajoute au panier
					array_push(	$_SESSION['panier'], 
								array(
									'id' => @$_GET['id'],
									'qte' => 1
								)
							);
				}
				else {
					$_SESSION['panier'][$elem_pos]['qte'] += 1;	
				}
			}

            // on retourne la réponse au front-end
            $response = array(
				'qte'=> qtePanierSession()
			);
			
			echo json_encode($response);
			
		}
		else{
			$response = array(
				'qte' => 0,
				'err' => 'id absent'
			);	

			
			echo json_encode($response);

		}
		

	break;

    // MISE A JOUR DU PANIER
    case "modifier":

		include_once("../../config.php");// Variables
		include_once("../../api/function.php");// Fonctions
		include_once("../../api/db.php");// Connexion à la db

        if(@$_GET['id']) {

            header("Content-Type: application/json");

            // on met à jour le produit modifié
            $elem_pos = array_search($_GET['id'],array_column($_SESSION['panier'], 'id'));
            $_SESSION['panier'][$elem_pos]['qte']=$_GET['qte'];

            // on retourne la réponse au front-end
            $response = array(
				'qte'=> qtePanierSession()
			);
            echo json_encode($response);
        }
        
    break;

    // SUPPRESSION D'UN PRODUIT
    case "supprimer":
		
		include_once("../../config.php");// Variables
		include_once("../../api/function.php");// Fonctions
		include_once("../../api/db.php");// Connexion à la db

        $elem_pos = array_search($_GET['id'],array_column($_SESSION['panier'], 'id'));
       
		unset($_SESSION['panier'][$elem_pos]);
		sort($_SESSION['panier']);

		// on retourne la réponse au front-end
		if(is_null(qtePanierSession())) $qte = 0; else $qte = qtePanierSession();

		$response = array(
			'qte'=> $qte
		);
		echo json_encode($response);

    break;

	// PAYER SA COMMANDE
	case "payer":
		
		include_once("../../config.php");// Variables
		include_once("../../api/function.php");// Fonctions
		include_once("../../api/db.php");// Connexion à la db

		$total = doubleval(str_replace(',','.',$_POST['total'])) * 100;

		// initialisation de la session stripe (cf. panier.config.php pour clé)
		require('stripe/init.php');
		\Stripe\Stripe::setApiKey($key);

		//header('Content-Type: application/json');

		// détail commande
		$line_items = array();
		foreach($_SESSION['panier'] as $key => $elem_panier) {
			array_push( 
				$line_items,
				[
					'price_data' => [
						'currency' => 'eur',
						'unit_amount' => (double)str_replace(',','.',$elem_panier['pu']) * 100,
						'product_data' => [
							'name' => $elem_panier['title'],
							'images' => [
								$GLOBALS['scheme'].$GLOBALS['domain'].$elem_panier['visuel']
							],
						]
					],
					'quantity' => (double)$elem_panier['qte'],
					/*'adjustable_quantity' => [
						'enabled' => true,
						'minimum' => 0,
						'maximum' => 10,
					]*/
				]
			);

		}

		//DEBUG
		//var_dump($line_items);
		//exit;

		// on récupère les options dans la base pour eviter les injections
		$sel_content = $connect->query("SELECT ".$GLOBALS['tc'].".* FROM ".$GLOBALS['tc']." WHERE url = '".@$_GET['permalink']."'");
		$res_content = json_decode($sel_content->fetch_assoc()['content'], true);

		$module = module('livraison',$res_content);
		
		$shipping_options = array();
		foreach($module as $key => $option) {
			if($key > 0) {
				array_push (
					$shipping_options,
					[
						'shipping_rate_data' => [	
							'type' => 'fixed_amount',
							'fixed_amount' => [	
								'amount' => (double)str_replace(',','.',$option['montant']) * 100,
								'currency' => 'eur'
							],
							'display_name' => strip_tags($option['libelle']),
							'delivery_estimate' => [
								'minimum' => ['unit' => 'business_day','value' => $option['minimum']],
								'maximum' => ['unit' => 'business_day','value' => $option['maximum']],
							]
						]
					]
				);
			}
		}

		
		// creation de la session de formation
		$checkout_session = \Stripe\Checkout\Session::create(
			[
				// livraison
				'shipping_address_collection' => [
					'allowed_countries' => ['FR'],
				  ],
				  'shipping_options' => [$shipping_options],
	
				//liste des éléments 
				'line_items' => $line_items,
				'mode' => 'payment',
				'success_url' => $GLOBALS['home']."panier/succes",
				'cancel_url' => $GLOBALS['home']."panier/annulation",
				//'metadata' => []
			]
		);

		// sauvegarde de l'id en variable de session pour reprise
		$_SESSION['checkout_session']['id'] =  $checkout_session->id;


		?>
		<script>document.location.href="<?=$checkout_session->url?>"</script>
		<?

	break;

	// LISTE DES SESSIONS STRIPE
	case "envoi-facture":
		
		/*include_once("../../config.php");// Variables
		include_once("../../api/function.php");// Fonctions
		include_once("../../api/db.php");// Connexion à la db*/

		print 'envoi facture mail';
		/*require('stripe/init.php');
		\Stripe\Stripe::setApiKey($key);*/


	break;

}

//------------------------
// FONCTIONS PANIER

// récupère la quantité d'éléments dans le panier en session
function qtePanierSession() {

    if(@$_SESSION['panier']) {

        // on calcule le nombre de produit dans le panier
        $id_elem = @array_column($_SESSION['panier'], 'qte');
        
        return @array_sum($id_elem);

    }
    
}


?>


