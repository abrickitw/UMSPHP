<?php
//koristimo GD biblioteku
//koristi se za dinamičku kreaciju slika, pisana je u C-u

function image_resize($src, $dst, $width, $height, $crop=0){
  //provjera da li možemo izvući width i height, tj. da li uopće radimo sa slikom
  if(!list($w, $h) = getimagesize($src)) return "Unsupported picture type!";
  
  //strrchr- pronalazimo posljednje pojavljivanje točke i uzimamo znakove sve do kraja stringa, pa izbacujemo točku pomoću substr i sve pretvaramo u mala slova
  $type = strtolower(substr(strrchr($src,"."),1));
  if($type == 'jpeg') $type = 'jpg';
  if($type == 'psd') return "Unsupported picture type!";
  switch($type){
    //kreiranje slike iz datog URL-a
    case 'bmp': $img = imagecreatefromwbmp($src); break;
    case 'gif': $img = imagecreatefromgif($src); break;
    case 'jpg': $img = imagecreatefromjpeg($src); break;
    case 'png': $img = imagecreatefrompng($src); break;
    default : return "Unsupported picture type!";
  }

  // resize, određivanje da li se proporcionalno ili ne proporcionalno mijenja slika ($crop)
  if($crop){
    if($w < $width or $h < $height) return "Picture is too small!";
    $ratio = max($width/$w, $height/$h);
    $h = $height / $ratio;
    $x = ($w - $width / $ratio) / 2;
    $w = $width / $ratio;
  }
  else{
    if($w < $width and $h < $height) return "Picture is too small!";
    $ratio = min($width/$w, $height/$h);
    $width = $w * $ratio;
    $height = $h * $ratio;
    $x = 0;
  }
  //kreiranje nove slike s određenom širinom i visinom
  $new = imagecreatetruecolor($width, $height);

  // preserve transparency
  if($type == "gif" or $type == "png"){
    //definiranje transparentne boje- imagecolortransparent
    //definiramo potpuno prozirnu boju-->127
    imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
    //postavljanje zastavice kako bi se spremao puni raspon alfa kanala
    imagealphablending($new, false);
    imagesavealpha($new, true);
  }
  //kopiranje dijela slike s resamplingom- ukoliko se smanjuje slika resampling izbacuje višak piksela
  imagecopyresampled($new, $img, 0, 0, $x, 0, $width, $height, $w, $h);

  switch($type){
    //spremanje slike
    case 'bmp': imagewbmp($new, $dst); break;
    case 'gif': imagegif($new, $dst); break;
    case 'jpg': imagejpeg($new, $dst); break;
    case 'png': imagepng($new, $dst); break;
  }
  return true;
}
