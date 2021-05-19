<?php
require_once 'db.inc.php';
require_once 'annonce_func.inc.php';
// SQL
// ==========================================================================================================
/**
 * Permet de récupérer tous les utilsiateurs
 *
 * @return array Retourne un array contenant tous les utilisateurs et leurs informations
 */
function GetUsers()
{
  //Déclaration du prepare statement en null s'il n'a pas déjà été instancié avant
  static $ps = null;
  $sql = 'SELECT * FROM utilisateurs';
  //Si le prepare statement n'a pas été instancié avant, il sera null et donc aura besoin d'être préparé à nouveau
  if ($ps == null) {
    $ps = db()->prepare($sql);
  }
  $answer = false;
  try {
    //Si la requête réussi sans soucis, fetch tous les résultats dans $answer
    if ($ps->execute())
      $answer = $ps->fetchAll(PDO::FETCH_NUM);

  } 
  //Si une exception survient, echo le message d'erreur
  catch (PDOException $e) 
  {
    echo $e->getMessage();
  }
  //Renvoie le résultat de la requête une fois terminé
  return $answer;
}
/**
 * Permet d'ajouter un nouveau mot-clé
 *
 * @param array $arrayLabel
 * @return bool Retourne true si la transaction a été correctement effectuée, false dans le cas contraire
 */
function AddKeywords($arrayLabel)
{
  try {
    //Début de la transaction
    db()->beginTransaction();
    //Déclaration du prepare statement en null s'il n'a pas déjà été instancié avant
    static $ps = null;
    $sql = 'INSERT INTO `keywords` (`label`) VALUES (:LABEL)';

    //Si le prepare statement n'a pas été instancié avant, il sera null et donc aura besoin d'être préparé à nouveau
    if ($ps == null) {
      $ps = db()->prepare($sql);
    }

    //Exécute et assigne le label pour chaque labels dans l'array
    foreach($arrayLabel as $label)
    {
      if(!empty($label))
      {
        $ps->bindParam(":LABEL",$label,PDO::PARAM_STR);
        $ps->execute();
      }
    }

    //Termine la transaction en la committant
    db()->commit();
    return true;
  } 
  //Si une erreur survient, rollback le tout, echo le message d'erreur et retourne false 
  catch (PDOException $e) 
  {
      db()->rollBack();
      echo $e->getMessage();
      return false;
  }
}

/**
 * Permet de mettre à jour les mots-clés fournis
 *
 * @param array $arrayIdKeyword
 * @param array $arrayLabelKeyword
 * @return bool Retourne true si la transaction a été correctement effectuée, false dans le cas contraire
 */
function UpdateKeywords($arrayIdKeyword,$arrayLabelKeyword)
{
 
  try {
    //Début de la transaction
    db()->beginTransaction();  

    //Déclaration du prepare statement en null s'il n'a pas déjà été instancié avant
    static $ps = null;
    $sql = "UPDATE `keywords` SET `label` = :LABEL WHERE (`id` = :IDKEYWORD)";

    //Si le prepare statement n'a pas été instancié avant, il sera null et donc aura besoin d'être préparé à nouveau
    if ($ps == null) {
      $ps = db()->prepare($sql);
    }
    //Exécute et assigne l'id du mot-clé ainsi que son label pour chaque IDs dans l'array array d'id de mots-clé
    for ($i=0; $i < count($arrayIdKeyword); $i++)
    {
      $ps->bindParam(':IDKEYWORD', $arrayIdKeyword[$i], PDO::PARAM_INT);
      $ps->bindParam(':LABEL', $arrayLabelKeyword[$i], PDO::PARAM_STR);
      $ps->execute();
    }
    //Termine la transaction en la committant
    db()->commit();
    return true;
  } 
  //Si une erreur survient, rollback le tout, echo le message d'erreur et retourne false 
  catch (PDOException $e) 
  {
      echo $e->getMessage();
      db()->rollBack();
      return false;
  }
}
/**
 * Permet de supprimer les mots-clés fournis
 *
 * @param [type] $arrayIdKeyword
 * @return bool Retourne true si la transaction a été correctement effectuée, false dans le cas contraire
 */
