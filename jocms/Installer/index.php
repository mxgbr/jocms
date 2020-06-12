<?php require_once('inc.php'); ?>
﻿<!DOCTYPE html>
<html lang="de">

<head>
    <title>Installer - jocms</title>

    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <link href="stylesheet.css" rel="stylesheet" type="text/css"/>
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
    <meta name="msapplication-TileColor" content="#BF0053">
    <meta name="msapplication-TileImage" content="/jocms/core/style/icons/favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#BF0053">


</head>

<body>
<form action="step2.php" method="post" id="wrapper">
    <header>
        Step 1 of 4
    </header>
    <section>
        <div id="logo"><img src="/jocms/core/style/icons/logo_jocms.svg"/></div>
        <h1>Please select your language!</h1>
        <?php
            $valid[0] = true;
            if($valid[0]==true){
              echo '<select name="lang">';
              foreach (jo_language_available() as $value) {
                  $value = htmlentities(basename($value, ".json"));
                  $selected = "";
                  if($value== $JO_S['lang']) $selected= "selected=\"selected\"";
                      echo "
                      <option value=\"".$value."\" ".$selected.">
                        ".strtoupper($value)."
                      </option>";
                  }
              echo '</select>';

            }else{

            }
        ?>
    </section>
    <footer>
        <?php
            if($valid[0]==true){
                echo '<input type="submit" value="'.$JO_LANG['I_NEXT'].'"/>';
            }
         ?>
    </footer>
</form>
</body>
</html>
