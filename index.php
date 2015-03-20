<?php
  /* INIT */
  while (ob_get_level() > 0) ob_end_flush();
  date_default_timezone_set("Asia/Jerusalem");
  mb_language("uni");
  mb_internal_encoding('UTF-8');
  setlocale(LC_ALL, 'en_US.UTF-8');
  require_once('fn.php');
  //~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~

  $content = send_main_connection();
  $content['analytics'] = send_analytics();


  /* MAIN - Content final-preparation */
  $content = true === get_arg_is_debug() ?
    json_encode($content, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG | JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
    :
    $content['content'];
  //~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~

  /* HEADERS */
  header('Content-Type: ' . get_arg_content_type() . '; charset=UTF-8');
  header('Content-Length: ' . mb_strlen($content, '8bit'));
  header('HTTP/1.1 200 OK', true, 200);
  //~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~

  /* OUTPUT */
  echo $content;
  flush();
  //~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~

  /* CLEANUP */
  unset ($content);
  die(0);
  //~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~
