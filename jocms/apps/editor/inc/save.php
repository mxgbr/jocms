<?php

if (!defined('JO_ROOT')) define("JO_ROOT", realpath(__DIR__."/../../../../"));
require_once(JO_ROOT.'/jocms/core/inc/db.php');
require_once(JO_ROOT.'/jocms/core/inc/simple_html_dom.php');
require_once(JO_ROOT.'/jocms/core/inc/settings.php');
require_once(JO_ROOT.'/jocms/core/inc/security.php');
jo_session();
try{
    //session check
    if(jo_login_verify() !== true){
        throw new Exception($JO_LANG['ERR_AUTH']);
    }
    //JSON check
    $decoded = jo_json_check();
    if($decoded == false){
      throw new Exception($JO_LANG['ERR_INP_JSON']);
    }


    //insert new contents
    function exchange_contents($filecontent, $insert){
        //get charset of file
        $enc_file = $GLOBALS['JO_S']['charset']; // mb_detect_encoding($filecontent,$GLOBALS['JO_S']['charset'].',UTF-8,ISO-8859-1,auto',true);

        //create dom object
        $filecontent = str_replace(array("\r\n", chr(10).chr(13), "\r", "\n", PHP_EOL, chr(10), chr(13)),'--jo:r--', $filecontent);
        $domobject = str_get_html ($filecontent);
        //get positions
        foreach ($domobject->find('*[data-jo="true"]') as $elem){

            $pos = $elem->getAttribute("data-cmspos");
            $elem->removeAttribute("data-cmspos");
            if(array_key_exists($pos,$insert)){
                //JSON charset
                $enc_inp = 'UTF-8';//mb_detect_encoding($insert[$pos],$GLOBALS['JO_S']['charset'].',UTF-8,ISO-8859-1,auto',true);

                //replace innerhtml
                $insertb = preg_replace('/<\s*script/',"[script",$insert[$pos]);
                $insertb = preg_replace('/<\s*\/\s*script/',"[/script",$insertb);
                $insertb = preg_replace('/<\s*\?\s*php/',"[php",$insertb);
                $insertb = preg_replace('/\?\s*>/',"[/php",$insertb);

                $elem->innertext = iconv($enc_inp,$enc_file.'//TRANSLIT',$insertb);

            }
        }
        //unmask line breaks
        return str_replace("--jo:r--", PHP_EOL,  $domobject->save());

    }

    //check input
    $url = $decoded['url'];
    $url = jo_repair_url($url);
    if($url == false){
      throw new Exception($JO_LANG['ERR_INP_URL']);
    }

    $url = parse_url($url)['path'];
    $url = preg_replace("/_cms_temp\.php$/","",$url);

    $type = $decoded['type'];

    if($type !== "cancel"){
        //task check
        if(jocms_task_check($url,'loggedin')==false){
            throw new Exception($JO_LANG['ERR_AUTH']);
        }
        //get code from db
        $filecontent = jocms_task_code($url);

        //write in history
        if(jocms_history($url,$filecontent)==false){                    //welcher filecont??
            throw new Exception($JO_LANG['ERR_404_DB']);
        }

        //insert new contens
        $filecontent = exchange_contents($filecontent, $decoded['html']);


        //write in file
        $myfile = fopen(JO_ROOT.$url, "w+");
        if(!$myfile){
            throw new Exception($JO_LANG['ERR_404_FILE']);
        }

        fwrite($myfile,$filecontent);
        fclose($myfile);

        //delete task and temp file
        jocms_task_delete($url);
    }else{
        //delete task and temp file if no error
        if(jocms_task_check($url,'loggedin')==false){
        }else{
            jocms_task_delete($url);
        }
    }






    $result_return["error"] = array(
      "status" => true,
      "message" => ""
    );
    $result_return["redirection"] = $url;
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
