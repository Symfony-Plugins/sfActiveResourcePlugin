<?php

class sfActiveResourceContainer extends sfServiceContainer
{
  protected $shared = array();

  public function __construct()
  {
    parent::__construct($this->getDefaultParameters());
  }

  protected function getSfActiveResourceService()
  {
    $instance = new sfActiveResource($this->getParameter('sfactiveresource.options'));

    return $instance;
  }

  protected function getDefaultParameters()
  {
    return array(
      'sfactiveresource.options' => array(
        'site' => 'http://testme.com',
        'request_format' => 'xml',
      ),
    );
  }
}