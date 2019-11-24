<?php # -*- coding: utf-8 -*-
/*
 * This file is part of the BrainMonkey package.
 *
 * (c) Giuseppe Mazzapica
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brain\Monkey\Tests\Unit\Name;

use Brain\Monkey\Name\ClosureParamStringForm;
use Brain\Monkey\Name\Exception\InvalidClosureParam;
use Brain\Monkey\Tests\UnitTestCase;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @package BrainMonkey
 * @license http://opensource.org/licenses/MIT MIT
 */
class ClosureParamStringFormTest extends UnitTestCase
{

    public function testFromStringThrowsForTooManyParameters()
    {
        $this->expectException(InvalidClosureParam::class);
        $this->expectExceptionCode(InvalidClosureParam::CODE_INVALID_NAME);
        ClosureParamStringForm::fromString('Foo $foo bar');
    }

    public function testFromStringThrowsForBadName()
    {
        $this->expectException(InvalidClosureParam::class);
        $this->expectExceptionCode(InvalidClosureParam::CODE_INVALID_NAME);
        ClosureParamStringForm::fromString('Foo $1foo');
    }

    public function testFromStringThrowsForBadType()
    {
        $this->expectException(InvalidClosureParam::class);
        $this->expectExceptionCode(InvalidClosureParam::CODE_INVALID_TYPE);
        ClosureParamStringForm::fromString('F-oo $foo');
    }

    public function testFromStringVariadic()
    {
        static::assertFalse(ClosureParamStringForm::fromString('Foo $foo')->isVariadic());
        static::assertTrue(ClosureParamStringForm::fromString('...$foo')->isVariadic());
        static::assertTrue(ClosureParamStringForm::fromString(' ... $foo')->isVariadic());
        static::assertTrue(ClosureParamStringForm::fromString('Foo ...$foo')->isVariadic());
        static::assertTrue(ClosureParamStringForm::fromString(' Foo ... $foo ')->isVariadic());
        static::assertFalse(ClosureParamStringForm::fromString(' $foo ')->isVariadic());
    }

    public function testFromStringToString7()
    {
        if (PHP_MAJOR_VERSION < 7) {
            $this->markTestSkipped('Skipping PHP 7 test.');

            return;
        }

        $param_a = ClosureParamStringForm::fromString('Foo $foo');
        $param_b = ClosureParamStringForm::fromString('...$foo');
        $param_c = ClosureParamStringForm::fromString(' ... $foo');
        $param_d = ClosureParamStringForm::fromString('Foo ...$foo');
        $param_e = ClosureParamStringForm::fromString(' Foo ... $foo ');
        $param_f = ClosureParamStringForm::fromString(' $foo ');

        static::assertSame('Foo $foo', (string)$param_a);
        static::assertSame('...$foo', (string)$param_b);
        static::assertSame('...$foo', (string)$param_c);
        static::assertSame('Foo ...$foo', (string)$param_d);
        static::assertSame('Foo ...$foo', (string)$param_e);
        static::assertSame('$foo', (string)$param_f);
    }

    public function testFromStringToString5()
    {
        if (PHP_MAJOR_VERSION >= 7) {
            $this->markTestSkipped('Skipping PHP 5.6 test.');

            return;
        }

        $param_a = ClosureParamStringForm::fromString('Foo $foo');
        $param_b = ClosureParamStringForm::fromString('...$foo');
        $param_c = ClosureParamStringForm::fromString(' ... $foo');
        $param_d = ClosureParamStringForm::fromString('Foo ...$foo');
        $param_e = ClosureParamStringForm::fromString(' Foo ... $foo ');
        $param_f = ClosureParamStringForm::fromString(' $foo ');

        static::assertSame('$foo', (string)$param_a);
        static::assertSame('...$foo', (string)$param_b);
        static::assertSame('...$foo', (string)$param_c);
        static::assertSame('...$foo', (string)$param_d);
        static::assertSame('...$foo', (string)$param_e);
        static::assertSame('$foo', (string)$param_f);
    }

    public function testFromReflectionToString7()
    {
        if (PHP_MAJOR_VERSION < 7) {
            $this->markTestSkipped('Skipping PHP 7 test.');

            return;
        }

        $param = \Mockery::mock(\ReflectionParameter::class);
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $param->shouldReceive('hasType')->andReturn(true);
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $param->shouldReceive('getType')->andReturn('array');
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $param->shouldReceive('getName')->andReturn('foo');
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $param->shouldReceive('isVariadic')->andReturn(true);

        /** @noinspection PhpParamsInspection */
        static::assertSame(
            'array ...$foo',
            (string)ClosureParamStringForm::fromReflectionParameter($param)
        );
    }

    public function testFromReflectionToString5()
    {

        if (PHP_MAJOR_VERSION >= 7) {
            $this->markTestSkipped('Skipping PHP 5.6 test.');

            return;
        }

        $param = \Mockery::mock(\ReflectionParameter::class);
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $param->shouldReceive('hasType')->never();
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $param->shouldReceive('getType')->never();
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $param->shouldReceive('getName')->andReturn('foo');
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $param->shouldReceive('isVariadic')->andReturn(true);

        /** @noinspection PhpParamsInspection */
        static::assertSame(
            '...$foo',
            (string)ClosureParamStringForm::fromReflectionParameter($param)
        );
    }
}