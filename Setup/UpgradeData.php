<?php
/**
 * Created by Nomad
 * Date: 7/23/19
 * Time: 6:59 PM
 */

namespace SM\XRetail\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (version_compare($context->getVersion(), '0.0.8', '<')) {
            $this->addSettingDefaultPhpRunTime($setup);
        }
        $installer->endSetup();
    }

    protected function addSettingDefaultPhpRunTime(ModuleDataSetupInterface $setup)
    {
        $configData = $setup->getTable('core_config_data');
        $data = [
            'path'     => "xpos/advance/php_run_time",
            'value'    => 'php',
            'scope'    => 'default',
            'scope_id' => 0,
        ];
        $setup->getConnection()->insertOnDuplicate($configData, $data, ['value']);
    }
}
