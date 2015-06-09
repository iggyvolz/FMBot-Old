<?php
class phpbbRemoteApi
{
  const COOKIE_FILE="cookies.txt";
  const TIMEOUT=50;
  public $url;
  public $f;
  public $t;
  public $user;
  private $pass;
  public $num_posts;
  private $handle;
  public function __construct($url,$f,$t,$user=NULL,$pass=NULL)
  {
    list($this->url,$this->f,$this->t,$this->user,$this->pass)=[$url,$f,$t,$user,$pass];
    if($user&&$pass)
    {
      $this->login();
    }
  }
  public function login()
  {
    $handle=$this->curlrequest(sprintf("%s/ucp.php?mode=login",$this->url),["username"=>$this->user,"password"=>$this->pass,"redirect"=>"./ucp.php","mode"=>"login","login"=>"Login"]);
    $result=curl_exec($handle);
    curl_close($handle);
    return $result;
  }
  private function curlrequest($url,$params=NULL)
  {
    sleep(3);
    $handle=curl_init($url);
    curl_setopt($handle, CURLOPT_COOKIEFILE, phpbbRemoteApi::COOKIE_FILE);
    curl_setopt($handle, CURLOPT_COOKIEJAR,   phpbbRemoteApi::COOKIE_FILE);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($handle, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($handle, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($handle, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.6) Gecko/20100625 Firefox/3.6.6 (.NET CLR 3.5.30729)");
    curl_setopt($handle, CURLOPT_TIMEOUT, round(phpbbRemoteApi::TIMEOUT,0));
    curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, round(phpbbRemoteApi::TIMEOUT,0));
    if($params)
    {
      curl_setopt($handle, CURLOPT_POST, true);
      curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($params));
    }
    return $handle;
  }
  public function download_post($s)
  {
    file_put_contents("posts/$s",serialize(new phpBBPost($this->url,$this->f,$this->t,$s)));
  }
  public function download_pm($p)
  {
    file_put_contents("pms/$p",serialize(new phpBBPM($this->url,$p)));
  }
  public function get_post($s)
  {
    return unserialize(file_get_contents("posts/$s"));
  }
  public function get_pm($p)
  {
    return unserialize(file_get_contents("pms/$p"));
  }
  public function update_num_posts()
  {
    $handle=$this->curlrequest(sprintf("%s/viewtopic.php?f=%u&t=%u",$this->url,$this->f,$this->t));
    $result=curl_exec($handle);
    curl_close($handle);
    $nresult=explode(" posts",explode("</div>",explode("<div class=\"pagination\">",$result)[1])[0])[0];
    if(count(explode("<a",$nresult))>1)
    {
      $nresult=explode("<a",$nresult)[1]; // Get rid of Unread Posts if any
    }
    $this->num_posts=trim($nresult);
    return $this->num_posts+0;
  }
  public function get_unread_pm()
  {
    $handle=$this->curlrequest(sprintf("%s/ucp.php?i=pm&folder=inbox",$this->url));
    $result=curl_exec($handle);
    curl_close($handle);
    $nresult=explode("\"",explode("<a href=\"./ucp.php?i=pm&amp;mode=view&amp;f=0&amp;p=",explode("<ul class=\"topiclist cplist pmlist\">",$result)[1])[1])[0];
    return $nresult+0;
  }
  public function delete_pm()
  {

  }
  public function create_post($subject,$message)
  {
    $ihandle=$this->curlrequest(sprintf("%s/posting.php?mode=reply&f=%u&t=%u",$this->url,$this->f,$this->t));
    $iresult=curl_exec($ihandle);
    curl_close($ihandle);
    $topic_cur_post_id=explode("\"",explode("<input type=\"hidden\" name=\"topic_cur_post_id\" value=\"",$iresult)[1])[0];
    $lastclick=explode("\"",explode("<input type=\"hidden\" name=\"lastclick\" value=\"",$iresult)[1])[0];
    $creation_time=explode("\"",explode("<input type=\"hidden\" name=\"creation_time\" value=\"",$iresult)[1])[0];
    $form_token=explode("\"",explode("<input type=\"hidden\" name=\"form_token\" value=\"",$iresult)[1])[0];
    $sid=explode("\"",explode("<input type=\"hidden\" name=\"sid\" value=\"",$iresult)[1])[0];
    $forum_id=explode("\"",explode("<input type=\"hidden\" name=\"forum_id\" value=\"",$iresult)[1])[0];
    $topic_id=explode("\"",explode("<input type=\"hidden\" name=\"topic_id\" value=\"",$iresult)[1])[0];
    $handle=$this->curlrequest(true?sprintf("%s/posting.php?mode=reply&f=%u&t=%u",$this->url,$this->f,$this->t):"http://requestb.in/uyd9gvuy",["subject"=>$subject,"addbbcode20"=>"100","message"=>$message,"topic_cur_post_id"=>$topic_cur_post_id,"lastclick"=>$lastclick,"post"=>"Submit","attach_sig"=>"on","creation_time"=>$creation_time,"form_token"=>$form_token,"sid"=>$sid,"forum_id"=>$forum_id,"topic_id"=>$topic_id]);
    //sprintf("%s/posting.php?mode=reply&f=%u&t=%u",$this->url,$this->f,$this->t)
    //http://requestb.in/1fmjbdx1
    $result=curl_exec($handle);
    curl_close($handle);
    return $result;
  }
}
class phpBBPost
{
  public $f;
  public $t;
  public $s;
  public $author;
  public $time;
  public $conts;
  public function __construct($url,$f,$t,$s)
  {
    list($this->f,$this->t,$this->s)=[$f,$t,$s];
    $handle=$this->curlrequest(sprintf("%s/viewtopic.php?f=%u&t=%u&start=%u",$url,$f,$t,$s));
    $result=curl_exec($handle);
    curl_close($handle);
    //file_put_contents("result.html",$result);
    $result=preg_replace("~<blockquote(.*?)>(.*)</blockquote>~si","",' '.$result.' ',1);
    $this->author=explode("<",explode("\">",explode("<strong><a href",explode("<p class=\"author\">",$result)[1])[1])[1])[0];
    $this->time=new DateTime(explode(" </p>",explode("</strong> &raquo; ",explode("<p class=\"author\">",$result)[1])[1])[0]);
    $this->conts=strip_tags(explode("</div>",explode("<div class=\"content\">",$result)[1])[0]);
  }
  private function curlrequest($url,$params=NULL)
  {
    sleep(3);
    $handle=curl_init($url);
    curl_setopt($handle, CURLOPT_COOKIEFILE, phpbbRemoteApi::COOKIE_FILE);
    curl_setopt($handle, CURLOPT_COOKIEJAR,   phpbbRemoteApi::COOKIE_FILE);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($handle, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($handle, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($handle, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.6) Gecko/20100625 Firefox/3.6.6 (.NET CLR 3.5.30729)");
    curl_setopt($handle, CURLOPT_TIMEOUT, round(phpbbRemoteApi::TIMEOUT,0));
    curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, round(phpbbRemoteApi::TIMEOUT,0));
    if($params)
    {
      curl_setopt($handle, CURLOPT_POST, true);
      curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($params));
    }
    return $handle;
  }
}
class phpBBPM
{
  public $p;
  public $time;
  public $subject;
  public $conts;
  public function __construct($url,$p)
  {
    $this->p=$p;
    $handle=$this->curlrequest(sprintf("%s/ucp.php?i=pm&mode=view&f=0&p=%u",$url,$p));
    $result=curl_exec($handle);
    curl_close($handle);
    //file_put_contents("result.html",$result);
    $result=preg_replace("~<blockquote(.*?)>(.*)</blockquote>~si","",' '.$result.' ',1);
    $this->time=new DateTime(trim(explode("<br />",explode("</strong>",explode("<p class=\"author\">",$result)[1])[1])[0]));
    $this->subject=strip_tags(explode("</h3>",explode("<h3 class=\"first\">",$result)[1])[0]);
    $this->conts=strip_tags(explode("</div>",explode("<div class=\"content\">",$result)[1])[0]);
  }
  private function curlrequest($url,$params=NULL)
  {
    sleep(3);
    $handle=curl_init($url);
    curl_setopt($handle, CURLOPT_COOKIEFILE, phpbbRemoteApi::COOKIE_FILE);
    curl_setopt($handle, CURLOPT_COOKIEJAR,   phpbbRemoteApi::COOKIE_FILE);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($handle, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($handle, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($handle, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.6) Gecko/20100625 Firefox/3.6.6 (.NET CLR 3.5.30729)");
    curl_setopt($handle, CURLOPT_TIMEOUT, round(phpbbRemoteApi::TIMEOUT,0));
    curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, round(phpbbRemoteApi::TIMEOUT,0));
    if($params)
    {
      curl_setopt($handle, CURLOPT_POST, true);
      curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($params));
    }
    return $handle;
  }
}
