<?php

  /**
   * ...../fetch/?url=google.com                            --- output is text plain of the page.
   * ...../fetch/?url=google.com&content_type=text/html     --- output is rendered by browser because of mime-type.
   * ...../fetch/?url=google.com&debug                      --- output is text + debug information.
   *
   * @author Elad Karako (icompile.eladkarako.com)
   * @link   http://icompile.eladkarako.com
   */

  error_reporting(E_STRICT);

  $data = collectData($data); //update data with

  //  define('PROXY_URL', !hasArg('proxy_url_override') ? 'http://193.43.245.165:80' : filter_var($_REQUEST['proxy_url_override'], FILTER_SANITIZE_URL));

  define('IP_CLIENT', isset($data['ip']) ? $data['ip'] : '');
  define('IP_SERVER', isset($data['ipserver']) ? $data['ipserver'] : '');
  //  define('IP_PROXY_URL', ('' !== PROXY_URL) ? gethostbyname(parse_url(PROXY_URL, PHP_URL_HOST)) : '');


  //

  //=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-


  /**
   * @param array $aArray1
   * @param array $aArray2
   *
   * @return array
   */
  function arrayRecursiveDiff($aArray1, $aArray2) {
    $aReturn = [];

    foreach ($aArray1 as $mKey => $mValue) {
      if (array_key_exists($mKey, $aArray2)) {
        if (is_array($mValue)) {
          $aRecursiveDiff = arrayRecursiveDiff($mValue, $aArray2[ $mKey ]);
          if (count($aRecursiveDiff)) {
            $aReturn[ $mKey ] = $aRecursiveDiff;
          }
        }
        else {
          if ($mValue != $aArray2[ $mKey ]) {
            $aReturn[ $mKey ] = $mValue;
          }
        }
      }
      else {
        $aReturn[ $mKey ] = $mValue;
      }
    }

    return $aReturn;
  }

  /**
   * @param array $aArray1
   * @param array $aArray2
   *
   * @return array
   */
  function arrayRecursiveDiffBeforeAfter($aArray1, $aArray2) {
    $aReturn = [];

    foreach ($aArray1 as $mKey => $mValue) {
      if (array_key_exists($mKey, $aArray2)) {
        if (is_array($mValue)) {
          $aRecursiveDiff = arrayRecursiveDiffBeforeAfter($mValue, $aArray2[ $mKey ]);
          if (count($aRecursiveDiff)) {
            $aReturn[ $mKey ] = $aRecursiveDiff;
          }
        }
        else {
          if ($mValue != $aArray2[ $mKey ]) {
            $aReturn[ $mKey ] = ["from" => $aArray2[ $mKey ], "to" => $mValue];
          }
        }
      }
      else {
        $aReturn[ $mKey ] = ["+added" => $mValue];

      }
    }

    return $aReturn;
  }

  /**
   * for GoDaddy.com Servers
   * - enable var_dump if disabled (in production mode without your request..)
   * - re-enable colors, as in generating HTML output, make sure that you use mime-type text/html ("Content-Type"
   * header).
   * - disable remote debugger and profiler, as by default from WAMP settings.
   * - enable larger data showing.
   */
  function setXDebugVARDUMP() {
    //re-enable var_dump
    ini_set('xdebug.overload_var_dump', true); // enable var_dump

    //re-enable colors for HTML PHP pages.
    ini_set('xdebug.default_enable', true); // enable colors #1
    ini_set('html_errors', true); // enable colors #1

    //re-enable colors for Terminal/Putty/Telnet using Escape-Characters - (Client-Mode such as php.exe)
    //ini_set('xdebug.cli_color', 1); //2

    //Type and number of elements, for example "string(6)" or "array(8)", with a tool tip for the full information.
    ini_set('xdebug.collect_params', 2);

    //default from WAMP (windows)
    ini_set('xdebug.remote_enable', 'off');
    ini_set('xdebug.profiler_enable', 'off');
    ini_set('xdebug.profiler_enable_trigger', 'off');
    ini_set('xdebug.profiler_output_name', 'cachegrind.out.%t.%p');

    //enable larger data showing
    ini_set('xdebug.var_display_max_children', -1);
    ini_set('xdebug.var_display_max_data', -1);
    ini_set('xdebug.var_display_max_depth', -1);
  }


  /**
   * @param array  $associative_array
   * @param string $key_value_separator
   *
   * @return array
   */
  function parse_associative_array_to_flat_array($associative_array, $key_value_separator = ": ") {
    $flat_array = [];

    $keys = array_keys($associative_array);
    $values = array_values($associative_array);
    foreach ($keys as $i => $key) {
      array_push($flat_array, $key . $key_value_separator . $values[ $i ]);
    }

    return $flat_array;
  }


  /**
   * @param array  $lines
   * @param string $key_value_separator
   *
   * @return array
   */
  function parse_flat_array_to_associative_array($lines, $key_value_separator = ": ") {
    $associative_array = [];

    foreach ($lines as $i => $line) {
      if ('' === $line) {
        continue;
      } // ------------------------------- skip empty lines.


      $parts = explode($key_value_separator, $line);
      if (isset($parts[1])) {
        $associative_array[ $parts[0] ] = $parts[1]; // ---------------------- "header name" => header value
      }
      else {
        $associative_array[ $i ] = $line; // --------------------------------- "header index" => whole line
      }
    }

    return $associative_array;
  }


  /**
   * @param string $string
   * @param string $lineSeparator
   *
   * @return array
   */
  function parse_string_to_flat_array($string, $lineSeparator = "\r\n") {
    if ('' === $string) {
      return [];
    }

    return explode($lineSeparator, $string); // ------------------------- break lines
  }


  /**
   * @param array  $array
   * @param string $lineSeparator
   *
   * @return string
   */
  function parse_flat_array_to_string($array, $lineSeparator = "\r\n") {
    if (empty($array)) {
      return '';
    }

    return implode($lineSeparator, $array); // ------------------------- join lines
  }


  /**
   * @param array $array
   *
   * @return array
   */
  function array_clone($array) {
    return array_merge_recursive([], $array);
  }


  /**
   * @param array $array - associative array (by-reference), to modify its value according to key and value
   * @param mixed $key
   * @param mixed $value
   *
   * @return mixed the same value
   */
  function set_and_return(&$array, $key, $value) {
    $array[ $key ] = $value;

    return $value;
  }


  /**
   * @param       $conditional - if true 'set' action will be done otherwise it will just quit.
   * @param array $array       - associative array (by-reference), to modify its value according to key and value
   * @param mixed $key
   * @param mixed $value
   *
   * @return bool
   */
  function conditional_set($conditional, &$array, $key, $value) {
    if ($conditional === true) {
      $array[ $key ] = $value;

      return true;
    }

    return false;
  }


  /**
   * @param  array $args an associative array with the arguments for CURL-OPTS and HTTP-Headers.
   *
   * @return array
   */
  function get_curl_opts(&$args) {
    //---------------------------------------------------------------------- get values, on non-existing, use (and set) default.

    $url = isset($args['url']) ?
      $args['url'] : set_and_return($args, 'additional_request_headers', []);

    $additional_request_headers = isset($args['additional_request_headers']) ?
      $args['additional_request_headers'] : set_and_return($args, 'additional_request_headers', []);

    $timeout = isset($args['timeout']) ?
      $args['timeout'] : set_and_return($args, 'timeout', 200);

    $is_no_cache = isset($args['is_no_cache']) ?
      $args['is_no_cache'] : set_and_return($args, 'is_no_cache', true);

    $is_follow_locations = isset($args['is_follow_locations']) ?
      $args['is_follow_locations'] : set_and_return($args, 'is_follow_locations', false);

    $is_head = isset($args['is_head']) ?
      $args['is_head'] : set_and_return($args, 'is_head', false);

    $is_post = isset($args['is_post']) ?
      $args['is_post'] : set_and_return($args, 'is_post', false);

    $post_data = isset($args['post_data']) ?
      $args['post_data'] : set_and_return($args, 'post_data', []);

    $is_forwarded_for = isset($args['is_forwarded_for']) ?
      $args['is_forwarded_for'] : set_and_return($args, 'is_forwarded_for', false);

    $is_auto_set_host_header = isset($args['is_auto_set_host_header']) ?
      $args['is_auto_set_host_header'] : set_and_return($args, 'is_auto_set_host_header', false);

    $is_url_to_ip_first = isset($args['is_url_to_ip_first']) ?
      $args['is_url_to_ip_first'] : set_and_return($args, 'is_url_to_ip_first', false);
    //------------------------------------------------------------------------------------------------

    $opts = [
      CURLOPT_URL            => $url, // -------------------------------------------- set full target URL.
      CURLOPT_CONNECTTIMEOUT => $timeout, // ---------------------------------------- timeout on connect, in seconds
      CURLOPT_TIMEOUT        => $timeout, // ---------------------------------------- timeout on response, in seconds
      CURLOPT_BUFFERSIZE     => 2048, // -------------------------------------------- smaller buffer-size for proxies.
      CURLOPT_HEADER         => true, // -------------------------------------------- return headers too
      CURLINFO_HEADER_OUT    => true, // -------------------------------------------- to use $rh = curl_getinfo($ch); var_dump($rh['request_header']);
      CURLOPT_RETURNTRANSFER => true, // -------------------------------------------- return as string
      CURLOPT_FAILONERROR    => true, // -------------------------------------------- don't fetch error-page's content (500, 403, 404 pages etc..)
      CURLOPT_SSL_VERIFYHOST => false, // ------------------------------------------- don't verify ssl
      CURLOPT_SSL_VERIFYPEER => false, // ------------------------------------------- don't verify ssl
      CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4, // ------------------------------- force IPv4 (instead of IPv6)
      CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1 // ---------------------------- force HTTP 1.1
    ];


    //---------------------------------------------------------------------------------------------
    $url_host_part = parse_url($url, PHP_URL_HOST); // ---------------- extract HOST from URL.
    $url_host_part_ip = gethostbyname($url_host_part); // ------------- resolve HOST to IP.
    //---------------------------------------------------------------------------------------------


    if ($is_auto_set_host_header) {
      $additional_request_headers['Host'] = $url_host_part; // ---------- fill the "Host" Header, must on HTTP 1.1 otherwise you'll get HTTP error 400.
    }


    if ($is_url_to_ip_first) { // ------------- don't use CURL to resolve host to IP, do it in PHP area..
      $url = mb_eregi_replace($url_host_part, $url_host_part_ip, $url); // -------- better then 'str_replace' --- reconstruct URL with IP as HOST. //www.oref.org.il (===>>> a1254.b.akamai.net) ===>>> 212.143.162.210
      $settings[ CURLOPT_DNS_USE_GLOBAL_CACHE ] = false; //do not try to resolve HOST to IP
      $settings[ CURLOPT_DNS_CACHE_TIMEOUT ] = 1; //practically no DNS cache
    }


    if (true === $is_follow_locations) {
      $opts[ CURLOPT_FOLLOWLOCATION ] = true;
      $opts[ CURLOPT_MAXREDIRS ] = 5;
    }

    if (true === $is_no_cache) {
      $url .= (false === mb_strpos($url, '?') ? '?' : '&') . uniqid('__=rnd', true);
      $opts[ CURLOPT_URL ] = $url;
    }

    if (true === $is_post) {
      $opts[ CURLOPT_POST ] = true;
      $opts[ CURLOPT_POSTFIELDS ] = utf8_encode(http_build_query($post_data));
      $additional_request_headers['Content-Type'] = "application/x-www-form-urlencoded; charset=UTF-8"; //(associative array) override with POST content-type
    }
    else if ($is_head === true) {
      $opts[ CURLOPT_CUSTOMREQUEST ] = 'HEAD';
      $opts[ CURLOPT_NOBODY ] = true;
    }
    else {
      //a regular GET request...
    }


    if (true === $is_forwarded_for) {
      $header_value = "";

      if ('' !== IP_CLIENT) { //fill what is available
        $header_value .= IP_CLIENT;
        if ('' !== IP_SERVER)
          if (IP_SERVER !== IP_CLIENT)
            $header_value .= ', ' . IP_SERVER;
      }


      if ('' !== $header_value) {
        $additional_request_headers["X-Forwarded-For"] = $header_value;
      }

      if ('' !== IP_SERVER) { //additional headers similar to X-Forwarded-For (even if its used for emails..)
        $additional_request_headers["X-Forwarded-Server"] = 'fetch.eladkarako.com';

      }

      if ('' !== IP_CLIENT) { //additional headers similar to X-Forwarded-For (even if its used for emails..)
        $additional_request_headers["X-Originating-IP"] = '[' . IP_CLIENT . ']';
        $additional_request_headers["X-Forwarded-Host"] = trim(array_slice(explode(',', IP_CLIENT), 0, 1));
      }

    }


    $additional_request_headers = parse_associative_array_to_flat_array($additional_request_headers);
    $opts[ CURLOPT_HTTPHEADER ] = $additional_request_headers;


    return $opts;
  }


  /**
   * @param string $content
   *
   * @return mixed
   */
  function add_commas($content) {
    return preg_replace_callback('/(\d)(?=(\d{3})+$)/', function ($matches) {
      return isset($matches[0]) ? "$matches[0]," : "";
    }, $content);
  }


  /**
   * checks if the content is base64
   *
   * @param {string} $str
   *
   * @return bool
   */
  function is_base64($str) {
    if ('localhost' === mb_strtolower($str)) return false; //some special case cut-out.
    if ($str !== preg_replace("#[^a-z0-9\+\=]#i", '', $str)) return false; //string contains characters other then BASE64.
    if (0 !== mb_strlen($str) % 4) return false; //number of letter is not divided by 4.
    if ($str !== filter_var(filter_var($str, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_STRIP_LOW), FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_STRIP_HIGH)) return false; //base64 should be ASCII


    return true;
//    $debased64 = $str;
//    $debased64 = base64_decode($debased64);
//    $debased64 = filter_var($debased64, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_STRIP_LOW);
//    $debased64 = filter_var($debased64, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_STRIP_HIGH);
//    return $str = $debased64;
  }

  /**
   * fetching-url may be base64 encoded
   *
   * @return string
   */
  function get_arg_url() {

    $url = filter_input(INPUT_GET, 'url');
    $url = (null === $url || "" === trim($url)) ? 'https://www.example.com' : $url;

    //de-base64 ?
    $url = is_base64($url) ? base64_decode($url) : $url;

    $url = filter_var($url, FILTER_SANITIZE_URL);

    return $url;
  }

  function get_arg_is_debug() {

    $is_debug = filter_input(INPUT_GET, 'debug');
    $is_debug = null !== $is_debug && "false" !== mb_strtolower($is_debug);


    return $is_debug;
  }

  /**
   * @return string
   */
  function get_arg_content_type() {
    $content_type = filter_input(INPUT_GET, 'content_type');
    $content_type = null === $content_type ? "text/plain" : preg_replace("#[^a-z0-9\.\-\/\+\=]#i", '', $content_type);

    return $content_type;
  }

  /**
   * @param float $size                - the memory size to format
   * @param bool  $is_add_commas       (optional) - separate every 3 digits from the end so 56789 will be "56,789".
   * @param bool  $is_full_description (optional) - use *Bytes instead of *b (Gigabytes instead of gb, etc...).
   * @param int   $digits              (optional) - number of digits decimal point, to limit, or -1 to not use padding
   *                                   or limiting.
   *
   * @return string
   */
  function human_readable_memory_sizes($size, $is_add_commas = false, $is_full_description = false, $digits = -1) {
    $unit = ['b', 'kb', 'mb', 'gb', 'tb', 'pb'];
    $unit_full = ['Bytes', 'KiloByte', 'MegaBytes', 'GigaBytes', 'TeraBytes', 'PetaBytes'];

    $out = $size / pow(1024, ($i = ((int)floor(log($size, 1024)))));

    $out = (-1 !== $digits) ? sprintf("%." . $digits . "f", $out) : $out;

    $out = $is_add_commas ? add_commas($out) : $out;

    $out .= ' ' . (!$is_full_description ? $unit[ $i ] : $unit_full[ $i ]);

    return $out;
  }


  /**
   * @param mixed $var
   * @param bool  $is_beautified (optional)
   *
   * @return mixed|null|string
   */
  function size_of_var($var, $is_beautified = false) {
    $holder = null;
    $collection = [];

    //collect memory data for better averaging the results
    for ($i = 0; $i < 100; $i += 1) {
      $memory_before = memory_get_usage();
      $tmp = unserialize(serialize($var));
      usleep(5 * 1000); //5 milliseconds
      array_push($collection, (memory_get_usage() - $memory_before - (PHP_INT_SIZE * 8)));

      unset($tmp);
      usleep(2 * 1000); //5 milliseconds
    }

    //sum the relative result to the 100 total experiment.
    $holder = array_reduce($collection, function ($previous_return_result, $current_item) {
      return $previous_return_result + ($current_item / 100);
    });

    $holder = $is_beautified ? human_readable_memory_sizes($holder) : $holder;

    return $holder;
  }


  /**
   * @param array $args - associative array of arguments and headers to be used by the function.
   *
   * @return array
   */
  function curlWrap($args) {
    $args_initial = array_clone($args);
    $url = $args['url'];

    $ch = curl_init();
    curl_setopt_array($ch, get_curl_opts($args));

    $response_headers_and_content = curl_exec($ch);
    $info = curl_getinfo($ch);
    $err_num = curl_errno($ch);
    $err_str = curl_error($ch);
    curl_close($ch);

    $headers_request = parse_flat_array_to_associative_array(parse_string_to_flat_array($info['request_header']));
    unset($info['request_header']);

    $response_headers_and_content = explode("\r\n\r\n", $response_headers_and_content); //response(s)-headers and content are \r\n\r\n separated (last is content).
    $content = array_pop($response_headers_and_content);
    $headers_response = $response_headers_and_content; //might be more then one item, due to redirection(s)
    unset($response_headers_and_content); //we have '$headers_response' and '$content'.

    $headers_response = array_map(function ($item) {
      return parse_flat_array_to_associative_array(parse_string_to_flat_array($item));
    }, $headers_response);

    $headers_response_is_real = true;
    if (true === empty($headers_response)) {
      $headers_response = get_headers($url, true);
      $headers_response_is_real = false;
    }


    return [
      "content"                      => $content
      //      , "content_base64"             => base64_encode($content)
      //      , "content_serialize"          => serialize($content)

      , "connection"                 => [
        "headers" => [
          "request"            => $headers_request
          , "response"         => $headers_response
          , "is_response_real" => $headers_response_is_real
        ]
        , "info"  => $info,
        "errors"  => [
          "number"      => $err_num,
          "description" => $err_str
        ]
      ]
      , "function_calling_arguments" => [
        "changes"      => arrayRecursiveDiffBeforeAfter($args, $args_initial),
        "initial"      => $args_initial,
        "after_change" => $args
      ]
    ];
  }


  /**
   * @param array $previous_data
   *
   * @return array
   */
  function collectData($previous_data) {
    $too_frequent_request_rate_in_seconds = 0.95;
    $default_user_agent = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36';
    $default_ip = '127.0.0.1';
    $default_ip_server = '127.0.0.1';
    //----------------------------------------------------------

    $data = array_clone($previous_data); //make a copy of the previous data, to no mess with references..

    //----------------------------------------------------------------------------------------------------- session friendly


    $data['unique_id_v1'] = isset($data['unique_id_v1']) ? $data['unique_id_v1'] : uniqid('', true);
    $data['unique_id_v4'] = isset($data['unique_id_v4']) ? $data['unique_id_v4'] : sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));


    $data['request_time_prev'] = isset($data['request_time_now']) ? $data['request_time_now'] : 0; // ---- take previous time if available.
    $data['request_time_now'] = isset($_SERVER['REQUEST_TIME_FLOAT']) ? $_SERVER['REQUEST_TIME_FLOAT'] : ($too_frequent_request_rate_in_seconds + 1); //if not exist last request, use minimum allows + 1 second to be sure
    $data['request_time_diff'] = $data['request_time_now'] - $data['request_time_prev']; // -------------- * re-calculate new
    $data['request_time_minimal_rate_per_second'] = $too_frequent_request_rate_in_seconds;


    $data['requests']['this_page']['content'] = "-in-progress-";
    $data['requests']['this_page']['headers']['request'] = isset($data['requests']['this_page']['headers']['request']) ?
      $data['requests']['this_page']['headers']['request'] : getallheaders();

    $data['requests']['this_page']['response'] = "-in-progress-";
    $data['requests']['this_page']['is_original_response_headers'] = true;


    $data['language'] = isset($data['language']) ? $data['language'] : call_user_func(function () {
      $lngHeader = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : 'en';
      $lngHeader = explode(',', $lngHeader);

      $lngData = [];

      foreach ($lngHeader as $lngItem) {
        $lngItemName = preg_replace('/([^;]+);.*$/', '${1}', $lngItem); //extract actual language string

        $lngItemValue = preg_replace('/^[^q]*q=([^\,]+)*$/', '${1}', $lngItem); //extract evaluation as string
        $lngItemValue = is_numeric($lngItemValue) ? floatval($lngItemValue) : 1.0; //parse evaluation, if no q=__ (first language is like "1"), then we will use 1.0

        $lngData[ $lngItemName ] = $lngItemValue; //store this one item
      }

      array_multisort($lngData, SORT_DESC, SORT_NUMERIC); // SORT_NATURAL   higher first
      return $lngData;
    });

    $data['language_selected'] = isset($data['language_selected']) ? $data['language_selected'] : call_user_func_array(function ($languages) {
      $keys = array_keys($languages);

      return isset($keys[0]) ? $keys[0] : '';
    }, [$data['language']]);


    //----------------------------------------------------------------------------------------------------- fresh each request


    $data['referer'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
//
//    $data['url_canonise'] = call_user_func(function () {
//
//
//      $url = [];
//      //---------------------------------------- part #2 first : what are our url-args ?
//      if (hasArg('beautify')) {
//        array_push($url, 'beautify');
//      }
//
//      if (hasArg('callback')) {
//        array_push($url, 'callback');
//      }
//
//      if (hasArg('debug')) {
//        array_push($url, 'debug');
//      }
//
//      if (hasArg('nounicode')) {
//        array_push($url, 'nounicode');
//      }
//
//      natsort($url); //so same order will kept every time.
//      $url = (count($url) > 0) ? ('?' . implode("&", $url)) : ""; //if there are url-args use ?key1&key2&... if not don't use '?'
//
//      //---------------------------------------- part #1. the relative url as prefix.
//      $url = strtolower($_SERVER['SCRIPT_NAME']) . $url;
//
//      return $url;
//    });

//    $data['url_canonise_full'] = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $data['url_canonise'];

    $data['useragent'] = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : $default_user_agent;

    $data['ipserver'] = call_user_func_array(function ($default_ip_server) { //the host-IP of the php script.
      $ipserver = $default_ip_server;

      //get data
      if (isset($_SERVER['SERVER_ADDR']) && !empty($_SERVER['SERVER_ADDR'])) {
        $ipserver = $_SERVER['SERVER_ADDR'];
      }
      else {
        if (isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR'])) {
          $ipserver = $_SERVER['REMOTE_ADDR'];
        }
      }

      //if list given take the right one (last)
      $ipserver = str_replace(" ", "", $ipserver); //remove whitespaces.
      $ipserver = explode(',', $ipserver);
      $ipserver = array_pop($ipserver);

      $ipserver = '::1' === $ipserver ? $default_ip_server : $ipserver; //fix localhost-IPv6 to localhost-IPv4.
      $ipserver = filter_var($ipserver, FILTER_VALIDATE_IP) ? $ipserver : $default_ip_server; //validate it looks like IP.

      return $ipserver;
    }, [$default_ip_server]);

    /**
     * store user ip, pay importance to X-Forwarded-For
     */
    $data['ip'] = call_user_func_array(function ($default_ip) {
      $ip = $default_ip;

      //ordered by effectiveness, X-Forward-For, Remote-Address, Client-IP
      if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
      }
      else {
        if (isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR'])) {
          $ip = $_SERVER['REMOTE_ADDR'];
        }
        else {
          if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
          }
        }
      }

      //if list given take the left one (first)
      $ip = str_replace(" ", "", $ip); //remove whitespaces.
      $ip = explode(',', $ip);
      $ip = array_shift($ip);

      $ip = '::1' === $ip ? '127.0.0.1' : $ip; //fix localhost-IPv6 to localhost-IPv4.
      $ip = '127.0.0.1' === $ip ? $default_ip : $ip; //ignore localhost and put generic ip instead.
      $ip = filter_var($ip, FILTER_VALIDATE_IP) ? $ip : $default_ip; //validate it looks like IP.

      return $ip;
    }, [$default_ip]);


    //-----------
    //-----------
    return $data;
    //-----------
    //-----------
  }


  /**
   * look for keys exist in $_SERVER
   *
   * @param string $toFind
   *
   * @return bool
   */
  function hasArg($toFind) {
    $query_string = isset($_SERVER['QUERY_STRING']) ? mb_strtolower($_SERVER['QUERY_STRING']) : '';

    return (
      (false !== mb_stripos('?' . $query_string, $toFind)) ||
      (false !== mb_stripos('&' . $query_string, $toFind))
    );

  }


  /**
   * @param string $unique_id (optional) - also return _ga cookie with this value.
   *
   * @return array
   */
  function getRequestHeaders($unique_id = '') {
    return [
      "Accept"           => "*/*",
      "Connection"       => "keep-alive",
      "DNT"              => "1",
      "Cache-Control"    => "no-cache",
      "Pragma"           => "no-cache",
      "X-Requested-With" => "XMLHttpRequest",
      "Accept-Language"  => "en,en-US;q=0.8,he;q=0.6",
      "Cookie"           => "_ga=GA1.3." . ('' !== $unique_id ? $unique_id : uniqid('', true)) . "; __atuvc=1%7C30; pakar_last_warning_id=1406195405000;",
      "User-Agent"       => "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36"
    ];
  }


  /**
   * @param string $content_raw
   *
   * @return array
   */
  function encoding_investigating($content_raw) {
    $content = $content_raw;

    $encoding_match_readable = [];
    $encoding_match_not_readable = [];
    $encoding_not_match = [];
    $hebrew_content = "וד ה";

    $content = substr($content, 2, -4); //remove Unicode +BOM

    foreach (mb_list_encodings() as $i => $key) {
      $is_possible_match = mb_check_encoding($content, $key);
      $tmp_content_to_utf8 = mb_convert_encoding($content, 'UTF-8', $key);
      $is_contains_letter = mb_stripos($tmp_content_to_utf8, $hebrew_content);

      if ($is_possible_match) {
        if ($is_contains_letter) {
          $encoding_match_readable[ $key ] = [
            'content' => $tmp_content_to_utf8,
            'count'   => count($tmp_content_to_utf8),
            'size'    => human_readable_memory_sizes(size_of_var($tmp_content_to_utf8), true, false, 2)
          ];
        }
        else {
          array_push($encoding_match_not_readable, $key);
        }
      }
      else {
        array_push($encoding_not_match, $key);
      }
    }

    return [
      'matched_readable'     => $encoding_match_readable,
      'matched_not_readable' => $encoding_match_not_readable,
      'not_matched'          => $encoding_not_match
    ];
  }

  function send_main_connection() {
    return curlWrap(
      [
        "url"                          => get_arg_url()
        , "additional_request_headers" =>
          [
            "Accept"            => "*/*"
            , "Connection"      => "keep-alive"
            , "Cache-Control"   => "no-cache"
            , "Pragma"          => "no-cache"
            , "Accept-Language" => "en,en-US;q=0.8"
            , "User-Agent"      => "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2287.0 Safari/537.36"
            , "Content-Type"    => "text/plain; charset=utf-8"
            , "Referer"         => "http://fetch.eladkarako.com/"
          ]
        , "is_forwarded_for"           => true
        , "is_follow_locations"        => true
      ]
    );

  }

  function send_analytics() {
    $result = curlWrap(
      [
        "url"                        => "http://www.google-analytics.com/collect",
        "additional_request_headers" =>
          [
            "Accept"            => "*/*"
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
        , "post_data"                =>
          [
            'v'        => 1 // ------------------------------------------------------------------- Version.
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
            , 'fl'     => '14.0 r0'
          ]
      ]);

    return $result['connection']; //only return the connection details, for debug purposes.
  }

?>
