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

class Request
{
    public const METHOD_DELETE = 0;
    public const METHOD_GET = 1;
    public const METHOD_POST = 2;
    public const METHOD_PUT = 3;
    public const METHOD_HEAD = 4;
    public const METHOD_PATCH = 5;
    public const METHOD_OPTIONS = 6;

    /**
     * Create Request
     *
     * @param string $path URI path of ArangoDB Rest API
     * @param Vpack $vpack Request data
     * @param array|null $queryParams Map of query params added to URI path
     *
     * @return Request
     */
    public function __construct(string $path, Vpack $vpack, array $queryParams = null)
    {
        // TODO not used at the moment
    }
}
