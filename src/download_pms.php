<?php
trait download_pms
{
  public static $pms=[];
  public static $unread_pms=[];
  public static function download_pms()
  {
    while($n=self::$api->get_unread_pm())
    {
      $pms[$n]=self::$api->download_pm($n);
      $unread_pms[]=$n;
      self::$api->delete_pm($n);
    }
  }
}
