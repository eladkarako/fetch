<?php

  /**
   * ...../fetch/?url=google.com                            --- output is text plain of the page.
   * ...../fetch/?url=google.com&content_type=text/html     --- output is rendered by browser because of mime-type.
   * ...../fetch/?url=google.com&debug                      --- output is text + debug information.
   *
   * @author Elad Karako (icompile.eladkarako.com)
   * @link   http://icompile.eladkarako.com
   */

  while (ob_get_level() > 0) ob_end_flush();

  date_default_timezone_set("Asia/Jerusalem");
  mb_language("uni");
  mb_internal_encoding('UTF-8');
  setlocale(LC_ALL, 'en_US.UTF-8');

  header('Charset: UTF-8', true);
  header('Content-Encoding: UTF-8');
  header('Content-Type: ' . (isset($_REQUEST['content_type']) ? $_REQUEST['content_type'] : 'text/plain') . '; charset=UTF-8', true);

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
                      , "is_follow_locations"        => true
                     ]);


  $result['analytics'] = curlWrap(["url"                        => "http://www.google-analytics.com/collect",
                                   "additional_request_headers" => ["Accept"            => "*/*"
                                                                    , "Connection"      => "keep-alive"
                                                                    , "Cache-Control"   => "no-cache"
                                                                    , "Pragma"          => "no-cache"
                                                                    , "Accept-Language" => isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : "en,en-US;q=0.8"
                                                                    , "User-Agent"      => $data['useragent']
                                                                    , "Content-Type"    => "text/plain; charset=utf-8"
                                                                    , "Referer"         => "http://fetch.eladkarako.com/"
                                                                    , "Cookie"          => '_ga=GA1.2.' . $data['unique_id_v1'] . '; ']
                                   , "is_forwarded_for"         => true
                                   , "is_follow_locations"      => true
                                   , "is_post"                  => true //send as post
                                   , "post_data"                => ['v'        => 1 // ------------------------------------------------------------------- Version.
                                                                    , '_v'     => 'j23' // --------------------------------------------------------------- new versions (javascript)
                                                                    , 't'      => 'pageview' // ---------------------------------------------------------- action
                                                                    , 'dl'     => 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']  // ------------------------------------------ The landing page (for example home page http://icompile.eladkarako.com)
                                                                    , 'dp'     => 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] // ------------------------------------------ The page that will receive the pageview (for example home page http://icompile.eladkarako.com?p=200)
                                                                    , 'dh'     => 'redalert.eladkarako.com' // ------------------------------------------- domain name used. hard coded
                                                                    , 'dr'     => $data['referer'] // ---------------------------------------------------- real referer - http referrer or empty string
                                                                    , 'cs'     => $data['referer'] // ---------------------------------------------------- real referer - http referrer or empty string (the source of the visit (for example 'google') )
                                                                    , 'ul'     => $data['language_selected'] // ------------------------------------------ browser language "en" or "he_IL", etc..
                                                                    , 'de'     => 'UTF-8' // ------------------------------------------------------------- charset supported
                                                                    , 'dt'     => 'fetch.eladkarako.com' // ----------------------------------------------------- page's title
                                                                    , 'uip'    => $data['ip'] // --------------------------------------------------------- client-ip.
                                                                    , 'ua'     => $data['useragent'] // -------------------------------------------------- client user-agent.
                                                                    , 'cid'    => $data['unique_id_v1'] // ----------------------------------------------- client unique-id (uniqueId or UUIDv4)
                                                                    , 'linkid' => 'content' // ----------------------------------------------------------- flag to collect more data, and aggregate it in reports (demographic, etc..)
                                                                    , 'tid'    => 'UA-59223625-1' // ----------------------------------------------------- Google Analytics account ID (UA-98765432-1). hard coded.

                                                                    //some stuff that are not important since we can not really measure them at all (no javascript client side..)
                                                                    , 'a'      => 4998045 // ------------------------------------------------------------- ?
                                                                    , '_s'     => 1 // ------------------------------------------------------------------- ?
                                                                    , 'sd'     => '24-bit' // ------------------------------------------------------------ mock browser data : screen 24bit color
                                                                    , 'sr'     => '1366x768' // ---------------------------------------------------------- mock browser data : screen 24bit color
                                                                    , 'vp'     => '1341x397' // ---------------------------------------------------------- mock browser data : screen view-port.
                                                                    // ------------------------------------------------------------ mock browser data : screen view-port.
                                                                    , 'fl'     => '14.0 r0']
                                  ]);

  $result['analytics'] = $result['analytics']['connection']; //only keep the connection information (and only visible in "debug mode".


  //extra information, or not..
  if (isset($_REQUEST['debug']))
    echo json_encode($result, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG | JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
  else
    echo $result['content'];


  unset ($result);
  die(0);
