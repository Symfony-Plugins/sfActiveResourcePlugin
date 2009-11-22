<?php

class sfActiveResourceRouting {

  static public function routePattern() {
    return "/activeresource/:objectName/:methodName";
  }

  static public function routePatternDefault() {
    return "/activeresource";
  }

  static public function listenToRoutingLoadConfigEvent(sfEvent $event)
  {
    $r = $event->getSubject();

    // preprend our routes
    $r->prependRoute('sf_activeresource_notification', 
         new sfRoute(
              self::routePattern(),
              array('module' => 'sfActiveResource', 'action' => 'notification')
         )
     );

    $r->prependRoute('sf_activeresource_notification_default',
         new sfRoute(
              self::routePatternDefault(),
              array('module' => 'sfActiveResource', 'action' => 'notificationDefault')
         )
     );

    sfContext::getInstance()->getLogger()->log("Registered route for sf_activeresource_notification");

  }
}