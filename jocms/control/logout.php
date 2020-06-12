<?php
if (!defined('JO_ROOT')) define("JO_ROOT", realpath(__DIR__."/../../"));
require_once(JO_ROOT.'/jocms/core/inc/db.php');
require_once(JO_ROOT.'/jocms/core/inc/security.php');
require_once(JO_ROOT.'/jocms/core/inc/settings.php');
jocms_taskkiller();

jo_session_lite();
jo_session_close();
?>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $JO_LANG['BYE_TITL']; ?> - jocms</title>
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
                </div>
            </div>
    </div>
    </div>
	<div id="menu">
	<div>
    <h1><?php echo $JO_LANG['BYE']; ?></h1>
        <p><?php echo $JO_LANG['BYE_TXT']; ?> <a href="/jocms" class="jo_btn"><?php echo $JO_LANG['BYE_BACK']; ?></a></p>
	</div>
	</div>

</body>

</html>
