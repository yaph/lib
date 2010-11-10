<?php
abstract class Page {
  public $title = '';
  public $h1 = '';
  public $content = '';
  public $assets = ''; // assets path
  public $root = ''; // root path
  public $meta; // Meta object
  
  private static $vars = array();

  abstract public function generate($data, $lang);

  public function __construct() {
    self::$vars = array_keys(get_object_vars($this));
  }
  

  public function render($template, $type = 'page', $format = 'Content-Type: text/html') {
    if ('page' == $type) {
      $Page = $this;
    }
    header($format);
#    ob_start('ob_gzhandler');
    include $template;
    $out = ob_get_contents();
    ob_end_clean();
    return $out;
  }

  public function setProp($prop, $val) {
    if (in_array($prop, self::$vars)) {
      $this->$prop = $val;
    }
  }

  // @see http://api.drupal.org/api/function/check_plain/7
  public static function check_plain($text) {
    if (is_string($text))
      return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    return '';
  }
  
  public static function a($href, $anchor) {
    return sprintf('<a href="%s">%s</a>',
      self::check_plain($href),
      self::check_plain($anchor));
  }
}

class Meta {
  public $description;

  public function __construct(){}
}