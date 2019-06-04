<?php
/**
 * Created by mr.vjcspy@gmail.com - khoild@smartosc.com.
 * Date: 19/01/2017
 * Time: 14:47
 */

namespace SM\XRetail\Repositories;


use Magento\Framework\DataObject;
use SM\Core\Api\Data\MediaLibrary;
use SM\XRetail\Repositories\Contract\ServiceAbstract;

/**
 * Class MediaLibrabyManagement
 *
 * @package SM\XRetail\Repositories
 */
class MediaLibraryManagement extends ServiceAbstract {

    /**
     * @var \SM\XRetail\Model\ResourceModel\MediaLibrary\CollectionFactory
     */
    protected $mediaLibraryCollectionFactory;
    /**
     * @var \SM\XRetail\MediaLibrary\MediaLibraryFactory
     */
    protected $mediaLibraryFactory;

    /**
     * MediaLibrabyManagement constructor.
     *
     * @param \Magento\Framework\App\RequestInterface                   $requestInterface
     * @param \SM\XRetail\Helper\DataConfig                             $dataConfig
     * @param \Magento\Store\Model\StoreManagerInterface                $storeManager
     * @param \SM\XRetail\Model\ResourceModel\MediaLibrary\CollectionFactory $mediaLibraryCollectionFactory,
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $requestInterface,
        \SM\XRetail\Helper\DataConfig $dataConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \SM\XRetail\Model\ResourceModel\MediaLibrary\CollectionFactory $mediaLibraryCollectionFactory,
        \SM\XRetail\Model\MediaLibraryFactory $mediaLibraryFactory
    )
    {
        $this->mediaLibraryFactory           = $mediaLibraryFactory;
        $this->mediaLibraryCollectionFactory = $mediaLibraryCollectionFactory;
        parent::__construct($requestInterface, $dataConfig, $storeManager);
    }

    /**
     * @return array
     */
    public function getMediaLibraryData()
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

        /** @var \SM\XRetail\Model\MediaLibrary $mediaLibrary */
        $mediaLibrary = $this->mediaLibraryFactory->create();
        $id      = $data->getId();
        if ($id) {
            $mediaLibrary->load($id);
            if (!$mediaLibrary->getId())
                throw new \Exception("Can't find media");
        }
        $data->unsetData('id');

        if ($data->getData('is_active') == true) {
            $is_active = 1;
        }
        else {
            $is_active = 0;
        }

        $data->setData('is_active', $is_active);
        $mediaLibrary->addData($data->getData())->save();

        $searchCriteria = new DataObject(
            [
                'ids' => $mediaLibrary->getId()
            ]);

        return $this->load($searchCriteria)->getOutput();
    }

    public function delete()
    {
        $data = $this->getRequestData();
        if ($id = $data->getData('id')) {
            /** @var \SM\XRetail\Model\MediaLibrary $mediaLibrary */
            $mediaLibrary = $this->mediaLibraryFactory->create();
            $mediaLibrary->load($id);
            if (!$mediaLibrary->getId()) {
                throw new \Exception("Can not find media data");
            } else {
                $mediaLibrary->delete();
            }
        } else {
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

        $collection = $this->getMediaLibraryCollection($searchCriteria);

        $items = [];
        if ($collection->getLastPageNumber() < $searchCriteria->getData('currentPage')) {
        }
        else
            foreach ($collection as $item) {
                $i = new MediaLibrary();
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
     * @return \SM\XRetail\Model\ResourceModel\MediaLibrary\Collection
     */
    public function getMediaLibraryCollection(DataObject $searchCriteria)
    {
        /** @var \SM\XRetail\Model\ResourceModel\MediaLibrary\Collection $collection */
        $collection = $this->mediaLibraryCollectionFactory->create();

        if ($searchCriteria->getData('ids')) {
            $collection->addFieldToFilter('id', ['in' => explode(",", $searchCriteria->getData('ids'))]);
        }

        return $collection;
    }

}
