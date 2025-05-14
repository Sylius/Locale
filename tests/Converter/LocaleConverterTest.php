<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\Component\Locale\Converter;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Locale\Converter\LocaleConverter;
use Sylius\Component\Locale\Converter\LocaleConverterInterface;

final class LocaleConverterTest extends TestCase
{
    private LocaleConverter $localeConverter;

    protected function setUp(): void
    {
        $this->localeConverter = new LocaleConverter();
    }

    public function testALocaleConverter(): void
    {
        $this->assertInstanceOf(LocaleConverterInterface::class, $this->localeConverter);
    }

    public function testConvertsLocaleNameToLocaleCode(): void
    {
        $this->assertSame('de', $this->localeConverter->convertNameToCode('German'));
        $this->assertSame('no', $this->localeConverter->convertNameToCode('Norwegian'));
        $this->assertSame('pl', $this->localeConverter->convertNameToCode('Polish'));
    }

    public function testConvertsLocaleCodeToLocaleName(): void
    {
        $this->assertSame('German', $this->localeConverter->convertCodeToName('de'));
        $this->assertSame('Norwegian', $this->localeConverter->convertCodeToName('no'));
        $this->assertSame('Polish', $this->localeConverter->convertCodeToName('pl'));
    }

    public function testThrowsInvalidArgumentExceptionIfCannotConvertNameToCode(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->localeConverter->convertNameToCode('xyz');
    }

    public function testThrowsInvalidArgumentExceptionIfCannotConvertCodeToName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->localeConverter->convertCodeToName('xyz');
    }
}
