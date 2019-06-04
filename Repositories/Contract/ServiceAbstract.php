<?php
/**
 * Created by IntelliJ IDEA.
 * User: vjcspy
 * Date: 20/06/2016
 * Time: 11:35
 */

namespace SM\XRetail\Repositories\Contract;

use Exception;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
use SM\Core\Api\SearchResult;
use SM\Core\Model\DataObject;
use SM\XRetail\Helper\DataConfig;

class ServiceAbstract
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;
    /**
     * @var DataObject
     */
    protected $searchCriteria;
    /**
     * @var \SM\XRetail\Helper\DataConfig
     */
    protected $dataConfig;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \SM\Core\Api\SearchResult
     */
    protected $searchResult;

    protected $requestData;

    public function __construct(
        RequestInterface $requestInterface,
        DataConfig $dataConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->request      = $requestInterface;
        $this->dataConfig  = $dataConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * @return \SM\Core\Api\SearchResult
     */
    public function getSearchResult()
    {
        if (is_null($this->searchResult)) {
            $this->searchResult = new SearchResult();
        }

        return $this->searchResult;
    }

    /**
     * @return \Magento\Framework\App\RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Retrieve search criteria as DataObject
     *
     * @return \Magento\Framework\DataObject
     * @throws \Exception
     */
    public function getSearchCriteria()
    {
        if (is_null($this->searchCriteria)) {
            if (is_null($this->getRequest()->getParam('searchCriteria'))) {
                throw new Exception('Not found field: searchCriteria');
            } else {
                $this->searchCriteria = new \Magento\Framework\DataObject($this->getRequest()->getParam('searchCriteria'));
            }
        }

        return $this->searchCriteria;
    }

    /**
     * @return \SM\XRetail\Helper\DataConfig
     */
    public function getDataConfig()
    {
        return $this->dataConfig;
    }

    /**
     * @return \Magento\Store\Model\StoreManagerInterface
     */
    public function getStoreManager()
    {
        return $this->storeManager;
    }

    public function getRequestData()
    {
        if (is_null($this->requestData)) {
            $this->requestData = new DataObject($this->getRequest()->getParams());
        }

        return $this->requestData;
    }
}
