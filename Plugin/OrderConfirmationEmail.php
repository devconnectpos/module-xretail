<?php

namespace SM\XRetail\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Model\Order\Email\Container\OrderIdentity;

class OrderConfirmationEmail
{
    const XML_PATH_CPOS_EMAIL_TEMPLATE = 'xpos/order_email/template';
    const XML_PATH_CPOS_EMAIL_ENABLED = 'xpos/order_email/enabled';
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param OrderIdentity $subject
     * @param $result
     * @return mixed
     */
    public function afterGetTemplateId(
        OrderIdentity $subject,
        $result
    ) {
        if ($this->getConfigValue(self::XML_PATH_CPOS_EMAIL_ENABLED, $subject->getStore()->getStoreId())
            && \SM\Sales\Repositories\OrderManagement::$FROM_API) {
            return $this->getConfigValue(self::XML_PATH_CPOS_EMAIL_TEMPLATE, $subject->getStore()->getStoreId());
        }

        return $result;
    }

    public function afterIsEnabled(
        OrderIdentity $subject,
        $result
    ) {
        if (\SM\Sales\Repositories\OrderManagement::$FROM_API) {
            return $this->getConfigValue(self::XML_PATH_CPOS_EMAIL_ENABLED, $subject->getStore()->getStoreId());
        }
        return $result;
    }

    /**
     * Return store configuration value
     *
     * @param string $path
     * @param int $storeId
     * @return mixed
     */
    protected function getConfigValue($path, $storeId)
    {
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
