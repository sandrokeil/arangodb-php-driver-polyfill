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

use ArangoDBClient\Cursor as HttpCursor;
use ArangoDBClient\Statement;

class Cursor implements \Countable, \Iterator
{
    public const ENTRY_TYPE = 0;
    public const ENTRY_TYPE_JSON = 100;
    public const ENTRY_TYPE_ARRAY = 101;
    public const ENTRY_TYPE_OBJECT = 102;

    /**
     * Cursor
     *
     * @var HttpCursor
     */
    private $cursor;

    /**
     * @var Statement
     */
    private $statement;

    /**
     * Entry type
     *
     * @var int
     */
    private $options;

    /**
     * @var int
     */
    private $httpCode;

    /**
     * @var string
     */
    private $body;

    public function __construct(Statement $statement, array $options = [])
    {
        $this->statement = $statement;
        $this->options = $options;
    }

    /**
     * Returns the number of results
     *
     * Query must be run with count and fullCount = true
     *
     * @return int
     * @throws RequestFailedException
     */
    public function count()
    {
        if ($this->cursor) {
            try {
                return $this->cursor->getCount();
            } catch (\ArangoDBClient\Exception $e) {
                $this->httpCode = $e->getCode();
                $this->body = $e->getMessage();
                throw new RequestFailedException($e->getMessage(), $e->getCode(), $e);
            }
        }

        return 0;
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        $data = $this->cursor->current();

        switch ($this->options[self::ENTRY_TYPE] ?? 0) {
            case self::ENTRY_TYPE_OBJECT:
                return (object) $data;
            case self::ENTRY_TYPE_ARRAY:
                return $data;
            case self::ENTRY_TYPE_JSON:
            default:
                return json_encode($data);
        }
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        if ($this->cursor) {
            $this->cursor->next();
        }
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->cursor ? $this->cursor->key() : null;
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @throws RequestFailedException
     */
    public function valid()
    {
        try {
            return $this->cursor ? $this->cursor->valid() : false;
        } catch (\ArangoDBClient\Exception $e) {
            $this->httpCode = $e->getCode();
            $this->body = $e->getMessage();
            throw new RequestFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Rewind the Iterator to the first element and resets the cursor
     *
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @throws RequestFailedException
     */
    public function rewind()
    {
        try {
            if (null === $this->cursor) {
                $this->cursor = $this->statement->execute();
            } else {
                $this->cursor->rewind();
            }
        } catch (\ArangoDBClient\Exception $e) {
            $this->httpCode = $e->getCode();
            $this->body = $e->getMessage();
            throw new RequestFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Rewinds the cursor and returns the result
     * @throws RequestFailedException
     */
    public function toArray(): array
    {
        $this->rewind();

        try {
            return $this->cursor->getAll();
        } catch (\ArangoDBClient\Exception $e) {
            $this->httpCode = $e->getCode();
            $this->body = $e->getMessage();
            throw new RequestFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getResponse(): Response
    {
        return new Response(
            new class($this->httpCode, $this->body) {
                private $body;
                private $httpCode;

                public function __construct(int $httpCode, string $body)
                {
                    $this->httpCode = $httpCode;
                    $this->body = $body;
                }

                public function getBody(): string
                {
                    return $this->body;
                }

                public function getHttpCode(): int
                {
                    return $this->httpCode;
                }
            }
        );
    }
}
