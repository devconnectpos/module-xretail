<?php
/**
 * Created by IntelliJ IDEA.
 * User: vjcspy
 * Date: 20/06/2016
 * Time: 14:35
 */

namespace SM\XRetail\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\DateTime\DateTimeFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use SM\XRetail\Model\OutletFactory;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;

class Data extends AbstractHelper
{

    /**
     * @var \SM\XRetail\Model\Outlet\RegisterFactory
     */
    protected $outletFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezoneInterface;

    /**
     * @var array
     */
    private $storeTimezone = [];

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTimeFactory
     */
    protected $dateFactory;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var
     */
    protected $serializer;

    /**
     * @var string
     */
    public static $API_VERSION = '1.1.0';

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface
     * @param \Magento\Framework\Stdlib\DateTime\DateTimeFactory   $dateFactory
     * @param \SM\XRetail\Model\OutletFactory                      $outletFactory
     * @param \Magento\Framework\App\Helper\Context                $context
     * @param \Magento\Framework\ObjectManagerInterface            $objectManager
     * @param \Magento\Store\Model\StoreManagerInterface           $storeManager
     */
    public function __construct(
        TimezoneInterface $timezoneInterface,
        DateTimeFactory $dateFactory,
        OutletFactory $outletFactory,
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager
    ) {
        $this->dateFactory      = $dateFactory;
        $this->outletFactory     = $outletFactory;
        $this->timezoneInterface = $timezoneInterface;
        $this->objectManager     = $objectManager;
        $this->storeManager      = $storeManager;
        parent::__construct($context);
    }

    /**
     * @param      $mess
     * @param null $level
     * @param null $file
     *
     * @return \Zend\Log\Logger
     */
    public function addLog($mess, $level = null, $file = null)
    {
        if (is_null($file)) {
            $file = 'xRetail_api.log';
        }
        $writer = new Stream(BP . '/var/log/xretail' . $file);
        $logger = new Logger();
        $logger->addWriter($writer);

        return $logger->log($level, $mess);
    }

    /**
     * @param $mess
     */
    public static function addLogFile($mess)
    {
        $file = 'xretail_debug.txt';
        file_put_contents($file, $mess, FILE_APPEND | LOCK_EX);
    }

    /**
     * @return array
     */
    public function arraySumIdenticalKeys()
    {
        $arrays      = func_get_args();
        $arrayReduce = array_reduce(
            $arrays,
            function ($keys, $arr) {
                $a = $keys + $arr;

                return $a;
            },
            []
        );
        $keys        = array_keys($arrayReduce);
        $sums        = [];

        foreach ($keys as $key) {
            $sums[$key] = array_reduce($arrays, function ($sum, $arr) use ($key) {return $sum + @$arr[$key];});
        }

        return $sums;
    }

    /**
     * Wrap magento get Config
     *
     * @param $path
     *
     * @return mixed
     */
    public function getStoreConfig($path)
    {
        return $this->scopeConfig->getValue($path);
    }


    /**
     * @param integer $storeId
     *
     * @return mixed
     */
    protected function getTimezoneForStore($storeId)
    {
        if (!is_numeric($storeId)) {
            $storeId = $storeId->getId();
        }
        if (!isset($this->storeTimezone[$storeId])) {
            $storeManager = $this->storeManager->getStore($storeId);

            $this->storeTimezone[$storeId] = $this->timezoneInterface->getConfigTimezone(
                ScopeInterface::SCOPE_STORE,
                $storeManager->getCode()
            );
        }

        return $this->storeTimezone[$storeId];
    }

    /**
     * @param string  $time
     * @param integer $storeId
     *
     * @return string
     * @throws \Exception
     */
    public function convertTimeDBUsingTimeZone($time, $storeId)
    {
        $timeObject = new \DateTime($time);
        $timeObject->setTimezone(new \DateTimeZone($this->getTimezoneForStore($storeId)));

        return $timeObject->format(DateTime::DATETIME_PHP_FORMAT);
    }

    /**
     * @return string
     */
    public function getCurrentTime()
    {
        return $this->dateFactory->create()->gmtDate();
    }


    /**
     * @return \SM\XRetail\Model\Outlet
     */
    protected function getOutletModel()
    {
        return $this->outletFactory->create();
    }

    /**
     * @param $outletId
     *
     * @return mixed
     * @throws \Exception
     */
    public function getStoreByOutletId($outletId)
    {
        $outlet = $this->getOutletModel()->load($outletId);
        if (!$outlet->getId()) {
            throw new  \Exception("Can not find outlet data");
        } else {
            return $outlet->getData("store_id");
        }
    }


    /**
     * @return \Magento\Framework\Serialize\Serializer\Json
     */
    private function getSerialize()
    {
        if (is_null($this->serializer)) {
            $this->serializer = $this->objectManager->create('\Magento\Framework\Serialize\Serializer\Json');
        }

        return $this->serializer;
    }


    /**
     * @param $value
     *
     * @return string
     */
    public function serialize($value)
    {
        if (class_exists('\Magento\Framework\Serialize\Serializer\Json')) {
            return $this->getSerialize()->serialize($value);
        } else {
            return serialize($value);
        }
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function unserialize($value)
    {
        if (class_exists('\Magento\Framework\Serialize\Serializer\Json')) {
            return $this->getSerialize()->unserialize($value);
        } else {
            return unserialize($value);
        }
    }

    /**
     * @return string
     */
    public function getCurrentVersion()
    {
        return self::$API_VERSION;
    }

    /**
     * @return string
     */
    public static function generateCallTrace()
    {
        $e     = new \Exception();
        $trace = explode("\n", $e->getTraceAsString());
        // reverse array to make steps line up chronologically
        $trace = array_reverse($trace);
        array_shift($trace); // remove {main}
        array_pop($trace); // remove call to this method
        $length = count($trace);
        $result = [];

        for ($i = 0; $i < $length; $i++) {
            $result[] = ($i + 1) . ')' . substr($trace[$i], strpos($trace[$i], ' '));
            // replace '#someNum' with '$i)', set the right ordering
        }

        return "\t" . implode("\n\t", $result);
    }
}
