<?php

class sfServiceDefinitionExtender extends sfServiceDefinition {
  
  protected $wrappedMethods = array();

  public function addWrappedMethod($methodName,  array $arguments = null) {
    $this->wrappedMethods[$methodName] = array('arguments' => $arguments, 'calls' => array());
    return $this;
  }

  public function getWrappedMethods() {
    return $this->wrappedMethods;
  }

  public function hasWrappedMethods() {
    return count($this->wrappedMethods)>0;
  }

  public function addWrappedMethodCall() {

  }


}


?>
