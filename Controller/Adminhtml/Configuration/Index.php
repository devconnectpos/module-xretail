<?php

namespace SM\XRetail\Controller\Adminhtml\Configuration;

class Index extends \Magento\Backend\App\AbstractAction
{
    public function execute()
    {
        $this->_redirect('adminhtml/system_config/edit', ['section' => 'xpos']);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}
