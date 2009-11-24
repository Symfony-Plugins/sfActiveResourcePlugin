<?php

/**
 *
 * Represents the Zerigo DNS ActiveResource Service
 *
 */
class sfZerigoDNS extends sfActiveResource {
  
  /**
   *
   * @var string The root of the namespace for the API 
   */
  var $site = 'http://ns.zerigo.com/api/1.1/';
  var $request_format = 'xml';
  var $element_name ="";

  /**
   * Represents the zone namespace
   *
   * @return sfZerigoDNS
   */
  public function zone() {
    $this->element_name = "zones";
    return $this;
  }

  /**
   * Represents the host namespace
   *
   * @return sfZerigoDNS
   */
  public function host() {
    $this->element_name = "hosts";
    return $this;
  }

}