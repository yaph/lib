<?php
class DBpedia {
  public static $ns = array(
    'http://www.w3.org/2002/07/owl#' => 'owl',
    'http://www.w3.org/2001/XMLSchema#' => 'xsd',
    'http://www.w3.org/2000/01/rdf-schema#' => 'rdfs',
    'http://www.w3.org/1999/02/22-rdf-syntax-ns#' => 'rdf',
    'http://xmlns.com/foaf/0.1/' => 'foaf',
    'http://purl.org/dc/elements/1.1/' => 'dc',
    'http://dbpedia.org/resource/' => '',
    'http://dbpedia.org/property/' => 'dbpedia2',
    'http://dbpedia.org/' => 'dbpedia',
    'http://www.w3.org/2004/02/skos/core#' => 'skos'
  );
  
  public $data = array();
  
  public $uri = '';

  public function parseJSON($JSON_string) {
    $JSON = json_decode($JSON_string);
    $this->recurseJSON($JSON);
    return $this->data;
  }
  
  private function recurseJSON($JSON) {
    foreach ($JSON as $k => $v) {
      $tv = gettype($v);
      switch ($tv) {
        case 'object':
          if (isset($v->type)) {
            if ('literal' == $v->type) {
              $this->data[$this->uri][$v->lang] = $v->value;
            }
            elseif ('uri' == $v->type) {
              $this->uri = $v->value;
            }
          }
        case 'array':
var_dump($v);
          $this->recurseJSON($v);
          break;
      }
    }
  }

  public static function url($url, $format = 'json') {
    if ($format) {
      $url .= '.' . $format;
      $url = str_replace('dbpedia.org/resource/', 'dbpedia.org/data/', $url);
    } else {
      $url = str_replace('dbpedia.org/resource/', 'dbpedia.org/page/', $url);
    }
    return $url;
  }
}