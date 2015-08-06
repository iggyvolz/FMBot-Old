<?php
trait download_posts
{
  public $posts=[];
  public function download_posts()
  {
    for($i=0;$i<FMBot::$api->num_posts();$i++)
    {
      if(isset($this->$posts[$i])) { continue; }
      $this->$posts[$i]=FMBot::$api->download_post($i);
    }
  }
}
