<?php
namespace SM\XRetail\Model\ResourceModel;
class MediaLibrary extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('sm_media_library','id');
    }
}
