<?php

namespace SM\XRetail\Controller\V1;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\PageCache\Model\Config;
use SM\XRetail\Auth\Authenticate;
use SM\XRetail\Controller\Contract\ApiAbstract;
use SM\XRetail\Helper\Data as RetailHelper;
use SM\XRetail\Model\Api\Configuration;
use Exception;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class Xretail
 * Magento 2.3 implement new CORS site check. But we don't need implement the new interface here. We already support it on client by adding ajax tag.
 *
 * @package SM\XRetail\Controller\V1
 */
class Xretail extends ApiAbstract
{
    /**
     * @var \Magento\Config\Model\Config\Loader
     */
    protected $configLoader;

    /**
     * @var \SM\XRetail\Auth\Authenticate
     */
    private $authenticate;
    /**
     * @var RetailHelper
     */
    protected $retailHelper;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * Xretail constructor.
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param Configuration $configuration
     * @param Config $config
     * @param Authenticate $authenticate
     * @param RetailHelper $retailHelper
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        Configuration $configuration,
        Config $config,
        Authenticate $authenticate,
        RetailHelper $retailHelper,
        ObjectManagerInterface $objectManager
    ) {
        parent::__construct($context, $scopeConfig, $configuration, $config, $objectManager);
        $this->authenticate = $authenticate;
        $this->retailHelper = $retailHelper;
    }

    /**
     * @return \Magento\Framework\App\Response\Http|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            // authenticate
            $secureSetting = $this->retailHelper->getStoreConfig('xretail/pos/enable_secure_request');
            if ($secureSetting) {
                $this->requireFirebaseJwtSdk();
                $this->authenticate->authenticate($this);
            }

            // communicate with api before
            $this->dispatchEvent('rest_api_before', ['apiController' => $this]);
            // call service
            $this->setOutput(
                call_user_func_array(
                    [$this->getService(), $this->getFunction()],
                    $this->getRequest()->getParams()
                )
            );
            // communicate with api after
            $this->dispatchEvent('rest_api_after', ['apiController' => $this]);

            // output data
            return $this->jsonOutput();

        } catch (\Exception $e) {
            return $this->outputError($e->getMessage(), $this->getStatusCode());
        }
    }

    private function requireFirebaseJwtSdk() {
        if (!$this->retailHelper->checkFirebaseJwtSdk()) {
            throw new Exception(
                __('Firebase JWT is not installed yet. Please run \'composer require firebase/php-jwt\' to install Firebase JWT!')
            );
        }
    }
}
