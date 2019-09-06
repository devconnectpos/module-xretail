<?php

namespace SM\XRetail\Config\Model;
class ShowOutOfStockProducts implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $options = [
            [
                'label' => __('No'),
                'value' => 'no',
            ],
            [
                'label' => __('Yes'),
                'value' => 'yes',
            ],
        ];
        return $options;
    }
}