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

namespace ArangoDbTest;

use ArangoDb\Vpack;
use PHPUnit\Framework\TestCase;

/**
 * @group vpack
 */
class VpackTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_vpack_from_array(): void
    {
        $arr = ['1' => 100, '2' => 1000000, 'c' => 'd', 'test' => true];

        $vpack = Vpack::fromArray($arr);
        $vpack1 = $vpack->toJson();

        $vpack = Vpack::fromJson('{"1":100,"2":1000000,"c":"d","test":true}');
        $vpack2 = $vpack->toJson();

        $this->assertTrue($vpack1 === $vpack2);
    }

    /**
     * @test
     */
    public function it_fails_creating_vpack_from_invalid_json(): void
    {
        $this->expectException(\ArangoDb\RuntimeException::class);

        Vpack::fromJson('{a:"b"}');
    }

    /**
     * @test
     */
    public function it_serializes_vpack_into_json(): void
    {
        $initalJson = '{"1":100,"2":1000000,"c":"d","test":true}';

        $vpack = Vpack::fromJson($initalJson);
        $json = $vpack->toJson();

        $this->assertTrue($json === $initalJson);
    }

    /**
     * @test
     */
    public function it_produces_a_proper_vpack(): void
    {
        $arr = [
            'a' => '111',
            'b' => 222,
            'c' => true,
            'd' => false,
            'e' => 3.2,
            10,
            20,
            'arr' => [
                'a' => 'b',
                111,
            ],
            [23, 58, 10],
            [0 => 10, 1 => 20, 3 => 30],
            'null' => null,
            'obj' => new \stdClass(),
        ];

        $vpackFromArray = Vpack::fromArray($arr);

        $this->assertEquals(
            '{"a":"111","b":222,"c":true,"d":false,"e":3.2,"0":10,"1":20,"arr":{"a":"b","0":111},"2":[23,58,10],"3":{"0":10,"1":20,"3":30},"null":null,"obj":{}}',
            $vpackFromArray->toJson()
        );
    }
}
