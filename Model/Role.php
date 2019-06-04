<?php
namespace SM\XRetail\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Role extends AbstractModel implements RoleInterface, IdentityInterface
{
    const CACHE_TAG = 'sm_xretail_role';

    protected function _construct()
    {
        $this->_init('SM\XRetail\Model\ResourceModel\Role');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
