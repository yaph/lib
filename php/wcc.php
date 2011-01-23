<?php
/**
 * A web client class with built-in caching.
 */
class WCC {
  /**
   * File system root path to cache dir.
   * @var string
   */
  protected $fs_cache_root_path = '';

  /**
   * Cache lifetime in seconds or false.
   * @var int|bool
   */
  protected $cache_lifetime = false;

  /**
   * Cache object
   * @var object
   */
  protected $cache = NULL;
  
  public function __construct($opts) {
    $this->cache_lifetime = $opts['cache_lifetime'];
    $this->fs_cache_root_path = $opts['fs_cache_root_path'];
    $this->cache = new FSCache($this->fs_cache_root_path);
    #$this->cache = new FSCache();
  }

  public function request($url, $params = array(), $cache_lifetime = false) {
    $get_from_cache = $response = false;
    $url = $this->getRequestURL($url, $params);

    if (false === $cache_lifetime)
      $cache_lifetime = $this->cache_lifetime;
    if (false !== $cache_lifetime)
      $get_from_cache = true;

    if ($get_from_cache) {
      $id = $this->cache->getIDFromURL($url);
      $response = $this->cache->get($id, $cache_lifetime);
    }

    if (!$get_from_cache || !$response)
      $response = file_get_contents($url);

    if ($response && $get_from_cache) {
      if (!isset($id))
        $id = $this->cache->getIDFromURL($url);
      $this->cache->set($id, $response);
    }

    return $response;
  }

  private function getRequestURL($url, $params = array()) {
    if ($params) {
      asort($params);
      $url .= '?' . http_build_query($params);
    }
    return $url;
  }
}

interface URLCache {
  public function get($id, $lifetime);
  public function set($id, $data);
  public function getIDFromURL($url);
}

class FSCache implements URLCache {
  const UMASK = 0777;

  private $root_dir = '/tmp';

  public function __construct($root_dir = '') {
    if ($root_dir)
      $this->root_dir = $root_dir;
  }
  /**
   * @param $id file name
   */
  public function get($id, $lifetime) {
    if ( (0 === $lifetime) 
      || (file_exists($id) && (filemtime($id) + $lifetime > time())) )
      return file_get_contents($id);
    return false;
  }

  /**
   * @param int   $id file name
   * @param mixed $data
   */
  public function set($id, $data) {
    if (!file_exists($id)) {
      $dir = dirname($id);
      if (!file_exists($dir))
        mkdir($dir, self::UMASK, true);
      touch($id);
    }
    return file_put_contents($id, $data, LOCK_EX);
  }

  public function getIDFromURL($url) {
    $dir = $this->root_dir;
    $parts = parse_url($url);
    if (isset($parts['host']) && isset($parts['path'])) {
      $subdir = $parts['host'] . $parts['path'];
      $dir .= '/' . $subdir;
    }
    $id = $dir . '/' . md5($url);
    return $id;
  }
}