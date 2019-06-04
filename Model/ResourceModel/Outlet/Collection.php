<?php
namespace SM\XRetail\Model\ResourceModel\Outlet;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            'SM\XRetail\Model\Outlet',
            'SM\XRetail\Model\ResourceModel\Outlet'
        );
    }
}
