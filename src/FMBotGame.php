<?php
require __DIR__."/download_posts.php";
require __DIR__."/handle_whispers.php";
require __DIR__."/handle_confirm_multi_whispers.php";
require __DIR__."/vote_count.php";
class FMBotGame
{
  public $settings; // FMBotGameSettings object
  public $players; // ARRAY - keys are username, values are FMBotPlayer objects
  public $hooks=["daystart"=>["handle_whispers_day_start","vote_count_day_start"],"dayend"=>[],"nightstart"=>[],"nightend"=>[],"update"=>["download_posts","handle_whispers","handle_confirm_multi_whispers","vote_count"]];
  use handle_whispers;
  use handle_confirm_multi_whispers;
  use download_posts;
  use vote_count;
}
?>
