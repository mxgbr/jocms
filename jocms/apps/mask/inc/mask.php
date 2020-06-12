<?php
if (!defined('JO_ROOT')) define("JO_ROOT", realpath(__DIR__."/../../../../"));
require_once(JO_ROOT.'/jocms/core/inc/db.php');
require_once(JO_ROOT.'/jocms/core/inc/security.php');
require_once(JO_ROOT.'/jocms/core/inc/settings.php');
error_reporting(0);
jo_session();
try{

    if(jo_login_verify() !== true){
        throw new Exception($JO_LANG['ERR_AUTH']);
    }
    if($_SESSION['jocms_user'] != 'admin'){
      throw new Exception($JO_LANG['ERR_AUTH_ADM']);
    }

    //JSON check
    $decoded = jo_json_check();
    if($decoded == false){
      throw new Exception($JO_LANG['ERR_INP_JSON']);
    }

    $mask = jo_get_masks($decoded["id"])[0];
    $result_return["id"] = $mask["id"];
    $result_return["name"] = $mask["name"];
    $result_return["code"] = $mask["code"];

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
