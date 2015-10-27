<?php
//creating Event stream 
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
 
$name=strip_tags($_GET['name']);
$msg=strip_tags($_GET['msg']);
 
function sendMsg($msg) {
  echo "data: $msg" . PHP_EOL;
  ob_flush();
  flush();
}
if(!empty($name) && !empty($msg)){
 $fp = fopen("_chat.txt", 'a');  
    fwrite($fp, '<div class="chatmsg"><b>'.$name.'</b>: '.$msg.'<br/></div>'.PHP_EOL);  
    fclose($fp);  
}
 
  if(file_exists("_chat.txt") && filesize("_chat.txt") > 0){  
   $arrhtml=array_reverse(file("_chat.txt"));
   $html=$arrhtml[0];
    
  }
  if(filesize("_chat.txt") > 100){
    unlink("_chat.txt");
  }
  
 
sendMsg($html);
?>