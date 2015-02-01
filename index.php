<?php
  while (ob_get_level() > 0) ob_end_flush();

  date_default_timezone_set("Asia/Jerusalem");
  mb_language("uni");
  mb_internal_encoding('UTF-8');
  setlocale(LC_ALL, 'en_US.UTF-8');

  header('Charset: UTF-8', true);
  header('Content-Encoding: UTF-8');
  header('Content-Type: text/' . (isset($_REQUEST['content_type']) ? $_REQUEST['content_type'] : 'plain') . '; charset=UTF-8', true);

  header('Access-Control-Allow-Origin: *', true, 200);



  require_once("./fn.php");


  $result = curlWrap(["url"                          => isset($_REQUEST['url']) ? $_REQUEST['url'] : 'http://eladkarako.com'
                      , "additional_request_headers" => ["Accept"            => "*/*"
                                                         , "Connection"      => "keep-alive"
                                                         , "Cache-Control"   => "no-cache"
                                                         , "Pragma"          => "no-cache"
                                                         , "Accept-Language" => "en,en-US;q=0.8"
                                                         , "User-Agent"      => "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2287.0 Safari/537.36"
                                                         , "Content-Type"    => "text/plain; charset=utf-8"
                                                         , "Referer"         => "http://fetch.eladkarako.com/"]
                      , "is_forwarded_for"           => true
                     ]);

  //extra information, or not..
  if (isset($_REQUEST['debug']))
    echo json_encode($result, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG | JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
  else
    echo $result['content'];

  unset ($result);
  die(0);
