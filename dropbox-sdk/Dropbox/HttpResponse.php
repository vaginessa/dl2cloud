<?php

namespace Dropbox;

/**
 * @internal
 */
final class HttpResponse
{
    public $statusCode;
    public $body;

    public function __construct($statusCode, $body)
    {
        $this->statusCode = $statusCode;
        $this->body = $body;
    }
}
