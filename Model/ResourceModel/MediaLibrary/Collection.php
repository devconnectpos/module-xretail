<?php
namespace SM\XRetail\Model\ResourceModel\MediaLibrary;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('SM\XRetail\Model\MediaLibrary','SM\XRetail\Model\ResourceModel\MediaLibrary');
    }
}
