<?php
trait handle_confirm_multi_whispers
{
  public function handle_confirm_multi_whispers()
  {
    foreach($this->unread_pms as $n=>$i)
    {
      $pm=$this->pms[$i];
      if(strpos($pm->subject,"Re: [{$this->settings->slug}] Whisper to ")!==0)
      {
        continue;
      }
      if(strpos($pm->conts,"CONFIRM SEND PM ")!==0)
      {
        continue;
      }
      $i=0+(substr($pm->conts,16));
      if(!isset($pendingwhispers[$i]))
      {
        continue;
      }
      unset($this->unread_pms[$n]);
      $this->do_send_whisper(...$pendingwhispers[$i]);
    }
  }
}
