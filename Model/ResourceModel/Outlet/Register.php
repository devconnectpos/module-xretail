<?php
namespace SM\XRetail\Model\ResourceModel\Outlet;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Register extends AbstractDb
{
    protected function _construct() {
        $this->_init('sm_xretail_register', 'id');
    }
}
