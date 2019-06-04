<?php
namespace SM\XRetail\Model\ResourceModel\Role;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            'SM\XRetail\Model\Role',
            'SM\XRetail\Model\ResourceModel\Role'
        );
    }
}
