<?php require_once('inc.php'); ?>
<!DOCTYPE html>

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
<form action="step4.php" method="post" id="wrapper">
    <header>
        Step 3 of 4
    </header>
    <section>
        <div id="logo"><img src="/jocms/core/style/icons/logo_jocms.svg"/></div>
        <h1><?php echo $JO_LANG['I_ACC']; ?></h1>
        <?php
            if (!defined('JO_ROOT')) define("JO_ROOT", realpath(__DIR__."/../.."));
            $valid[0] = true;
            if(!isset($_GET['v'])){

                //PHP version checken
                if(version_compare(phpversion(),'5.5.0') == -1){
                    $valid[0] = false;
                    $valid[] = $JO_LANG['I_ERR_PHP'];
                }

                // SQLite 3 checken
                if (!extension_loaded('sqlite3')) {
                  $valid[0] = false;
                  $valid[] = $JO_LANG['I_ERR_SQL'];
                }

                //DB erstellen
                if($valid[0]==true){
                    try{
                        $handle = new SQLite3(JO_ROOT.'/jocms/core/database/database.db');
                        $handle-> exec('DROP TABLE IF EXISTS history');
                        $handle-> exec('DROP TABLE IF EXISTS rights');
                        $handle-> exec('DROP TABLE IF EXISTS tasks');
                        $handle-> exec('DROP TABLE IF EXISTS user');
                        $handle-> exec('DROP TABLE IF EXISTS login_attempts');
                        $handle-> exec('CREATE TABLE IF NOT EXISTS history (
                                id INTEGER NOT NULL PRIMARY KEY,
                                task varchar(255) NOT NULL,
                                user_id int(11) NOT NULL,
                                time datetime NOT NULL,
                                code text NOT NULL);');

                        $handle->exec("CREATE TABLE IF NOT EXISTS rights (
                                id INTEGER NOT NULL PRIMARY KEY,
                                user_id int(11) NOT NULL,
                                ban text NOT NULL);");

                        $handle->exec("CREATE TABLE IF NOT EXISTS tasks (
                                id INTEGER NOT NULL PRIMARY KEY,
                                task text NOT NULL,
                                user_id int(11) NOT NULL,
                                time datetime NOT NULL,
                                code text NOT NULL);");

                        $handle->exec("CREATE TABLE IF NOT EXISTS user (
                                id INTEGER NOT NULL PRIMARY KEY,
                                type varchar(255) NOT NULL,
                                mail varchar(255) NOT NULL,
                                password varchar(255) NOT NULL);");

                        $handle->exec("CREATE TABLE IF NOT EXISTS masks (
                                id INTEGER NOT NULL PRIMARY KEY,
                                name varchar(255) NOT NULL,
                                type varchar(255) NOT NULL,
                                code text NOT NULL,
                                ref INTEGER NULL);");

                       $handle->exec("CREATE TABLE IF NOT EXISTS login_attempts (
                                user_id INT(11) NOT NULL,
                                time VARCHAR(30) NOT NULL);");

                    }

                    catch (Exception $exception) {
                        $valid[0] = false;
                        $valid[] = $JO_LANG['ERR_404_DB'].$exception->getMessage();
                    }
                }
            }

            if($valid[0]==true){
              echo $JO_LANG['I_ACC_TEXT'].'<br />
              <input type="email" name="name" placeholder="'.$JO_LANG['LGN_MAIL'].'"/>
              <input type="password" name="password" placeholder="'.$JO_LANG['SET_PW_NEW'].'"/>
              <input type="password" name="password2" placeholder="'.$JO_LANG['SET_PW_NEW2'].'"/>
                  ';
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
                echo '<input type="submit" value="'.$JO_LANG['I_NEXT'].'"/>';
            }
         ?>

        <a href="step2.php?v=1"><?php echo $JO_LANG['I_BACK']; ?></a>
    </footer>
</form>
</body>
</html>
