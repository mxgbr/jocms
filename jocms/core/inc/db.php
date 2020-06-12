<?php
//packages
if (!defined('JO_ROOT')) define("JO_ROOT", realpath(__DIR__."/../../"));
require_once(JO_ROOT.'/jocms/core/inc/settings.php');

date_default_timezone_set('UTC');

function jocms_db_link(){
    return new SQLite3(JO_ROOT.'/jocms/core/database/database.db');
}

//checks login for users, checks if brute force
//input: mail, password
//return: true if successfully logged in
function jocms_login_check($name,$password){
        $now = time();
        $valid_attempts = $now - (2 * 60 * 60);
        $handle = jocms_db_link();

        $result = $handle->query("SELECT * FROM user WHERE mail='".$handle->escapeString($name)."'");
        $output = $result->fetchArray();

        if(isset($output['id']) AND isset($output['password'])){
          $result = $handle->query("SELECT COUNT(*) AS count FROM login_attempts WHERE user_id = ".$output['id']." AND time > '".$handle->escapeString($valid_attempts)."'");
          $brute_output = $result->fetchArray();
          if($brute_output['count']>5){
              echo "Das Passwort wurde zu oft falsch eingegeben. Zur Sicherheit vor einer Brute-Force-Attacke wurde dein Konto für max. 2h gesperrt.";
              exit;
          }

           if(password_verify($password,$output['password']) != true){
              $result = $handle->exec("INSERT INTO login_attempts(user_id, time) VALUES ('".$handle->escapeString($output['id'])."', '".$handle->escapeString($now)."')");
              return false;
          }else{
              $result = $handle->exec("DELETE FROM login_attempts WHERE user_id = '".$handle->escapeString($output['id'])."'");
              return [$output['id'],$output['mail'],$output['type']];
          }
        }
        return false;


}

//sets new password
//input: mail, password
//return: true if success
function jo_set_password($mail,$password){
  $handle = jocms_db_link();
  $return = $handle->exec("UPDATE user SET password='".password_hash($password,PASSWORD_DEFAULT)."' WHERE mail='".$handle->escapeString($mail)."'");   //escapen
  return $return;
}

//sets new mail
//input: new mail, olf mail
//return: true if success
function jo_set_mail($old,$new){
  $handle = jocms_db_link();
  $return = $handle->exec("UPDATE user SET mail='".$handle->escapeString($new)."' WHERE mail='".$handle->escapeString($old)."'");   //escapen
  return $return;
}

//gets users
//input: type
//output: user, type, id
function jo_get_users($type, $selector){
  $condition = "";
  $users = [];
  $handle = jocms_db_link();
  if($type != "all"){
    $condition = " WHERE ".$selector."='".$handle->escapeString($type)."'";
  }
  $result = $handle->query("SELECT * FROM user".$condition);
  while($output = $result->fetchArray()){
      $users[] = $output;
  }
  return $users;
}

//deletes user
//input: id
function jo_delete_user($id){
  $handle = jocms_db_link();
  $return = $handle->exec("DELETE FROM user WHERE id='".$handle->escapeString($id)."'");              //escapen???
  return $return;
}
//updates users
//input: mail, type
function jo_update_users($id, $type){
  $handle = jocms_db_link();
  $return = $handle->exec("UPDATE user SET type=COALESCE(NULLIF('".$handle->escapeString($type)."', ''), type) WHERE id='".$handle->escapeString($id)."'");       //escapen
  return $return;
}

//inserts new user
//input: mail, type, passwort
//output: boolean
function jo_set_user($mail, $type, $password){
  $handle = jocms_db_link();
  $return = $handle->exec("INSERT INTO user(mail,type,password) VALUES ('".$handle->escapeString($mail)."','".$handle->escapeString($type)."','".password_hash($password,PASSWORD_DEFAULT)."')");   //escapen
  return $return;
}

//Taskmanager

//checks if a task existing, deletes task if older than specific time
//input: path of requested file, 'loggedin' if taskvalid, 'notloggedin' if open new file
//return: true if task still exists (loggedin) and true if task is free (notloggedin)
function jocms_task_check($path,$type){
    $handle = jocms_db_link();

    if($type == 'loggedin'){
        $result = $handle->query("SELECT COUNT(id) AS total FROM tasks WHERE user_id='".$handle->escapeString($_SESSION['jocms_id'])."' AND task='".$handle->escapeString($path)."'");    //escapen
        $output = $result->fetchArray();
        if($output['total']  !== 0){
            return true;
        }
    }elseif($type == 'notloggedin'){
        $result = $handle->query("SELECT COUNT(id) AS total FROM tasks WHERE task='".$handle->escapeString($path)."'");   //escapen
        $output = $result->fetchArray();
        if(intval($output ['total'])  === 0){
            return true;
          }else{
            //check timestamp
            $result = $handle->query("SELECT time FROM tasks WHERE task='".$handle->escapeString($path)."'");  //escapen
            while($output = $result->fetchArray()){
                $time = $output['time'];
            }
            $time_diff = strtotime(date("Y-m-d H:i:s")) - strtotime($time);
            if ($time_diff > 3){

                if(jocms_task_delete($path)==true){
                    return true;
                }
            }
          }
    }
    return false;
}

