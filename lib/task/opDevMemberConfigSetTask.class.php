<?php
/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * opDevMemberConfigSetTask
 *
 * @package    opDevUtilPlugin
 * @subpackage task
 * @author     hidenorigoto <hidenorigoto@gmail.com>
 */
class opDevMemberConfigSetTask extends sfBaseTask
{
  /**
   * opDevMemberConfigSetTask::configure()
   *
   * @return
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('member_id',    sfCommandArgument::REQUIRED, 'member id'),
      new sfCommandArgument('config_key',   sfCommandArgument::REQUIRED, 'config key'),
      new sfCommandArgument('config_value', sfCommandArgument::REQUIRED, 'new value'),
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env',         null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
      new sfCommandOption('connection',  null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
    ));

    $this->namespace           = 'opDev';
    $this->name                = 'member-config-set';
    $this->briefDescription    = '';
    $this->detailedDescription = <<<EOF
The [opDev:member-config-set|INFO] task updates/inserts the certain member_config value of the member specified with its id.
Call it with:

  [php symfony opDev:member-config-set|INFO]
EOF;
  }

  /**
   * opDevMemberConfigSetTask::execute()
   *
   * @param array $arguments
   * @param array $options
   * @return
   */
  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    // get member_id from arguments
    $member_id = $arguments['member_id'];

    // check whether the specified member exists
    $count = Doctrine_Core::getTable('Member')->count($member_id);
    if ($count == 0)
    {
        $this->log(sprintf('Can\'t find any member with id %s', $memberId));

        return;
    }

    // get other arguments
    $config_key   = $arguments['config_key'];
    $config_value = $arguments['config_value'];

    if ('password' === $config_key)
    {
      $config_value = md5($config_value);
    }

    // save new value for the key
    Doctrine_Core::getTable('MemberConfig')
      ->setValue($member_id, $config_key, $config_value);

    // show complete message
    $this->log('specified configuration has been updated successfully.');
  }
}
