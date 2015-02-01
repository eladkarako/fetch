<?php
//----------------------------------------------------------------------------------
define('URL', 
( isset($_REQUEST['url']) ? $_REQUEST['url'] : 'about:blank' ) 
);
//----------------------------------------------------------------------------------



//----------------------------------------------------------------------------------
date_default_timezone_set("Asia/Jerusalem");
setlocale(LC_ALL, 'en_US.UTF-8');
mb_internal_encoding('UTF-8');
header('Content-Type: text/' . 
                               ( isset($_REQUEST['contenttype']) ? $_REQUEST['contenttype'] : 'plain' ) 
                                                                                                        . '; charset=utf-8');
//----------------------------------------------------------------------------------


//----------------------------------------------------------------------------------
echo @file_get_contents(
URL,
false,
stream_context_create(['http' => [
  'method'=>'GET',
  'header'=>implode('\r\n', [
              'DNT: 1'
              ,'Pragma: no-cache'
              ,'Connection: close'
              ,'Cache-Control: no-cache'
              ,'Referer: http://eladkarako.com/'
              ,'Content-Type: text/plain; charset=utf-8'
              ,'Accept-Language: en-US,en;q=0.8,he;q=0.6'
              ,'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2234.0 Safari/537.36'
            ])
]]));
//----------------------------------------------------------------------------------
