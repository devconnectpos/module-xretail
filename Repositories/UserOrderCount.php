<?php
/**
 * Created by mr.vjcspy@gmail.com - khoild@smartosc.com.
 * Date: 10/01/2017
 * Time: 09:31
 */

namespace SM\XRetail\Repositories;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Store\Model\StoreManagerInterface;
use SM\Core\Api\Data\XUserOrderCount;
use SM\XRetail\Helper\DataConfig;
use SM\XRetail\Model\ResourceModel\UserOrderCounter\CollectionFactory;
use SM\XRetail\Repositories\Contract\ServiceAbstract;

class UserOrderCount extends ServiceAbstract
{
    protected $orderCountFactory;

    public function __construct(
        RequestInterface $requestInterface,
        DataConfig $dataConfig,
        StoreManagerInterface $storeManager,
        CollectionFactory $orderCountFactory
    ) {
        $this->orderCountFactory = $orderCountFactory;
        parent::__construct($requestInterface, $dataConfig, $storeManager);
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    public function getUserOrderCount()
    {
        return $this->loadOrderCount($this->getSearchCriteria())->getOutput();
    }

    public function loadOrderCount($searchCriteria)
    {
        if (is_null($searchCriteria) || !$searchCriteria) {
            $searchCriteria = $this->getSearchCriteria();
        }

        $collection = $this->getOrderCountCollection($searchCriteria);

        $items = [];
        if ($collection->getLastPageNumber() < $searchCriteria->getData('currentPage')) {
        } else {
            foreach ($collection as $item) {
                $xUserOrderCount = new XUserOrderCount($item->getData());
                $items[]         = $xUserOrderCount;
            }
        }

        return $this->getSearchResult()
                    ->setSearchCriteria($searchCriteria)
                    ->setItems($items)
                    ->setTotalCount($collection->getSize())
                    ->setLastPageNumber($collection->getLastPageNumber());
    }

    protected function getOrderCountCollection(DataObject $searchCriteria)
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->orderCountFactory->create();
        if (is_nan($searchCriteria->getData('currentPage'))) {
            $collection->setCurPage(1);
        } else {
            $collection->setCurPage($searchCriteria->getData('currentPage'));
        }
        if (is_nan($searchCriteria->getData('pageSize'))) {
            $collection->setPageSize(
                DataConfig::PAGE_SIZE_LOAD_DATA
            );
        } else {
            $collection->setPageSize(
                $searchCriteria->getData('pageSize')
            );
        }

        return $collection;
    }
}
