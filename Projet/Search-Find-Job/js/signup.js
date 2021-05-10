var emailInput = $("#email");
var passwordInput = $("#pswd");
var passwordVerifyInput = $("#pswd2");

$("input").on('keyup',function(){
    var email = emailInput.val();
    var pswd = passwordInput.val();
    var pswdVer = passwordVerifyInput .val();
    if(email.length > 0 && email.search(/^([\w\d._\-#])+@([\w\d._\-#]+[.][\w\d._\-#]+)+$/) != -1)
    emailInput.css('background-color',"");
    else
    {
        emailInput.css('background-color',"red");  
        return;
    }

    if(pswd.length > 0)   
    passwordInput.css('background-color',"");
    else
    {
        passwordInput.css('background-color',"red");  
        return;
    }

    if(pswd == pswdVer)
    {
        passwordVerifyInput.css('background-color',"")     
    }
    else
    {
        passwordVerifyInput.css('background-color',"red");
        return;
    }

    if(pswd.length > 0&&pswd == pswdVer&&email.length > 0 && email.search(/^([\w\d._\-#])+@([\w\d._\-#]+[.][\w\d._\-#]+)+$/) != -1)
    {
        $("#register").prop('disabled',false);
    }
})