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

  private $_uriResource = 'http://dbpedia.org/resource/';

  private $_uriData = 'http://dbpedia.org/data/';

  private $_uriRedirect = 'http://dbpedia.org/property/redirect';

  private $_properties = array();

  private $_parentUri = '';

  /**
   * TODO documentation
   * @param unknown_type $JSON_string
   */
  public function parseJSON($JSON_string) {
    $JSON = json_decode($JSON_string);
    $this->_recurseJSON($JSON);
  }

  /**
   * TODO documentation
   * @param unknown_type $JSON
   */
  private function _recurseJSON($JSON) {
    foreach ($JSON as $uri => $data) {
      $typeData = gettype($data);
      if ('string' == gettype($uri)) {
        $this->_parentUri = $uri;
        $key = $uri;
      } else {
        $key = $this->_parentUri;
      }
      # FIXME doesn't yet work as intended
      if ('object' == $typeData || 'array' == $typeData) {
        $this->_recurseJSON($data);
        if ($this->_uriRedirect != $uri
        && (false === strpos($uri, $this->_uriResource))) {
          $this->_properties[$key] = $data;
        }
      }
    }
  }

  /**
   * Get an array of properties keyed by property name.
   * @param string $name
   * Options:
   *  uri = complete URI
   *  property = property part of URI, i.e. the part after the last slash
   * @param string $lang
   */
  public function getProperties($name = 'uri', $lang = '') {
    return $this->_properties;
  }

  /**
   * Get the corrsponding data URI for a resource.
   * @param string $url
   * @param string $format
   */
  public static function resourceDataUri($url, $format = 'json') {
    $url = str_replace($this->_uriResource, $this->_uriData, $url);
    if ($format) {
      $url .= '.' . $format;
    }
    return $url;
  }
}