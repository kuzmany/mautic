<?php

/*
 * @copyright   2017 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\LeadBundle\Helper;

use Mautic\CoreBundle\Helper\DateTimeHelper;

/**
 * Helper class custom field operations.
 */
class CustomFieldHelper
{
    const TYPE_BOOLEAN = 'boolean';

    const TYPE_NUMBER  = 'number';

    const TYPE_SELECT  = 'select';

    /**
     * Fixes value type for specific field types.
     *
     * @param string $type
     * @param mixed  $value
     *
     * @return mixed
     */
    public static function fixValueType($type, $value)
    {
        if (!is_null($value)) {
            switch ($type) {
                case self::TYPE_NUMBER:
                    $value = (float) $value;
                    break;
                case self::TYPE_BOOLEAN:
                    $value = (bool) $value;
                    break;
                case self::TYPE_SELECT:
                    $value = (string) $value;
                    break;
            }
        }

        return $value;
    }

    /**
     * Format date type (today, yesterday..).
     *
     * @param $type
     * @param $value
     *
     * @return mixed
     */
    public static function formatDateType($type, $value)
    {
        $dtHelper = new DateTimeHelper($value, null, 'local');

        switch ($type) {
            case 'datetime':
                return $dtHelper->toLocalString('Y-m-d H:i:s');
            case 'date':
                return $dtHelper->toLocalString('Y-m-d');
            case 'time':
                return $dtHelper->toLocalString('H:i:s');
        }

        return $value;
    }
}
