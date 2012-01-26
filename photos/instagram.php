<?php

  $access_token='19120281.12eb222.1614fb6d1334221222d66fcd5f258fb';
  $count_images=90;
  $cache_time=60;
  $image_size='thumbnail';
  
  /**
  Output each image with HTML markup
 */
  function echoimage($val, $imagesize) {
      $image = $val["images"][$imagesize]["url"];
      $link = $val["link"];
      $tag=md5($link);
      return "<a href=\"$link\" target=\"_blank\"><img src=\"$image\"/></a>";
      }
      
  /**
  Getting the Data from the API
 */
  function getDataFromApi($token, $number){
      // Instagram API bearbeiten
      $url="https://api.instagram.com/v1/users/self/media/recent/?access_token=$token&count=$number";
      $contents = file_get_contents($url);
      return $contents;
  }
  
  /**
  Gets the Data from either the Cache or the API
 */
  function getData($token, $number, $cache_time){

      $cache_folder = "your cache path";
  
      if(!is_dir($cache_folder)){
        if(mkdir($cache_folder, 0777) == false){
          return getDataFromApi($token, $number);
        }
      }
  
      $cachefile = $cache_folder."user.cache";
  
      if (file_exists($cachefile) && time()-filemtime($cachefile)<$cache_time) {
        try{
          $contents = file_get_contents($cachefile);
        }catch(Exception $e){
          $contents = getDataFromApi($token, $number);
          file_put_contents($cachefile, $contents);
        }
  
      } else {
        $contents = getDataFromApi($token, $number);
        file_put_contents($cachefile, $contents);
      }
  
      return $contents;
  }
  
  $json = json_decode(getData($access_token, $count_images, $cache_time), true);

  echo '<div class="photosdiv">';
  foreach ($json["data"] as $value)
      echo echoimage($value, $image_size);
  echo '</div>';
  
?>