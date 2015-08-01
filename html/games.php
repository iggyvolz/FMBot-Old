<?php
namespace FMBot;
class Game
{
  public $name;
  public $modules;
  public $slug;
}
require dirname(__DIR__) . "settings.php";
$games=[];
$episode7=new Game();
$episode7->name="Episode 7";
$episode7->slug="episode7";
$episode7->modules=["Download Posts"=>get_status("(FMBot) Download Posts"),"Vote Count"=>get_status("(FMBot) Vote Count")]; // TODO somehow export this to config files.   I'll just do it manually for now
$games[]=$episode7;
return $games;
function get_status($job)
{
  if(strpos(file_get_contents(FMBOT_JENKINS_URL."/job/".str_replace(" ","%20",$job)."/api/json"),'"color":"disabled"')!==FALSE)
  {
    return NULL;
  }
  if(strpos(file_get_contents(FMBOT_JENKINS_URL."/job/".str_replace(" ","%20",$job)."/lastBuild/api/json"),'"result":"SUCCESS"')!==FALSE)
  {
    return true;
  }
  return false;
}
