<?php
namespace SM\XRetail\Model\ResourceModel\Token;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            'SM\XRetail\Model\Token',
            'SM\XRetail\Model\ResourceModel\Token'
        );
    }
}
