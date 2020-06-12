<?php
if (!defined('JO_ROOT')) define("JO_ROOT", realpath(__DIR__."/../../../"));
//include database manager and dom parser
require_once(JO_ROOT.'/jocms/core/inc/db.php');
require_once(JO_ROOT.'/jocms/core/inc/security.php');
require_once(JO_ROOT.'/jocms/core/inc/simple_html_dom.php');
require_once(JO_ROOT.'/jocms/core/inc/settings.php');

jo_session();
if(jo_login_verify()!= true OR $_SESSION['jocms_user'] != "admin"){
    header('Location: /jocms');
    exit;
}

try{
    if($_SESSION['jocms_user'] != "admin"){
      throw new Exception($JO_LANG['ERR_AUTH_ADM']);
    }

    //url korrigieren
    $url = jo_repair_url($_GET['file']);
    if($url == false){
      throw new Exception($JO_LANG['ERR_INP_URL']);
    }

    //Blacklist
    if(jocms_blacklist_check($url)==false){
        throw new Exception($JO_LANG['ERR_AUTH_REST']);
    }

    if(jocms_task_check($url,'notloggedin')==false){
        throw new Exception($JO_LANG['ERR_TASK']);
    }


    //editor interface
    function replacement(){
        $includings = '
            <!-- CMS INCLUDINGS -->
            <script type="text/javascript" src="/jocms/apps/areaselector/js/manager.js"></script>
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


    $filecontent = str_get_html ($filecontent);
    foreach ($filecontent->find('*[data-cms="cms"]') as $elem){
        $elem->setAttribute ( "data-jo", "true");
        $elem->setAttribute ( "data-cms", null);
    }

    //create pin for temp-file
    $pin= "QWERTZUIOPASDFGHJKLYXCVBNMqwertzuiopasdfghjklyxcvbnm0123456789-._";
    $str = '';
    $length = 255;
    $max = mb_strlen($pin, '8bit') - 1;

    for ($i = 0; $i < $length; ++$i) {
        $str .= $pin[rand(0, $max)];
      }
    $pin = $str;

    //insert positions of editable contents
    //$code =  str_get_html($filecontent);
    //$code = $filecontent;
    $body = $filecontent->find('body',0);

    if($body == false){
      throw new Exception($JO_LANG['AREA_INT']);
    }

    //loop through all elements
    $i = 0;
    function insert_id($elem,$i){
      foreach ($elem->find('*') as $element) {
        $outertext = $element->outertext;
        if(strpos($outertext, '<?', 0) == false AND strpos($outertext, "?>", 0) == false){
          $element->setAttribute ( "data-cmsid", $i );
          $i++;
        }
        $i = insert_id($element,$i);
      }
      return $i;
    }
    insert_id($body,$i);

    $filecode = $filecontent->outertext; //->save() ?
    $filecode = str_replace("--jo:r--", PHP_EOL,  $filecode);

    //insert in taskmanager
    if(jocms_task_insert($url,$filecode)==false){
        throw new Exception($JO_LANG['ERR_404_DB']);
    }

    $body->innertext = $body->innertext."--jo:r--".replacement();

    $filecode = $filecontent->outertext; //->save() ?
    $filecode = str_replace("--jo:r--", PHP_EOL,  $filecode);

    $replacement = '<?php session_start(); if($_GET["jocms"] != "'.$pin.'"){exit("Access denied!");} ?>';
    $filecode = substr_replace($filecode,$replacement, 0,0);

    //write temporary file
    $myfile = fopen(JO_ROOT.$url."_cms_temp.php", "w+");       //taskid einf�gen
    if(!$myfile){
        throw new Exception($JO_LANG['ERR_404_DIR']);
    }

    fwrite($myfile,$filecode);
    fclose($myfile);
    chmod(JO_ROOT.$url."_cms_temp.php", 0644);


    header('Location: '.$url."_cms_temp.php?jocms=".$pin);
}
catch(Exception $e){
    $message = $e->getMessage();
    echo '
    <html>
    <head>
        <meta charset="utf-8">
        <title>'.$JO_LANG['AREA'].' | jocms</title>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />

        <link href="/jocms/control/css/backend_style.css" rel="stylesheet" type="text/css"/>
        <link href="/jocms/core/style/css/stylesheet.css" rel="stylesheet" type="text/css"/>
        <link rel="apple-touch-icon" sizes="57x57" href="/jocms/core/style/icons/favicon/apple-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="/jocms/core/style/icons/favicon/apple-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="/jocms/core/style/icons/favicon/apple-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="/jocms/core/style/icons/favicon/apple-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="/jocms/core/style/icons/favicon/apple-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="/jocms/core/style/icons/favicon/apple-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="/jocms/core/style/icons/favicon/apple-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="/jocms/core/style/icons/favicon/apple-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="/jocms/core/style/icons/favicon/apple-icon-180x180.png">
        <link rel="icon" type="image/png" sizes="192x192"  href="/jocms/core/style/icons/favicon/android-icon-192x192.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/jocms/core/style/icons/favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="96x96" href="/jocms/core/style/icons/favicon/favicon-96x96.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/jocms/core/style/icons/favicon/favicon-16x16.png">
        <link rel="manifest" href="/jocms/core/style/icons/favicon/manifest.json">
        <meta name="msapplication-TileColor" content="#BF0040">
        <meta name="msapplication-TileImage" content="/jocms/core/style/icons/favicon/ms-icon-144x144.png">
        <meta name="theme-color" content="#BF0040">

    </head>
    <body >
        <div id="navigation">
        <div id="nav_container">
                <div id="sidebar">
                    <div>

                    </div>
                </div>
        </div>
        </div>
      <div id="menu">
          <div>
                <h1>'.$JO_LANG['AREA'].'</h1>
                '.$message.' <a id="form_cancel" class="jo_btn" href="/jocms/control/cms.php">'.$JO_LANG['FORM_DISM'].'</a>

          </div>
      </div>
    </body>
    </html>
    ';
}
?>