//gets file source code from task DB
//input: path to file_exists
//return: code
function jocms_task_code ($path){
    $handle = jocms_db_link();
    $result = $handle->query("SELECT code FROM tasks WHERE task='".$handle->escapeString($path)."'");                //escapen
    while($output = $result->fetchArray()){
        $code = $output['code'];
    }
    return $code;
}

//inserts a new task
//input: path to file, source code of file (, and user id via session)
//return: true if success
function jocms_task_insert($path,$code){
    $handle = jocms_db_link();
    $return = $handle->exec("INSERT INTO tasks(task,user_id,time,code) VALUES ('".$handle->escapeString($path)."','".$handle->escapeString($_SESSION['jocms_id'])."',datetime('now'),'".$handle->escapeString($code)."')");   //escapen
    return $return;
}

//deletes task an temp-file
//input: path to file
//return: true if success of DB delete
function jocms_task_delete($path){
    $handle = jocms_db_link();
    if(file_exists(JO_ROOT.$path."_cms_temp.php")){
      unlink(JO_ROOT.$path."_cms_temp.php");
    }
    $return = $handle->exec("DELETE FROM tasks WHERE task='".$handle->escapeString($path)."'");              //escapen???/////////////////////////////////////////////////////
    return $return;
}

//adds new timestamp to task entry
//input: path to file
//return: true if success
function jocms_task_valid($path){
    $handle = jocms_db_link();
    $return = $handle->exec("UPDATE tasks SET time=datetime('now') WHERE task='".$handle->escapeString($path)."'");       //escapen
    return $return;
}

//deletes tasks older than specific time
//input: -
//return: -
function jocms_taskkiller(){
    $handle = jocms_db_link();
    $result = $handle->query("SELECT id,task FROM tasks WHERE time<='".date("Y-m-d H:i:s",strtotime("-60 seconds", time()))."'");
    while($output = $result->fetchArray()){
        if(file_exists(JO_ROOT.$output['task']."_cms_temp.php")){
            unlink(JO_ROOT.$output['task']."_cms_temp.php");
        }
        $handle->exec("DELETE FROM tasks WHERE id='".$handle->escapeString($output['id'])."'");
    }
}
//blacklist

//checks if specific task is prohibited by admin
//input: path to file
//return: true if not prohibited
function jocms_blacklist_check($path){
    $handle = jocms_db_link();
    $result = $handle->query("SELECT COUNT(ban) AS total FROM rights WHERE user_id='".$handle->escapeString($_SESSION['jocms_id'])."' AND ban='".$handle->escapeString($path)."'");    //escapen
    $output = $result->fetchArray();
    if($output ['total']  > 0){
        return false;
    }else{
        return true;
    }
}
//history

//inserts new file history entry and deletes old entries
//input: path to file, code
//return: true if success
function jocms_history($path,$code){
    $handle = jocms_db_link();
    $return = $handle->exec("INSERT INTO history(task,user_id,time,code) VALUES ('".$handle->escapeString($path)."','".$handle->escapeString($_SESSION['jocms_id'])."',datetime('now'),'".$handle->escapeString($code)."')");
    $result = $handle->exec("DELETE FROM history WHERE time<datetime('now', '-". $handle->escapeString($GLOBALS['JO_S']['history'])." day')");
    return $return;                                                                                                                                    //sql ausf�hrung pr�fen
}

//gets history entries for recovery list
//input: path to file
//return: code (array)
function jo_get_history($path){
    $code;
    $handle = jocms_db_link();
    $result = $handle->query("SELECT history.user_id, history.id, history.time, user.mail FROM history INNER JOIN user ON history.user_id = user.id WHERE history.task='".$handle->escapeString($path)."' ORDER BY history.time DESC");
    while($output = $result->fetchArray()){
        $code[] = $output;
    }
    if(!isset($code)){
      $code = false;
    }
    return $code;
}

//gets history entries for recovery
//input: history id
//return: code
function jo_history_reset($id){
    $handle = jocms_db_link();
    $result = $handle->query("SELECT code,user_id,task FROM history WHERE id='".$handle->escapeString($id)."'");//hintereinander
    while($output = $result->fetchArray()){
        $code[] = $output;
    }
    return $code[0];
}

//inserts new mask
//input: id, name, type, code
//output: true/false
function jo_set_mask($id, $name, $type, $code){
    $handle = jocms_db_link();
    if($id != 0){
        $return = $handle->exec("UPDATE masks SET name='".$name."', code='".$code."' WHERE id='".$id."'");
        $return = $id;
    }else{
        $return = $handle->exec("INSERT INTO masks(name,type,code) VALUES ('".$handle->escapeString($name)."','".$handle->escapeString($type)."','".$handle->escapeString($code)."')");
        $return = $handle->lastInsertRowid();
    }
    return $return;
}

//gets mask entries
//input: id or "all"
//output: id, name, code
function jo_get_masks($id){
    $condition = "";
    $masks = [];
    if($id != "all"){
        $condition = " WHERE id='".$id."' ";
    }else{
        $condition = " WHERE type='mask' ";
    }
    $code;
    $handle = jocms_db_link();
    $result = $handle->query("SELECT * FROM masks ".$condition." ORDER BY name");
    while($output = $result->fetchArray()){
        $masks[] = $output;
    }
    return $masks;
}

//delete mask
//input: id
//output: -
function jo_delete_mask($id){
    $handle = jocms_db_link();
    $result = $handle->query("DELETE FROM masks WHERE id='".$id."'");
    return $result;
}
?>
