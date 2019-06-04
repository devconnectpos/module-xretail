<?php
namespace SM\XRetail\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Outlet extends AbstractModel implements OutletInterface, IdentityInterface
{
    const CACHE_TAG = 'sm_xretail_outlet';

    protected function _construct()
    {
        $this->_init('SM\XRetail\Model\ResourceModel\Outlet');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
