<?php

namespace SM\XRetail\Observer;

use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use SM\Performance\Gateway\Sender;
use Magento\Framework\App\Config\ScopeConfigInterface;
use SM\XRetail\Helper\Data;

class ChangeConfigXposAfter implements ObserverInterface
{
    protected static $instance;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManagement;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    private $encryptor;

    private $helper;
    /**
     * @var string
     */
    protected $licenseKey;

    /**
     * @var string
     */
    protected $baseUrl;

    protected $apiVersion;

    protected $sender;

    public function __construct(
        StoreManagerInterface $storeManagement,
        LoggerInterface $logger,
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface $encryptor,
        Data $helper,
        Sender $sender
    ) {
        $this->encryptor       = $encryptor;
        $this->scopeConfig     = $scopeConfig;
        $this->logger          = $logger;
        $this->storeManagement = $storeManagement;
        $this->helper          = $helper;
        $this->sender          = $sender;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(Observer $observer)
    {
        if (is_null($this->licenseKey)) {
            $this->licenseKey = $this->encryptor->decrypt($this->scopeConfig->getValue("xpos/general/retail_license"));
        }

        if (is_null($this->baseUrl)) {
            $this->baseUrl = rtrim($this->storeManagement->getStore()
                                                         ->getBaseUrl(UrlInterface::URL_TYPE_LINK, true), '/');
        }
        if (is_null($this->apiVersion)) {
            $this->apiVersion = $this->helper->getCurrentVersion();
        }
        $data       = [
            'url'         => $this->baseUrl,
            'license_key' => $this->licenseKey,
            'api_version' => $this->apiVersion
        ];
        $this->sender->sendPostViaSocket($this->getBaseUrl(), $data);
    }

    protected function getBaseUrl()
    {
        return Sender::$CLOUD_URL."/methods/client.save_api_version";
    }
}
