<?php
function ShowAlert()
{
    if(isset($_POST['alertType']))
    {
        if($_POST['alertType'] == "error" && isset($_POST['alertNumber']))
        {

                $message = "";
                switch ($_POST['alertNumber']) 
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
                        $message = "Une erreur s'est produite, Veuillez contacter un administrateur";
                        break;
                    case 6:
                        $message = "Veuillez remplir tous les champs";
                        break;
                    case 7:
                        $message = "Vous ne possédez pas les droits nécessaires à l'accès de cette page";
                        break;
                    case 8:
                        $message = "Le média fournit n'est pas du bon type";
                        break;
                    case 9:
                        $message = "L'email fournit n'est pas valide";
                        break;
                    case 10:
                        $message = "Une erreur s'est produite lors de la suppression de l'annonce";
                        break;
                    case 11:
                        $message = "Une erreur s'est produite lors de la mise à jour de l'annonce";
                        break;
                    case 12:
                        $message = "Une erreur s'est produite lors du retrait d'une annonce de la wishlist";
                        break;
                    case 13:
                        $message = "Un ou plusieurs IDs fournis ne sont pas disponibles dans la Base de Données, veuillez fournir des ID valides";
                        break;
                    case 14:
                        $message = "Un ou plusieurs Types fournis ne sont pas disponibles dans la Base de Données, veuillez fournir des Types valides";
                        break;
                    case 15:
                        $message = "Une erreur s'est produite lors de la mise à jour des données";
                        break;
                    default:
                        $message = "";
                        break;
                }
            $message = "<div class=\"alert alert-danger\" role=\"alert\">" . $message . "</div>";
           
            
        }
        else if($_POST['alertType'] == "success" && isset($_POST['alertNumber']))
        {
            $message = "";
                    switch ($_POST['alertNumber']) 
                    {
                        case 1:
                            $message = "Ajout de l'annonce à la wishlist avec succès";
                            break;
                        case 2:
                            $message = "Données mises à jour avec succès";
                            break;
                        default:
                            $message = "";
                            break;
                    }
                $message = "<div class=\"alert alert-success\" role=\"alert\">" . $message . "</div>";
        }
        echo $message;
    }   
   
   
}

function SetAlert($type,$number)
{
    $_POST['alertNumber'] = $number;
    $_POST['alertType'] = $type;
}
function GetAlert()
{
    if(!isset($_POST['errormsg']) &&!isset($_POST['alertType']))
    return null;
    else   
    return [$_POST['errormsg'],$_POST['alertType']];
    
   
}