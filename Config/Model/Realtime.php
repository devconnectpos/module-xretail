<?php
/**
 * Created by PhpStorm.
 * User: kid
 * Date: 04/06/2018
 * Time: 11:19
 */

namespace SM\XRetail\Config\Model;

use Magento\Framework\Option\ArrayInterface;

class Realtime implements ArrayInterface
{
    public function toOptionArray()
    {
        $options = [
            [
                'label' => __('Immediately'),
                'value' => 'immediately',
            ],
            [
                'label' => __('Cronjob'),
                'value' => 'cronjob',
            ],
        ];
        return $options;
    }
}
