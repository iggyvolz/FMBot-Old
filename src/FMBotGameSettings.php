<?php
class FMBotGameSettings
{
  public $name; // STRING - expanded name of the game
  public $slug; // STRING - slug of the game.  All letters in caps.  Example - FM7B, GFM1R, FM2
  public $type; // STRING - "CFM", "FMG" (FM Game), "FME" (FM Episode), "TFM", "SFM", or "GFM"
  public $num; // INT - num of episode of each type.
  public $subnum; // Subnumber of the game - for example "B" for Game 7B.  Put "A" if not applicable.
  public $forum; // INT - ID of the forum
  public $thread; // INT - topic ID of the thread
  public $whispers_per_player; // INT - # of whispers per player per day
  public $max_whisper_length; // INT - maximum # of chars that a single whisper can be
  public $confirm_multiple_whispers; // BOOL - ask for confirmation before sending multiple whispers
}
?>
