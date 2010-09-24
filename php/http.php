<?php
class HTTP {
  public static function get($url, $params = array()) {
    if ($params) {
      $url .= '?' . http_build_query($params);
    }
    $request = curl_init($url);
    curl_setopt($request, CURLOPT_RETURNTRANSFER, TRUE);
    $responsetext = curl_exec($request);
    curl_close($request);
    return $responsetext;
  }
}