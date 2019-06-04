<?php
namespace SM\XRetail\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Receipt extends AbstractModel implements ReceiptInterface, IdentityInterface
{
    const CACHE_TAG = 'sm_xretail_receipt';

    protected function _construct()
    {
        $this->_init('SM\XRetail\Model\ResourceModel\Receipt');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
