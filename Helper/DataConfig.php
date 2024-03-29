<?php
/**
 * Created by IntelliJ IDEA.
 * User: vjcspy
 * Date: 20/06/2016
 * Time: 14:24
 */

namespace SM\XRetail\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class DataConfig
 *
 * @package SM\XRetail\Helper
 */
class DataConfig extends AbstractHelper
{
    const PAGE_SIZE_LOAD_PRODUCT = 100;
    const PAGE_SIZE_LOAD_PRODUCT_CACHED = 5000;
    const PAGE_SIZE_LOAD_CUSTOMER = 200;
    const PAGE_SIZE_LOAD_DATA = 200;

    /**
     * @return bool
     */
    public function getSupportCustomOptionsSimpleProduct()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function getApiGetCustomAttributes()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function getOrderCreateAllowEvent()
    {
        return false;
    }

    /**
     * Cách tính discount per item.
     * True: Tính discount ammount theo tỷ trọng
     * False: Trừ theo thứ tự
     *
     * @return bool
     */
    public function calculateDiscountByProportion()
    {
        return true;
    }

    /**
     * Có tích hợp với các extension khác hay không.(chung nhất)
     * Nếu có thì sẽ cho phép fire event của magento
     * Nếu không thì sẽ hạn chế tối đa việc fire event mặc định của magento
     *
     * @return bool
     */
    public function isIntegrate()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isBlockingCustomerFromUnsubscribe()
    {
        return $this->scopeConfig->getValue('xretail/pos/block_customer_unsubscribe') == 1;
    }

    /**
     * @return bool
     */
    public function isRoundingOrderStoreCreditData()
    {
        return $this->scopeConfig->getValue('xretail/pos/round_order_store_credit_data') == 1;
    }
}
