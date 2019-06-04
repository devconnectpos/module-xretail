<?php
namespace SM\XRetail\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Receipt extends AbstractDb
{
    protected function _construct() {
        $this->_init('sm_xretail_receipt', 'id');
    }
}
