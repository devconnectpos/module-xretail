<?php

namespace SM\XRetail\Config\Model;

use Magento\Framework\Option\ArrayInterface;

class ShowProductVisibility implements ArrayInterface
{

    /*
     * Option getter
     * @return array
     */
    public function toOptionArray()
    {
        $arr = $this->toArray();
        $ret = [];
        foreach ($arr as $key => $value) {
            $ret[] = [
                'value' => $key,
                'label' => $value
            ];
        }
        return $ret;
    }

    /*
     * Get options in "key-value" format
     * @return array
     */
    public function toArray()
    {
        $options = [
            '1' => 'Not Visible Individually',
            '2' => 'Catalog',
            '3' => 'Search',
            '4' => 'Catalog, Search'

        ];
        return $options;
    }
}