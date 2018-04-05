<?php

namespace Framework\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class InvalidCsrfTokenException
 *
 * @package Framework\Exception
 */
class InvalidCsrfTokenException extends HttpException {

    /**
     * {@inheritDoc}
     */
    public function __construct(
        int $statusCode = 403,
        string $message = null,
        \Exception $previous = null,
        array $headers = [],
        ?int $code = 0
    ) {
        parent::__construct( $statusCode, $message, $previous, $headers, $code );
    }
}
