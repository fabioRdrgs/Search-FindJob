<?php
require_once 'db.inc.php';

function GetUsers()
{
  static $ps = null;
  $sql = 'SELECT * FROM utilisateurs';

  if ($ps == null) {
    $ps = db()->prepare($sql);
  }
  $answer = false;
  try {
    if ($ps->execute())
      $answer = $ps->fetchAll(PDO::FETCH_NUM);
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
  return $answer;
}
function UpdateUser($idUtilisateur,$type,$password)
{ 
    try {     
        db()->beginTransaction();  
        if(!empty($type))
        {
            
            static $psType = null;
            $sqlType = 'UPDATE `utilisateurs` SET type = :TYPE WHERE id = :IDUTILISATEUR';
        
            if ($psType == null) {
            $psType = db()->prepare($sqlType);
            }
            $psType->bindParam(':IDUTILISATEUR',$idUtilisateur,PDO::PARAM_INT);
            $psType->bindParam(':TYPE',$type,PDO::PARAM_STR);   
            $psType->execute();
            
        }        
        if(!empty($password))
        {
            
            static $psPassword = null;
            $sqlPassword = 'UPDATE `utilisateurs` SET password = :PASSWORD WHERE id = :IDUTILISATEUR';
            if ($psPassword == null) {
                $psPassword = db()->prepare($sqlPassword);
            }
            $psPassword->bindParam(':IDUTILISATEUR',$idUtilisateur,PDO::PARAM_INT);
            $psPassword->bindParam(':PASSWORD',$password,PDO::PARAM_STR);
            $psPassword->execute();
           
        }      
        db()->commit();
        return true;
    } 
    catch (PDOException $e) 
    {
        db()->rollBack();
        return false;
    }
}
function CreateAllTypeSelect($userType)
{
  $typeList = $GLOBALS['typeList'];
  $select="";
  $select.="<select required name=\"type[]\">";
        $select.="<option disabled selected value> -- Veuillez sélectionner une option -- </option>";

        foreach($typeList as $type)
        {
         if($userType==$type)
          $select.= "<option selected value=\"$type\">$type</option>"; 
          else
          $select.= "<option value=\"$type\">$type</option>"; 
        }
        $select.="</select>";
  return  $select;
}
function IsEveryGivenTypeInDB($typeArray)
{
    $typeList = $GLOBALS['typeList'];
    foreach($typeArray as $type)
    {
        if(!in_array($type,$typeList))
        return false;
    }
    return true;
}
function IsEveryGivenIndexInDB($idArray)
{
    static $ps = null;
    $sql = 'SELECT * FROM utilisateurs WHERE id=:IDUTILISATEUR';
  
    if ($ps == null) {
      $ps = db()->prepare($sql);
    }
    $answer = true;
    try {
        foreach($idArray as $id)
        {
            $ps->bindParam(':IDUTILISATEUR',$id,PDO::PARAM_INT);
            if ($ps->execute())
            if(empty($ps->fetch(PDO::FETCH_NUM)))
                $answer=false;
        }
    } catch (PDOException $e) {
      echo $e->getMessage();
    }
    return $answer;
}
function ShowUserManagement()
{
    $users = GetUsers();
    $table="";
    $table.="<table class=\"table table-bordered table-striped mb-0\">";
    $table.="  <thead>";
    $table.="       <tr>";
    $table.="           <th scope=\"col\">#</th>";
    $table.="           <th scope=\"col\">Login</th>";
    $table.="           <th scope=\"col\">Type</th>";
    $table.="           <th scope=\"col\">Mot de Passe</th>";
    $table.="       </tr>";    
    $table.="  </thead>";
    $table.="  <tbody>";
    foreach($users as $user)
    {
        $table.="   <tr>";
        $table.="       <th scope=\"row\"><input readonly name=\"idUser[]\" type=\"number\" value=\"".$user[0]."\"/></th>";
        $table.="       <td>".$user[1]."</td>";
        $table.="       <td>";
        $table.=CreateAllTypeSelect($user[3]);
        $table.="       </td>";
        $table.="       <td><input class=\"form-control input-lg\" name=\"passwordUser[]\" type=\"password\" placeholder=\"Nouveau mot de passe\"/></td>";
        $table.="   </tr>";
    }      
      $table.=" </tbody>";
    $table.=" </table>";
    echo $table;
}