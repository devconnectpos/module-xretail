<?php

namespace SM\XRetail\Config\Model;

use Magento\Framework\Option\ArrayInterface;

class getAllAttributeProduct implements ArrayInterface
{

    protected $_objectManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    )
    {
        $this->_objectManager = $objectManager;
    }

    /*
     * Option getter
     * @return array
     */
    public function toOptionArray()
    {
        $arr = $this->getAttributes();
        $ret = [];
        foreach ($arr as $key => $value) {
            $ret[] = [
                'value' => $key,
                'label' => $value
            ];
        }
        return $ret;
    }

    public function getAttributes() {

        /** @var  $coll \Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection */
        $coll = $this->_objectManager->create(\Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection::class);
        // add filter by entity type to get product attributes only
        // '4' is the default type ID for 'catalog_product' entity - see 'eav_entity_type' table)
        // or skip the next line to get all attributes for all types of entities
        $coll->addFieldToFilter(\Magento\Eav\Model\Entity\Attribute\Set::KEY_ENTITY_TYPE_ID, 4);
        $attrAll = $coll->load()->getItems();

        $attribute_data = [];
        $attribute_data['id'] = 'ID';
        $attribute_data['type_id'] = 'Type ID';

        $arrString = array('varchar', 'string', 'text');
        foreach($attrAll as $attributes) {
            if(in_array($attributes->getFrontendInput(), $arrString) && $attributes->getAttributeCode() !== 'category_ids') {
                $attribute_data[$attributes->getAttributeCode()] = $attributes->getFrontendLabel();
            }
        }

        return $attribute_data;
    }
}
