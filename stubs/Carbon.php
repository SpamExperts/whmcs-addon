<?php

/**
 * Editor stub, see whmcs.php.
 */

namespace Carbon;

class Carbon extends \DateTime
{
    /**
     * @disregard P1003 Stub, the parameters are unused
     * @param \DateTimeZone|string|null $tz
     * @return static
     */
    public static function now($tz = null)
    {
        return new static();
    }
}
