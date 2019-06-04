<?php

namespace SM\XRetail\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class UserOrderCounter extends AbstractModel implements UserOrderCounterInterface, IdentityInterface
{

    const CACHE_TAG = 'sm_xretail_userordercounter';

    protected function _construct()
    {
        $this->_init('SM\XRetail\Model\ResourceModel\UserOrderCounter');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getOrderCount()
    {
        return $this->getData('order_count');
    }

    /**
     * @param $outletId
     * @param $registerId
     * @param $userId
     *
     * @return \Magento\Framework\DataObject
     * @throws \Exception
     */
    public function loadOrderCount($outletId, $registerId, $userId)
    {
        if (!$outletId || !$userId) {
            throw new \Exception("Must have param outlet_id and user_id");
        }
        $collection = $this->getCollection();

        return $collection->addFieldToFilter('outlet_id', $outletId)
            // Không filter user bởi vì nếu như thế các user khác nhau sẽ có chung id trên cùng 1 register
            //->addFieldToFilter('user_id', $userId)
                          ->addFieldToFilter('register_id', $registerId)
                          ->getFirstItem();
    }
}
