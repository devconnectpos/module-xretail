<?php
namespace SM\XRetail\Model\ResourceModel\Receipt;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            'SM\XRetail\Model\Receipt',
            'SM\XRetail\Model\ResourceModel\Receipt'
        );
    }
}
