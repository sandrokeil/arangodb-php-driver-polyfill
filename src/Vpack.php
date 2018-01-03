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

class Vpack
{
    /**
     * JSON data
     *
     * @var string
     */
    private $data;

    /**
     * Creates a Vpack instance from array data
     *
     * @param array $data
     * @return Vpack
     */
    public static function fromArray(array $data): Vpack
    {
        $self = new self();
        $self->data = json_encode($data);

        return $self;
    }

    /**
     * Creates a Vpack instance from JSON data
     *
     * @param string $json JSON data
     * @return Vpack
     * @throws RuntimeException
     */
    public static function fromJson(string $json): Vpack
    {
        $self = new self();
        if (! $self->isJson($json)) {
            throw new RuntimeException(
                sprintf('Provided data "%s" is not a valid JSON string.', $json)
            );
        }
        $self->data = $json;

        return $self;
    }

    /**
     * @return string
     */
    public function toJson(): string
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return json_decode($this->data, true);
    }

    private function isJson(string $data)
    {
        $json = json_decode($data);

        return $json && $data !== $json;
    }
}
