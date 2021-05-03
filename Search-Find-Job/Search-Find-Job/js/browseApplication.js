searchMode = $("#searchMode");

limit = 1;
//Lorsque la page est rafraîchie, affiche le bon mode correspondant ainsi que les champs ayant été remplis au préalable
$(document).on('ready',function()
{
    if(searchMode.is(":checked"))
    $("#search").prepend("<label for=\"searchField\">Nom Job</label><input id=\"searchField\" value=\""+search+"\" name=\"searchField\" type=\"text\" placeholder=\"Rechercher un job\"/><label for=\"minSalary\">Salaire Minimum</label><input id=\"minSalary\"type=\"number\" value=\""+minSalary+"\"name=\"minSalarySearch\" placeholder=\"Salaire minimum\"/><label for=\"maxSalary\">Salaire Maximum</label><input id=\"maxSalary\"type=\"number\" value=\""+maxSalary+"\" name=\"maxSalarySearch\" placeholder=\"Salaire maximum\"/><label for=\"skillCount\">Nombre de compétences requises maximum</label><input id=\"skillCount\" type=\"number\" name=\"skillCountSearch\" value=\""+skillCount+"\" placeholder=\"Compétences maximum\"/><input id=\"searchBtn\" type=\"submit\" name=\"submit\" value=\"Rechercher\"/>");       
    else
    $("#search").prepend("<label for=\"searchField\">Nom Job</label><input id=\"searchField\" value=\""+search+"\" style=\"width:50rem;\"  name=\"searchField\" type=\"text\" placeholder=\"Rechercher un job\"/><input id=\"searchBtn\" type=\"submit\" name=\"submit\" value=\"Rechercher\"/>");
})

//Action quand l'on clique sur la checkbox de changement de mode de recherche
searchMode.on('click',function(){
    //Si la checkbox est cochée, affiche le mode avancé
    if(searchMode.is(":checked"))
    {$("#search").children().remove();
    $("#search").prepend("<label for=\"searchField\">Nom Job</label><input id=\"searchField\" value=\""+search+"\" name=\"searchField\" type=\"text\" placeholder=\"Rechercher un job\"/><label for=\"minSalary\">Salaire Minimum</label><input id=\"minSalary\"type=\"number\" value=\""+minSalary+"\"name=\"minSalarySearch\" placeholder=\"Salaire minimum\"/><label for=\"maxSalary\">Salaire Maximum</label><input id=\"maxSalary\"type=\"number\" value=\""+maxSalary+"\" name=\"maxSalarySearch\" placeholder=\"Salaire maximum\"/><label for=\"skillCount\">Nombre de compétences requises maximum</label><input id=\"skillCount\" type=\"number\" name=\"skillCountSearch\" value=\""+skillCount+"\" placeholder=\"Compétences maximum\"/><input id=\"searchBtn\" type=\"submit\" name=\"submit\" value=\"Rechercher\"/>");       
    }
    //Sinon, affiche le mode simple
    else
    {
       $("#search").children().remove();
       $("#search").prepend("<label for=\"searchField\">Nom Job</label><input id=\"searchField\" value=\""+search+"\" style=\"width:50rem;\"  name=\"searchField\" type=\"text\" placeholder=\"Rechercher un job\"/><input id=\"searchBtn\" type=\"submit\" name=\"submit\" value=\"Rechercher\"/>");
    }  

})
var request;
$("#moreJobs").on('click',function(){
// event.preventDefault();
 //SendAjaxRequest();
})

function SendAjaxRequest()
{
    if(request)
    request.abort();
    // setup some local variables
var $form = $("form");

// Let's select and cache all the fields
var $inputs = $form.find("input");

// Serialize the data in the form
var serializedData = $form.find("input").serialize();
//var serializedData =  $form.find("input").serialize()

    // Let's disable the inputs for the duration of the Ajax request.
    // Note: we disable elements AFTER the form data has been serialized.
    // Disabled form elements will not be serialized.
    $inputs.prop("disabled", true);

    // Fire off the request to /form.php
    request = $.post({
        url:"../js/browse-job.php",
        type: "POST",
        data: serializedData
    });

 // Callback handler that will be called on success
 request.done(function (response, textStatus, jqXHR){
    // Log a message to the console
    console.log("Hooray, it worked!");
});

// Callback handler that will be called on failure
request.fail(function (jqXHR, textStatus, errorThrown){
    // Log the error to the console
    console.error(
        "The following error occurred: "+
        textStatus, errorThrown
    );
});

// Callback handler that will be called regardless
// if the request failed or succeeded
request.always(function () {
    // Reenable the inputs
    $inputs.prop("disabled", false);
});
}
