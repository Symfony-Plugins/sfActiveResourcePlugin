<?php
  require_once "../lib/vendor/dependency-injection/lib/sfServiceContainerAutoloader.php";
  sfServiceContainerAutoloader::register();

  $sc = new sfServiceContainerBuilder();

  $loader = new sfServiceContainerLoaderFileXml($sc, array(
      '/tmp/',
  ));

  $loader->load('container-original.xml');

  $dumper = new sfServiceContainerDumperPhp($sc);
  file_put_contents('/tmp/container-new.php', $dumper->dump(array('class'=>'sfActiveResourceContainer')));
