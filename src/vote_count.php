<?php
trait vote_count
{
  public $votecountmd5=null;
  public $votecountdaystart;
  public function vote_count()
  {
    $votes=[];
    $votecount=[];
    for($i=$votecountdaystart;isset($this->posts[$i]);$i++)
    {
      list($author,$conts,$time)=[$api->get_post($i)->author,$api->get_post($i)->conts,$api->get_post($i)->time];
      if(!isset($votes[$author]))
      {
        $votes[$author]=NULL;
      }
      $player_found=false;
      foreach($this->players as $player=>$data)
      {
        if(!$data->alive)
        {
          continue;
        }
        if(strpos($conts,"/vote $player")!==FALSE)
        {
          $votes[$author]=$player;
          $player_found=true;
          break;
        }
      }
      if($player_found)
      {
        continue;
      }
      if(strpos($conts,"/nolynch")!==FALSE)
      {
        $votes[$author]="No Lynch";
        continue;
      }
      if(strpos($conts,"/unvote")!==FALSE)
      {
        $votes[$author]=NULL;
        continue;
      }
    }
    foreach ($votes as $player => $vote)
    {
      if(!$vote)
      {
        continue;
      }
      if(!isset($votecount[$vote]))
      {
        $votecount[$vote]=[];
      }
      $votecount[$vote][]=$player;
    }
    $nliveplayers=0;
    foreach($this->players as $player=>$data)
    {
      if($data->alive)
      {
        $nliveplayers++;
      }
    }
    $votecounttext="[votes=".$nliveplayers."]";
    $first=true;
    foreach($votecount as $player=>$votes)
    {
      if($first)
      {
        $first=false;
      }
      else
      {
        $votecounttext.=";";
      }
      $count=count($votes);
      $vvotes=implode($votes,", ");
      $votecounttext.="$player|$count|$vvotes";
    }
    $votecounttext.="[/votes]";
    if(md5($votecounttext)==$this->votecountmd5)
    {
      return;
    }
    $api->create_post($this->settings->f,$this->settings->t,"Vote Count",$votecounttext);
    $this->votecountmd5=md5($votecounttext);
  }
  public function vote_count_day_start()
  {
    $votecountdaystart=FMBot::$api->num_posts();
  }
}
