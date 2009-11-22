<?php

class sfActiveResourcePluginConfiguration extends sfPluginConfiguration {

  public function initialize() {

    if (sfConfig::get('app_activeresource_routes_register', true) && in_array('sfActiveResource', sfConfig::get('sf_enabled_modules', array()))) {
      $this->dispatcher->connect('routing.load_configuration', array('sfActiveResourceRouting', 'listenToRoutingLoadConfigEvent'));
    }
    
//
//    parent::initialize();
//
//    $includePath = realpath(__FILE__)."../lib/vendor/dependency-injection/lib/";
//
//    set_include_path($includePath.PATH_SEPARATOR.get_include_path());
//    require_once($includePath.'sfServiceContainerAutoloader.php');
//    spl_autoload_register(array('sfServiceContainerAutoloader', 'register'));

  }

}