function DeleteKeywords($arrayIdKeyword)
{
 
  try {
    //Début de la transaction
    db()->beginTransaction();  
    //Déclaration du prepare statement en null s'il n'a pas déjà été instancié avant
    static $ps = null;
    $sql = "DELETE FROM `keywords` WHERE (`id` = :IDKEYWORD);";
    //Si le prepare statement n'a pas été instancié avant, il sera null et donc aura besoin d'être préparé à nouveau
    if ($ps == null) {
      $ps = db()->prepare($sql);
    }
    //Exécute et assigne l'id du mot-clé pour chaque IDs dans l'array array d'id de mots-clé
    foreach($arrayIdKeyword as $idKeyword)
    {
      $ps->bindParam(':IDKEYWORD', $idKeyword, PDO::PARAM_INT);
      $ps->execute();
    }
    //Termine la transaction en la committant
    db()->commit();
    return true; 
  }
  //Si une erreur survient, rollback le tout, echo le message d'erreur et retourne false 
  catch (PDOException $e) 
  {
        echo $e->getMessage();
        db()->rollBack();
        return false;
  }
}
/**
 * Permet de mettre à jour les informations des utilisateurs fournis
 *
 * @param array $idUtilisateurArray
 * @param array $typeArray
 * @param array $passwordArray
 * @return bool Retourne true si la transaction a été correctement effectuée, false dans le cas contraire
 */
function UpdateUsers($idUtilisateurArray,$typeArray,$passwordArray)
{ 
    try {       
        //Début de la transaction
        db()->beginTransaction();  
        //Déclaration du prepare statement en null s'il n'a pas déjà été instancié avant
        static $psType = null;
        $sqlType = 'UPDATE `utilisateurs` SET type = :TYPE WHERE id = :IDUTILISATEUR';
        //Si le prepare statement n'a pas été instancié avant, il sera null et donc aura besoin d'être préparé à nouveau
        if ($psType == null) {
        $psType = db()->prepare($sqlType);
        }
        //Va s'assurer que le type fournit n'est pas vide et va ensuite assigner l'id utilisateur et le type avant de l'exécuter
        for ($i=0; $i < count($idUtilisateurArray); $i++)
        { 
          if(!empty($typeArray[$i]))
          {                
              $psType->bindParam(':IDUTILISATEUR',$idUtilisateurArray[$i],PDO::PARAM_INT);
              $psType->bindParam(':TYPE',$typeArray[$i],PDO::PARAM_STR);   
              $psType->execute();          
          }   
        }
        //Déclaration du prepare statement en null s'il n'a pas déjà été instancié avant
        static $psPassword = null;
        $sqlPassword = 'UPDATE `utilisateurs` SET password = :PASSWORD WHERE id = :IDUTILISATEUR';
        //Si le prepare statement n'a pas été instancié avant, il sera null et donc aura besoin d'être préparé à nouveau
        if ($psPassword == null) {
            $psPassword = db()->prepare($sqlPassword);
        }
        //Va s'assurer que le mot de passe fournit n'est pas vide et va ensuite assigner l'id utilisateur et le mot de passe avant de l'exécuter
        for ($i=0; $i < count($idUtilisateurArray); $i++)
        { 
          if(!empty($passwordArray[$i]))
          {       
              $psPassword->bindParam(':IDUTILISATEUR',$idUtilisateurArray[$i],PDO::PARAM_INT);
              $psPassword->bindParam(':PASSWORD',$passwordArray[$i],PDO::PARAM_STR);
              $psPassword->execute();            
          }  
        }    
        //Termine la transaction en la committant
        db()->commit();
        return true;
    }
    //Si une erreur survient, rollback le tout, echo le message d'erreur et retourne false  
    catch (PDOException $e) 
    {
        echo $e->getMessage();
        db()->rollBack();
        return false;
    }
}
/**
 * Pertmet de tester si tous les id utilisateurs existent dans la base
 *
 * @param array $idArray
 * @return bool Retourne Vrai si tous les IDs fournis sont bien présent dans la base, sinon retourne false
 */
