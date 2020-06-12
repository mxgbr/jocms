<?php
if (!defined('JO_ROOT')) define("JO_ROOT", realpath(__DIR__."/../../../"));
//packages
require_once(JO_ROOT.'/jocms/core/inc/db.php');
require_once(JO_ROOT.'/jocms/core/inc/security.php');
require_once(JO_ROOT.'/jocms/core/inc/settings.php');

jo_session();


if(jo_login_verify() != true){
    header('Location: /jocms');
    exit;
}

$editing = true;
$status = false;
$input = "";
$items = "";
try{
    function hist_output($path){
        $items = jo_get_history($path);
        $output_items = [];
        if($_SESSION['jocms_user'] !== "admin"){

          foreach($items as $i){
            if($i['user_id'] != $_SESSION['jocms_id']){
              break;
            }
            $output_items[] = $i;
          }
        }else{
          $output_items = $items;
        }
        if(sizeof($output_items) == 0){
            throw new Exception($GLOBALS['JO_LANG']['ERR_404_EMPT']);
        }

        $input =   "<form action='' method='post' class=\"jo_form\"><fieldset><table>";
        foreach($output_items as $i){
            $input = $input.'<tr>
              <td>
                <input type="radio" id="'.htmlentities($i['id']).'" name="id" value="'.htmlentities($i['id']).'">
              </td>
              <td>
                <label for="'.htmlentities($i['id']).'">'.htmlentities(date("d.m.Y H:i", strtotime($i['time'].' UTC'))." UTC").'</label>
              </td>
              <td>
                '.htmlentities($i['mail']).'
              </td>
            </tr>';
        }
        $input = $input.'</table></fieldset><input class="jo_btn" type="submit" value="'.$GLOBALS['JO_LANG']['FORM_SAVE'].'"/><a class="jo_btn" href="/jocms/control/cms.php?path='.rawurlencode(htmlentities($path)).'">'.$GLOBALS['JO_LANG']['FORM_DISM'].'</a></form>';
        return $input;
    }


    if(isset($_POST['id'])){//after submit
        $id = $_POST['id'];
        if(filter_var($id,FILTER_VALIDATE_INT) === false){
            throw new Exception($JO_LANG['ERR_INP']);
        }
        $reset = jo_history_reset($id);
        $path = $reset['task'];
        $editing = jocms_task_check($path,'notloggedin');
        if($editing == true){
          if($_SESSION['jocms_id'] != $id AND $_SESSION['jocms_user'] != "admin"){
              throw new Exception($JO_LANG['ERR_AUTH_ADM']);
          }
          jocms_history($path,$reset['code']);
          file_put_contents(realpath(JO_ROOT.$path),$reset['code']);
          header('Location: /jocms/control/cms.php?path='.rawurlencode($path));
          exit;
        }else{
          $input = hist_output($path);
        }

    }elseif(isset($_GET['file'])){ //before submit
        $path= rawurldecode($_GET['file']);

        $path = jo_repair_url($path);
        if($path == false){
          throw new Exception($JO_LANG['ERR_INP_URL']);
        }

        $input = hist_output($path);

    }else{
        throw new Exception($JO_LANG['ERR_INP']);
    }

    if($editing == false){
        $input = "<span style='color: red;'>".$JO_LANG['ERR_TASK_SAVE']."</span><br />".$input;
    }


}catch(Exception $e){
    $input = $JO_LANG['ERR'].$e->getMessage()."<a id=\"form_cancel\" class=\"jo_btn\" href=\"/jocms/control/cms.php\">".$JO_LANG['FORM_DISM']."</a>";
}
?>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $JO_LANG['HIST']; ?> | jocms</title>
    <script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>

    <script src="/jocms/core/js/ui.js"></script>

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
            <h1><?php echo $JO_LANG['HIST']; ?></h1>
            <?php
            echo $input;
                ?>

    	</div>
	</div>

</body>
</html>
