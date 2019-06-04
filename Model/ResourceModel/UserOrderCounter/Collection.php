<?php
namespace SM\XRetail\Model\ResourceModel\UserOrderCounter;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            'SM\XRetail\Model\UserOrderCounter',
            'SM\XRetail\Model\ResourceModel\UserOrderCounter'
        );
    }
}
