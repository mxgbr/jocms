<?php
if (!defined('JO_ROOT')) define("JO_ROOT", realpath(__DIR__."/../../../"));
require_once(JO_ROOT."/jocms/core/inc/db.php");
require_once(JO_ROOT."/jocms/core/inc/settings.php");

//starts session secure way
//input: -
//return true if success
function jo_session(){
  $session_name = 'jocms_session';
  $secure = $GLOBALS['JO_S']['httpsonly'];
  $httponly = true;
  if (ini_set('session.use_only_cookies', 1) === FALSE) {
      return false;
  }
  $cookieParams = session_get_cookie_params();
  session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly);
  session_name($session_name);
  $session = session_start();
  if($session == false){
    return false;
  }
  session_regenerate_id();
  return true;
}

//starts session lite way
//input: -
//return: -
function jo_session_lite(){
  $session_name = 'jocms_session';
  session_name($session_name);
  session_start();
}

//terminates session
//input: -
//return: -
function jo_session_close(){
  $_SESSION = array();
  $params = session_get_cookie_params();
  setcookie(session_name(),
          '', time() - 42000,
          $params["path"],
          $params["domain"],
          $params["secure"],
          $params["httponly"]);
  session_unset();
  session_destroy();
}

//checks auth status and user agent
//input: -
//return: true if logged in and user agent unchanged
function jo_login_verify(){
  if($_SESSION['jocms_active'] != true){
    return false;
  }
  if ($_SESSION['jocms_agent'] != $_SERVER['HTTP_USER_AGENT']) {
    return false;
  }
  return true;
}

//verifies url (with file name)
//input: url, [allowed extensions (array)]
//return: cleaned url or false if error
function jo_repair_url($url,$allowed_extensions = array("htm","html","php")){  //tests absolute urls to existing files
  if($url==""){
      return false;
  }

  $url = rawurldecode($url);

  if($url=="undefined"){
      return false;
  }

  if(filter_var("http://www.example.com".$url,FILTER_VALIDATE_URL) === false){
    return false;
  }
  $url = parse_url($url, PHP_URL_PATH);
  if($url == false){
    return false;
  }

  if(preg_match('/^\//',$url) != true){
      return false;
  }

  if(strpos($url,'/jocms') != false){
    return false;
  }

  if(!in_array(strtolower(pathinfo( $url, PATHINFO_EXTENSION )), $allowed_extensions)){
      if(preg_match('/\/$/',$url) != true){
          $url = $url."/";
      }
      $ext_match = false;
      foreach ($allowed_extensions as $key) {
        if(file_exists(JO_ROOT.$url."index.".$key) AND is_readable(JO_ROOT.$url."index.".$key)){
          $url = $url."index.".$key;
          $ext_match = true;
          break;
        }
      }
      if($ext_match != true){
          return false;
      }
  }elseif(!file_exists(JO_ROOT.$url) OR !is_readable(JO_ROOT.$url)){
      return false;
  }
  return $url;
}

//checks if json is correct and decodes
//input: - (json by post method)
//return: decoded json (array)
function jo_json_check(){
  if(strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') != 0){
      return false;
  }

  $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
  if(strpos(strtolower($contentType), 'application/json') != 0){
      return false;
  }

  $content = trim(file_get_contents("php://input"));

  $decoded = json_decode($content, true);

  if(!is_array($decoded)){
      return false;
  }
  return $decoded;
}
