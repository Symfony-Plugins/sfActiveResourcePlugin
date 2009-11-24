<?php

class configureProjectDNSTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('domain', sfCommandArgument::REQUIRED, 'The domain name to configure'),
      new sfCommandArgument('username', sfCommandArgument::OPTIONAL, 'The username to configure'),
      new sfCommandArgument('password', sfCommandArgument::OPTIONAL, 'and the password'),
    ));

    $this->addOptions(array(
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'all'),
    ));

    $this->namespace        = 'configure';
    $this->name             = 'dns-settings';
    $this->briefDescription = 'Configures project DNS settings';
    $this->detailedDescription = <<<EOF
The [dns|INFO] task configures the username & password for the DNS provider.

Call this task with:

  [php symfony configure:dns-settings domain-name|INFO]

or including the username and password:

  [php symfony configure:dns-settings domain-name username password|INFO]

and you can additionally provide the environment it should apply to:

  [php symfony configure:dns-settings --env=dev domain-name username password|INFO]
EOF;
  }

  protected function saveConfig($config = array()) {
    $configFilename = sfConfig::get('sf_config_dir').'/app.yml';

    file_put_contents($configFilename, sfYaml::dump($config, 4));
  }

  protected function loadConfig() {

    $configFilename = sfConfig::get('sf_config_dir').'/app.yml';
    $config = file_exists($configFilename) ? sfYaml::load($configFilename) : array();

    return $config;
  }

  protected function execute($arguments = array(), $options = array())
  {
    $config = $this->loadConfig();
    $config[$options['env']]['dns']['domain'] = $arguments['domain'];

    if (isset($arguments['username'])) {
      if (!isset($arguments['password'])) {
        throw new sfCommandException('Password must also be provided');
      }
      $config[$options['env']]['dns']['username'] = $arguments['username'];
      $config[$options['env']]['dns']['password'] = $arguments['password'];
    }

    $this->saveConfig($config);
  }
}
