<?php 
  $GLOBALS['typeList'] = ['Admin', 'Chercheur', 'Annonceur'];
  $email = filter_input(INPUT_POST,'email',FILTER_VALIDATE_EMAIL);
  $password = filter_input(INPUT_POST,'password', FILTER_SANITIZE_STRING);
  $passwordVer = filter_input(INPUT_POST,'passwordVerify',FILTER_SANITIZE_STRING);
  $type = filter_input(INPUT_POST,'type',FILTER_SANITIZE_STRING);

require_once "./php/db.inc.php";
/**
 * Récupère les informations de l'utilisateur
 *
 * @param string $email
 * @return array Renvoie les informations de l'utilisateur
 */
function GetUserInfo($email)
{
  static $ps = null;

  $sql = "SELECT id, login, type, password FROM `utilisateurs` WHERE login = :EMAIL";
  if($ps == null)
  {
    $ps = db()->prepare($sql);
  }

  $answer = false;
  try
  {
    $ps->bindParam(':EMAIL', $email, PDO::PARAM_STR);
    if ($ps->execute())
    $answer = $ps->fetch(PDO::FETCH_NUM);
  } 
  catch (PDOException $e) 
  { 
    echo $e->getMessage();
  }
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
  static $ps = null;
  $sql = "INSERT INTO `utilisateurs` (`login`, `password`,`type`) ";
  $sql .= "VALUES (:EMAIL, :PASSWORD,:TYPE)";
  if ($ps == null) {
    $ps = db()->prepare($sql);
  }
  $answer = false;
  try {
    $ps->bindParam(':EMAIL', $email, PDO::PARAM_STR);
    $ps->bindParam(':PASSWORD', $password, PDO::PARAM_STR);
    $ps->bindParam(':TYPE', $type, PDO::PARAM_STR);
    if($ps->execute())
    $answer = true;
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
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
    
  static $ps = null;
  $sql = 'SELECT login FROM `utilisateurs` WHERE login = :EMAIL';

  if ($ps == null) {
    $ps = db()->prepare($sql);
  }
  $answer = false;
  try {
    $ps->bindParam(':EMAIL', $email, PDO::PARAM_STR);

    if ($ps->execute() && $ps->rowCount() > 0)
      $answer = true;
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
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
  $typeList = $GLOBALS['typeList'];
  echo"<select required name=\"type\" class=\"form-control input-lg\">
  <option disabled selected value> -- Veuillez sélectionner une option -- </option>";
  foreach($typeList as $type)
  {
    if($type != 'Admin')
    echo "<option value=\"$type\">$type</option>"; 
  }
  echo"</select>";
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
  if(VerifyIfMailExists($email) != null)
  {
    $infoUser = GetUserInfo($email);
   if(password_verify($password,$infoUser[3]))
    {
      ChangeLoginState(true);
      $_SESSION['user']['id'] = $infoUser[0];
      $_SESSION['user']['login'] = $infoUser[1];
      $_SESSION['user']['type']=$infoUser[2];
      
      return true;
    }  
  }
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
 * @return string/null Renvoie le type sous forme de string de l'utilisateur, sinon renvoie null
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
 * @return void
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
  if(VerifyIfMailExists($email) == null || VerifyIfMailExists($email) == false)
  {
    $hashedPassword = password_hash($password,PASSWORD_DEFAULT);
    if(CreateUser($email,$hashedPassword,$type))
    ChangeLoginState(true);
    $infoUser = GetUserInfo($email);
    $_SESSION['user']['id'] = $infoUser[0];
    $_SESSION['user']['login'] = $infoUser[1];
    $_SESSION['user']['type'] = $infoUser[2];
    return true;
  }
  return false;
}