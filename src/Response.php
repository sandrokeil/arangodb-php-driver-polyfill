<?php
/**
 * This file is part of the prooph/arangodb-php-driver-polyfill.
 * (c) 2018 prooph software GmbH <contact@prooph.de>
 * (c) 2018 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace ArangoDb;

use ArangoDBClient\HttpResponse;

class Response
{
    /**
     * Http response
     *
     * @var HttpResponse
     */
    private $response;

    /**
     * Data
     *
     * @var array
     */
    private $data;

    public function __construct($response)
    {
        // TODO no type hint workaround for Cursor
        $this->response = $response;
    }

    /**
     * @return int HTTP status code
     */
    public function getHttpCode(): int
    {
        return $this->response->getHttpCode();
    }

    /**
     * @return string Response body
     */
    public function getBody(): string
    {
        return $this->response->getBody();
    }

    public function get(string $path)
    {
        if (null === $this->data) {
            $this->data = json_decode($this->response->getBody(), true);
        }
        // TODO implement path splitting (dot notation)
        return $this->data[$path] ?? null;
    }
}
