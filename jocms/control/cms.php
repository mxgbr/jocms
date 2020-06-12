<?php
if (!defined('JO_ROOT')) define("JO_ROOT", realpath(__DIR__."/../../"));
require_once(JO_ROOT.'/jocms/core/inc/db.php');
require_once(JO_ROOT.'/jocms/core/inc/security.php');
require_once(JO_ROOT.'/jocms/core/inc/settings.php');

jo_session();

if(jo_login_verify() != true){
  header('Location: /jocms');
  exit;
}

if(!isset($_GET['path'])){
  $path = "";
}else{
  $path = $_GET['path'];
}
 ?>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $JO_LANG['CMS']; ?> | jocms</title>
    <script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
    <script type="text/javascript" src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="/jocms/core/js/ui.js" type="text/javascript"></script>
    <script src="/jocms/control/js/cms.js" type="text/javascript"></script>
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
    <meta name="viewport" content="width=device-width" />
</head>
<body >

    <div id="navigation">
        <div id="nav_container">
            <div id="sidebar">
                <div>
                    <a id="active" href="/jocms/control/cms.php"  title="<?php echo $JO_LANG['CMS']; ?>"><img src="/jocms/core/style/icons/home.svg"/></a>
                    <?php echo $_SESSION['jocms_user'] == 'admin' ? '<a href="/jocms/apps/mask/mask.php"  title="'.$JO_LANG['MSK_TITL'].'"><img src="/jocms/core/style/icons/mask.svg"/></a>' : '' ?>
                    <?php echo $_SESSION['jocms_user'] == 'admin' ? '<a href="/jocms/control/users.php" title="'.$JO_LANG['USR'].'"><img src="/jocms/core/style/icons/users.svg" /></a>' : ''  ?>
                    <a href="/jocms/control/settings.php"  title="<?php echo $JO_LANG['SET']; ?>"><img src="/jocms/core/style/icons/settings.svg"/></a>
                    <a href="/jocms/control/logout.php"  title="<?php echo $JO_LANG['BYE_TITL']; ?>"><img src="/jocms/core/style/icons/logout.svg"/></a>
                </div>
            </div>
        <div id="explorer">
          <div id="file_explorer">
            <h1><?php echo $JO_LANG['CMS_FILE']; ?></h1>
            <?php
                //output files
		              require(JO_ROOT."/jocms/control/fileexplorer.php");
	                $iframepath = "/";
                  jo_display_dir($_SESSION["jocms_files"],"");
	                $edited = false;
	                if($path != ""){
                      $iframepath = htmlentities(strip_tags(rawurldecode($path)));
                      $edited = true;
                  }
		        ?>
		        </ul>
          </div>
          <div id="options_explorer">
            <h1><?php echo $JO_LANG['CMS_OPT']; ?></h1>
            <ul>
              <?php
                if($_SESSION['jocms_user'] == 'admin'){
                  echo '<li>
                    <a id="areaselector">'.$JO_LANG['AREA'].'</a>
                  </li>';
                }
               ?>
              <?php
                if($_SESSION['jocms_user'] == 'admin'){
                  echo '<li>
                    <a id="code">'.$JO_LANG['CODE'].'</a>
                  </li>';
                }
               ?>
              <li>
                <a id="history"><?php echo $JO_LANG['HIST']; ?></a>
              </li>
            </ul>
          </div>
        </div>
        </div>
        <div id="buttons">
            <div id="explorer_files" class="openmenu"><img src="/jocms/core/style/icons/files.svg" title="<?php echo $JO_LANG['CMS_FILE']; ?>"/></div>
            <div id="explorer_more" class="openmenu"><img src="/jocms/core/style/icons/more.svg" title="<?php echo $JO_LANG['CMS_OPT']; ?>"/></div>
        </div>
    </div>
	<div id="menu">
        <iframe id="frame" src="<?php echo $iframepath; echo "?jocms=".rand(1,1000);?>"></iframe>
	</div>
<script>

</script>
</body>
</html>