function IsEveryGivenUserIndexInDB($idArray)
{
    //Déclaration du prepare statement en null s'il n'a pas déjà été instancié avant
    static $ps = null;
    $sql = 'SELECT * FROM utilisateurs WHERE id=:IDUTILISATEUR';
    //Si le prepare statement n'a pas été instancié avant, il sera null et donc aura besoin d'être préparé à nouveau
    if ($ps == null) {
      $ps = db()->prepare($sql);
    }
    $answer = true;
    try {
      //Va assigner l'id utilisateur à chaque fois pour l'id en question et l'exécuter et affecter false à la réponse si aucun résultat n'est fournit
        foreach($idArray as $id)
        {
            $ps->bindParam(':IDUTILISATEUR',$id,PDO::PARAM_INT);
            if ($ps->execute() && $ps->rowCount() == 0)
                $answer=false;
        }

    }
    //Si une erreur survient, rollback le tout, echo le message d'erreur et retourne false   
    catch (PDOException $e) {
      echo $e->getMessage();
    }
    //Renvoie le résultat de la requête une fois terminé
    return $answer;
}
// PHP
// ==========================================================================================================

/**
 * Permet d'afficher un select multiple contenant tous les types d'utilisateurs
 *
 * @param [type] $userType
 * @return string Retourne le select multiple
 */
function CreateAllTypeSelect($userType)
{
  //Récupère tous les types existant dans la base de donnée
  $typeList = $GLOBALS['typeList'];
  //Affecte à la variable tout le contenu HTML voulu
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
  //Retourne le contenu HTML du select multiple
  return  $select;
}
/**
 * Permet de tester si les types fournis existent dans la base de donnée
 *
 * @param array $typeArray
 * @return bool Retourne true si tous les types existent bien dans la base de donnée, retourne false dans le cas contraire
 */
function IsEveryGivenTypeInDB($typeArray)
{
    //Récupère tous les types existant dans la base de donnée
    $typeList = $GLOBALS['typeList'];
    //Teste si le type actuel dans la liste est dans les types existant, retourne false si ce n'est pas le cas 
    foreach($typeArray as $type)
    {
        if(!in_array($type,$typeList))
        return false;
    }
    //Sinon, retourne true si tous les types sont dans la base de donnée
    return true;
}

/**
 * Permet d'afficher la vue gestion d'utilisateurs
 *
 * @return void Echo la gestion d'utilisateurs
 */
function ShowUserManagement()
{
    //Récupère tous les utilisateurs
    $users = GetUsers();
    //Affecte à la variable tout le contenu HTML voulu
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
    //Affecte à la variable chaque utilisateurs et ses infos
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
    //Echo la variable contenant le contenu HTML
    echo $table;
}
/**
 * Permet d'afficher la vue gestion de Mots-dClés
 *
 * @return void Echo la gestion de Mots-Clés
 */
function ShowKeywordManagement()
{
    //Récupère tous le Mots-Clés dans la Base de données
    $keywords = GetKeywords();
    //Affecte à la variable tout le contenu HTML voulu
    $table="";
    $table.=" <div class=\"row\">";
    $table.="<div class=\"col-md-9\">";

    $table.="<table id=\"tableKeywords\" class=\"table table-bordered table-striped mb-0\">";
    $table.="  <thead>";
    $table.="       <tr>";
    $table.="           <th scope=\"col\">#</th>";
    $table.="           <th scope=\"col\">Label</th>";
    $table.="           <th scope=\"col\">Supprimer</th>";
    $table.="       </tr>";    
    $table.="  </thead>";
    $table.="  <tbody>";
    //Affecte à la variable chaque Mot-Clé et ses infos
    foreach($keywords as $keyword)
    {
        $table.="   <tr>";
        $table.="       <th scope=\"row\"><input readonly name=\"idKeyword[]\" type=\"number\" value=\"".$keyword[0]."\"/></th>";
        $table.="       <td scope=\"row\"><input required type=\"text\" name=\"labelsKeywords[]\" value=\"".$keyword[1]."\"/></td>";
        $table.="       <td scope=\"row\"><input type=\"checkbox\" class=\"form-control input-lg\" name=\"deleteCheckbox[]\" value=\"".$keyword[0]."\"/></td>";
        $table.="   </tr>";
    }      
    $table.=" </tbody>";
    $table.=" </table>";

    $table.="</div>";
    $table.="<div class=\"col-sm-1\">";
    $table.="<table id=\"tableAddKeywords\" class=\"table table-bordered table-striped mb-0\">";
    $table.="  <thead>";
    $table.="       <tr>";
    $table.="           <th scope=\"col\">Ajouter des mots-clés <button id=\"addLabel\" type=\"button\"/>+</button><button id=\"removeLabel\" type=\"button\"/>-</button></th>";
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
    
    $table.="</div>";
    $table.="</div>";
  

    //Echo la variable contenant le contenu HTML
    echo $table;
}