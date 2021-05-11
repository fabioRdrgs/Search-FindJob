<?php
require_once 'db.inc.php';
require_once 'annonce_func.inc.php';
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

function AddKeyword($label)
{
  static $ps = null;
  $sql = 'INSERT INTO `keywords` (`label`) VALUES (:LABEL)';

  if ($ps == null) {
    $ps = db()->prepare($sql);
  }
  $answer = false;
  try {
    $ps->bindParam(":LABEL",$label,PDO::PARAM_STR);
    if ($ps->execute())
      $answer = true;
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
  return $answer;
}


function UpdateKeyword($idKeyword,$label)
{
  static $ps = null;

  $sql = "UPDATE `keywords` SET `label` = :LABEL WHERE (`id` = :IDKEYWORD)";
  if ($ps == null) {
    $ps = db()->prepare($sql);
  }
  $answer = false;
  try {
    $ps->bindParam(':IDKEYWORD', $idKeyword, PDO::PARAM_INT);
    $ps->bindParam(':LABEL', $label, PDO::PARAM_STR);
    $ps->execute();
    if($ps->rowCount() > 0)
    $answer = true;
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
  return $answer;
}

function DeleteKeyword($idKeyword)
{
  static $ps = null;
  $sql = "DELETE FROM `keywords` WHERE (`id` = :IDKEYWORD);";
  if ($ps == null) {
    $ps = db()->prepare($sql);
  }
  $answer = false;
  try {
    $ps->bindParam(':IDKEYWORD', $idKeyword, PDO::PARAM_INT);
    $ps->execute();
    if($ps->rowCount() > 0)
    $answer = true; 
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
    $table.="<table  id=\"tableUsers\" class=\"table table-bordered table-striped mb-0\">";
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
function ShowKeywordManagement()
{
    $keywords = GetKeywords();
    $table="";
    $table.="<table>";
    $table.="<tr>";
    $table.="<td>";

    $table.="<table id=\"tableKeywords\" class=\"table table-bordered table-striped mb-0\">";
    $table.="  <thead>";
    $table.="       <tr>";
    $table.="           <th scope=\"col\">#</th>";
    $table.="           <th scope=\"col\">Label</th>";
    $table.="           <th scope=\"col\">Supprimer</th>";
    $table.="       </tr>";    
    $table.="  </thead>";
    $table.="  <tbody>";
    foreach($keywords as $keyword)
    {
        $table.="   <tr>";
        $table.="       <th scope=\"row\"><input readonly name=\"idKeyword[]\" type=\"number\" value=\"".$keyword[0]."\"/></th>";
        $table.="       <td scope=\"row\"><input type=\"text\" name=\"labelsKeywords[]\" value=\"".$keyword[1]."\"/></td>";
        $table.="       <td scope=\"row\"><input type=\"checkbox\" class=\"form-control input-lg\" name=\"deleteCheckbox[]\" value=\"".$keyword[0]."\"/></td>";
        $table.="   </tr>";
    }      
    $table.=" </tbody>";
    $table.=" </table>";

    $table.="</td>";
    $table.="<td>";

    $table.="<table id=\"tableAddKeywords\" class=\"table table-bordered table-striped mb-0\">";
    $table.="  <thead>";
    $table.="       <tr>";
    $table.="           <th scope=\"col\">Ajouter des mots clés <button id=\"addLabel\" type=\"button\"/>+</button><button id=\"removeLabel\" type=\"button\"/>-</button></th>";
    $table.="       </tr>";    
    $table.="  </thead>";
    $table.="  <tbody id=\"LabelBody\">";
  
    $table.= "<tr id=\"labelNewKeywords\">";
    $table.="<td>";
    $table.="<input  name=\"labelNewKeywords[]\" type=\"text\" placeholder=\"Nouveau Mot-Clé N°0\"/>";
    $table.="</td>";
    $table.="</tr>";    
    $table.=" </tbody>";
    $table.=" </table>";
    
    $table.="</td>";
    $table.="</tr>";
    $table.=" </table>";

    
    echo $table;
}