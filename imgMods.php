<?php
session_start();  
function resize_picture ($sourceFile,$targetWidth,$target_file)
{
    $source_file_dimensions = getimagesize($sourceFile);
    
    $width = $source_file_dimensions[0];
    $height = $source_file_dimensions[1];
    
    $img_type = $source_file_dimensions[2];
    
    if($width<$height)
    {
        $aspect = $width / $targetWidth;
        $new_width = $targetWidth;
        $new_height= $height / $aspect;
    }
    else
    {
        $aspect = $height / $targetWidth;
        $new_height = $targetWidth;
        $new_width= $width / $aspect;
    }        
        
    $small = imagecreatetruecolor($new_width, $new_height);
    
    switch($img_type){
        
        case IMAGETYPE_PNG:
            $source = imagecreatefrompng($sourceFile);    
            imagecopyresampled($small, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            imagepng($small,$target_file);
            break;
        
        case IMAGETYPE_GIF:
            $source = imagecreatefromgif($sourceFile);    
            imagecopyresampled($small, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            imagegif($small,$target_file);
            break;
        
        case IMAGETYPE_JPEG:
            $source = imagecreatefromjpeg($sourceFile);    
            imagecopyresampled($small, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            imagejpeg($small,$target_file);
            break;
        
    }    
    return true;
    
}


function delete_picture($picture_name)
{  
    //$picPath = ('pics/'.$_SESSION['IDAccount'].'/768/'.$picture_name);
    if(!unlink('pics/'.$_SESSION['IDAccount'].'/768/'.$picture_name)) { return false; }
    if(!unlink('pics/'.$_SESSION['IDAccount'].'/480/'.$picture_name)) { return false; }
    if(!unlink('pics/'.$_SESSION['IDAccount'].'/320/'.$picture_name)) { return false; }
    if(!unlink('pics/'.$_SESSION['IDAccount'].'/160/'.$picture_name)) { return false; }
    
    return true;
}



function removeSpecialChars($str) {
    $replace = [
    '&lt;' => '', '&gt;' => '', '&#039;' => '', '&amp;' => '',
    '&quot;' => '', '??' => 'A', '??' => 'A', '??' => 'A', '??' => 'A', '??' => 'Ae',
    '&Auml;' => 'A', '??' => 'A', '??' => 'A', '??' => 'A', '??' => 'A', '??' => 'Ae',
    '??' => 'C', '??' => 'C', '??' => 'C', '??' => 'C', '??' => 'C', '??' => 'D', '??' => 'D',
    '??' => 'D', '??' => 'E', '??' => 'E', '??' => 'E', '??' => 'E', '??' => 'E',
    '??' => 'E', '??' => 'E', '??' => 'E', '??' => 'E', '??' => 'G', '??' => 'G',
    '??' => 'G', '??' => 'G', '??' => 'H', '??' => 'H', '??' => 'I', '??' => 'I',
    '??' => 'I', '??' => 'I', '??' => 'I', '??' => 'I', '??' => 'I', '??' => 'I',
    '??' => 'I', '??' => 'IJ', '??' => 'J', '??' => 'K', '??' => 'K', '??' => 'K',
    '??' => 'K', '??' => 'K', '??' => 'K', '??' => 'N', '??' => 'N', '??' => 'N',
    '??' => 'N', '??' => 'N', '??' => 'O', '??' => 'O', '??' => 'O', '??' => 'O',
    '??' => 'Oe', '&Ouml;' => 'Oe', '??' => 'O', '??' => 'O', '??' => 'O', '??' => 'O',
    '??' => 'OE', '??' => 'R', '??' => 'R', '??' => 'R', '??' => 'S', '??' => 's' ,'??' => 'S',
    '??' => 'S', '??' => 'S', '??' => 'S', '??' => 'T', '??' => 'T', '??' => 'T',
    '??' => 'T', '??' => 'U', '??' => 'U', '??' => 'U', '??' => 'Ue', '??' => 'U',
    '&Uuml;' => 'Ue', '??' => 'U', '??' => 'U', '??' => 'U', '??' => 'U', '??' => 'U',
    '??' => 'W', '??' => 'Y', '??' => 'Y', '??' => 'Y', '??' => 'Z', '??' => 'Z',
    '??' => 'Z', '??' => 'T', '??' => 'a', '??' => 'a', '??' => 'a', '??' => 'a',
    '??' => 'ae', '&auml;' => 'ae', '??' => 'a', '??' => 'a', '??' => 'a', '??' => 'a',
    '??' => 'ae', '??' => 'c', '??' => 'c', '??' => 'c', '??' => 'c', '??' => 'c',
    '??' => 'd', '??' => 'd', '??' => 'd', '??' => 'e', '??' => 'e', '??' => 'e',
    '??' => 'e', '??' => 'e', '??' => 'e', '??' => 'e', '??' => 'e', '??' => 'e',
    '??' => 'f', '??' => 'g', '??' => 'g', '??' => 'g', '??' => 'g', '??' => 'h',
    '??' => 'h', '??' => 'i', '??' => 'i', '??' => 'i', '??' => 'i', '??' => 'i',
    '??' => 'i', '??' => 'i', '??' => 'i', '??' => 'i', '??' => 'ij', '??' => 'j',
    '??' => 'k', '??' => 'k', '??' => 'l', '??' => 'l', '??' => 'l', '??' => 'l',
    '??' => 'l', '??' => 'n', '??' => 'n', '??' => 'n', '??' => 'n', '??' => 'n',
    '??' => 'n', '??' => 'o', '??' => 'o', '??' => 'o', '??' => 'o', '??' => 'oe',
    '&ouml;' => 'oe', '??' => 'o', '??' => 'o', '??' => 'o', '??' => 'o', '??' => 'oe',
    '??' => 'r', '??' => 'r', '??' => 'r', '??' => 's', '??' => 'u', '??' => 'u',
    '??' => 'u', '??' => 'ue', '??' => 'u', '&uuml;' => 'ue', '??' => 'u', '??' => 'u',
    '??' => 'u', '??' => 'u', '??' => 'u', '??' => 'w', '??' => 'y', '??' => 'y',
    '??' => 'y', '??' => 'z', '??' => 'z', '??' => 'z', '??' => 't', '??' => 'ss',
    '??' => 'ss', '????' => 'iy', '??' => 'A', '??' => 'B', '??' => 'V', '??' => 'G',
    '??' => 'D', '??' => 'E', '??' => 'YO', '??' => 'ZH', '??' => 'Z', '??' => 'I',
    '??' => 'Y', '??' => 'K', '??' => 'L', '??' => 'M', '??' => 'N', '??' => 'O',
    '??' => 'P', '??' => 'R', '??' => 'S', '??' => 'T', '??' => 'U', '??' => 'F',
    '??' => 'H', '??' => 'C', '??' => 'CH', '??' => 'SH', '??' => 'SCH', '??' => '',
    '??' => 'Y', '??' => '', '??' => 'E', '??' => 'YU', '??' => 'YA', '??' => 'a',
    '??' => 'b', '??' => 'v', '??' => 'g', '??' => 'd', '??' => 'e', '??' => 'yo',
    '??' => 'zh', '??' => 'z', '??' => 'i', '??' => 'y', '??' => 'k', '??' => 'l',
    '??' => 'm', '??' => 'n', '??' => 'o', '??' => 'p', '??' => 'r', '??' => 's',
    '??' => 't', '??' => 'u', '??' => 'f', '??' => 'h', '??' => 'c', '??' => 'ch',
    '??' => 'sh', '??' => 'sch', '??' => '', '??' => 'y', '??' => '', '??' => 'e',
    '??' => 'yu', '??' => 'ya'
];

$res = str_replace(array_keys($replace), $replace, $str);  

    return $res;
}

    
function pic_rotate($picture,$angle){
    //header("Content-type: image/jpeg");
    $pic_properties = getimagesize($picture);
    $pic_name = basename($picture);
    $dir_name = dirname($picture);
    //making sure that chosen file is an image
    if($pic_properties==false){
        echo "</br>error - file is not valid picture [only PNG, GIF and JPG allowed]!" ;
        return false;        
    }
    $img_type = $pic_properties[2] ;
    
    switch($img_type){
        
        case IMAGETYPE_PNG:
            $source = imagecreatefrompng($picture);                
            $rotate = imagerotate($source,$angle,0);
            imagepng($rotate,$dir_name."/".$pic_name);
            $_SESSION['msg_rotate'] = "PNG rotated!";
            break;
        
        case IMAGETYPE_GIF:
            $source = imagecreatefromgif($picture);    
            $rotate = imagerotate($source,$angle,0);
            imagegif($rotate,$dir_name."/".$pic_name);
            $_SESSION['msg_rotate'] = "GIF rotated!";
            break;
        
        case IMAGETYPE_JPEG:
            $source = imagecreatefromjpeg($picture);    
            $rotate = imagerotate($source,$angle,0);
            imagejpeg($rotate,$dir_name."/".$pic_name);
            $_SESSION['msg_rotate'] = "JPG rotated!";
            break;
        
        $_SESSION['msg_rotate'] = "no picture was rotated";
        
    }    
    return true;
    
    
}