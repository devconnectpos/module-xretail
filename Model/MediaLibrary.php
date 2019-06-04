<?php
namespace SM\XRetail\Model;
class MediaLibrary extends \Magento\Framework\Model\AbstractModel implements MediaLibraryInterface, \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'sm_media_library';

    protected function _construct()
    {
        $this->_init('SM\XRetail\Model\ResourceModel\MediaLibrary');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
