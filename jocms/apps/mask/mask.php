<?php
if (!defined('JO_ROOT')) define("JO_ROOT", realpath(__DIR__."/../../../"));
//packages
require_once(JO_ROOT."/jocms/core/inc/settings.php");
require_once(JO_ROOT."/jocms/core/inc/security.php");
require_once(JO_ROOT."/jocms/core/inc/db.php");
require_once(JO_ROOT.'/jocms/core/inc/simple_html_dom.php');

jo_session();
if(jo_login_verify() != true){
    header('Location: /jocms');
    exit;
}
if($_SESSION['jocms_user'] != 'admin'){
  header('Location: /jocms');
  exit;
}

if(isset($_POST["saved"])){
    if(isset($_POST["id"]) AND isset($_POST["name"]) AND isset($_POST["code"])){
        $code = $_POST["code"];
        $code = str_replace(array("\r\n", chr(10).chr(13), "\r", "\n", PHP_EOL, chr(10), chr(13)),'--jo:r--', $code);
        $domobject = str_get_html ($code);
        $attr = "data-jo-content";
        $mask = $domobject->find("*", 0);
        $mask->$attr = "noneditable";
        $code = str_replace("--jo:r--", PHP_EOL,  $domobject->save());
        jo_set_mask($_POST["id"], $_POST["name"], "mask", $code);
    }
}
if(isset($_GET["deleted"]) AND isset($_GET["id"])){
    jo_delete_mask($_GET["id"]);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $JO_LANG['MSK_TITL']; ?> | jocms</title>
    <script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
    <script type="text/javascript" src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="/jocms/core/js/ui.js"></script>
    <script src="/jocms/apps/mask/js/mask.js"></script>

    <script src="/jocms/apps/codeeditor/codemirror/lib/codemirror.js"></script>
    <link rel="stylesheet" href="/jocms/apps/codeeditor/codemirror/lib/codemirror.css">
    <script src="/jocms/apps/codeeditor/codemirror/mode/php/php.js"></script>
    <script src="/jocms/apps/codeeditor/codemirror/mode/xml/xml.js"></script>
    <script src="/jocms/apps/codeeditor/codemirror/mode/javascript/javascript.js"></script>
    <script src="/jocms/apps/codeeditor/codemirror/mode/htmlmixed/htmlmixed.js"></script>
    <script src="/jocms/apps/codeeditor/codemirror/mode/clike/clike.js"></script>

    <link href="/jocms/core/style/css/stylesheet.css" rel="stylesheet" type="text/css"/>
    <link href="/jocms/control/css/backend_style.css" rel="stylesheet" type="text/css"/>
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
                    <a href="/jocms/control/cms.php"  title="<?php echo $JO_LANG['CMS_FILE']; ?>"><img src="/jocms/core/style/icons/home.svg"/></a>
                    <a id="active" href="/jocms/apps/mask/mask.php"  title="<?php echo $JO_LANG['MSK_TITL']; ?>"><img src="/jocms/core/style/icons/mask.svg"/></a>
                    <?php echo $_SESSION['jocms_user'] == 'admin' ? '<a href="/jocms/control/users.php" title="'.$JO_LANG['USR'].'"><img src="/jocms/core/style/icons/users.svg" /></a>' : ''  ?>
                    <a href="/jocms/control/settings.php" title="<?php echo $JO_LANG['SET']; ?>"><img src="/jocms/core/style/icons/settings.svg"/></a>
                    <a href="/jocms/control/logout.php"  title="<?php echo $JO_LANG['BYE_TITL']; ?>"><img src="/jocms/core/style/icons/logout.svg"/></a>
                </div>
            </div>
    </div>
    </div>
	<div id="menu">
	       <div>
               <h1><?php echo $JO_LANG['MSK_TITL']; ?></h1>
               <div class="column_container">
                   <div class="settings mask_list">
                       <a class="jo_btn"><img src="/jocms/core/style/icons/plus.svg" /> <?php echo $JO_LANG['MSK_NEW'] ?></a>
                       <?php
                            foreach (jo_get_masks("all") as $mask) {
                                echo "<a data-id=".$mask['id'].">".$mask['name']."</a>";
                            }
                        ?>
                   </div>
                   <div class="settings mask_set">
                       <form action="/jocms/apps/mask/mask.php" method="post" class="jo_form">
                           <table>
                               <tr>
                                   <td>
                                       <?php echo $JO_LANG['MSK_NME'] ?>
                                   </td>
                                   <td>
                                       <input id="name" type="text" name="name" placeholder="<?php echo $JO_LANG['MSK_NEW_NME'] ?>" value=""/>
                                   </td>
                               </tr>
                               <tr>
                                   <td>
                                       <?php echo $JO_LANG['MSK_CDE'] ?>
                                   </td>
                                   <td>
                                       <textarea id="textarea" name="code"></textarea>
                                   </td>
                               </tr>
                           </table>
                           <input type="hidden" name="saved" value="true" />
                           <input id="id" type="hidden" name="id" value="0" />
                           <input type="submit" value="<?php echo $JO_LANG['FORM_OK'] ?>" /><a id="delete" class="jo_btn" href=""><?php echo $JO_LANG['FORM_DEL'] ?></a>
                       </form>
                   </div>
               </div>
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
