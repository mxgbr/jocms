<?php
if (!defined('JO_ROOT')) define("JO_ROOT", realpath(__DIR__."/../../../"));
require_once(JO_ROOT.'/jocms/core/inc/db.php');
require_once(JO_ROOT.'/jocms/core/inc/security.php');
require_once(JO_ROOT.'/jocms/core/inc/settings.php');
error_reporting(0);
jo_session_lite();
try{

    if(jo_login_verify() !== true){
        throw new Exception($JO_LANG['ERR_AUTH']);
    }

    $result_return['lang'] = $JO_LANG;
    $result_return['set'] = $JO_S;
    $result_return["error"] = array(
      "status" => true,
      "message" => ""
    );
    header('Content-type: application/json');
    echo json_encode($result_return);
}
catch(Exception $e){
  $result_return["error"] = array(
    "status" => false,
    "message" => $e->getMessage()
  );
  header('Content-type: application/json');
  echo json_encode($result_return);
}




?>
