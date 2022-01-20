



<?php
// gestion des retours depuis Stripe
switch(@array_keys($GLOBALS['filter'])[0]) {

   default : //AFFICHAGE PANIER

      // si le panier est vide on echappe le traitement
      if(!isset($_SESSION['panier']) || count($_SESSION['panier']) == 0) {
      ?>
         
         <section class="mw960p mod center mtm mbl">
         
            <h1>Panier vide !</h1>
         
         </section>
               
      <?php
      exit;
      }

      // on annule la session ouverte précédement
      if(@array_keys($GLOBALS['filter'])[0] == 'annulation' && @$_SESSION['checkout_session']['id']) 
      {

         require_once('theme/stripe/stripe/init.php');
		   \Stripe\Stripe::setApiKey($key);

         $retrieve_session = \Stripe\Checkout\Session::Retrieve(
            $_SESSION['checkout_session']['id']
         );

         $retrieve_session->expire();

         unset($_SESSION['checkout_session']['id']);


      }
            
      // sauvegarde de l'id de la session en cas de retour sur la page
      //if(@array_keys($GLOBALS['filter'])[0] == 'annulation' && @$GLOBALS['filter']['id'])
      //   $_SESSION['checkout_session_id'] = str_replace('-','_',$GLOBALS['filter']['id']);
         
      //unset($_SESSION['checkout_session']);


      // affichage de la commande
      ?>

      <style>
      
         #panier fieldset { border: 0; padding: 0;}
         #panier fieldset legend {margin-left: auto; margin-right: auto;}
      
         #panier #coordonees {
            display: grid;
         }
      
         @media (min-width: 760px) {
      
      
         }
      
      </style>
      
      <!-- panier -->
      <section class="mw960p mod center mtm mbl">
      
         <?php h1('titre',array('default' => 'Votre panier'))?>
      
         <form id="panier" method="POST">
      
            <fieldset id="commande" class="mbl">
      
               <legend><?php h2('commande-titre',array('default' => 'Détail de votre commande'))?></legend>
      
               <table class="w100 tc">
      
                  <thead>
                     <tr class="up">
                        <th colspan="2">Produit</th>
                        <th>Prix unitaire</th>
                        <th>Quantité</th>
                        <th>Sous-total</th>
                        <th></th>
                     </tr>
                  </thead>
      
                  <tbody>
                  <?php 
                  // on récupère le détail des produits pour enrichir le panier
                  $id_list = array_column($_SESSION['panier'],'id');
                  $id_list = implode(',', $id_list);
      
                  $sql = "SELECT SQL_CALC_FOUND_ROWS ".$tc.".* FROM ".$tc;
                  $sql.= " WHERE (".$tc.".type='product') AND ".$tc.".id IN (".$id_list.")";
      
                  $sel_produit = $connect->query($sql);
      
                  while($res_produit = $sel_produit->fetch_assoc()) {
      
                     $content_produit =  json_decode($res_produit['content'], true);
      
                     // Création de la miniature du visuel
                     $new_width = 65;// Largeur max
                     //$new_height = 200; // hauteur max
                     if(!isset($content_produit['visuel'])) $visuel = '/theme/'.$GLOBALS['theme'].'/media/visuel-absent-65x65.jpg';
                     else
                     {
                        $visuel = $content_produit['visuel'];
      
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
      
                     $elem_pos = array_search($res_produit['id'],array_column($_SESSION['panier'], 'id'));
      
                     $_SESSION['panier'][$elem_pos] = array_merge( 
                        $_SESSION['panier'][$elem_pos],
                        [
                           'title' => @$res_produit['title'],
                           'url' => @$res_produit['url'],
                           'pu' => @$content_produit['prix'],
                           'visuel' => @$visuel
                        ]
                     );
                  }
      
                  // on affiche le panier
                  $total_panier = 0;
      
                  foreach($_SESSION['panier'] as $key => $elem_panier) 
                  {
      
                     $prix =  (double)str_replace(',','.',$elem_panier['pu']) * (double)$elem_panier['qte']; 
                     ?>
      
                     <tr data-id="<?=$elem_panier['id']?>">
      
                        <td data-item="visuel">
                           <a href="<?=$elem_panier['url'];?>" title="voir le détail du produit : <?=$elem_panier['title'];?>">
                           <img src="<?=$elem_panier['visuel'];?>" alt="photo du produit : <?=$elem_panier['title'];?>">
                           </a>
                        </td>
      
                        <td data-item="title">
                           <a href="<?=$elem_panier['url'];?>" title="voir le détail du produit : <?=$elem_panier['title'];?>">
                              <?=$elem_panier['title']; ?>
                           </a>
                        </td> 
      
                        <td data-item="pu" data-devise="€">
                           <?=$elem_panier['pu'];?>
                        </td>
      
                        <td data-item="qte">
                           <input type="number" min="0" step="1" value="<?=$elem_panier['qte'];?>" class="modifier" data-id="<?=$elem_panier['id']?>">
                        </td>  
      
                        <td data-item="sstotal" data-devise="€">
                           <?=$prix; ?>
                        </td>
      
                        <td><i class="fa fa-trash pointer supprimer" data-id="<?=$elem_panier['id']?>"></i></td>
      
                     </tr>
      
                     <?php
                     $total_panier += $prix;
                  }
                  ?>
      
                  </tbody>
      
                  <tfoot>
      
                     <tr class="up">
      
                        <th colspan="4" class="tr">Total panier</th>
      
                        <th class="tc" data-item="total-panier" data-devise="€">
                           <?=$total_panier?>
                        </th>
      
                        <th></th>
      
                     </tr>
      
                  </tfoot>
      
               </table>
      
            </fieldset>
      
            <fieldset id="livraisons" class="mbl relative">
      
               <legend><?php h2('livraisons-titre',array('default' => 'Livraison'))?></legend>
      
               <div>
               <!-- .module pour bien identifier que ce sont les elements à dupliquer et a sauvegardé -->
               <ul id="livraison" class="module unstyled pan auto tc">
               <?php
               // nom du module "partenaire" = id du module, et au début des id des txt() media() ...
               $module = module("livraison");
               foreach($module as $key => $val)
               {
                  ?>
                  <li class="flex">
                     <?txt('livraison-libelle-'.$key,['class'=> 'mrs', 'placeholder'=>'libelle'])?>
                     <?txt('livraison-montant-'.$key,['class'=> 'mrs', 'placeholder'=>'montant'])?>
                     <?input('livraison-minimum-'.$key,['type' => 'number', 'class'=> 'mrs', 'placeholder'=>'Délai minimum'])?>
                     <?input('livraison-maximum-'.$key,['type' => 'number', 'class'=> 'mrs', 'placeholder'=>'Délai maximum'])?>
                  </li>
                  <?php
               }
               ?>
               </ul>
               </div>
      
            </fieldset>
      
            <div class="tc">
               <button id="payer" class="bt"><?php _e('Continuer vers Stripe')?></button>
               <a <?href('mention-href')?> class="bt" title="En savoir plus">En savoir plus</a>
            </div>
                        
         </form>
      
      </section>

   <?
   break;

   case 'succes':
      //on vide le panier en session
      unset($_SESSION['panier']);

      require_once('theme/stripe/stripe/init.php');
		\Stripe\Stripe::setApiKey($key);

      $retrieve_session = \Stripe\Checkout\Session::retrieve(
         $_SESSION['checkout_session']['id'],
         []
      );

      /*
      email : $retrieve_session->customer_details->email
      nom: $retrieve_session->shipping->name
      adresse : 
         $retrieve_session->shipping->address->line_1
         $retrieve_session->shipping->address->line_2
         $retrieve_session->shipping->address->postal_code
         $retrieve_session->shipping->address->city

      */

      var_dump($retrieve_session->shipping->address);
      ?>

      <section class="mw960p mod center mtm mbl">
         
         <h1>Panier vide !</h1>
      
      </section>

      <?

      //suppression des variable de session
      //unset($_SESSION['chekout_session']);

   break;

   /*case 'annulation':  
      print 'annulation<br>';
      $_SESSION['checkout_session_id'] = str_replace('-','_',$GLOBALS['filter']['id']);
      
      print $_SESSION['checkout_session_id'].'<br>';
   break;*/
}

?>

<!-- fonctions panier -->
<script src="/theme/<?=$GLOBALS['theme']?>/panier<?=$GLOBALS['min']?>.js"></script>



