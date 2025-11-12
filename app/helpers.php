<?php

if (! function_exists('falseyToNull')) {
    /**
     * Get the impersonator user
     */
    function falseyToNull(mixed $value, ?callable $returner = null): mixed
    {
        if (! $value) {
            return null;
        }

        if ($returner) {
            return $returner();
        }

        return $value;
    }
}
