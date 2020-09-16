<?php
namespace SM\XRetail\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Token extends AbstractModel
{
    const CACHE_TAG = 'sm_token';

    protected function _construct()
    {
        $this->_init('SM\XRetail\Model\ResourceModel\Token');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
