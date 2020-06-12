<?php
if (!defined('JO_ROOT')) define("JO_ROOT", realpath(__DIR__."/../../../../"));
require_once(JO_ROOT."/jocms/core/inc/db.php");
require_once(JO_ROOT."/jocms/core/inc/settings.php");
require_once(JO_ROOT."/jocms/core/inc/security.php");

jo_session_lite();

try{
    if(jo_login_verify() !== true){
        throw new Exception($JO_LANG['ERR_AUTH']);
    }

    function imagecreatefromfile( $filename ) {
        if (!file_exists($filename)) {
            throw new Exception($GLOBALS['JO_LANG']['ERR_404_FILE']);
          }
        switch ( strtolower( pathinfo( $filename, PATHINFO_EXTENSION ))) {
            case 'jpeg':
            case 'jpg':
                return imagecreatefromjpeg($filename);
            break;

            case 'png':
                return imagecreatefrompng($filename);
            break;

            case 'gif':
                return imagecreatefromgif($filename);
            break;

            default:
                throw new InvalidArgumentException($GLOBALS['JO_LANG']['ERR_UPL_IMG']);
            break;
        }
    }
    function thumb($file,$extension,$size,$quality){
        $type = strtolower( pathinfo( $file, PATHINFO_EXTENSION ));

        $image_size = getimagesize($file);
        $image_size_w = $image_size[0];
        $image_size_h = $image_size[1];
        $ratio = $image_size_w / $image_size_h;

        $thumb_size_h = $thumb_size_w = min($size,max($image_size_w,$image_size_h));

        if($ratio < 1){          //hochformat
            $thumb_size_w = $thumb_size_h * $ratio;
        }else{
            $thumb_size_h = $thumb_size_w /$ratio;
        }

        $image = imagecreatefromfile($file);
        $thumb_img = imagecreatetruecolor($thumb_size_w, $thumb_size_h);
        imagecopyresampled($thumb_img, $image,
                   0, 0,
                   0, 0,
                   $thumb_size_w, $thumb_size_h,
                   $image_size_w, $image_size_h);

        if($type == "jpg" OR $type=="jpeg"){
            if($extension != ""){
                $extension = $extension.".jpg";
            }
            imagejpeg($thumb_img, $file.$extension, $quality);
        }elseif($type == "png"){
            if($extension != ""){
                $extension = $extension.".png";
            }
            $quality = round((9*$quality)/100);
            ImageAlphaBlending($thumb_img, false);
            ImageSaveAlpha($thumb_img, true);
            ImageCopyResampled($thumb_img, $image, 0, 0, 0, 0, $thumb_size_w, $thumb_size_h, $image_size_w, $image_size_h);
            imagepng($thumb_img, $file.$extension, $quality);
        }elseif($type == "gif"){
            if($extension != ""){
                $extension = $extension.".gif";
            }
            imagegif($thumb_img, $file.$extension);
        }else{
            throw new Exception($GLOBALS['JO_LANG']['ERR_UPL']);
        }
        imagedestroy($thumb_img);
    }

    //sortiern der ankommenden dateien
    function reArrayFiles(&$file_post) {

        $file_ary = array();
        if(is_array($file_post['name'])){
            $file_count = count($file_post['name']);

        }else{
            $file_count = 1;
        }

        if($file_count > 1){
            $file_keys = array_keys($file_post);
          for ($i=0; $i<$file_count; $i++) {
              foreach ($file_keys as $key) {
                  $file_ary[$i][$key] = $file_post[$key][$i];
              }
          }
        }elseif($file_count == 1){
          $file_ary[0] = $file_post;
        }else{
          throw new Exception($GLOBALS['JO_LANG']['ERR_UPL']);

        }
        return $file_ary;
    }


    //code
    if(!$_FILES['file']){
        throw new Exception($JO_LANG['ERR_UPL']);
    }

    $file_ary = reArrayFiles($_FILES['file']);
    $upload_path = $GLOBALS['JO_S']['up_dir'];

    foreach ($file_ary as $file){

        if($file['name'] !="" AND $file['error']==0){

            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            //tests filetype
            $allowed_extensions = array('png', 'jpg', 'jpeg', 'gif');
            $extension2 = 1;
            if(in_array($extension, array('png', 'jpg', 'jpeg', 'gif'))){ //for pictures
                $extension2 = getimagesize($file['tmp_name'])[2];
            }

            if(!in_array($extension, $allowed_extensions) OR $extension2<1 OR $extension2>3) {
                throw new Exception($JO_LANG['ERR_UPL_IMG']);
            }

            //tests filesize
            $max_size = 10000*1024; //10 MB
            if($file['size'] > $max_size) {
                throw new Exception($JO_LANG['ERR_UPL_SIZE']);
            }

            //clean filename
            $file['name'] = preg_replace("/[^A-Z0-9._-]/i", "_", $file["name"]);

            //$file['name'] = uniqid(mt_rand(1000000000,mt_getrandmax())).".".$extension;
            $temp_file = JO_ROOT.$upload_path."temp_upload_file.".$extension;
            $exist_file = JO_ROOT.$upload_path.$file['name'];

            move_uploaded_file($file['tmp_name'], $temp_file);
            thumb($temp_file,'',$GLOBALS['JO_S']['img_size'],$GLOBALS['JO_S']['img_quality']);

            //check if file exists already
            $exists = false;
            $equal = false;
            if(file_exists($exist_file)){
              $exists = true;
              if(filesize($exist_file) === filesize($temp_file)){
                if(md5_file($exist_file) === md5_file($temp_file)){
                    $equal = true;
                }
              }
            }

            if($equal == false){
              if($exists == true){
                  $i = 1;
                  $loop_path = JO_ROOT.$upload_path.pathinfo($exist_file)['filename'];
                  while(file_exists($loop_path."(".$i.").".$extension)){
                    $i++;
                  }

                  $exist_file = $loop_path."(".$i.").".$extension;
                  $file['name'] = pathinfo($exist_file)['basename'];

              }
              rename($temp_file,$exist_file);
              thumb($exist_file,'_thumb',150,50);
            }else{
              unlink($temp_file);
            }



            $mce_return = $upload_path.$file['name'];
            $return_files[] = $upload_path.$file['name'];
        }elseif($file['error'] == 2 OR $file['error'] == 3){
          throw new Exception($JO_LANG['ERR_UPL_SIZE']);
        }else{
          throw new Exception($JO_LANG['ERR_UPL']);

        }
    }


    $result_return["error"] = array(
      "status" => true,
      "message" => ""
    );
    $result_return['files'] = $return_files;
    $result_return['location'] = $mce_return;
    header('Content-type: application/json');
    echo json_encode($result_return);
}
catch(Exception $e){
    $result_return["error"] = array(
      "status" => false,
      "message" => $e->getMessage()
    );
    header('Content-type: application/json');
    echo json_encode($result_return);
}
?>
