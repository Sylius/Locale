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

namespace Tests\Sylius\Component\Locale\Context;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Locale\Context\ProviderBasedLocaleContext;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;

final class ProviderBasedLocaleContextTest extends TestCase
{
    /** @var LocaleProviderInterface&MockObject */
    private MockObject $localeProviderMock;

    private ProviderBasedLocaleContext $providerBasedLocaleContext;

    protected function setUp(): void
    {
        $this->localeProviderMock = $this->createMock(LocaleProviderInterface::class);
        $this->providerBasedLocaleContext = new ProviderBasedLocaleContext($this->localeProviderMock);
    }

    public function testALocaleContext(): void
    {
        $this->assertInstanceOf(LocaleContextInterface::class, $this->providerBasedLocaleContext);
    }

    public function testReturnsTheChannelsDefaultLocale(): void
    {
        $this->localeProviderMock->expects($this->once())->method('getAvailableLocalesCodes')->willReturn(['pl_PL', 'en_US']);
        $this->localeProviderMock->expects($this->once())->method('getDefaultLocaleCode')->willReturn('pl_PL');
        $this->assertSame('pl_PL', $this->providerBasedLocaleContext->getLocaleCode());
    }

    public function testThrowsALocaleNotFoundExceptionIfDefaultLocaleIsNotAvailable(): void
    {
        $this->localeProviderMock->expects($this->once())->method('getAvailableLocalesCodes')->willReturn(['es_ES', 'en_US']);
        $this->localeProviderMock->expects($this->once())->method('getDefaultLocaleCode')->willReturn('pl_PL');
        $this->expectException(LocaleNotFoundException::class);
        $this->providerBasedLocaleContext->getLocaleCode();
    }
}
