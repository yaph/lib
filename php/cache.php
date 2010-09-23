<?php
/**
 * A caching class
 */
class Cache {
  /**
   * Cache directory
   *
   * @var string $dir
   */
  private $dir;

  /**
   * Cache duration in seconds
   *
   * @var int $duration
   */
  private $duration;

  /**
   * Serialize data or not
   *
   * @var bool $serialize
   */
  private $serialize;
  
  const UMASK = 0777;

  /**
   * Constructor
   *
   * @param string $dir
   * @param int $duration
   * Cache duration in seconds, 0 meaning cache forever.
   * 
   * @param bool $serialize
   */
  public function __construct($dir, $duration = 0, $serialize = FALSE) {
    $this->dir = $dir;
    $this->duration = $duration;
    $this->serialize = $serialize;
  }

  /**
   * Get and/or set cache directory
   *
   * @param string $dir
   * @return string $dir
   */
  public function dir($dir = '') {
    if ($dir) {
      $this->dir = $dir;
    }
    return $this->dir;
  }

  /**
   * Get and/or set cache duration
   *
   * @param string $duration
   * @return string $duration
   */
  public function duration($duration = NULL) {
    if (!is_null($duration)) {
      $this->duration = $duration;
    }
    return $this->duration;
  }

  /**
   * Get and/or set cache serialization
   * @param bool $serialize
   * @return void
   */
  public function serialize($serialize = NULL) {
    if (!is_null($serialize)) {
      $this->serialize = $serialize;
    }
    return $this->serialize;
  }

  /**
   * Get cached data for given cache id
   *
   * @param string $cache_id
   * @return $data
   */
  public function get($cache_id) {
    $cache_file = $this->path($cache_id) . $cache_id;
    if (!file_exists($cache_file)) {
      return FALSE;
    }

    $duration = $this->duration();
    if ($duration && (time() - filemtime($cache_file) > $duration)) {
      return FALSE;
    }

    if (!$fh = fopen($cache_file, 'r')) {
      return FALSE;
    }

    $data = '';
    while (($buffer = fread($fh, 4096)) != '') {
      $data .= $buffer;
    }

    if ($this->serialize()) {
      $data = unserialize($data);
    }

    return $data;
  }

  /**
   * Cache data
   *
   * @param string $cache_id
   * @param string $data
   * @return bool
   */
  public function set($cache_id, $data) {
    $path = $this->path($cache_id);
    $this->create_dir($path);
    $cache_file = $path . $cache_id;
    if (!$fh = fopen($cache_file, 'w')) {
      return FALSE;
    }

    if ($this->serialize()) {
      $data = serialize($data);
    }

    if (fwrite($fh, $data) === FALSE) {
      return FALSE;
    }
    fclose($fh);
    return TRUE;
  }
  
  public function path($cache_id) {
    return $this->dir() . substr($cache_id, 0 ,2) . DIRECTORY_SEPARATOR;
  }

  /**
   * Create a directory
   *
   * @param string $dir
   * @return bool
   */
  public function create_dir($dir) {
    if (!file_exists($dir)) {
      $old = umask(0);
      if (!mkdir($dir, self::UMASK, TRUE)) {
        return FALSE;
      }
      umask($old);
    }
    return TRUE;
  }
}