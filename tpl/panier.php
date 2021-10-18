<?php
// affichage panier vide
if(!isset($_SESSION['panier']) || count($_SESSION['panier']) == 0) {
?>

   <section class="mw960p mod center mtm mbl">

      <h1>Panier vide !</h1>

   </section>

   <?php
   exit;
}

?>

<style>

#panier fieldset { border: 0; padding: 0;}
#panier fieldset legend {margin-left: auto; margin-right: auto;}

#panier { counter-reset: fieldset; }
#panier legend h2::before {
  counter-increment: fieldset;
  content: counter(fieldset)' - ';
}

#panier #coordonees {
   display: grid;
}

@media (min-width: 760px) {


}


</style>

<!--contenu panier-->
<section class="mw960p mod center mtm mbl">

   <?php h1('titre')?>

   <form id="panier" method="post">

      <fieldset id="synthese" class="mbl">

         <legend><?php h2('synthese-titre',array('default' => 'Votre panier'))?></legend>

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

                     $_SESSION['panier'][$elem_pos] = array_merge( $_SESSION['panier'][$elem_pos],
                                                                  array(
                                                                     'title' => @$res_produit['title'],
                                                                     'url' => @$res_produit['url'],
                                                                     'pu' => @$content_produit['prix'],
                                                                     'visuel' => @$visuel
                                                                  )
                                                                  );
                  }

                  // on affiche le panier
                  $total_panier = 0;

                  foreach($_SESSION['panier'] as $key => $elem_panier) {

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

               <?php 
               ?>

            </tbody>

         </table>

      </fieldset>

      <fieldset id="frais" class="mbl">

         <legend><?php h2('frais-titre',array('default' => 'Mode de livraison'))?></legend>

         <table class="w100">

            <tbody>
               <tr>

                  <td>Total panier</td>
                  <td class="tc" data-item="total-panier" data-devise="€">
                     <?=$total_panier?>
                  </td>
                  <td style="width:20px"></td>
               </tr>

               <tr class="big bold">
                  <td  class="w80">Montant Total</td>
                  <td class="tc" data-item="total-commande" data-devise="€">
                     <?=$total_panier?>
                  </td>
                  <td style="width:20px"></td>
               </tr>

            </tbody>

         </table>
      </fieldset>

      <fieldset id="coordonees" class="mbl">
         <legend><?php h2('coordonees-titre',array('default' => 'Coordonees de livraison'))?></legend>

         <div>

            <div>
               <label for="prenom" class="block">Votre prénom</label>
               <input type="text" id="prenom" name="prenom" placeholder="Prénom*" required>
            </div>           

            <div>
               <label for="nom" class="block">Votre nom</label>
               <input type="text" id="nom" name="nom" placeholder="Nom*" required>
            </div>

            <div>
               <label for="email" class="block">Adresse courriel</label>
               <input type="email" id="email" name="email" placeholder="Mail*" required>
            </div>

            <div>
               <label for="tel" class="block">Numéro de téléphone</label>
               <input type="text" id="tel" name="tel" placeholder="Téléphone*" required>
            </div>

            <div>

               <label for="adresse" class="block">Votre adresse de livraison</label>
               <input class="w40" type="text" id="adresse" name="adresse" placeholder="Adresse de livraison*" required>
               <input class="w10" type="text" name="cp" placeholder="Code postal*" pattern="[0-9]*" required>
               <input class="w20" type="text" name="ville" placeholder="Ville*" required>
               <input class="w20" type="text" name="pays" placeholder="Pays" value="">

            </div>

         </div>
      
      </fieldset>

      <fieldset id="reglement" class="mbl tc">
         <legend><?php h2('reglement-titre',array('default' => 'Règlement'))?></legend>

         <div class="pbs">
            <input type="checkbox" id="cgu" name="cgu" required>
            <label for="cgu"><?php span('cgu',array('default' => 'Accepter les conditions générales de ventes'))?></label>
         </div>

         <button class="bt"><?php span('bt-reglement', array('default' => 'Continuer vers Stripe'))?>
      
      </fieldset>
                    
   </form>
</section>

<!-- fonctions panier -->
<script src="/theme/<?=$GLOBALS['theme']?>/panier<?=$GLOBALS['min']?>.js"></script>