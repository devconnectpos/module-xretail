<?php
/**
 * Created by: khoild@smartosc.com/mr.vjcspy@gmail.com
 * Date: 8/4/16
 * Time: 9:19 AM
 */

namespace SM\XRetail\Model\Shell;

use Exception;
use Magento\Config\Model\Config\Loader;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\DataObject;
use Psr\Log\LoggerInterface;

class Process extends DataObject
{
    /**
     * @var
     */
    private $pid;
    /**
     * @var bool
     */
    private $command;
    /**
     * @var \Magento\Config\Model\Config\Loader
     */
    private $configLoader;
    /**
     * @var bool
     */
    private $usePhp;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    private $directoryList;

    /**
     * Process constructor.
     *
     * @param \Magento\Config\Model\Config\Loader             $loader
     * @param \Psr\Log\LoggerInterface                        $logger
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param bool                                            $cl
     * @param array                                           $data
     *
     * @throws \Exception
     */
    public function __construct(
        Loader $loader,
        LoggerInterface $logger,
        DirectoryList $directoryList,
        $cl = false,
        array $data = []
    ) {
        $this->directoryList = $directoryList;
        $this->logger        = $logger;
        $this->configLoader  = $loader;
        if ($cl != false) {
            $this->command = $cl;
            $this->runCom();
        }
        parent::__construct($data);
    }

    /**
     * @param boolean $command
     *
     * @param bool    $usePhp
     *
     * @return $this
     */
    public function setCommand($command, $usePhp = true)
    {
        $this->command = $command;
        $this->usePhp = $usePhp;

        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    private function runCom()
    {
        if ($this->usePhp) {
            $config = $this->getConfig('xpos/advance');
            if (isset($config['xpos/advance/php_run_time'])) {
                $this->command = $config['xpos/advance/php_run_time']['value'] . " " . $this->directoryList->getRoot() . "/" . $this->command;
            } else {
                $this->logger->debug("User hasn't set php run time");
                throw new Exception("Can't find php run time ");
            }
        }
        // fix file permission
        $command = 'nohup '
                   . $this->command
                   . ' > '
                   . $this->directoryList->getRoot()
                   . '/var/log/output.out 2> '
                   . $this->directoryList->getRoot()
                   . '/var/log/output.err < /dev/null &';
        //$command = $this->command;
        exec($command, $op);
        //var_dump(shell_exec($command));die;

        if (isset($op[0])) {
            $this->pid = (int)$op[0];
        }

        return $this;
    }

    /**
     * @param $path
     *
     * @return array
     */
    private function getConfig($path)
    {
        return $this->configLoader->getConfigByPath($path, 'default', 0);
    }

    /**
     * @param $pid
     *
     * @return $this
     */
    public function setPid($pid)
    {
        $this->pid = $pid;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * @return bool
     */
    public function status()
    {
        $command = 'ps -p ' . $this->pid;
        exec($command, $op);
        if (!isset($op[1])) {
            return false;
        }
        return true;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function start()
    {
        if ($this->command != '') {
            $this->runCom();
        }
        return true;
    }

    /**
     * @return bool
     */
    public function stop()
    {
        $command = 'kill ' . $this->pid;
        exec($command);
        if ($this->status() == false) {
            return true;
        }
        return false;
    }
}
