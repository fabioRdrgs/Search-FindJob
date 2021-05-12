<?php 
$GLOBALS['typeList'] = ['Admin', 'Chercheur', 'Annonceur'];
require_once "./php/db.inc.php";
/**
 * Récupère les informations de l'utilisateur
 *
 * @param string $email
 * @return array Renvoie les informations de l'utilisateur
 */
function GetUserInfo($email)
{
  //Déclaration du prepare statement en null s'il n'a pas déjà été instancié avant
  static $ps = null;
  $sql = "SELECT id, login, type, password FROM `utilisateurs` WHERE login = :EMAIL";

  //Si le prepare statement n'a pas été instancié avant, il sera null et donc aura besoin d'être préparé à nouveau
  if($ps == null)
  {
    $ps = db()->prepare($sql);
  }

  $answer = false;
  try
  {
    //Affecte tous les paramètres avec la variable correspondante
    $ps->bindParam(':EMAIL', $email, PDO::PARAM_STR);
    //Si la requête réussi sans soucis, fetch le résultat dans $answer
    if ($ps->execute())
    $answer = $ps->fetch(PDO::FETCH_NUM);
  } 
  //Si une erreur survient, rollback le tout, echo le message d'erreur et retourne false   
  catch (PDOException $e) 
  { 
    echo $e->getMessage();
  }
  //Renvoie le résultat de la requête une fois terminé
   return $answer;
}
/**
 * Permet de créer un nouvel utilisateur
 *
 * @param string $email
 * @param string $password
 * @param string $type
 * @return bool Renvoie True si la requête s'est correctement effectuée, False dans le cas contraire
 */
function CreateUser($email, $password,$type)
{
  //Déclaration du prepare statement en null s'il n'a pas déjà été instancié avant
  static $ps = null;
  $sql = "INSERT INTO `utilisateurs` (`login`, `password`,`type`) ";
  $sql .= "VALUES (:EMAIL, :PASSWORD,:TYPE)";
  //Si le prepare statement n'a pas été instancié avant, il sera null et donc aura besoin d'être préparé à nouveau
  if ($ps == null) {
    $ps = db()->prepare($sql);
  }
  $answer = false;
  try {
    //Affecte tous les paramètres avec la variable correspondante
    $ps->bindParam(':EMAIL', $email, PDO::PARAM_STR);
    $ps->bindParam(':PASSWORD', $password, PDO::PARAM_STR);
    $ps->bindParam(':TYPE', $type, PDO::PARAM_STR);
    //Si la requête réussi sans soucis, fetch le résultat dans $answer
    if($ps->execute())
    $answer = true;
  } 
  //Si une erreur survient, rollback le tout, echo le message d'erreur et retourne false   
  catch (PDOException $e) 
  {
    echo $e->getMessage();
  }
  //Renvoie le résultat de la requête une fois terminé
  return $answer;
}
/**
 * Permet de vérifier si l'email fournit existe déjà dans la base de donnée
 *
 * @param string $email
 * @return bool Renvoie true si l'email est déjà présent dans la base de donnée, False dans le cas contraire
 */
function VerifyIfMailExists($email)
{
  //Déclaration du prepare statement en null s'il n'a pas déjà été instancié avant
  static $ps = null;
  $sql = 'SELECT login FROM `utilisateurs` WHERE login = :EMAIL';
  //Si le prepare statement n'a pas été instancié avant, il sera null et donc aura besoin d'être préparé à nouveau
  if ($ps == null) {
    $ps = db()->prepare($sql);
  }
  $answer = false;
  try {
    //Affecte tous les paramètres avec la variable correspondante
    $ps->bindParam(':EMAIL', $email, PDO::PARAM_STR);
    //Si la requête réussi sans soucis et qu'il y a plus de 0 lignes retournées, assigne true à la variable $answer
    if ($ps->execute() && $ps->rowCount() > 0)
      $answer = true;
  } 
  //Si une erreur survient, rollback le tout, echo le message d'erreur et retourne false   
  catch (PDOException $e) 
  {
    echo $e->getMessage();
  }
  //Renvoie le résultat de la requête une fois terminé
  return $answer;
}
// PHP
// ==========================================================================================================

