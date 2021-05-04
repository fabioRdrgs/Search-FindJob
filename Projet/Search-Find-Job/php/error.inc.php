<?php
function ShowError()
{
    if (isset($_POST['errormsg'])) 
    {
        $message = "";
        switch ($_POST['errormsg']) 
        {
            case 1:
                $message = "Le mot de passe ou l'email entré est erroné";
                break;
            case 2:
                $message = "Veuillez vous connecter avant de procéder";
                break;
            case 3:
                $message = "Cet Email est déjà utilisé";
                break;
            case 4:
                $message = "Les mots de passes ne sont pas identiques";
                break;
            case 5:
                $message = "Une erreur lors de l'inscription s'est produite, veuillez réessayer ou contacter un administrateur";
                break;
            case 6:
                $message = "Veuillez remplir tous les champs";
                break;
                case 7:
                $message = "Vous ne possédez pas les droits nécessaires à l'accès de cette page";
                break;
            default:
                $message = "";
                break;
        }
    
        echo
        "<div class=\"alert alert-danger\" role=\"alert\">
    " . $message . "
    </div>";
    }
}


function SetError($number)
{
    $_POST['errormsg'] = $number;
}