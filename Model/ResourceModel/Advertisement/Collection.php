<?php
namespace SM\XRetail\Model\ResourceModel\Advertisement;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected function _construct()
    {
        $this->_init('SM\XRetail\Model\Advertisement', 'SM\XRetail\Model\ResourceModel\Advertisement');
    }
}
