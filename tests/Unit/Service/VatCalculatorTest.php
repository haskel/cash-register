<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Entity\Product;
use App\Enum\VatClass;
use App\Exception\UnexpectedValueException;
use App\Service\VatCalculator;
use PHPUnit\Framework\TestCase;
use Throwable;

class VatCalculatorTest extends TestCase
{
    /**
     * @test
     * @dataProvider getPercentByClassDataProvider
     *
     * @param array<int, numeric> $config
     */
    public function getPercentByClass(VatClass $vatClass, mixed $expected, array $config): void
    {
        $vatCalculator = new VatCalculator($config);
        $actual = $vatCalculator->getPercentByClass($vatClass);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array<mixed> $config
     */
    public function getPercentByClassDataProvider(): array
    {
        return [
            [
                VatClass::Zero,
                0,
                [
                    VatClass::Zero->value     => 0,
                    VatClass::Reduced->value  => 10,
                    VatClass::Standard->value => 20,
                ]
            ],
            [
                VatClass::Reduced,
                10.12,
                [
                    VatClass::Zero->value     => 0,
                    VatClass::Reduced->value  => 10.12,
                    VatClass::Standard->value => 20.232,
                ]
            ]
        ];
    }

    /**
     * @test
     * @dataProvider serviceInitSuccessfulDataProvider
     *
     * @param array<int, numeric> $config
     */
    public function serviceInitSuccessful(array $config): void
    {
        new VatCalculator($config);
        $this->assertTrue(true);
    }

    /**
     * @return array<array<mixed>>
     */
    public function serviceInitSuccessfulDataProvider(): array
    {
        return [
            [[]],
            [
                [
                    VatClass::Zero->value     => 0,
                    VatClass::Reduced->value  => 10,
                    VatClass::Standard->value => 20,
                ]
            ],
            [
                [
                    VatClass::Zero->value     => 0,
                    VatClass::Reduced->value  => 0,
                    VatClass::Standard->value => 0,
                ]
            ],
            [
                [
                    VatClass::Zero->value     => 0.1,
                    VatClass::Standard->value => 1.3543544345,
                ]
            ],
            [
                [
                    VatClass::Zero->value     => '10',
                    VatClass::Standard->value => '1.25',
                ]
            ],
        ];
    }

    /**
     * @test
     * @dataProvider serviceInitFailDataProvider
     *
     * @param array<int, numeric> $config
     * @param class-string<Throwable> $exceptionClass
     */
    public function serviceInitFail(array $config, string $exceptionClass): void
    {
        $this->expectException($exceptionClass);

        new VatCalculator($config);
    }


    /**
     * @return array<array<mixed>> $config
     */
    public function serviceInitFailDataProvider()
    {
        return [
            [
                [
                    -1 => 1,
                ],
                UnexpectedValueException::class,
            ],
            [
                [
                    'qwerty' => 1,
                ],
                UnexpectedValueException::class,
            ],
            [
                [
                    VatClass::Zero->value    => -10,
                    VatClass::Reduced->value => 10,
                ],
                UnexpectedValueException::class,
            ],
            [
                [
                    VatClass::Zero->value    => 1,
                    VatClass::Reduced->value => 10000,
                ],
                UnexpectedValueException::class,
            ],
            [
                [
                    VatClass::Zero->value => 'qwerty',
                ],
                UnexpectedValueException::class,
            ],
            [
                [
                    VatClass::Zero->value => '1,25',
                ],
                UnexpectedValueException::class,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider calculateDataProvider
     */
    public function calculate(Product $product): void
    {
        $zeroPercent = 0;
        $reducedPercent = 6;
        $standardPercent = 21.33;

        $config = [
            VatClass::Zero->value     => $zeroPercent,
            VatClass::Reduced->value  => $reducedPercent,
            VatClass::Standard->value => $standardPercent,
        ];

        $vatCalculator = new VatCalculator($config);

        $actual = $vatCalculator->calculate($product);

        $percent = $vatCalculator->getPercentByClass($product->getVatClass());
        $expected = $percent / 100 * $product->getPrice();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array<array<Product>> $config
     */
    public function calculateDataProvider(): array
    {
        return [
            [new Product('123', 'name', 100, VatClass::Zero)],
            [new Product('123', 'name', 100, VatClass::Reduced)],
            [new Product('123', 'name', 100, VatClass::Standard)],
            [new Product('123', 'name', 0, VatClass::Zero)],
            [new Product('123', 'name', 0, VatClass::Reduced)],
            [new Product('123', 'name', 0, VatClass::Standard)],
            [new Product('123', 'name', 123.123123, VatClass::Zero)],
            [new Product('123', 'name', 123.123123, VatClass::Reduced)],
            [new Product('123', 'name', 123.123123, VatClass::Standard)],
        ];
    }
}
