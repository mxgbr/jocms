<?php
if (!defined('JO_ROOT')) define("JO_ROOT", realpath(__DIR__."/../../"));
require_once(JO_ROOT.'/jocms/core/inc/db.php');
require_once(JO_ROOT.'/jocms/core/inc/security.php');
require_once(JO_ROOT.'/jocms/core/inc/settings.php');
require_once(JO_ROOT.'/jocms/control/fileexplorer.php');

try{
  $session = jo_session();
  if ($session == false){
    throw new Exception($JO_LANG['ERR_AUTH_SEC']);
  }

  //delete old tasks
  jocms_taskkiller();

  if(!isset($_SESSION['jocms_active'])){
    $_SESSION['jocms_active'] = false;
  }
  if(jo_login_verify() != true){

      if(!isset($_POST['name']) OR !isset($_POST['password'])){
          throw new Exception($JO_LANG['LGN_ERR']);
      }else{
          $password = $_POST['password'];
          $name = $_POST['name'];
          if(filter_var($name,FILTER_VALIDATE_EMAIL) != true){
            throw new Exception($JO_LANG['LGN_ERR']);
          }
          $output = jocms_login_check($name,$password);
          if($output == false){
              throw new Exception($JO_LANG['LGN_ERR']);
          }else{
              $_SESSION['jocms_active'] = true;
              $_SESSION['jocms_id'] = $output[0];
              $_SESSION['jocms_mail'] = $output[1];
              $_SESSION['jocms_user'] = $output[2];
              $_SESSION['jocms_agent'] = $_SERVER['HTTP_USER_AGENT'];
              $_SESSION['jocms_files'] = jo_read_dir(JO_ROOT);
          }
      }

  }

  header('Location: /jocms/control/cms.php');
}
catch(Exception $e){
  if(isset($_POST["times"])){
      $message = '<div style="color: red;">'.htmlentities($e->getMessage()).'</div>';
      $mail = htmlentities($_POST['name']);
  }else{
      $message = '';
      $mail = "";
  }
}
