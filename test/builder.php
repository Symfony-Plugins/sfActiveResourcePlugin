<?php

require_once "../lib/vendor/dependency-injection/lib/sfServiceContainerAutoloader.php";
sfServiceContainerAutoloader::register();


$sc = new sfServiceContainerBuilder();

$sc->addParameters(array(
    'sfactiveresource.options' =>
    array(
      'site'            => 'http://testme.com',
      'request_format'  => 'xml',
      ),
    'message.options'   =>
      array(
          'postback_url' => '',
          'body' => '',
          'subject' => '',
          'recipients' =>
            array ()
      )
    )
  );


//        array (
//          'postback_url' => $postbackUrl,
//          'body' => 'Message body goes here...',
//          'subject' => 'Subject line for emails',
//          'recipients' =>
//            array (
//              'recipient' =>
//                  array (
//                    'position' => 1,
//                    'channel' => 'email',
//                    'address' => 'david@inspiredthinking.co.uk'
//                  )
//            )
//        )


$sDef = new sfServiceDefinitionExtender('sfActiveResource', array('%message.options%'));
//$sDef->addArgument('%message.options%')->setShared(true);
$sDef->setShared(true);
$sDef->addWrappedMethod('send',array('paramOne'));

//$sc->
//  register('Message', 'sfActiveResource')->
//  addArgument('%message.options%')->
//  setShared(true);

$sc->setServiceDefinition('Message', $sDef);

//$sc->
//  register('send','sfActiveResource')->
//  addMethodCall('initialize', array(new sfServiceReference('MessagePub.Message')))->
//  addMethodCall('save', array());

//$sc->setService('bugger', $sc->resolveServices('MessagePub.Message'));

//$sc->setAlias('altSend','send');

$dumper = new sfServiceContainerDumperPhp($sc);
file_put_contents('/tmp/container-original.php', $dumper->dump(array('class'=>'MessagePub')));
