#!/usr/bin/env php
<?php
require "vendor/autoload.php";
require "settings.php";
require "src/FMBot.php";
FMBot::$games["FM7"]=new FMBotGame();
FMBot::$games["FM7"]->players=["iggyvolz","Metrion","ObiWan"];
FMBot::$games["FM7"]->settings=new FMBotGameSettings();
FMBot::$games["FM7"]->settings->name="FMBot Test Game";
FMBot::$games["FM7"]->settings->slug="FMT";
FMBot::$games["FM7"]->settings->type="TEST";
FMBot::$games["FM7"]->settings->num=1;
FMBot::$games["FM7"]->settings->subnum="A";
FMBot::$games["FM7"]->settings->forum=31;
FMBot::$games["FM7"]->settings->thread=26854;
FMBot::$games["FM7"]->settings->whispers_per_player=5;
FMBot::$games["FM7"]->settings->max_whisper_length=200;
FMBot::$games["FM7"]->settings->confirm_multiple_whispers=true;
?>
