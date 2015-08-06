<?php
require __DIR__."/download_pms.php";
require __DIR__."/FMBotGame.php";
class FMBot
{
  public static $api;
  public static $games=[];
  use download_pms;
  public static function initialize_api($url,$user,$pass)
  {
    new phpbbRemoteApi($url,$user,$pass);
  }
}
?>
