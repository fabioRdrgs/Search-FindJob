    //Une demande de confirmation s'affiche lorsque le lien supprimer est cliqué
	$("#supprimerAnnonce").on("click",function(){
        return confirm("Voulez-vous vraiment supprimer cette annonce ?");
    });