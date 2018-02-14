<?php

namespace Fi\CoreBundle\Utils;

/**
 * Description of JqgridResponse.
 *
 * @author manzolo
 */
abstract class FieldTypeUtility
{

    public static function getBooleanValue($value)
    {
        if (is_null($value)) {
            $newval = null;
        } else {
            if ($value) {
                $newval = true;
            } else {
                $newval = false;
            }
        }
        return $newval;
    }

    public static function getDateTimeValueFromTimestamp($value)
    {
        $date = new \DateTime();
        $date->setTimestamp($value);
        return $date;
    }
}
