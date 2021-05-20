//Variables
var emailInput = $("#email");
var passwordInput = $("#pswd");
var passwordVerifyInput = $("#pswd2");

//Effectue une action lorsqu'une touche est pressée dans un champ input
$("input").on('keyup',function(){
    //Récupère les valeurs des champs email, mot de passe et vérification de mot de passe
    var email = emailInput.val();
    var pswd = passwordInput.val();
    var pswdVer = passwordVerifyInput .val();
    //Teste si le mail est conforme aux normes de mail usuelles. Si oui, enlève la couleur du fond, si non, mets le fond en rouge
    if(email.length > 0 && email.search(/^([\w\d._\-#])+@([\w\d._\-#]+[.][\w\d._\-#]+)+$/) != -1)
    emailInput.css('background-color',"");
    else
    {
        emailInput.css('background-color',"red");  
        return;
    }
    //Teste si un mot de passe est rentré, si oui, enlève la couleur du fond, si non, met le fond en rouge
    if(pswd.length > 0)   
    passwordInput.css('background-color',"");
    else
    {
        passwordInput.css('background-color',"red");  
        return;
    }
    //Teste si les mots de passes sont identiques. Si oui, envlève la couleur du fond, si non, mets le fond en rouge
    if(pswd == pswdVer)
    {
        passwordVerifyInput.css('background-color',"")     
    }
    else
    {
        passwordVerifyInput.css('background-color',"red");
        return;
    }
    //Si les mots de passes sont identiques et non vides et que le mail respecte les normes de mail, active le bouton s'enregistrer
    if(pswd.length > 0&&pswd == pswdVer&&email.length > 0 && email.search(/^([\w\d._\-#])+@([\w\d._\-#]+[.][\w\d._\-#]+)+$/) != -1)
    {
        $("#register").prop('disabled',false);
    }
})