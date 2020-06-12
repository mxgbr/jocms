<?php
if (!defined('JO_ROOT')) define("JO_ROOT", realpath(__DIR__."/../../../"));
require_once(JO_ROOT."/jocms/core/inc/security.php");

//reads settings from settings.ini
//input: -
//return: settings (array)
function jo_get_settings(){
  $settings = file_get_contents(JO_ROOT."/jocms/core/settings/settings.ini");
  $settings = parse_ini_string($settings,true);

  return $settings;
}

//rewrites settings.ini with new values if defined
//input: new settings (array)
// return: true if success
function jo_set_settings($settings){
  $old = jo_get_settings();
  $lang = $old['lang'];
  $string = "";
  foreach ($old as $key => $value) {
    $char = "";
    if(array_key_exists($key,$settings)){
      $value = $settings[$key];
      $old[$key] = $value;
    }
    if(!is_numeric($value) AND !is_bool($value)){
      $char = "\"";
    }
    $line = "\r\n".$key." = ".$char.htmlspecialchars($value).$char;
    $string = $string.$line;
  }
  if(file_put_contents(JO_ROOT."/jocms/core/settings/settings.ini",$string)== false){
    return false;
  }
  $GLOBALS['JO_S'] = $old;
  if($GLOBALS['JO_S']['lang'] != $lang){
    $GLOBALS['JO_LANG'] = jo_language($GLOBALS['JO_S']['lang']);
  }
  return true;
}

//gets language contents or default en
//input: language abbreviation, [path to lang files]
//return: language (array)
function jo_language($lang,$path = "/jocms/core/lang/"){
  $url = $path.$lang.".json";
  if(jo_repair_url($url,array("json"))==false){
    $url = "/jocms/core/lang/en.json";
  }
  $lang_set = json_decode(file_get_contents(JO_ROOT.$url), true);
  $lang_en_set = json_decode(file_get_contents(JO_ROOT.$path."en.json"), true);
  $lang_set = array_merge($lang_en_set, $lang_set);
  return $lang_set;
}
function jo_language_available(){
  return glob(JO_ROOT."/jocms/core/lang/*.json");
}
$JO_S = jo_get_settings();
$JO_LANG = jo_language($JO_S['lang']);
?>
