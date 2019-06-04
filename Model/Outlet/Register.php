<?php
namespace SM\XRetail\Model\Outlet;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Register extends AbstractModel implements IdentityInterface
{

    const CACHE_TAG = 'sm_xretail_register';

    protected function _construct()
    {
        $this->_init('SM\XRetail\Model\ResourceModel\Outlet\Register');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
