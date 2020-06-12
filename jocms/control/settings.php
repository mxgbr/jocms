<?php
if (!defined('JO_ROOT')) define("JO_ROOT", realpath(__DIR__."/../../"));
//packages
require_once(JO_ROOT."/jocms/core/inc/settings.php");
require_once(JO_ROOT."/jocms/core/inc/security.php");

jo_session();
if(jo_login_verify() != true){
    header('Location: /jocms');
    exit;
}
$error = $JO_LANG['SET_OK'];
try{
  if(isset($_POST['type'])){
    $type = $_POST['type'];
    switch ($type) {
      case 'change_pw':
        if(!isset($_POST['pw']) OR !isset($_POST['new']) OR !isset($_POST['new2'])){
          throw new Exception($JO_LANG['SET_ERR']);
        }
        if(jocms_login_check($_SESSION['jocms_mail'], $_POST['pw']) != true){
          throw new Exception($JO_LANG['SET_ERR']);
        }
        if($_POST['new'] != $_POST['new2']){
          throw new Exception($JO_LANG['SET_ERR_PW']);
        }
        $pw = $_POST['new'];
        if(jo_set_password($_SESSION['jocms_mail'],$pw) == false){
          throw new Exception($JO_LANG['SET_ERR_PWM']);
        }
        break;
      case 'change_mail':
        if(!isset($_POST['pw']) OR !isset($_POST['new']) OR !isset($_POST['old'])){
          throw new Exception($JO_LANG['SET_ERR']);
        }
        if(filter_var($_POST['new'],FILTER_VALIDATE_EMAIL) != true OR filter_var($_POST['old'],FILTER_VALIDATE_EMAIL) != true){
          throw new Exception($JO_LANG['LGN_ERR']);
        }
        if($_SESSION['jocms_mail'] != $_POST['old']){
          throw new Exception($JO_LANG['SET_ERR_MAIL']);
        }
        if(jocms_login_check($_SESSION['jocms_mail'], $_POST['pw']) != true){
          throw new Exception($JO_LANG['SET_ERR']);
        }
        $mail = $_POST['new'];
        if(jo_set_mail($_SESSION['jocms_mail'],$mail) == false){
          throw new Exception($JO_LANG['SET_ERR_MAIM']);
        }
        $_SESSION['jocms_mail'] = $mail;
        break;
      case 'change_syst':
        if($_SESSION['jocms_user'] != 'admin'){
          throw new Exception($JO_LANG['ERR_AUTH_ADM']);
        }
        if(!isset($_POST['httpsonly'])){
          $_POST['httpsonly']= 0;
        }elseif($_POST['httpsonly'] == true){
          $_POST['httpsonly']= 1;
        }
        if(!isset($_POST['charset']) OR !isset($_POST['lang']) OR !isset($_POST['history']) OR $_POST['history']<0){
          throw new Exception($JO_LANG['SET_ERR']);
        }
        if(filter_var($_POST['history'],FILTER_VALIDATE_INT) != true OR (filter_var($_POST['httpsonly'],FILTER_VALIDATE_BOOLEAN) != true AND $_POST['httpsonly'] != false)){
          throw new Exception($JO_LANG['SET_ERR']);
        }
        $_POST['lang'] = strtolower($_POST['lang']);
        if(!jo_set_settings($_POST)){
          throw new Exception($JO_LANG['SET_ERR']);
        }
        break;
      case 'change_upl':
        if($_SESSION['jocms_user'] != 'admin'){
          throw new Exception($JO_LANG['ERR_AUTH_ADM']);
        }
        if(!isset($_POST['up_dir']) OR !isset($_POST['img_size']) OR !isset($_POST['img_quality']) OR $_POST['img_quality']<1 OR $_POST['img_quality']>100 OR $_POST['img_size']<1){
          throw new Exception($JO_LANG['SET_ERR']);
        }
        if(filter_var($_POST['img_size'],FILTER_VALIDATE_INT) != true OR filter_var($_POST['img_quality'],FILTER_VALIDATE_INT) != true){
          throw new Exception($JO_LANG['SET_ERR']);
        }
        if(!jo_set_settings($_POST)){
          throw new Exception($JO_LANG['SET_ERR']);
        }
        break;
      case 'change_ed':
        if($_SESSION['jocms_user'] != 'admin'){
          throw new Exception($JO_LANG['ERR_AUTH_ADM']);
        }
        if(!isset($_POST['ed_block'])){
          throw new Exception($JO_LANG['SET_ERR']);
        }
        if(!jo_set_settings($_POST)){
          throw new Exception($JO_LANG['SET_ERR']);
        }
        break;
      default:
        break;
    }
  }else{
    $error = "";
  }
}
catch(Exception $e){
  $error = $e->getMessage();
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $JO_LANG['SET']; ?> | jocms</title>
    <script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
    <script type="text/javascript" src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="/jocms/core/js/ui.js"></script>
    <script>
      $(document).ready(function(){
        jo_folder_toggle($('.jo_folder'));
      });
    </script>
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
                    <a href="/jocms/control/cms.php"  title="<?php echo $JO_LANG['CMS']; ?>"><img src="/jocms/core/style/icons/home.svg"/></a>
                    <?php echo $_SESSION['jocms_user'] == 'admin' ? '<a href="/jocms/apps/mask/mask.php"  title="'.$JO_LANG['MSK_TITL'].'"><img src="/jocms/core/style/icons/mask.svg"/></a>' : '' ?>
                    <?php echo $_SESSION['jocms_user'] == 'admin' ? '<a href="/jocms/control/users.php" title="'.$JO_LANG['USR'].'"><img src="/jocms/core/style/icons/users.svg" /></a>' : ''  ?>
                    <a id="active" href="/jocms/control/settings.php" title="<?php echo $JO_LANG['SET']; ?>"><img src="/jocms/core/style/icons/settings.svg"/></a>
                    <a href="/jocms/control/logout.php"  title="<?php echo $JO_LANG['BYE_TITL']; ?>"><img src="/jocms/core/style/icons/logout.svg"/></a>
                </div>
            </div>
    </div>
    </div>
	<div id="menu">
	<div>
    <h1><?php echo $JO_LANG['SET']; ?></h1>
    <p>jocms <?php echo $JO_LANG['SET_VERS']; ?> 0.8 <a href="https://jocms.net/updates/" class="jo_btn"><?php echo $JO_LANG['SET_UPD']; ?></a> <a href="mailto:feedback@jocms.net" class="jo_btn"><?php echo $JO_LANG['SET_REP']; ?></a> </p>
    <?php
    if($error != ""){
      echo '
      <div class="settings" style="background-color: #fc462a; color: white;">
        <span>'.$error.'</span>
      </div>
      ';
    }
     ?>

      <div class="settings" style="background-color: #fff187;">
          <a class="jo_folder jo_folder_active"><?php echo $JO_LANG['JO_SURV']; ?></a>
          <div>
            <?php echo $JO_LANG['JO_SURV_TXT']; ?>
          </div>
      </div>
      <?php
        if($_SESSION['jocms_user'] == 'admin'){
          echo '<div class="settings">
              <a class="jo_folder">'.$JO_LANG['SET_SYST'].'</a>
              <form action="" method="post" class="jo_form">
                <fieldset>
                  <table>
                    <tr>
                      <td>
                        '.$JO_LANG['SET_SYST_LANG'].'
                      </td>
                      <td>
                        <select name="lang">';

                            foreach (jo_language_available() as $value) {
                              $value = htmlentities(basename($value, ".json"));
                              $selected = "";
                              if($value== $JO_S['lang']) $selected= "selected=\"selected\"";
                              echo "
                              <option value=\"".$value."\" ".$selected.">
                                ".strtoupper($value)."
                              </option>";
                            }

                        echo '</select>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        '.$JO_LANG['SET_SYST_CHAR'].'
                      </td>
                      <td>
                        <input type="text" name="charset" value="'.$JO_S['charset'].'"/>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        '.$JO_LANG['SET_SYST_HIST'].'
                      </td>
                      <td>
                        <input type="number" min="1" name="history" value="'.$JO_S['history'].'" />
                      </td>
                    </tr>
                    <tr>
                      <td>
                        '.$JO_LANG['SET_SYST_HTPS'].'
                      </td>
                      <td>
                        <input type="checkbox" name="httpsonly" '.($JO_S['httpsonly'] ? "checked" : "").'/>
                      </td>
                    </tr>
                  </table>
                </fieldset>
                <input type="hidden" value="change_syst" name="type" />
                <input type="submit" value="'.$JO_LANG['FORM_OK'].'"  />
              </form>
          </div>
          <div class="settings">
              <a class="jo_folder">'.$JO_LANG['SET_UPL'].'</a>
              <form action="" method="post" class="jo_form">
                <fieldset>
                  <table>
                    <tr>
                      <td>
                        '.$JO_LANG['SET_UPL_DIR'].'
                      </td>
                      <td>
                        <input type="text" name="up_dir" value="'.$JO_S['up_dir'].'"/>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        '.$JO_LANG['SET_UPL_IMGS'].'
                      </td>
                      <td>
                        <input type="number" min="1" name="img_size" value="'.$JO_S['img_size'].'" />
                      </td>
                    </tr>
                    <tr>
                      <td>
                        '.$JO_LANG['SET_UPL_IMGQ'].'
                      </td>
                      <td>
                        <input type="number" min="1" max="100" name="img_quality" value="'.$JO_S['img_quality'].'" />
                      </td>
                    </tr>
                  </table>
                </fieldset>
                <input type="hidden" value="change_upl" name="type" />
                <input type="submit" value="'.$JO_LANG['FORM_OK'].'"  />
              </form>
          </div>
          <div class="settings">
              <a class="jo_folder">'.$JO_LANG['ED'].'</a>
              <form action="" method="post" class="jo_form">
                <fieldset>
                  <table>
                    <tr>
                      <td>
                        '.$JO_LANG['SET_ED_BLC'].'
                      </td>
                      <td>
                        <input type="text" name="ed_block" value="'.$JO_S['ed_block'].'"/>
                      </td>
                    </tr>
                  </table>
                </fieldset>
                <input type="hidden" value="change_ed" name="type" />
                <input type="submit" value="'.$JO_LANG['FORM_OK'].'"  />
              </form>
          </div>';
        }
       ?>
      <div class="settings">
          <a class="jo_folder"><?php echo $JO_LANG['SET_PW']; ?></a>
          <form action="" method="post" class="jo_form">
            <fieldset>
              <input type="password" placeholder="<?php echo $JO_LANG['SET_PW_OLD']; ?>" name="pw" />
              <input type="password" placeholder="<?php echo $JO_LANG['SET_PW_NEW']; ?>"  name="new"/>
              <input type="password" placeholder="<?php echo $JO_LANG['SET_PW_NEW2']; ?>" name="new2" />
              <input type="hidden" value="change_pw" name="type" />
            </fieldset>
            <input type="submit" value="<?php echo $JO_LANG['FORM_OK']; ?>"  />
          </form>
      </div>
      <div class="settings">
          <a class="jo_folder"><?php echo $JO_LANG['SET_MAIL']; ?></a>
          <form action="" method="post" class="jo_form">
            <fieldset>
              <input type="email" placeholder="<?php echo $JO_LANG['SET_MAIL_OLD']; ?>"  name="old"/>
              <input type="email" placeholder="<?php echo $JO_LANG['SET_MAIL_NEW']; ?>" name="new" />
              <input type="password" placeholder="<?php echo $JO_LANG['LGN_PW']; ?>"  name="pw"/>
              <input type="hidden" value="change_mail" name="type" />
            </fieldset>
            <input type="submit" value="<?php echo $JO_LANG['FORM_OK']; ?>"  />
          </form>
      </div>

	</div>
	</div>

</body>
</html>
