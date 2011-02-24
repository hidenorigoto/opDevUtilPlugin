<?php
/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * opDevMakeTestMembersTask
 *
 * initial code
 *   https://github.com/zunivus/opCloudoPlugin/blob/master/lib/task/MakeTask.class.php writtern by tejima
 *
 * @package    OpenPNE
 * @subpackage task
 * @author     hidenorigoto <hidenorigoto@gmail.com>
 */

class opDevMakeTestMembersTask extends sfDoctrineBaseTask
{
  protected function configure()
  {
    $this->namespace        = 'opDev';
    $this->name             = 'make-test-members';

    $this->addArguments(array(
      new sfCommandArgument('count', sfCommandArgument::OPTIONAL, 'number of records you want to insert', 100),
    ));

    $this->addOptions(array(
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
    ));

    $this->briefDescription = 'Install OpenPNE';
    $this->detailedDescription = <<<EOF
The [openpne:install|INFO] task installs and configures OpenPNE.
Call it with:

  [./symfony openpne:install|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    $databaseManager = new sfDatabaseManager($this->configuration);

    $count = (int)$arguments['count'];
    for ($i = 0; $i < $count; ++$i)
    {
      $m = new Member();
      $m->is_active = 1;
      $m->save();
      $name = "DO民ID:".$m->id;
      $m->name = $name;
      $m->save();
    }
  }
}
