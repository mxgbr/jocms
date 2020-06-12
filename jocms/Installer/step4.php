<?php require_once('inc.php'); ?>
<!DOCTYPE html>
<html lang="de">

<head>
    <title>Installer - jocms</title>

    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <link href="stylesheet.css" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
    <script>
        $(document).ready(function() {
            $(window).keydown(function(event){
            if(event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
            });
        });
    </script>
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
<form action="/jocms" method="post" id="wrapper">
    <header>
        Step 4 of 4
    </header>
    <section>
        <div id="logo"><img src="/jocms/core/style/icons/logo_jocms.svg"/></div>
        <h1><?php echo $JO_LANG['I_OK'] ?></h1>
        <?php
            if (!defined('JO_ROOT')) define("JO_ROOT", realpath(__DIR__."/../.."));
            $valid[0] = true;

            if($_POST['name']=="" OR $_POST['password']=="" OR $_POST['password2']==""){
                $valid[0] =  false;
                $valid[] = $JO_LANG['SET_ERR'];
            }
            if($_POST['password'] != $_POST['password2']){
                $valid[0] =  false;
                $valid[] = $JO_LANG['SET_ERR_PW'];
            }
            if($valid[0]==true){
                try{
                    $handle = new SQLite3(JO_ROOT.'/jocms/core/database/database.db');
                    $result = $handle->query("SELECT COUNT(id) AS total FROM user WHERE mail='".$_POST['name']."'");
                    $output = $result->fetchArray();
                    if($output["total"] >0){
                        $handle->exec("UPDATE user SET password='".password_hash($_POST['password'],PASSWORD_DEFAULT)."' WHERE mail='".$_POST['name']."'");
                    }else{
                        $handle->exec("INSERT INTO user(type,mail,password) VALUES ('admin','".$_POST['name']."','".password_hash($_POST['password'],PASSWORD_DEFAULT)."')");
                    }
                }
                catch (Exception $exception) {
                    $valid[0] = false;
                    $valid[] = $JO_LANG['ERR_404_DB'].$exception->getMessage();
                }
            }
            if($valid[0]==true){
             echo $JO_LANG['I_OK_TEXT'];
            }else{
                echo '
                    <div id="error">'.$JO_LANG['ERR'].'<br />
                    ';
                foreach ($valid as $message){
                    echo $message.'<br />';
                }
                echo '</div>
                ';
            }
        ?>
    </section>
    <footer>
        <?php
            if($valid[0]==true){
                echo '<input type="submit" value="'.$JO_LANG['FORM_OK'].'"/>';
            }
         ?>

        <a href="step3.php?v=1"><?php echo $JO_LANG['I_BACK']; ?></a>
    </footer>
</form>
</body>
</html>
