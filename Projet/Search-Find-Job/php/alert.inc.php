<?php
//Permet d'afficher l'erreur adéquate si une erreur a été envoyée en GET par une autre page
if(isset($_GET['alert'])&&isset($_GET['num']))
SetAlert($_GET['alert'],$_GET['num']);
/**
 * Permet d'afficher les alertes 
 *
 * @return void Echo l'alerte avec le contenu HTML adéquat
 */
function ShowAlert()
{
    //S'assure que le type alerte est bien définit
    if(isset($_POST['alertType']))
    {
        //Teste si le type d'alerte est "erreur" et que le numéro de l'alerte est définit
        if($_POST['alertType'] == "error" && isset($_POST['alertNumber']))
        {
                $message = "";
                //Choisit quel message sera affiché
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
                    case 16:
                        $message = "Une erreur s'est produite lors de la suppression des mots-clés";
                        break;
                    case 17:
                        $message = "Une erreur s'est produite lors de l'ajout des mots-clés";
                        break;
                    case 18:
                        $message="Aucun média n'a été fournit";
                        break;
                    case 19:
                        $message = "Une erreur s'est produite lors de la création de l'annonce";
                        break;
                    case 20:
                        $message = "L'annonce voulue n'existe pas";
                        break;
                    default:
                        $message = "";
                        break;
                }
            //Créé l'alerte allant être affichée
            $message = "<div class=\"alert alert-danger\" role=\"alert\">" . $message . "</div>";
           
            
        }
         //Teste si le type d'alerte est "succès" et que le numéro de l'alerte est définit
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
                        case 3:
                            $message = "Annonce créée avec succès";
                            break;
                    }
                //Créé l'alerte allant être affichée
                $message = "<div class=\"alert alert-success\" role=\"alert\">" . $message . "</div>";
        }
        //Affiche l'alerte
        echo $message;
    }    
}
/**
 * Permet de définir quel type d'alerte l'on souhaite et quel message souhaité
 *
 * @param [type] $type
 * @param [type] $number
 * @return void
 */
function SetAlert($type,$number)
{
    $_POST['alertNumber'] = $number;
    $_POST['alertType'] = $type;
}
/**
 * Permet de récupérer l'alerte définie
 *
 * @return null/array Retourne null si le type ou le numéro n'est pas défini, sinon retourne un array contenant les 2 informations
 */
function GetAlert()
{
    if(!isset($_POST['alertNumber']) &&!isset($_POST['alertType']))
    return null;
    else   
    return [$_POST['alertNumber'],$_POST['alertType']];
    
   
}