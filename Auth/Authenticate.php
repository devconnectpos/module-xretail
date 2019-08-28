<?php
/**
 * Created by KhoiLe - mr.vjcspy@gmail.com
 * Date: 7/4/17
 * Time: 2:02 PM
 */

namespace SM\XRetail\Auth;

use Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use SM\XRetail\Controller\V1\Xretail;

class Authenticate
{
    private $_configuration;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $encryptor;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface $encryptor
    ) {
        $this->_configuration = $scopeConfig;
        $this->encryptor      = $encryptor;
    }

    /**
     * @param \SM\XRetail\Controller\V1\Xretail $controller
     *
     * @return $this
     * @throws \Exception
     */
    public function authenticate(Xretail $controller)
    {
        $tokenKey = $controller->getRequest()->getParam('__token_key');
        $retailLicense = base64_encode($this->encryptor->decrypt($this->_configuration->getValue("xpos/general/retail_license")));

        if ($tokenKey && $tokenKey === $retailLicense) {
            return $this;
        }
        $controller->setStatusCode(403);
        throw new Exception('Forbidden');
    }
}
