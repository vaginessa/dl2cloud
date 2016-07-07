<?php

namespace Dropbox;

/**
 * Thrown if Dropbox returns some other error about the authorization request.
 */
class Provider extends \Exception
{
    /**
     * @param string $message
     *
     * @internal
     */
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
