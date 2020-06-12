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
    if(strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') != 0){
        throw new Exception("Request Method Error");
    }

    $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
    if(strpos(strtolower($contentType), 'application/json') != 0){
        throw new Exception($JO_LANG['ERR_INP_JSON']);
    }

    $log_directory = JO_ROOT.$GLOBALS['JO_S']['up_dir'];
    if(!is_readable($log_directory)){
        throw new Exception($JO_LANG['ERR_404_DIR']);
    }
    $dirname = JO_ROOT.$GLOBALS['JO_S']['up_dir'];
    $files = [];
    if($handle = opendir($dirname)) {
	   while(false !== ($file = readdir($handle)))
				    if(is_dir($dirname."/".$file)){

				    }
				    else {
					   if ($file != "." && $file != ".." && !strstr($file, "_thumb.jpg") && !strstr($file, "_thumb.png") && !strstr($file, "_thumb.gif") && (strstr($file, ".jpg")  OR strstr($file, ".png" ) OR strstr($file, ".gif"))){
					       $files[] = $GLOBALS['JO_S']['up_dir'].$file;
				        }

                    }
			         closedir($handle);
		        }

  $result_return["error"] = array(
    "status" => true,
    "message" => ""
  );
    $result_return["files"] = $files;
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
