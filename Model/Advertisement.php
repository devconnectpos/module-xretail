<?php
namespace SM\XRetail\Model;
class Advertisement extends \Magento\Framework\Model\AbstractModel implements AdvertisementInterface, \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'sm_advertisement';

    protected function _construct()
    {
        $this->_init('SM\XRetail\Model\ResourceModel\Advertisement');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
