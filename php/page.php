<?php
abstract class Page {
  public $title = '';
  public $h1 = '';
  public $content = '';
  public $meta; // Meta object

  abstract public function generate();

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
  
  // @see http://api.drupal.org/api/function/check_plain/7
  public static function check_plain($text) {
    if (is_string($text))
      return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    return '';
  }
}

class Meta {
  public $description;

  public function __construct(){}
}