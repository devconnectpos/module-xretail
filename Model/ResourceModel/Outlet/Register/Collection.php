<?php
namespace SM\XRetail\Model\ResourceModel\Outlet\Register;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct() {
        $this->_init('SM\XRetail\Model\Outlet\Register', 'SM\XRetail\Model\ResourceModel\Outlet\Register');
    }
}
