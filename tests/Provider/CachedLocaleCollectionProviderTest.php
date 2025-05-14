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

namespace Tests\Sylius\Component\Locale\Provider;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Locale\Provider\CachedLocaleCollectionProvider;
use Sylius\Component\Locale\Provider\LocaleCollectionProviderInterface;
use Symfony\Contracts\Cache\CacheInterface;

final class CachedLocaleCollectionProviderTest extends TestCase
{
    /** @var LocaleCollectionProviderInterface&MockObject */
    private MockObject $decoratedMock;

    /** @var CacheInterface&MockObject */
    private MockObject $cacheMock;

    private CachedLocaleCollectionProvider $cachedLocaleCollectionProvider;

    protected function setUp(): void
    {
        $this->decoratedMock = $this->createMock(LocaleCollectionProviderInterface::class);
        $this->cacheMock = $this->createMock(CacheInterface::class);
        $this->cachedLocaleCollectionProvider = new CachedLocaleCollectionProvider($this->decoratedMock, $this->cacheMock);
    }

    public function testImplementsLocaleCollectionProviderInterface(): void
    {
        $this->assertInstanceOf(LocaleCollectionProviderInterface::class, $this->cachedLocaleCollectionProvider);
    }

    public function testReturnsAllLocalesViaCache(): void
    {
        /** @var LocaleInterface&MockObject $someLocaleMock */
        $someLocaleMock = $this->createMock(LocaleInterface::class);
        /** @var LocaleInterface&MockObject $anotherLocaleMock */
        $anotherLocaleMock = $this->createMock(LocaleInterface::class);
        $someLocaleMock->expects($this->once())->method('getCode')->willReturn('en_US');
        $anotherLocaleMock->expects($this->once())->method('getCode')->willReturn('en_GB');
        $this->cacheMock->expects($this->once())->method('get')
            ->with('sylius_locales', $this->isType('callable'))
            ->willReturnCallback(function (string $key, callable $callback) {
                return $callback();
            });
        $this->decoratedMock->expects($this->once())->method('getAll')->willReturn(['en_US' => $someLocaleMock, 'en_GB' => $anotherLocaleMock]);
        $this->assertSame(['en_US' => $someLocaleMock, 'en_GB' => $anotherLocaleMock], $this->cachedLocaleCollectionProvider->getAll());
    }
}
