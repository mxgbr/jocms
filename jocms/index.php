<?php
if (!defined('JO_ROOT')) define("JO_ROOT", realpath(__DIR__."/.."));
$message = "";
$mail = "";
require_once(JO_ROOT.'/jocms/control/login.php');
?>

<html>
<head>
<meta charset="utf-8">
<title><?php echo $JO_LANG['LGN']; ?> | jocms</title>
<meta description="Login | jocms" />
    <style>
        body{
            background-color: #BF0040;
        }
        #left{
            background-color: white;
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            min-width: 290px;
            width: 18%;
            padding: 35px;
            text-align: right;
            display: flex;
            align-items: center;
            box-shadow: #505050 0px -0px 40px;
            font-family: arial;
        }
        #left div{
            width: 100%;
        }
        #logo{
            font-size: 60px;
            font-weight: bold;
            font-family: arial;
            color: #494949;

        }
        #logo span{
            color: #BF0040;
        }
        #logo img{
            width: 200px;
        }
        @media only screen and (max-width: 450px){
            #left{
                width: 100%;
                box-sizing: border-box;
            }
        }
    </style>

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
    <link rel="icon" type="image/png" sizes="192x192"  href="/core/style/jocms/icons/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/jocms/core/style/icons/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/jocms/core/style/icons/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/jocms/core/style/icons/favicon/favicon-16x16.png">
    <link rel="manifest" href="/jocms/core/style/icons/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#BF0040">
    <meta name="msapplication-TileImage" content="/jocms/core/style/icons/favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#BF0040">
    <meta name="viewport" content="width=device-width" />
    </head>
    <body>
    <div id="left">
        <div>
            <div id="logo">
                <a href="http://jocms.rusciori.org" target="_blank" title="<?php echo $JO_LANG['JO_ADV']; ?>"><img src="/jocms/core/style/icons/logo_jocms.svg"/></a>
            </div>
            <?php echo $message;?>
            <form action="" method="post" class="jo_form">
              <fieldset>
                <input value="<?php echo htmlentities($mail);?>" id="mail" type="text" name="name" placeholder="<?php echo $JO_LANG['LGN_MAIL']; ?>"/>
                <input type="password" name="password" placeholder="<?php echo $JO_LANG['LGN_PW']; ?>"/>
                <input type="hidden" name="times" value="1"/>
              </fieldset>
                <input class="jo_btn" type="submit" value="<?php echo $JO_LANG['LGN']; ?>"/>
            </form>
        </div>

    </div>
    <script>
      document.getElementById('mail').focus();
    </script>
    </body>
    </html>
