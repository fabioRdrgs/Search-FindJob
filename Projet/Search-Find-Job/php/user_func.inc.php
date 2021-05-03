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
 * @param [string] $email
 * @return void
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

    if ($ps->execute())
      $answer = $ps->fetch(PDO::FETCH_NUM);
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
  return $answer;
}

/*                   PHP
 *   ************************************* 
 */
function CreateTypeSelect()
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
      unset($infoUser);
      
      return true;
    }  
  unset($infoUser);
  }
  return false;
}
function ChangeLoginState($state)
{
$_SESSION['user']['loggedIn'] = $state;
}
function GetUserType()
{
  if(IsUserLoggedIn())
  return $_SESSION['user']['type'];
}
function IsUserLoggedIn()
{
  if($_SESSION['user']['loggedIn'])
  return true;
  else
  return false;
}

function RegisterUser($email,$password,$type)
{
  if(VerifyIfMailExists($email) == null || VerifyIfMailExists($email) == false)
  {
    $encPassword = password_hash($password,PASSWORD_DEFAULT);
    unset($password);
    if(CreateUser($email,$encPassword,$type))
    ChangeLoginState(true);
    $infoUser = GetUserInfo($email);
    $_SESSION['user']['id'] = $infoUser[0];
    $_SESSION['user']['login'] = $infoUser[1];
    $_SESSION['user']['type'] = $infoUser[2];

    unset($infoUser);
    unset($encPassword);

    return true;
  }
  return false;
}