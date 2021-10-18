<?php

if(@$_GET['mode']) {
    include_once("../../config.php");// Variables
    include_once("../../api/function.php");// Fonctions
    include_once("../../api/db.php");// Connexion à la db
}


//------------------------
// MODE APPEL XHR
switch(@$_GET['mode'])
{
	default:	
	break;

    // INSTALLATION BDD
    case 'installer':

		$GLOBALS['connect']->query(
			"CREATE TABLE IF NOT EXISTS `".$GLOBALS['db_prefix']."panier` (
				`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				`statut` varchar(20) NOT NULL DEFAULT 'panier', 
				`contenu` text,
				`port` text,
				`coordonees` text,
				`date_insert` datetime NOT NULL,
				`date_update` datetime DEFAULT NULL,
				`historique` text NOT NULL,
				PRIMARY KEY (`id`),
				KEY `statut` (`statut`)
			) 
			ENGINE=MyISAM DEFAULT CHARSET=utf8;"

		);
    break;

    // AJOUT AU PANIER
	case "ajouter": 

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

	break;

    // MISE A JOUR DU PANIER
    case "modifier":

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


