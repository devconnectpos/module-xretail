<?php
namespace SM\XRetail\Model\ResourceModel\Permission;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            'SM\XRetail\Model\Permission',
            'SM\XRetail\Model\ResourceModel\Permission'
        );
    }
}
