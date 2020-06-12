<?php
if (!defined('JO_ROOT')) define("JO_ROOT", realpath(__DIR__."/../../../../"));
require_once(JO_ROOT.'/jocms/core/inc/db.php');
require_once(JO_ROOT.'/jocms/core/inc/settings.php');
require_once(JO_ROOT.'/jocms/core/inc/security.php');
require_once(JO_ROOT.'/jocms/core/inc/simple_html_dom.php');

jo_session();
try{


    //session test
    if(jo_login_verify() !== true){
        throw new Exception($JO_LANG['ERR_AUTH']);
    }

    //JSON check
    $decoded = jo_json_check();
    if($decoded == false){
      throw new Exception($JO_LANG['ERR_INP_JSON']);
    }

    $url = $decoded['url'];
    //url korrigieren
    $url = jo_repair_url($url);
    if($url == false){
      throw new Exception($JO_LANG['ERR_INP_URL']);
    }

    //Blacklist
    if(jocms_blacklist_check($url)==false){
        throw new Exception($JO_LANG['ERR_AUTH_REST']);
    }

    if(jocms_task_check($url,'notloggedin')==false){
        throw new Exception($JO_LANG['ERR_TASK_REFR']);
    }


    //editor interface
    function replacement($url){
        $includings = '
            <!-- CMS INCLUDINGS -->
            <script type="text/javascript" src="/jocms/apps/editor/js/manager.js"></script>
            <!-- /CMS INCLUDINGS -->
            ';
        return $includings;
    }


    //get content from file
    $filecontent = file_get_contents(JO_ROOT.$url);
    if(!$filecontent){
        throw new Exception($JO_LANG['ERR_404_FILE']);
    }

    //mask breaks in code
    $filecontent = str_replace(array("\r\n", chr(10).chr(13), "\r", "\n", PHP_EOL, chr(10), chr(13)),'--jo:r--', $filecontent);

    $domobject = str_get_html ($filecontent);
    foreach ($domobject->find('*[data-cms="cms"]') as $elem){
        $elem->setAttribute ( "data-jo", "true");
        $elem->setAttribute ( "data-cms", null);
    }
    $filecontent = $domobject;

    //check editable contents
    if(strpos($filecontent, 'data-jo="true"', 0)!== false OR strpos($filecontent, "data-jo='cms'", 0)!== false){

        //create pin for temp-file
        $pin= "QWERTZUIOPASDFGHJKLYXCVBNMqwertzuiopasdfghjklyxcvbnm0123456789";
        $str = '';
        $length = 255;
        $max = mb_strlen($pin, '8bit') - 1;

        for ($i = 0; $i < $length; ++$i) {
            $str .= $pin[rand(0, $max)];
          }
        $pin = $str;

       //insert positions of editable contents
        $domobject = str_get_html ($filecontent);
        $cmspos = 0;
        foreach ($domobject->find('*[data-jo="true"]') as $elem){
            $elem->setAttribute ( "data-cmspos", $cmspos );  //->save() ?
            $cmspos = $cmspos + 1;
        }
        $filecontent = $domobject;

        //unmask breaks in code
        $filecontent = str_replace('--jo:r--', PHP_EOL,  $filecontent);

        //insert in taskmanager
        if(jocms_task_insert($url,$filecontent)==false){
            throw new Exception($JO_LANG['ERR_404_DB']);
        }

        //insert edit interface
        preg_match("/<[^<]*data-jo=[\"|']\s*true\s*[\"|']/", $filecontent, $match, PREG_OFFSET_CAPTURE);
        $match = $match[0][1];
        $filecontent = substr_replace($filecontent,replacement($url),$match ,0);
        $replacement = '<?php if($_GET["jocms"] != "'.$pin.'"){exit("Access denied!");} ?>';
        $filecontent = substr_replace($filecontent,$replacement, 0,0);

        //write temporary file
        $myfile = fopen(JO_ROOT.$url."_cms_temp.php", "w+");
        if(!$myfile){
            throw new Exception($JO_LANG['ERR_404_DIR']);
        }

        fwrite($myfile,$filecontent);
        fclose($myfile);
        chmod(JO_ROOT.$url."_cms_temp.php", 0644);
    }else{
        throw new Exception($JO_LANG['ED_ERR_EDIT']);
    }
    $result_return["error"] = array(
      "status" => true,
      "message" => ""
    );
    $result_return["pin"] = $pin;
    $result_return["url"] = rawurlencode($url);
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
