<?php
namespace SM\XRetail\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Permission extends AbstractModel implements PermissionInterface, IdentityInterface
{
    const CACHE_TAG = 'sm_xretail_permission';

    protected function _construct()
    {
        $this->_init('SM\XRetail\Model\ResourceModel\Permission');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
