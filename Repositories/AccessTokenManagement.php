<?php

namespace SM\XRetail\Repositories;

use Magento\Framework\App\RequestInterface;
use SM\XRetail\Auth\Authenticate;
use SM\XRetail\Helper\DataConfig;
use Magento\Store\Model\StoreManagerInterface;
use SM\XRetail\Repositories\Contract\ServiceAbstract;
use \Firebase\JWT\JWT;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Config\Model\Config\Loader;
use SM\XRetail\Model\ResourceModel\Token\CollectionFactory;
use SM\XRetail\Model\TokenFactory;

class AccessTokenManagement extends ServiceAbstract
{
    const XRETAIL_TOKEN_EXPIRE_AFTER = 'xpos/secure/token_expire_after';
    const API_SECRET_KEY_PATH = 'xretail/pos/api_secret_key';
    /**
     * @var Loader
     */
    protected $configLoader;
    /**
     * @var CollectionFactory
     */
    protected $tokenCollectionFactory;
    /**
     * @var TokenFactory
     */
    protected $tokenFactory;

    /**
     * AccessTokenManagement constructor.
     * @param RequestInterface $requestInterface
     * @param DataConfig $dataConfig
     * @param StoreManagerInterface $storeManager
     * @param Loader $loader
     * @param CollectionFactory $tokenCollectionFactory
     * @param TokenFactory $tokenFactory
     */
    public function __construct(
        RequestInterface $requestInterface,
        DataConfig $dataConfig,
        StoreManagerInterface $storeManager,
        Loader $loader,
        CollectionFactory $tokenCollectionFactory,
        TokenFactory $tokenFactory
    ){
        parent::__construct($requestInterface, $dataConfig, $storeManager);
        $this->configLoader  = $loader;
        $this->tokenCollectionFactory = $tokenCollectionFactory;
        $this->tokenFactory = $tokenFactory;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getSecretKey() {
        $config = $this->configLoader->getConfigByPath('xretail/pos', 'default', 0);
        if (!isset($config[AccessTokenManagement::API_SECRET_KEY_PATH])) {
            throw new Exception(__('Invalid Signature. Please try again'));
        }
        return $config[AccessTokenManagement::API_SECRET_KEY_PATH]['value'];
    }


    /**
     * @return int|mixed
     */
    private function getTimeExpireToken() {
        $config = $this->configLoader->getConfigByPath('xpos/secure', 'default', 0);
        return isset($config[AccessTokenManagement::XRETAIL_TOKEN_EXPIRE_AFTER]) ?
            $config[AccessTokenManagement::XRETAIL_TOKEN_EXPIRE_AFTER]['value'] : 60;
    }

    /**
     * @return mixed
     */
    public function generateAccessToken()
    {
        $tokenExpireAfter = $this->getTimeExpireToken();

        //Generate New Secret Key for each time generate access Token
        $secretKey = $this->getSecretKey();

        $tokenExpireAfter = !!$tokenExpireAfter ? floatval($tokenExpireAfter) : 600;
        $iat = time(); // time of token issued at
        $nbf = $iat + 10; //not before in seconds
        $exp = $iat + $tokenExpireAfter; // expire time of token in seconds

        $userId = $this->getRequest()->getParam('userId');
        $website = $this->getRequest()->getParam('website');

        if (!$userId) {
            throw new Exception(__('Wrong Username. Please try again'));
        }

        $token = array(
            "iat" => $iat,
            "nbf" => $nbf,
            "exp" => $exp,
            "userId" => $userId,
            "website" => $website
        );
        $token = JWT::encode($token, $secretKey);
        $this->saveTokenForUserId($userId, $token, $exp);

        return $token;
    }

    /**
     * @param $userId
     * @param $token
     * @param $exp
     * @return mixed|null
     */
    private function saveTokenForUserId($userId, $token, $exp) {
        if (!$userId || !$token) {
            throw new Exception(__('Wrong Username. Please try again'));
        }
        $tokenCollection = $this->getTokenCollectionByUserId($userId);

        if (!!$tokenCollection) {
            $tokenCollection->setData('token', $token);
            $tokenCollection->setData('expired_at', $exp);
            $tokenCollection->save();
        } else {
            $tokenCollection = $this->createNewTokenForUser($userId, $token, $exp);
        }
        return $tokenCollection;
    }

    /**
     * @param $userId
     * @param $token
     * @param $exp
     * @return mixed
     */
    public function createNewTokenForUser($userId, $token, $exp)
    {
        $tokenFactory = $this->tokenFactory->create();

        $tokenFactory->setData('token', $token);
        $tokenFactory->setData('expired_at', $exp);
        $tokenFactory->setData('user_id', $userId);
        $tokenFactory->save();

        return $tokenFactory;
    }

    /**
     * @param null $userId
     * @return mixed
     */
    public function getTokenCollectionByUserId($userId = null)
    {
        if (!$userId) {
            throw new Exception(__('Wrong Username. Please try again'));
        }

        $collection = $this->tokenCollectionFactory->create();

        $collection->addFieldToFilter('user_id', $userId);

        return $collection->getSize() > 0 ? $collection->getFirstItem() : null;
    }

}
