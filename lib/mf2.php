<?php
class MF2Object {
  protected $_data;
  protected $_parent;

  public function __construct($data, $parent=null) {
    if(is_array($data))
      $this->_data = json_decode(json_encode($data));
    else
      $this->_data = $data;
    $this->_parent = $parent;
  }

  public function __get($key) {
    $cacheKey = '_'.$key;

    if(method_exists($this, $key) && property_exists($this, $cacheKey)) {
      if($this->{$cacheKey} !== null)
        return $this->{$cacheKey};
      $val = $this->$key();
      $this->{$cacheKey} = $val;
      return $val;
    }

    return null;
  }

  protected function _is($obj, $type) {
    return in_array($type, $obj->type);
  }

  protected function _findFirstItem($type) {
    foreach($this->_data->items as $i) {
      if($this->_is($i, $type))
        return $i;
    }
    return null;
  }

  // Find the property in the 'properties' object and return the values
  // If $first is true, returns only the first value, otherwise returns an array
  public function property($key, $first=false) {
    if($this->_data && property_exists($this->_data, 'properties')) {
      if(property_exists($this->_data->properties, $key)) {
        if($first)
          return $this->_data->properties->{$key}[0];
        else
          return $this->_data->properties->{$key};
      }
    }
    if($first)
      return null;
    else 
      return array();
  }

  protected function _stringForProperty($key) {
    if($content=$this->property($key))
      return implode(' ', $content);
    return '';    
  }
}

class MF2Page extends MF2Object {
  protected $_hentry = null;
  protected $_hevent = null;
  protected $_author = null;

  protected function hentry() {
    $item = $this->_findFirstItem('h-entry');
    if($item)
      return new HEntry($item, $this);
    else
      return null;
  }

  protected function hevent() {
    $item = $this->_findFirstItem('h-event');
    if($item)
      return new HEvent($item, $this);
    else
      return null;
  }

  protected function author() {
    $author = null;
    $entry = $this->_findFirstItem('h-entry');
    if($entry) {
      if(property_exists($entry->properties, 'author')) {
        $author = $entry->properties->author;
      } else {

      }
    }
  }

}

class HEntry extends MF2Object {
  protected $_content = null;
  protected $_published = null;
  protected $_author = null;

  protected function content() {
    if($content=$this->property('name'))
      return implode(' ', $content);
    if($content=$this->property('content'))
      return implode(' ', $content);
    return '';
  }

  protected function published() {
    if($time=$this->property('published', true))
      return new DateTime($time);
    return null;
  }

  // Search the h-entry for an author. If none is found, fall back to the parent's h-card.
  protected function author() {
    if($author=$this->property('author', true))
      return new HCard($author);
    if($author=$this->_parent->_findFirstItem('h-card'))
      return new HCard($author);
    return null;
  }
}

class HEvent extends MF2Object {
  protected $_location = null;
  protected $_published = null;
  protected $_author = null;

  protected function location() {
    $locations = array();
    if($location=$this->property('location')) {
      foreach($location as $l)
        $locations[] = new HCard($l);
      return $locations;
    } else {
      return null;
    }
  }

  protected function published() {
    if($time=$this->property('published', true))
      return new DateTime($time);
    return null;
  }

  // Search the h-entry for an author. If none is found, fall back to the parent's h-card.
  protected function author() {
    if($author=$this->property('author', true))
      return new HCard($author);
    if($author=$this->_parent->_findFirstItem('h-card'))
      return new HCard($author);
    return null;
  }
}

class HCard extends MF2Object {
  protected $_name;
  protected $_nickname;
  protected $_given_name;
  protected $_family_name;
  protected $_adr;
  protected $_note;
  protected $_photo;
  protected $_url;

  protected function name() {
    return $this->_stringForProperty('name');
  }

  protected function nickname() {
    return $this->_stringForProperty('nickname');
  }

  protected function given_name() {
    return $this->_stringForProperty('given-name');
  }

  protected function family_name() {
    return $this->_stringForProperty('family-name');
  }

  protected function adr() {
    return $this->_stringForProperty('adr');
  }

  protected function note() {
    return $this->_stringForProperty('note');
  }

  protected function photo() {
    return $this->_stringForProperty('photo');
  }

  protected function url() {
    return $this->_stringForProperty('url');
  }

}

class ParserPlus extends mf2\Parser {
  public function xpath($query) {
    return $this->xpath->query($query);
  }
}

