<?php
if (!defined('JO_ROOT')) define("JO_ROOT", realpath(__DIR__."/../../../"));
//packages
require_once(JO_ROOT.'/jocms/core/inc/db.php');
require_once(JO_ROOT.'/jocms/core/inc/security.php');
require_once(JO_ROOT.'/jocms/core/inc/settings.php');

jo_session();


if(jo_login_verify()!= true OR $_SESSION['jocms_user'] != "admin"){
    header('Location: /jocms');
    exit;
}

$error = false;
$status = false;
$input = "";
try{

    if($_SESSION['jocms_user'] != "admin"){
      throw new Exception($JO_LANG['ERR_AUTH_ADM']);
    }

    if(isset($_POST['content']) AND isset($_POST['path'])){//after submit
        $path = rawurldecode($_POST['path']);
        $path = jo_repair_url($path);

        if(jocms_task_check($path,'loggedin')==false){
            $error = $JO_LANG['ERR_AUTH'];
        }

        $code = $_POST['content'];
        $status = "posted";
    }elseif(isset($_GET['file'])){ //before submit
        $path= rawurldecode($_GET['file']);
        $path = jo_repair_url($path);
        if($path == false){
          throw new Exception($JO_LANG['ERR_INP_URL']);
        }

        if(jocms_task_check($path,'notloggedin')==false){
            throw new Exception($JO_LANG['ERR_TASK']);
        }

        $code = file_get_contents(JO_ROOT.$path);
        if($code == ""){
            throw new Exception($JO_LANG['ERR_404_NOTF']);
        }

        if(jocms_task_insert($path,$code)==false){
            throw new Exception($JO_LANG['ERR_404_DB']);
        }

        $status = "requested";
    }else{
        throw new Exception($JO_LANG['ERR_INP']);
    }


    if($status == "posted" AND $error == false){
        jocms_history($path,$_POST['content']);
        $insert = $_POST['content'];
        $insert = htmlspecialchars_decode($insert,ENT_COMPAT | ENT_HTML5);
        $insert = iconv('UTF-8',$GLOBALS['JO_S']['charset'].'//TRANSLIT',$insert);
        file_put_contents(realpath(JO_ROOT.$path),$insert);
        jocms_task_delete($path);
        header('Location: /jocms/control/cms.php?path='.rawurlencode(htmlentities($path)));
        exit;
    }else{
      $code = iconv($GLOBALS['JO_S']['charset'],'UTF-8'.'//TRANSLIT',$code);
      $input =   $JO_LANG['CODE_FILE'].": ".htmlentities($path)."<form action=\"index.php\" method=\"post\" class=\"jo_form\">
          <textarea id='textarea' name=\"content\">".htmlspecialchars($code,ENT_COMPAT | ENT_HTML5, 'UTF-8')."</textarea>
          <input  type=\"hidden\" value=\"".htmlentities($path)."\" name=\"path\"/>
          <input  class= \"jo_btn\" type=\"submit\" value=\"".$JO_LANG['FORM_SAVE']."\"/>
          <a class= \"jo_btn\"  href=\"/jocms/control/cms.php?path=".rawurlencode(htmlentities($path))."\">".$JO_LANG['FORM_DISM']."</a>
        </form>";

      if($error == true){
          $input = "<span style='color: red;'>".$error."</span><br />".$input;
      }
    }
}catch(Exception $e){
    $input = $JO_LANG['ERR'].htmlentities($e->getMessage(),ENT_COMPAT | ENT_HTML5,'UTF-8')."<a id=\"form_cancel\" class=\"jo_btn\" href=\"/jocms/control/cms.php\">".$JO_LANG['FORM_DISM']."</a>";
}
?>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $JO_LANG['CODE']; ?> | jocms</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>

    <script src="/jocms/core/js/ui.js"></script>
    <script src="/jocms/apps/codeeditor/js/code.js" ></script>

    <script src="/jocms/apps/codeeditor/codemirror/lib/codemirror.js"></script>
    <link rel="stylesheet" href="/jocms/apps/codeeditor/codemirror/lib/codemirror.css">
    <script src="/jocms/apps/codeeditor/codemirror/mode/php/php.js"></script>
    <script src="/jocms/apps/codeeditor/codemirror/mode/xml/xml.js"></script>
    <script src="/jocms/apps/codeeditor/codemirror/mode/javascript/javascript.js"></script>
    <script src="/jocms/apps/codeeditor/codemirror/mode/htmlmixed/htmlmixed.js"></script>
    <script src="/jocms/apps/codeeditor/codemirror/mode/clike/clike.js"></script>

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
            <h1><?php echo $JO_LANG['CODE']; ?></h1>
            <?php
            echo $input;
                ?>

    	</div>
	</div>
  <script>
    var myCodeMirror = CodeMirror.fromTextArea(document.getElementById("textarea"),{
      lineNumbers: true,
      mode: "php"
    });
  </script>
</body>
</html>
