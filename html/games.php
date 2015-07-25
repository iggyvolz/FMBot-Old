<?php
namespace FMBot;
class Game
{
  public $name;
  public $modules;
  public $slug;
}
define("JENKINS_URL","http://192.99.42.178:8080");
$games=[];
$episode7=new Game();
$episode7->name="Episode 7";
$episode7->slug="episode7";
$episode7->modules=["Download Posts"=>get_status("(FMBot) Download Posts"),"Vote Count"=>get_status("(FMBot) Vote Count")];
$games[]=$episode7;
return $games;
function get_status($job)
{
  if(strpos(file_get_contents(JENKINS_URL."/job/".str_replace(" ","%20",$job)."/api/json"),'"color":"disabled"')!==FALSE)
  {
    return NULL;
  }
  if(strpos(file_get_contents(JENKINS_URL."/job/".str_replace(" ","%20",$job)."/lastBuild/api/json"),'"result":"SUCCESS"')!==FALSE)
  {
    return true;
  }
  return false;
}
