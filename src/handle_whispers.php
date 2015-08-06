<?php
trait handle_whispers
{
  public $whispersleft=[];
  public $pendingwhispers=[];
  public function handle_whispers()
  {
    foreach($this->unread_pms as $n=>$i)
    {
      $pm=$this->pms[$i];
      if(strpos($pm->subject,"[{$this->settings->slug}] Whisper to ")!==0)
      {
        continue;
      }
      unset($this->unread_pms[$n]);
      if(!isset($this->players[$pm->author]))
      {
        FMBot::$api->pm_reply($i,"Error: You are not in the game '{$this->settings->slug}'.  Message ignored.");
        continue;
      }
      if(!$this->players[$pm->author]->alive)
      {
        FMBot::$api->pm_reply($i,"Error: You are not alive in the game '{$this->settings->slug}'.  Message ignored.");
        continue;
      }
      $target=htmlentities(substr($pm->subject,14+strlen($this->settings->slug)));
      if(!isset($this->players[$target]))
      {
        FMBot::$api->pm_reply($i,"Error: Player '$target' does not exist.  Please re-send the message with a proper player.");
        continue;
      }
      if(!$this->players[$target]->alive)
      {
        FMBot::$api->pm_reply($i,"Error: Player '$target' is not alive.  Please re-send the message with a proper player.");
        continue;
      }
      $nw=ceil(strlen($pm->conts)/$this->settings->max_whisper_length); // Number of whispers used
      if($nw>1)
      {
        if(!$whispersleft[$pm->author])
        {
          FMBot::$api->pm_reply($i,"You have no whispers remaining!.");
          continue;
        }
        if($whispersleft[$pm->author]<$nw)
        {
          FMBot::$api->pm_reply($i,"Error: You only have {$whispersleft[$pm->author]} whispers left.  This whisper would require $nw whispers.  Please shorten it.");
          continue;
        }
        if($this->settings->confirm_multiple_whispers)
        {
          FMBot::$api->pm_reply($i,"This will cost you $nw whispers, and you have {$whispersleft[$pm->author]} whispers left.  If you wish to do this, please reply to this PM with the exact text: [code]CONFIRM SEND PM $i"."[/code].");
          $this->pendingwhispers[$i]=[$pm->subject,$target,$conts,$nw];
          continue;
        }
        $this->do_send_whisper($pm->subject,$target,$conts,$nw); // So that we can call this from handle_confirm_whisper
      }
    }
  }
  public function do_send_whisper($sender,$target,$conts,$nw)
  {
    $nwword=[null,""," twice"," thrice"," four times"," five times"][$nw];
    FMBot::$api->create_pm("[{$this->settings->slug}] Whisper from $sender","[quote=$sender]$conts"."[/quote]",[$target]);
    FMBot::$api->create_post($this->settings->forum,$this->settings->thread,"Whispers","[b][color={$this->settings->color}]$sender is whispering to $target$nwword![/b]");
    $this->whispersleft[$player]-=$nw;
  }
  public function handle_whispers_day_start()
  {
    $this->pendingwhispers=[];
    foreach($this->players as $player)
    {
      $this->whispersleft[$player]=$this->settings->whispers_per_player;
    }
  }
}
