<?php
/**
 * Created by mr.vjcspy@gmail.com - khoild@smartosc.com.
 * Date: 19/01/2017
 * Time: 14:47
 */

namespace SM\XRetail\Repositories;


use Magento\Framework\DataObject;
use SM\Core\Api\Data\Advertisement;
use SM\XRetail\Repositories\Contract\ServiceAbstract;

/**
 * Class AdvertisementManagement
 *
 * @package SM\XRetail\Repositories
 */
class AdvertisementManagement extends ServiceAbstract {

    /**
     * @var \SM\XRetail\Model\ResourceModel\Advertisement\CollectionFactory
     */
    protected $advertisementCollectionFactory;
    /**
     * @var \SM\XRetail\Advertisement\AdvertisementFactory
     */
    protected $advertisementFactory;

    /**
     * AdvertisementManagement constructor.
     *
     * @param \Magento\Framework\App\RequestInterface                   $requestInterface
     * @param \SM\XRetail\Helper\DataConfig                             $dataConfig
     * @param \Magento\Store\Model\StoreManagerInterface                $storeManager
     * @param \SM\XRetail\Model\ResourceModel\Advertisement\CollectionFactory $advertisementCollectionFactory,
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $requestInterface,
        \SM\XRetail\Helper\DataConfig $dataConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \SM\XRetail\Model\ResourceModel\Advertisement\CollectionFactory $advertisementCollectionFactory,
        \SM\XRetail\Model\AdvertisementFactory $advertisementFactory
    )
    {
        $this->advertisementFactory           = $advertisementFactory;
        $this->advertisementCollectionFactory = $advertisementCollectionFactory;
        parent::__construct($requestInterface, $dataConfig, $storeManager);
    }

    /**
     * @return array
     */
    public function getAdvertisementData()
    {
        return $this->load($this->getSearchCriteria())->getOutput();
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function save()
    {
        $data = $this->getRequestData();

        /** @var \SM\XRetail\Model\Advertisement $advertisement */
        $advertisement = $this->advertisementFactory->create();

        $id      = $data->getId();
        if ($id) {
            $advertisement->load($id);
            if (!$advertisement->getId())
                throw new \Exception("Can't find advertisement");
        }
        $data->unsetData('id');
        $list_media = json_encode(isset($data['list_media']) ? $data['list_media'] : []);
        $data->setData('list_media', $list_media);
        if ($data->getData('is_active') == true) {
            $is_active = 1;
        }
        else {
            $is_active = 0;
        }

        $data->setData('is_active', $is_active);
//        var_dump($data);
//        die();
        $advertisement->addData($data->getData())->save();

        $searchCriteria = new DataObject(
            [
                'ids' => $advertisement->getId()
            ]);

        return $this->load($searchCriteria)->getOutput();
    }

    public function delete()
    {
        $data = $this->getRequestData();
        if ($id = $data->getData('id')) {
            /** @var \SM\XRetail\Model\Advertisement $advertisement */
            $advertisement = $this->advertisementFactory->create();
            $advertisement->load($id)->delete();
        }
        else {
            throw new \Exception("Please define id");
        }
    }

    /**
     * @param \Magento\Framework\DataObject $searchCriteria
     *
     * @return \SM\Core\Api\SearchResult
     */
    public function load(DataObject $searchCriteria)
    {
        if (is_null($searchCriteria) || !$searchCriteria)
            $searchCriteria = $this->getSearchCriteria();

        $collection = $this->getAdvertisementCollection($searchCriteria);

        $items = [];
        if ($collection->getLastPageNumber() < $searchCriteria->getData('currentPage')) {
        }
        else
            foreach ($collection as $item) {
                $i = new Advertisement();
                $items[] = $i->addData($item->getData());
            }

        return $this->getSearchResult()
                    ->setSearchCriteria($searchCriteria)
                    ->setItems($items)
                    ->setTotalCount($collection->getSize());
    }

    /**
     * @param \Magento\Framework\DataObject $searchCriteria
     *
     * @return \SM\XRetail\Model\ResourceModel\Advertisement\Collection
     */
    public function getAdvertisementCollection(DataObject $searchCriteria)
    {
        /** @var \SM\XRetail\Model\ResourceModel\Advertisement\Collection $collection */
        $collection = $this->advertisementCollectionFactory->create();

        if ($searchCriteria->getData('ids')) {
            $collection->addFieldToFilter('id', ['in' => explode(",", $searchCriteria->getData('ids'))]);
        }

        return $collection;
    }

}
