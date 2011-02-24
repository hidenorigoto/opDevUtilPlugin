<?php
/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * opDevSearchMemberTask
 *
 * @package    opDevUtilPlugin
 * @subpackage task
 * @author     hidenorigoto <hidenorigoto@gmail.com>
 */

class opDevSearchMemberTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('keyword', sfCommandArgument::REQUIRED, 'keyword'),
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      // add your own options here
    ));

    $this->namespace        = 'opDev';
    $this->name             = 'member-search';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [opDevSearchMemberTask|INFO] task does things.
Call it with:

  [php symfony opDev:member:search|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $members = Doctrine_Core::getTable('Member')->createQuery('m')
      ->leftJoin('m.MemberConfig mc')
      ->where('mc.value = ?', $arguments['keyword'])
      ->orWhere('m.name = ?', $arguments['keyword'])
      ->execute();

    foreach ($members as $member)
    {
      $this->logBlock($member->getName(), 'INFO');
      $this->logSection('member_id      ', $member->getId());
      $this->logSection('pc_address     ', $member->getConfig('pc_address'));
      $this->logSection('mobile_address ', $member->getConfig('mobile_address'));
    }
  }
}
