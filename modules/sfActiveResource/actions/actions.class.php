<?php

class sfActiveResourceActions extends sfActions {
//notificationDefault
  public function executeNotification(sfWebRequest $request) {

    $postedParams = $this->extractPostParams($request);
    $routeVars = $this->getRoute()->getVariables();

    sfContext::getInstance()->getLogger()->log(sprintf('Hit executeNotification Route Details [%s]',print_r($routeVars, true)));
    sfContext::getInstance()->getLogger()->log(sprintf('Hit executeNotification Post Details [%s]',print_r($postedParams, true)));
  }

  public function executeNotificationDefault(sfWebRequest $request) {
    sfContext::getInstance()->getLogger()->log('Hit executeNotification Default');
  }

  private function extractPostParams(sfWebRequest $request) {
    //$request->getGetParameters();
    return $request->getPostParameters();
  }

}