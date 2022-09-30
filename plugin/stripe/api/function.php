<?php

// récupère la quantité d'éléments dans le panier en session
function qtePanierSession() {

    if(@$_SESSION['panier']) {

        // on calcule le nombre de produit dans le panier
        $id_elem = @array_column($_SESSION['panier'], 'qte');
        
        return @array_sum($id_elem);

    }
    
}

?>