// AJOUTER AU PANIER
var articles = document.querySelectorAll('.acheter');

articles.forEach(function(article) {

	article.addEventListener('click', function(){

        var id = this.getAttribute("data-id");
        var xhr = new XMLHttpRequest();

        xhr.open('GET', path+'theme/'+theme+'/panier.ajax.php?mode=ajouter&id='+id,true);

        xhr.responseType = 'json';

        xhr.onload = function()
        {
            //on incrémente la quantité du panier
            const data = xhr.response;
            document.querySelector("#mon-panier").setAttribute("data-quantite",parseInt(data['qte']));
            light("Produit ajouté au panier", 1000);
        }
        xhr.send();

	});
    
});

// MODIFIER LA QUANTITÉ
var articles = document.querySelectorAll('.modifier');

articles.forEach(function(article) {

	article.addEventListener('change', function(){

        var id = this.getAttribute("data-id");
        var qte = this.value;
        var xhr = new XMLHttpRequest();

        xhr.open('GET', path+'theme/'+theme+'/panier.ajax.php?mode=modifier&id='+id+'&qte='+this.value,true);
       
        xhr.responseType = 'json';

        xhr.onload = function()
        {
            const data = xhr.response;

            // on incrémente le sous-total produit
            var puProduit = document.querySelector("[data-id='"+id+"'] [data-item='pu']").textContent.replace(',','.');
            var ssTotal = puProduit * qte;
            document.querySelector("[data-id='"+id+"'] [data-item='sstotal']").textContent = ssTotal.toFixed(2).toString().replace('.',',');

            // on recalcule la valeur totale du panier
            document.querySelector("[data-item='total-panier']").textContent = calculTotalPanier();

            // on recalcule la valeur totale de la commande
            document.querySelector("[data-item='total-commande']").textContent = calculTotalCommande();

            // on incrémente l'indicateur du nombre d'élément
            document.querySelector("#mon-panier").setAttribute("data-quantite",parseInt(data['qte']));
           
        }
        xhr.send();

	});
    
});

// SUPPRIMER DU PANIER
var articles = document.querySelectorAll('.supprimer');

articles.forEach(function(article) {

	article.addEventListener('click', function(){

        var id = this.getAttribute("data-id");

        var xhr = new XMLHttpRequest();

        xhr.open('GET', path+'theme/'+theme+'/panier.ajax.php?mode=supprimer&id='+id,true);
        
        xhr.responseType = 'json';

        xhr.onload = function()
        {
            const data = xhr.response;

            if(parseInt(data['qte'])==0) {
                window.location.reload(true);
            }
            else {
                var elemPanier = document.querySelector('tr[data-id="'+id+'"]');
                elemPanier.parentNode.removeChild(elemPanier);

                // on recalcule la valeur totale du panier
                document.querySelector("[data-item='total-panier']").textContent = calculTotalPanier();

                // on incrémente l'indicateur du nombre d'élément
                document.querySelector("#mon-panier").setAttribute("data-quantite",parseInt(data['qte']));
            }
        }
        xhr.send();

	});
    
});

//------------------------
// FONCTIONS

// calcul du total du panier
function calculTotalPanier() {

    var ssTotalProduits = document.querySelectorAll("#panier [data-item='sstotal']");
    var total = 0;

    ssTotalProduits.forEach(function(ssTotalProduit) {
         
        ssTotal = parseFloat(ssTotalProduit.textContent.replace(',','.'));
        total = total + ssTotal;

    });
    
    total = total.toFixed(2).toString().replace('.',','); // transformation pour affichage

    return total;

}

// calcul du total de la commande (total panier + frais - reduc)
function calculTotalCommande() {

    var total = 0;
    var totalPanier = parseFloat(calculTotalPanier().replace(',','.'));

    total = totalPanier

    total = total.toFixed(2).toString().replace('.',','); // transformation pour affichage
    return total;
    

}

//----------------------------
// COORDONNEES
// Remplit le formulaire de coordonnées avec les données dans le cookie
var contact = get_cookie("coordonnees");// Extrait les données du cookie
if(contact) {
    contact = JSON.parse(contact);// Extrait les données json
    $.each(contact, function(i, obj) {
        if(obj.name == 'cookie' && obj.value=='on') $("#panier #cookie").prop("checked", true);// Coche la checkbox cookie
        else $("#panier #coordonnees input[name='"+obj.name+"']").val(obj.value);// Remplit les inputs
    });
}

// Mémorise les coordonnées dans un cookie
$("#panier #coordonnees input, #panier #cookie").on("change", function(event) 
{
    // Efface le cookie si décoché
    if(!$("#panier #cookie").prop("checked")) set_cookie("coordonnees", '', 0);

    // Si on demande de mémoriser les coordonnées dans un cookie du navigateur
    if($("#panier #cookie").prop("checked"))							
        // Création du cookie avec les données du formulaire de coordonnée en json
        set_cookie("coordonnees", JSON.stringify($("#panier").serializeArray()), 180);
});





//-----------------------------------------------------------------------------------rech
// LIVRAISON-FRAIS PORT
// Tableau Livraison par tranche    
//livraisons = jQuery.parseJSON('{"france":{"0-20":"4.5","20-50":"3.5","50-100":"2.8","101-":"0"},"etranger":{"0-40":"7","40-70":"12","70-":"20"}}');		
    // base - DE 0 à 20€ de produits, prix livr de 4.5 en FR; de 20 à 50€ de prod, liv de 3.5 -> basée sur le prix
    
    // @todo adapter au site 
/*format 15x20  //note: hors ue (TOM) et autre, c'est le même tarif
livraisons = jQuery.parseJSON('{"france":{"0-1":"7.29","2-":"7.29+(1*qte)"},"europeUE":{"0-1":"13.74","2-":"13.74+(3.05*qte)"},"europe":{"0-1":"28.94","2-":"28.94+(3.20*qte)"}, "autre":{"0-1":"28.94","2-":"28.94+(3.20*qte)"},}');	//à baser sur qty
format 20x30
livraisons = jQuery.parseJSON('{"france":{"0-1":"8.35","2-":"8.35+(1.80*qte)"},"europeUE":{"0-1":"16.95","2-":"16.95+(2.10*qte)"},"europe":{"0-1":"32.25","2-":"32.25+(11.70*qte)"}, "autre":{"0-1":"31.35","2-":"31.35+(11.40*qte)"},}');	//à baser sur qty

//ou penser en terme de tarif :"0-prix de la petite planche : 7.29"," multipe du prix de la petite planche : 7.29+(1xmultiple)"
//                             et prix petite planche-prix grde planche : etc

/* réflexion...
France
si 1 planche 15x20 : tarif 7.29€
si plus de 1 planche 15x20 : 7.29€ + 1€ chaque planche supplément
si 1 planche 20x30 : tarif 8.35€
si plus de 1 planche 15x20 : 8.35€ + 1.80€ chaque planche supplément

europeUE
si .. etc
*/
 

// MEMORISE LE MODE DE LIVRAISON 
   $("body").on("change", "#panier #prix-livraison", function(event) {

    // Décoche les cgv pour donner plus de temps à l'ajax de s'executé
    $("#rgpdcheckbox").prop("checked", false);

    $.ajax({
       type: "POST",
       url: path+"theme/"+theme+"/panier.php",  //@todo vérif url
       data: {
          "mode": "livraison",
          "name": $(this).val(),
          "prix": $(":selected", this).data("prix"),
          "palier": $(":selected", this).data("palier"),
          "transporteur": $(":selected", this).data("transporteur"),
       },
       success: function(html){
          $("body").append(html);
       }
    });
 });