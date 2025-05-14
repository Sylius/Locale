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
use Sylius\Component\Locale\Provider\LocaleCollectionProvider;
use Sylius\Component\Locale\Provider\LocaleCollectionProviderInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

final class LocaleCollectionProviderTest extends TestCase
{
    /** @var RepositoryInterface|MockObject */
    private MockObject $localeRepositoryMock;

    private LocaleCollectionProvider $localeCollectionProvider;

    protected function setUp(): void
    {
        $this->localeRepositoryMock = $this->createMock(RepositoryInterface::class);
        $this->localeCollectionProvider = new LocaleCollectionProvider($this->localeRepositoryMock);
    }

    public function testImplementsLocaleCollectionProviderInterface(): void
    {
        $this->assertInstanceOf(LocaleCollectionProviderInterface::class, $this->localeCollectionProvider);
    }

    public function testReturnsAllLocales(): void
    {
        /** @var LocaleInterface|MockObject $someLocaleMock */
        $someLocaleMock = $this->createMock(LocaleInterface::class);
        /** @var LocaleInterface|MockObject $anotherLocaleMock */
        $anotherLocaleMock = $this->createMock(LocaleInterface::class);
        $someLocaleMock->expects($this->once())->method('getCode')->willReturn('en_US');
        $anotherLocaleMock->expects($this->once())->method('getCode')->willReturn('en_GB');
        $this->localeRepositoryMock->expects($this->once())->method('findAll')->willReturn([$someLocaleMock, $anotherLocaleMock]);
        $this->assertSame(['en_US' => $someLocaleMock, 'en_GB' => $anotherLocaleMock], $this->localeCollectionProvider->getAll());
    }
}
