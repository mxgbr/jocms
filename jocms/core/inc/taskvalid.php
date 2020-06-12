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

    //JSON check
    $decoded = jo_json_check();
    if($decoded == false){
      throw new Exception($JO_LANG['ERR_INP_JSON']);
    }

    $url = $decoded['url'];
    //corrects url
    $url = jo_repair_url($url);
    $url = preg_replace("/_cms_temp\.php$/","",$url);
    if($url == false){
      throw new Exception($JO_LANG['ERR_INP_URL']);
    }
    if(jocms_task_check($url,'loggedin')==false){
        throw new Exception($JO_LANG['ERR_AUTH']);
    }
    jocms_task_valid($url);

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
