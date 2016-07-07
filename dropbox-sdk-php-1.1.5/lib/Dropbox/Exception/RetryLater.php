<?php

namespace Dropbox;

/**
 * The Dropbox server said it couldn't fulfil our request right now, but that we should try
 * again later.
 */
final class RetryLater extends Exception
{
    /**
     * @internal
     */
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
