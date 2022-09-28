<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\AlekseonEav\Model\Attribute\Source;

/**
 * Class Boolean
 * @package Alekseon\AlekseonEav\Model\Attribute\Source
 */
class Boolean extends AbstractSource
{
    const VALUE_NO = 0;
    const VALUE_YES = 1;

    /**
     * @return array|mixed
     */
    public function getOptions($storeId = null)
    {
        return [
            self::VALUE_NO => __('No'),
            self::VALUE_YES => __('Yes'),
        ];
    }
}
