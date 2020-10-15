<?php
/**
 * Created by Nomad
 * Date: 9/4/19
 * Time: 10:15 AM
 */

namespace SM\XRetail\Controller\Contract;

use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\PageCache\Model\Config;
use SM\XRetail\Auth\Authenticate;
use SM\XRetail\Model\Api\Configuration;

class ApiAbstract extends Action
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var
     */
    protected $output;
    /**
     * @var string
     */
    protected $function;
    /**
     * @var
     */
    protected $service;
    /**
     * @var \SM\XRetail\Model\Api\Configuration
     */
    protected $apiConfig;
    /**
     * @var array Data current Router in Config
     */
    protected $dataRouter;
    /**
     * @var \Magento\PageCache\Model\Config
     */
    protected $config;
    /**
     * @var integer
     */
    private $statusCode;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;
    private $accessTokenManagement;

    /**
     * ApiAbstract constructor.
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param Configuration $configuration
     * @param Config $config
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        Configuration $configuration,
        Config $config,
        ObjectManagerInterface $objectManager
    ) {
        $this->config      = $config;
        $this->apiConfig  = $configuration;
        $this->scopeConfig = $scopeConfig;
        $this->objectManager = $objectManager;
        parent::__construct($context);
    }

    /**
     * Dispatch request
     *
     * @return void
     */
    public function execute()
    {
        // TODO: Implement execute() method.
    }

    /**
     * @return mixed
     * @throws Exception
     */
    protected function jsonOutput()
    {
        /** @var \Magento\Framework\App\Response\Http $response */
        $response = $this->getResponse();

        $response->clearHeaders()
                 ->setHeader('Content-Type', 'application/json', true)
                 ->setHeader('Access-Control-Allow-Headers', 'Content-Type', true)
                 ->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, OPTIONS, PATCH, DELETE', true)
                 ->setHeader("Access-Control-Allow-Origin", "*", true);

        if (Authenticate::$isExpiredToken === true) {
            $response->setHeader('X-Token', $this->getLastTokenValue(), true);
            $response->setHeader('Access-Control-Expose-Headers', "*", true);
        }

        /*See Note: Magento 2 Full Page caching */
        if ($this->config->isEnabled()) {
            /*
             * Fix for magento 2ee:
             * \Magento\Framework\App\PageCache\Kernel::process
             * */
            $response->setPublicHeaders($this->config->getTtl());
        }
    
        return $response->setBody(json_encode($this->output, JSON_HEX_QUOT | JSON_HEX_APOS | JSON_HEX_TAG | JSON_HEX_AMP));
    }

    /**
     * @return mixed
     */
    protected function getAccessTokenManagement()
    {
        if (is_null($this->accessTokenManagement)) {
            $this->accessTokenManagement = $this->objectManager->get('\SM\XRetail\Repositories\AccessTokenManagement');
        }
        return $this->accessTokenManagement;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    private function getLastTokenValue()
    {
        $userId = $this->getRequest()->getParam('userId');

        if (!$userId) {
            throw new Exception(__('Wrong Username. Please try again'));
        }

        $token = $this->getAccessTokenManagement()->getTokenCollectionByUserId($userId);

        if ($token !== null) {
            return $token->getData('token');
        }

        return null;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getPath()
    {
        /** @var \Magento\Framework\App\Request\Http $request */
        $request = $this->getRequest();

        $path = explode('/', $request->getPathInfo());
        if (!isset($path[4])) {
            throw new Exception('router not found');
        }

        return $path[4];
    }

    /**
     * @throws \Exception
     */
    public function checkPath()
    {
        $path = $this->getPath();

        $this->validateRouter($path);
    }

    /**
     * @param $router
     *
     * @return bool
     * @throws \Exception
     */
    private function validateRouter($router)
    {
        /** @var \Magento\Framework\App\Response\Http $response */
        $response = $this->getResponse();

        $allXRetailApiRouter = $this->apiConfig->getApiRouters();

        foreach ($allXRetailApiRouter as $routerName => $r) {
            if ($router == $routerName) {
                if (isset($r[0]) && is_array($r[0])) {
                    foreach ($r as $routerData) {
                        if (!$this->validRouterData($routerData)) {
                            continue;
                        } else {
                            return true;
                        }
                    }
                } else {
                    if (!$this->validRouterData($r)) {
                        continue;
                    } else {
                        return true;
                    }
                }
            }
        }
        $response->setHttpResponseCode(404);
        throw new Exception('Router not found');
    }

    /**
     * @param $routerData
     *
     * @return bool
     */
    private function validRouterData($routerData)
    {
        /** @var \Magento\Framework\App\Request\Http $request */
        $request = $this->getRequest();

        //check method
        if (!isset($routerData['method'])) {
            return false;
        }
        //check type Method
        if (!call_user_func([$request, 'is' . $routerData['method']])) {
            return false;
        }
        //check function function
        $modelName = $routerData['service'];
        $this->setService($this->_objectManager->create($modelName));
        $this->setFunction($routerData['function']);
        if (!method_exists($this->getService(), $this->getFunction())) {
            return false;
        }

        return $this->dataRouter = $routerData;
    }

    /**
     * @return mixed
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param mixed $service
     */
    public function setService($service)
    {
        $this->service = $service;
    }

    /**
     * @return mixed
     */
    public function getFunction()
    {
        return $this->function;
    }

    /**
     * @param mixed $function
     */
    public function setFunction($function)
    {
        $this->function = $function;
    }

    /**
     * @param $output
     */
    public function setOutput($output)
    {
        $this->output = $output;
    }

    /**
     * @param     $error
     * @param int $code
     *
     * @return \Magento\Framework\App\Response\Http
     */
    public function outputError($error, $code = 400)
    {
        /** @var \Magento\Framework\App\Response\Http $response */
        $response = $this->getResponse();

        /** @var \Magento\Framework\App\Request\Http $request */
        $request = $this->getRequest();

        if ($request->isOptions()) {
            $response->clearHeaders()
                     ->setHeader('Content-Type', 'application/json', true)
                     ->setHeader("Access-Control-Allow-Origin", "*", true)
                     ->setHeader("Access-Control-Allow-Methods", "PUT,GET,POST,PATCH,DELETE", true)
                     ->setHeader(
                         "Access-Control-Allow-Headers",
                         "Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With,Authorization-Code",
                         true
                     )
                     ->setHttpResponseCode(200);
        } else {
            $response->clearHeaders()
                     ->setHeader('Content-Type', 'application/json', true)
                     ->setHeader("Access-Control-Allow-Origin", "*", true)
                     ->setHttpResponseCode($code | 400)
                     ->setBody(
                         json_encode(
                             [
                                 'error'   => true,
                                 'message' => $error
                             ]
                         )
                     );
        }
        /*See Note: Magento 2 Full Page caching */
        if ($this->config->isEnabled()) {
            $response->setPublicHeaders($this->config->getTtl());
        }

        return $response;
    }

    /**
     * @param       $name
     * @param array $data
     *
     * @return $this
     */
    public function dispatchEvent($name, array $data = [])
    {
        $this->_eventManager->dispatch($name, $data);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param mixed $statusCode
     *
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }
}
