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
            //document.body.append(response);
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
            //document.querySelector("[data-item='total-commande']").textContent = calculTotalCommande();

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

// REGLER LE PANIER
document.querySelector('#payer').addEventListener('click', function(event){

    event.preventDefault();

    // préparation des données
    const formData = new FormData(panier);

    var totalCommande = document.querySelector("[data-item='total-panier']").textContent;
    formData.append('total', totalCommande);

    // envoi de la requête
    const xhr = new XMLHttpRequest();
    
    xhr.open('POST', path+"theme/"+theme+(theme?"/":"")+"panier.ajax.php?mode=payer&permalink="+permalink,true);
    
    xhr.onload = function() {
        const response = document.createRange().createContextualFragment(this.response);
        document.body.append(response); 
    }

    xhr.send(formData);

});

//------------------------
// FONCTIONS

// calcul du total du panier
function calculTotalPanier() {

    var ssTotalProduits = document.querySelectorAll("#panier tr [data-item='sstotal']");
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