<?php
if (!defined('JO_ROOT')) define("JO_ROOT", realpath(__DIR__."/../../../../"));
//packages
require_once(JO_ROOT."/jocms/core/inc/db.php");
require_once(JO_ROOT."/jocms/core/inc/settings.php");
require_once(JO_ROOT."/jocms/core/inc/security.php");

jo_session_lite();

try{
    if(jo_login_verify() !== true){
        throw new Exception("Du bist nicht angemeldet. Bitte lade diese Seite neu.");
    }

    //JSON check
    $decoded = jo_json_check();
    if($decoded == false){
      throw new Exception($JO_LANG['ERR_INP_JSON']);
    }

    $masks = jo_get_masks($decoded["content"]);

  $result_return["error"] = array(
    "status" => true,
    "message" => ""
  );
    $result_return["masks"] = $masks;
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
