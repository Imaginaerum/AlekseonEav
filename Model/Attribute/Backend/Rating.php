<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\AlekseonEav\Model\Attribute\Backend;

/**
 * Class ArrayBackend
 * @package Alekseon\AlekseonEav\Model\Attribute\Backend
 */
class Rating extends AbstractBackend
{
    /**
     * @param $object
     * @return $this
     */
    public function beforeSave($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        $value = (int) $object->getData($attrCode);
        if ($value < 0) {
            $value = 0;
        }

        if ($value > 5) {
            $value = 5;
        }

        $object->setData($attrCode, $value);

        return parent::beforeSave($object);
    }

}
