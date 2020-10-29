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
use \Firebase\JWT\JWT;
use \Firebase\JWT\ExpiredException;
use SM\XRetail\Repositories\AccessTokenManagement;
use Magento\Config\Model\Config\Loader;

class Authenticate
{
    /**
     * @var Loader
     */
    protected $configLoader;
    /**
     * @var EncryptorInterface
     */
    protected $encryptor;
    /**
     * @var AccessTokenManagement
     */
    protected $accessTokenManagement;
    /**
     * @var bool
     */
    public static $isExpiredToken = false;
    const ALGORITHM = 'HS256';

    /**
     * Authenticate constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param EncryptorInterface $encryptor
     * @param AccessTokenManagement $accessTokenManagement
     * @param Loader $loader
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface $encryptor,
        AccessTokenManagement $accessTokenManagement,
        Loader $loader
    ) {
        $this->configLoader  = $loader;
        $this->encryptor   = $encryptor;
        $this->accessTokenManagement = $accessTokenManagement;
    }

    /**
     * @param Xretail $controller
     * @return $this
     * @throws Exception
     */
    public function authenticate(Xretail $controller)
    {
        if ($controller->getPath() === 'token' || $controller->getPath() === 'magento-invoice') {
            return $this;
        }
        if ($controller->getRequest()) {
            $authHeader = $controller->getRequest()->getHeader('authorization');
            $userId = $controller->getRequest()->getParam('userId');
            if (!$userId) {
                throw new Exception(__('Wrong Username. Please try again'));
            }
            /*
             * Look for the 'authorization' header
             */
            if ($authHeader) {
                /*
                 * Extract the jwt from the Bearer
                 */
                list($jwt) = sscanf( $authHeader, 'Bearer %s');

                if ($jwt) {
                    try {
                        //Get Last secret key
                        $secretKey = $this->accessTokenManagement->getSecretKey();

                        JWT::$leeway = 10;
                        JWT::decode($jwt, $secretKey, array(Authenticate::ALGORITHM));
                    } catch (Exception $e) {
                        // Handle expired token
                        // Auto generate new token and add to response header
                        if ($e instanceof ExpiredException && $this->isLastToken($jwt, $userId)) {
                            Authenticate::$isExpiredToken = true;
                            $this->accessTokenManagement->generateAccessToken();
                            return $this;
                        }
                        /*
                         * the token was not able to be decoded.
                         * this is likely because the signature was not able to be verified (tampered token)
                         */
                        $controller->setStatusCode(401);
                        throw new Exception('Unauthorized. Permission Denied! '. $e->getMessage());
                    }
                } else {
                    /*
                     * No token was able to be extracted from the authorization header
                     */
                    $controller->setStatusCode(400);
                    throw new Exception('Bad Request');
                }
            } else {
                /*
                 * The request lacks the authorization token
                 */
                $controller->setStatusCode(400);
                throw new Exception('Token Not Found In Request');
            }
        } else {
            $controller->setStatusCode(405);
            throw new Exception('405 Method Not Allowed');
        }
    }

    /**
     * @param $userId
     * @return mixed
     * @throws Exception
     */
    private final function getLastTokenByUserId($userId)
    {
        $token = $this->accessTokenManagement->getTokenCollectionByUserId($userId);

        if ($token !== null) {
            return $token->getData('token');
        }

        return null;
    }

    /**
     * @param $token
     * @param $userId
     * @return bool
     * @throws Exception
     */
    private final function isLastToken($token, $userId)
    {
        return $token === $this->getLastTokenByUserId($userId);
    }
}
