<?php

interface SessionUpdateTimestampHandlerInterface
{
    /**
     * Checks if a session identifier already exists or not.
     *
     * @param string $key
     *
     * @return boolean
     */
    public function validateId($key);

    /**
     * Updates the timestamp of a session when its data didn't change.
     *
     * @param string $key
     * @param string $val
     *
     * @return boolean
     */
    public function updateTimestamp($key, $val);
}
