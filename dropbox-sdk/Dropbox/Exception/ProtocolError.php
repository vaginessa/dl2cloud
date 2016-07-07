<?php

namespace Dropbox;

/**
 * There was an protocol misunderstanding between this SDK and the server.  One of us didn't
 * understand what the other one was saying.
 */
class ProtocolError extends Exception
{
    /**
     * @internal
     */
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