/**
 * Permet d'afficher un select contenant les différents types d'utilisateurs
 *
 * @return void Echo le select de types d'utilisateur
 */
function ShowTypeSelect()
{
  //Récupère les types se trouvant dans la base de donnée
  $typeList = $GLOBALS['typeList'];
  $selectType="";
  $selectType.="<select required name=\"type\" class=\"form-control input-lg\">";
  $selectType.="<option disabled selected value> -- Veuillez sélectionner une option -- </option>";
  //Affiche tous les types sauf admin
  foreach($typeList as $type)
  {
    if($type != 'Admin')
    $selectType.="<option value=\"$type\">$type</option>"; 
  }
  $selectType.="</select>";
  echo $selectType;
}
/**
 * Va tenter de connecter l'utilisateur si tous les tests sont valides
 *
 * @param string $email
 * @param string $password
 * @return bool Renvoie true si l'utilisateur a pu se connecter, false dans le cas contraire
 */
function ConnectUser($email, $password)
{
  //Vérifie si l'email existe
  if(VerifyIfMailExists($email) != null)
  {
   //Récupère les infos de l'utilisateur et vérifie son mot de passe
   $infoUser = GetUserInfo($email);
   //Si le mot de passe est correct, définit l'utilisateur comme connecté et assigne ses différentes informations en session et retourne true
   if(password_verify($password,$infoUser[3]))
    {
      ChangeLoginState(true);
      $_SESSION['user']['id'] = $infoUser[0];
      $_SESSION['user']['login'] = $infoUser[1];
      $_SESSION['user']['type']=$infoUser[2];
      
      return true;
    }  
  }
  //Sinon retourne false si l'utilisateur ne s'est pas connecté 
  return false;
}
/**
 * Permet de changer l'état de connexion de l'utilisateur
 *
 * @param string $state
 * @return void
 */
function ChangeLoginState($state)
{
  $_SESSION['user']['loggedIn'] = $state;
}
/**
 * Permet de récupérer le type de l'utilisateur connecté
 *
 * @return string/null Retourne le type sous forme de string de l'utilisateur, sinon renvoie null
 */
function GetUserType()
{
  if(IsUserLoggedIn())
  return $_SESSION['user']['type'];
  else
  return null;
}
/**
 * Permet de récupérer l'ID de l'utilisateur
 *
 * @return int/null Retourne l'id s'il est connecté ou retourne null
 */
function GetUserId()
{
  if(IsUserLoggedIn())
  return $_SESSION['user']['id'];
  else
  return null;
}
/**
 * Permet de tester si l'utilisateur est connecté
 *
 * @return bool Renvoie True s'il est connecté, false dans le cas contraire
 */
function IsUserLoggedIn()
{
  if(isset($_SESSION['user']['loggedIn'])&&$_SESSION['user']['loggedIn'])
  return true;
  else
  return false;
}
/**
 * Permet d'effectuer quelques tests avant de faire appel à la fonction de création d'utilisateur
 *
 * @param string $email
 * @param string $password
 * @param string $type
 * @return bool Renvoie True si l'utilisateur a bien été créé, false dans le cas contraire
 */
function RegisterUser($email,$password,$type)
{
  //Vérifie si l'email n'existe pas déjà
  if(VerifyIfMailExists($email) == null || VerifyIfMailExists($email) == false)
  {
    //Hash le mot de passe
    $hashedPassword = password_hash($password,PASSWORD_DEFAULT);
    //Si l'utilisateur a bien été créé, retourne true après avoir connecté l'utilisateur et assigné ses différentes informations
    if(CreateUser($email,$hashedPassword,$type))
    {
      ChangeLoginState(true);
      $infoUser = GetUserInfo($email);
      $_SESSION['user']['id'] = $infoUser[0];
      $_SESSION['user']['login'] = $infoUser[1];
      $_SESSION['user']['type'] = $infoUser[2];
      return true;
    }
  }
  //Retourne false s'il n'a pas réussi à s'inscrire
  return false;
}