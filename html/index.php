<?php
namespace FMBot;
require_once dirname(__DIR__) . "/vendor/autoload.php";
use htmlElement\htmlElement;
require dirname(__DIR__) . "settings.php";
$games=require "games.php";
$html=new htmlElement("html");
while($html->toggle())
{
  $head=new htmlElement("head");
  while($head->toggle())
  {
    $title=new htmlElement("title");
    while($title->toggle())
    {
      echo "FMBot Status Panel";
    }
    switch(FMBOT_JQUERY_PROVIDER):
      case "google":
        $jquery=new htmlElement("script",["src"=>"//ajax.googleapis.com/ajax/libs/jquery/"..FMBOT_JQUERY_VERSION.."/jquery.min.js"]);
        break;
      default:
        $jquery=new htmlElement("script",["src"=>FMBOT_JQUERY_URL]);
        break;
    endswitch;
    while($jquery->toggle()){}
    $script=new htmlElement("script");
    while($script->toggle())
    {
      echo file_get_contents(__DIR__ . "/script.js");
    }
  }
  $body=new htmlElement("body");
  while($body->toggle())
  {
    $tdiv=new htmlElement("div",["style"=>"height:10%;text-align:center;"]);
    while($tdiv->toggle())
    {
      $h1=new htmlElement("h1");
      while($h1->toggle())
      {
        echo "FMBot Status Panel";
      }
    }
    $ldiv=new htmlElement("div",["style"=>"width:25%;float:left;border-style: solid;border-width: 2px;height:89%;"]);
    while($ldiv->toggle())
    {
      foreach($games as $game)
      {
        $circle=get_modules_color($game->modules);
        while($circle->toggle()) {}
        $link=new htmlElement("a",["href"=>"#","id"=>$game->slug."_link","class"=>"game_link"]);
        while($link->toggle())
        {
          echo $game->name;
        }
      }
    }
    $rdiv=new htmlElement("div",["style"=>"width:74%;float:left;border-style: solid;border-width: 2px;height:89%"]);
    while($rdiv->toggle())
    {
      $idiv=new htmlElement("div");
      while($idiv->toggle())
      {
        $defaultcontents=new htmlElement("div",["class"=>"content","id"=>"defaultcontents"]);
        while($defaultcontents->toggle())
        {
          echo "Default contents";
        }
        foreach($games as $game)
        {
          $gdiv=new htmlElement("div",["class"=>"content","id"=>$game->slug."_contents"]);
          while($gdiv->toggle())
          {
            foreach($game->modules as $module=>$status)
            {
              $circle=get_item_color($status);
              while($circle->toggle()){}
              echo $module;
              $br=new htmlElement("br");
              $br->toggle();
            }
          }
        }
      }
    }
  }
}
function get_modules_color($modules)
{
  $any_true=false;
  $any_false=false;
  foreach($modules as $module_status)
  {
    if($module_status)
    {
      $any_true=true;
    }
    elseif($module_status===FALSE)
    {
      $any_false=true;
    }
    if($any_true&&$any_false)
    {
      return;
    }
  }
  return get_color(["ab"=>"green","aa"=>"yellow","ba"=>"red","bb"=>"blue"][($any_true?"a":"b").($any_false?"a":"b")]);
}
function get_item_color($status)
{
  if($status)
  {
    return get_color("green");
  }
  elseif($status===FALSE)
  {
    return get_color("red");
  }
  return get_color("blue");
}
function get_color($color,$width=15)
{
  return new htmlElement("div",["style"=>"border-radius:50%;width:${width}px;height:${width}px;background-color:${color};display:inline-block"]);
}
