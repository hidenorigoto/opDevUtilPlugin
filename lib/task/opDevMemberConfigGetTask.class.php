<?php
/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * opDevMemberConfigGetTask
 *
 * @package    opEditMemberConfigPlugin
 * @subpackage task
 * @author     hidenorigoto <hidenorigoto@gmail.com>
 */
class opDevMemberConfigGetTask extends sfBaseTask
{
  /**
   * opDevMemberConfigGetTask::configure()
   *
   * @return
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('member_id',  sfCommandArgument::REQUIRED, 'member id'),
      new sfCommandArgument('config_key', sfCommandArgument::REQUIRED, 'config key'),
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env',         null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
      new sfCommandOption('connection',  null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
    ));

    $this->namespace           = 'opDev';
    $this->name                = 'member-config-get';
    $this->briefDescription    = '';
    $this->detailedDescription = <<<EOF
The [opDev:member-config-get|INFO] task gets the certain member_config value of the member specified with its id.
Call it with:

  [php symfony opDev:member-config-get|INFO]
EOF;
  }

  /**
   * opDevMemberConfigGetTask::execute()
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
    $memberId = $arguments['member_id'];

    // check whether the specified member exists
    $count = Doctrine_Core::getTable('Member')->count($memberId);
    if ($count == 0)
    {
      $this->log(sprintf('Can\'t find any member with id %s', $memberId));

      return;
    }

    // get other arguments
    $configKey = $arguments['config_key'];

    // retrieve member_config record
    $configRecord = Doctrine_Core::getTable('MemberConfig')
      ->retrieveByNameAndMemberId($configKey, $memberId);
    if (null === $configRecord)
    {
      $this->log(sprintf('Can\'t find specified config entry: %s', $configKey));

      return;
    }

    // output the result
    $this->logSection($configKey, $configRecord->getValue());
  }
}
