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

use ArangoDBClient\Statement;

class Connection
{
    public const HOST = 'host';
    public const USER = 'user';
    public const PASSWORD = 'password';
    public const MAX_CHUNK_SIZE = 'max_chunk_size';
    public const VST_VERSION = 'vst_version';
    public const VST_VERSION_10 = 0;
    public const VST_VERSION_11 = 1;

    /**
     * Http connection
     *
     * @var \ArangoDBClient\Connection
     */
    private $connection;

    /**
     * Connection options
     *
     * @var array
     */
    private $options;

    /**
     * @param array $options Connection options
     * @throws InvalidOptionException
     */
    public function __construct(array $options)
    {
        // TODO host is deprecated
        $endpoint = str_replace('vst://', 'tcp://', $options[self::HOST] ?? '');
        unset($options[self::HOST], $options[self::USER], $options[self::PASSWORD], $options[self::MAX_CHUNK_SIZE], $options[self::VST_VERSION]);
        $this->options = $options;
        $this->options['endpoint'] = $endpoint;
    }

    /**
     * Sets the thread count for the loop service
     *
     * @param int $threadCount Number of threads
     * @return void
     * @throws InvalidArgumentException
     */
    public function setThreadCount(int $threadCount): void
    {
        if ($threadCount < 1) {
            throw new InvalidArgumentException('Invalid threadCount provided, must be >= 1');
        }
    }

    /**
     * Establishes a connection to ArangoDb
     */
    public function connect(): void
    {
        $this->connection = new \ArangoDBClient\Connection($this->options);
    }

    /**
     * Sends a HTTP DELETE request
     *
     * @param string $path URI path of ArangoDB Rest API
     * @param Vpack $vpack Request data
     * @param array $queryParams Map of query params, added to URI path
     * @return Response
     * @throws RequestFailedException
     */
    public function delete(string $path, Vpack $vpack = null, array $queryParams = null): Response
    {
        if (! empty($queryParams)) {
            $path .= '?' . http_build_query($queryParams);
        }
        try {
            $response = $this->connection->delete($path, [], $vpack ? $vpack->toJson() : '');
        } catch (\ArangoDBClient\Exception $e) {
            throw new RequestFailedException($e->getMessage(), $e->getCode(), $e);
        }

        return new Response($response);
    }

    /**
     * Sends a HTTP GET request
     *
     * @param string $path URI path of ArangoDB Rest API
     * @param array|null $queryParams Map of query params added to URI path
     * @return Response
     * @throws RequestFailedException
     */
    public function get(string $path, array $queryParams = null): Response
    {
        if (! empty($queryParams)) {
            $path .= '?' . http_build_query($queryParams);
        }
        try {
            $response = $this->connection->get($path);
        } catch (\ArangoDBClient\Exception $e) {
            throw new RequestFailedException($e->getMessage(), $e->getCode(), $e);
        }

        return new Response($response);
    }

    /**
     * Sends a HTTP POST request
     *
     * @param string $path URI path of ArangoDB Rest API
     * @param Vpack $vpack Request data
     * @param array|null $queryParams Map of query params added to URI path
     * @return Response
     * @throws RequestFailedException
     */
    public function post(string $path, Vpack $vpack, array $queryParams = null): Response
    {
        if (! empty($queryParams)) {
            $path .= '?' . http_build_query($queryParams);
        }
        try {
            $response = $this->connection->post($path, $vpack->toJson());
        } catch (\ArangoDBClient\Exception $e) {
            throw new RequestFailedException($e->getMessage(), $e->getCode(), $e);
        }

        return new Response($response);
    }

    /**
     * Sends a HTTP PUT request
     *
     * @param string $path URI path of ArangoDB Rest API
     * @param Vpack $vpack Request data
     * @param array|null $queryParams Map of query params added to URI path
     * @return Response
     * @throws RequestFailedException
     */
    public function put(string $path, Vpack $vpack, array $queryParams = null): Response
    {
        if (! empty($queryParams)) {
            $path .= '?' . http_build_query($queryParams);
        }
        try {
            $response = $this->connection->put($path, $vpack->toJson());
        } catch (\ArangoDBClient\Exception $e) {
            throw new RequestFailedException($e->getMessage(), $e->getCode(), $e);
        }

        return new Response($response);
    }

    /**
     * Sends a HTTP HEAD request
     *
     * @param string $path URI path of ArangoDB Rest API
     * @param array|null $queryParams Map of query params added to URI path
     * @return Response
     * @throws RequestFailedException
     */
    public function head(string $path, array $queryParams = null): Response
    {
        if (! empty($queryParams)) {
            $path .= '?' . http_build_query($queryParams);
        }
        try {
            $response = $this->connection->head($path);
        } catch (\ArangoDBClient\Exception $e) {
            throw new RequestFailedException($e->getMessage(), $e->getCode(), $e);
        }

        return new Response($response);
    }

    /**
     * Sends a HTTP PATCH request
     *
     * @param string $path URI path of ArangoDB Rest API
     * @param Vpack $vpack Request data
     * @param array|null $queryParams Map of query params added to URI path
     * @return Response
     * @throws RequestFailedException
     */
    public function patch(string $path, Vpack $vpack, array $queryParams = null): Response
    {
        if (! empty($queryParams)) {
            $path .= '?' . http_build_query($queryParams);
        }
        try {
            $response = $this->connection->patch($path, $vpack->toJson());
        } catch (\ArangoDBClient\Exception $e) {
            throw new RequestFailedException($e->getMessage(), $e->getCode(), $e);
        }

        return new Response($response);
    }

    /**
     * Sends a HTTP OPTIONS request
     *
     * @param string $path URI path of ArangoDB Rest API
     * @param array|null $queryParams Map of query params added to URI path
     * @return Response
     */
    public function options(string $path, array $queryParams = null): Response
    {
        throw new RuntimeException('Not supported at the moment');
    }

    /**
     * Sends a query
     *
     * @param Vpack $vpack
     * @param array $options
     * @return Cursor
     * @throws RequestFailedException
     */
    public function query(Vpack $vpack, array $options = []): Cursor
    {
        try {
            $statement = new Statement(
                $this->connection,
                array_merge($vpack->toArray(), ['_flat' => true])
            );

            return new Cursor($statement, $options);
        } catch (\ArangoDBClient\Exception $e) {
            throw new RequestFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
