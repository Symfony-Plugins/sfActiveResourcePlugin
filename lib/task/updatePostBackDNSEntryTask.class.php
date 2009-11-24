<?php

require_once dirname(__FILE__)."/../../doc/examples/sfActiveResourceDNS.class.php";

class updatePostBackDNSEntryTask extends sfBaseTask
{

  protected $config = array();
  protected $configComplete = array();

  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('domain-name', sfCommandArgument::OPTIONAL, 'The DNS entry to update'),
    ));

    $this->addOptions(array(
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment'),
    ));

    $this->namespace        = 'zerigo';
    $this->name             = 'update-dns';
    $this->briefDescription = 'Updates a DNS entry with your current IP address (NAT aware)';
    $this->detailedDescription = <<<EOF
The [update-dns|INFO] task updates the dns entry with your current IP address.

You can call this task without options to use those configured in the config/app.yml
be aware that it will use the current environment

  [php symfony zerigo:update-dns|INFO]

You can always override the environment:

  [php symfony zerigo:update-dns --env=dev|INFO]

If you'd prefer to pass a specific domain name to update (You'll be given the option to save the domain in config/app.yml):

  [php symfony zerigo:update-dns domain.name.ext |INFO]

See the following task for setting up the username & API key for the service

  [php symfony help configure:dns-settings|INFO]

EOF;
  }

  /**
   * Persists the config (config/app.yml)
   *
   * @param array $config
   */
  protected function saveConfig($config = array()) {
    $configFilename = sfConfig::get('sf_config_dir').'/app.yml';

    file_put_contents($configFilename, sfYaml::dump($config, 4));
  }

  /**
   * Load the config settings (config/app.yml)
   *
   * @return array The flattened config for this environment
   */
  protected function loadConfig() {

    $configFilename = sfConfig::get('sf_config_dir').'/app.yml';
    //$config = file_exists($configFilename) ? sfYaml::load($configFilename) : array();
    $this->configComplete = sfYamlConfigHandler::parseYaml($configFilename);
    $config = sfYamlConfigHandler::flattenConfigurationWithEnvironment($this->configComplete);

    return $config;
  }

  /**
   * This method retrieves the current public IP address via a web service
   *
   * @return string The current public IP address
   */
  protected function getPublicIPAddress() {

    $browser = new sfWebBrowser();
    $addressDetails = $browser->get('http://ipinfodb.com/ip_query_country.php?output=json')->getResponseText();
    $addressDetails = json_decode($addressDetails);

    return $addressDetails->Ip;
  }


  /**
   * Checks a response to determine if it's valid or not (using the sfActiveResource response_code)
   *
   * @param sfZerigoDNS $response
   * @return bool
   */
  private function isResponseValid($response) {

    if (isset($response->response_code) && intval($response->response_code)>0) {
      // We have a numerical response code

      switch($response->response_code) {
        case 200:
          return true;
        break;
        default:
          return false;
        break;
      }
      
    } else {
      return true;
    }
    
  }

  /**
   * Retrieves a zone record for the passed domainName from the REST service
   *
   * @param string $domainName
   * @return mixed|Array|null Returns null on error or the Zone Record for the passed domainName
   */
  protected function getZoneRecord($domainName) {

    $dns = new sfZerigoDNS();
    $dns->setAuth($this->config['username'], $this->config['password']);
    $zoneRecord = $dns->zone()->find($domainName);

    if (!$this->isResponseValid($zoneRecord)) {
      $this->debug(sprintf('No Valid Response Received from getHostInfo for %s',$domainName));
      return null;
    } else {
      $this->debug(sprintf('Valid Response Received from getHostInfo for %s',$domainName));
      return $zoneRecord->_data;
    }
  }

  /**
   * Retrieves the host record for the passed fqdn
   *
   * @param string $fqdn
   * @return mixed|Array|null Returns null on error or the Zone Record for the passed fqdn
   */
  protected function getHostInfo($fqdn) {

    $dns = new sfZerigoDNS();
    $dns->setAuth($this->config['username'], $this->config['password']);

    $results = $dns->host()->find('all',array('fqdn'=>$fqdn));

    if (!$this->isResponseValid($results)) {
      $this->debug(sprintf('No Valid Response Received from getHostInfo for %s',$fqdn));
      return null;
    } else {
      $this->debug(sprintf('Valid Response Received from getHostInfo for %s',$fqdn));
      return $results[0]->_data;
    }
  }

  /**
   * Simple debug helper - will debug information depending on the presence of the --trace option
   *
   * @param string $data
   */
  private function debug($data) {
    if ($this->options['trace']) {
      echo $data;
    }
  }

  protected function execute($arguments = array(), $options = array())
  {
    $this->options = array_merge($options, $this->options);
    // Get your current IP address
    $config = $this->loadConfig();
    $this->config = $config['dns'];

    $this->debug(print_r(array('Loaded Config[complete]' => $this->configComplete),true));
    $this->debug(print_r(array('Loaded Config[flat]' => $this->config),true));
    $this->debug(print_r(array('Plugin Options' => $options),true));

    $persistEntry = false;
    if (isset($arguments['domain-name'])) {
      $this->config['domain'] = $arguments['domain-name'];
      $persistEntry = $this->askConfirmation(
          sprintf("Do you want %s added to your configured domains in %s?",
              $this->getFormatter()->format($this->config['domain'],'COMMENT'),
              $this->getFormatter()->format("config/app.yml",'COMMENT')),
          'NONE'
      );
      if ($persistEntry) {
        $this->configComplete[$options['env']]['dns']['domain']=$arguments['domain-name'];
        $this->saveConfig($this->configComplete);
      }
    }

    $domainParts = explode(".",$this->config['domain']);
    $hostname = array_shift($domainParts);
    $domainName = implode(".",$domainParts);

    $this->debug("Working with '%s' and generated a hostname of '%s and a root domain name of '%s'",
        $this->config['domain'], $hostname, $domainName);

    $zoneRecord = $this->getZoneRecord($domainName);

    if (null === $zoneRecord) {
      $this->debug(sprintf("No Zone Record Found for %s",$domainName));

      throw new sfCommandException(sprintf("Unable to find the zone record for %s",$domainName));
    }

    $this->debug(sprintf("Zone Record:\n %s",print_r($zoneRecord,true)));

    $myIPAddress = $this->getPublicIPAddress();
    $this->debug(sprintf('Local IP Address: %s',$myIPAddress));

    $hostRecord = $this->getHostInfo($this->config['domain']);
    $this->debug(sprintf("Host Record:\n %s",print_r($hostRecord,true)));

    $recordCreated = false;

    if (null === $hostRecord) {

      $this->debug(sprintf("No host record found for %s",$this->config['domain']));
      $hostname = explode(".", $this->config['domain']);

      $hostRecord = array(
        'zone_id'   => $zoneRecord['id'],
        'fqdn'      => $this->config['domain'],
        'hostname'  => $hostname[0],
        'host_type' => 'A',
      );

      $recordCreated = true;
    }

    $hostRecord['data'] = $myIPAddress;

    $update = new sfZerigoDNS($hostRecord);
    $update->setAuth($this->config['username'], $this->config['password']);
    $results = $update->host()->save();

    $this->debug(sprintf("Results from Update: %s",print_r($results, true)));

    echo sprintf("The DNS record for %s has been %s IP Address %s\n",
        $this->getFormatter()->format($this->config['domain'],'COMMENT'),
        $this->getFormatter()->format(($recordCreated==true)?"Created with":"Updated to",'COMMENT'),
        $this->getFormatter()->format($myIPAddress,'COMMENT'));

  }
}
