<?php

class sfActiveResourceActions extends sfActions {
//notificationDefault
  public function executeNotification(sfWebRequest $request) {

    $postedParams = $this->extractPostParams($request);

    sfContext::getInstance()->getLogger()->log(sprintf('Hit executeNotification [%s]',print_r($postedParams, true)));
  }

  public function executeNotificationDefault(sfWebRequest $request) {
    sfContext::getInstance()->getLogger()->log('Hit executeNotification Default');
  }

  private function extractPostParams(sfWebRequest $request) {
    //$request->getGetParameters();
    return $request->getPostParameters();
  }

}