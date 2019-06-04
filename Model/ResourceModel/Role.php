<?php
namespace SM\XRetail\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Role extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('sm_role', 'id');
    }
}
