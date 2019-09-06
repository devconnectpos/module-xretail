<?php
/**
 * Created by PhpStorm.
 * User: kid
 * Date: 04/06/2018
 * Time: 11:19
 */

namespace SM\XRetail\Config\Model;
class RadioRewardPoints implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $options = [
            [
                'label' => __('No'),
                'value' => 'false',
            ],
            [
                'label' => __('Yes'),
                'value' => 'true',
            ],
        ];
        return $options;
    }
}