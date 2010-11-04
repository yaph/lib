<?php
class DBpedia {

  private $_uriResource = 'http://dbpedia.org/resource/';

  private $_uriData = 'http://dbpedia.org/data/';

  private $_uriRedirect = 'http://dbpedia.org/property/redirect';

  private $_properties = array();

  private $_parentUri = '';

  /**
   * Decode JSON string to PHP object and start to process it.
   * @param string $JSON_string
   */
  public function parseJSON($JSON_string) {
    $JSON = json_decode($JSON_string);
    $this->_recurseJSON($JSON);
  }

  /**
   * Recurse through given JSON object.
   * @param object $JSON
   */
  private function _recurseJSON($JSON) {
    foreach ($JSON as $uri => $data) {
      $typeData = gettype($data);
      if ($this->_isNamespace($uri)) {
        $this->_parentUri = $uri;
        $key = $uri;
      } else {
        $key = $this->_parentUri;
      }
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
   * Check wheter given data qualifies as a potential namespace URI.
   * @param $data
   * @return bool
   */
  private function _isNamespace($data) {
    if ('string' ==gettype($data) && 0 === strpos($data, 'http://')) {
      return true;
    }
    return false;
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

  # FIXME can be removed
  public static $ns = array(
    'http://www.w3.org/2002/07/owl#sameAs',
    'http://xmlns.com/foaf/0.1/primaryTopic',
    'http://www.w3.org/1999/02/22-rdf-syntax-ns#type',
    'http://www.w3.org/2002/07/owl#sameAs',
    'http://www.w3.org/2000/01/rdf-schema#comment',
    'http://www.w3.org/2004/02/skos/core#subject',
    'http://xmlns.com/foaf/0.1/depiction',
    'http://www.w3.org/2000/01/rdf-schema#label',
    'http://xmlns.com/foaf/0.1/name',
    'http://dbpedia.org/ontology/releaseDate',
    'http://xmlns.com/foaf/0.1/page',
    'http://dbpedia.org/ontology/runtime',
    'http://dbpedia.org/ontology/starring',
    'http://dbpedia.org/ontology/Work/runtime',
    'http://dbpedia.org/ontology/musicComposer',
    'http://dbpedia.org/property/wikiPageUsesTemplate',
    'http://dbpedia.org/property/name',
    'http://dbpedia.org/ontology/subsequentWork',
    'http://dbpedia.org/property/country',
    'http://dbpedia.org/property/hasPhotoCollection',
    'http://dbpedia.org/ontology/thumbnail',
    'http://dbpedia.org/property/writer',
    'http://dbpedia.org/property/director',
    'http://dbpedia.org/property/producer',
    'http://dbpedia.org/property/starring',
    'http://dbpedia.org/property/language',
    'http://dbpedia.org/property/released',
    'http://dbpedia.org/ontology/abstract',
    'http://dbpedia.org/property/reference',
    'http://dbpedia.org/property/wordnet_type',
    'http://dbpedia.org/property/id',
    'http://dbpedia.org/property/music',
    'http://dbpedia.org/ontology/budget',
    'http://dbpedia.org/ontology/writer',
    'http://dbpedia.org/property/title',
    'http://dbpedia.org/ontology/director',
    'http://dbpedia.org/ontology/language',
    'http://dbpedia.org/ontology/distributor',
    'http://dbpedia.org/property/distributor',
    'http://dbpedia.org/property/runtime',
    'http://dbpedia.org/property/budget',
    'http://dbpedia.org/property/followedBy',
    'http://dbpedia.org/ontology/previousWork',
  );
}