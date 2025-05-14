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

use PHPUnit\Framework\TestCase;
use Sylius\Component\Locale\Context\CompositeLocaleContext;
use PHPUnit\Framework\MockObject\MockObject;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;

final class CompositeLocaleContextTest extends TestCase
{
    private CompositeLocaleContext $compositeLocaleContext;
    protected function setUp(): void
    {
        $this->compositeLocaleContext = new CompositeLocaleContext();
    }
    public function testImplementsLocaleContextInterface(): void
    {
        $this->assertInstanceOf(LocaleContextInterface::class, $this->compositeLocaleContext);
    }

    public function testThrowsALocaleNotFoundExceptionIfThereAreNoNestedLocaleContextsDefined(): void
    {
        $this->expectException(LocaleNotFoundException::class);
        $this->compositeLocaleContext->getLocaleCode();
    }

    public function testThrowsALocaleNotFoundExceptionIfNoneOfNestedLocaleContextsReturnedALocale(): void
    {
        /** @var LocaleContextInterface|MockObject $localeContextMock */
        $localeContextMock = $this->createMock(LocaleContextInterface::class);
        $localeContextMock->expects($this->once())->method('getLocaleCode')->willThrowException(LocaleNotFoundException::class);
        $this->compositeLocaleContext->addContext($localeContextMock);
        $this->expectException(LocaleNotFoundException::class);
        $this->compositeLocaleContext->getLocaleCode();
    }

    public function testReturnsFirstResultReturnedByNestedRequestResolvers(): void
    {
        /** @var LocaleContextInterface|MockObject $firstLocaleContextMock */
        $firstLocaleContextMock = $this->createMock(LocaleContextInterface::class);
        /** @var LocaleContextInterface|MockObject $secondLocaleContextMock */
        $secondLocaleContextMock = $this->createMock(LocaleContextInterface::class);
        /** @var LocaleContextInterface|MockObject $thirdLocaleContextMock */
        $thirdLocaleContextMock = $this->createMock(LocaleContextInterface::class);
        $firstLocaleContextMock->expects($this->once())->method('getLocaleCode')->willThrowException(LocaleNotFoundException::class);
        $secondLocaleContextMock->expects($this->once())->method('getLocaleCode')->willReturn('en_US');
        $thirdLocaleContextMock->expects($this->never())->method('getLocaleCode');
        $this->compositeLocaleContext->addContext($firstLocaleContextMock);
        $this->compositeLocaleContext->addContext($secondLocaleContextMock);
        $this->compositeLocaleContext->addContext($thirdLocaleContextMock);
        $this->assertSame('en_US', $this->compositeLocaleContext->getLocaleCode());
    }

    public function testItsNestedRequestResolversCanHavePriority(): void
    {
        /** @var LocaleContextInterface|MockObject $firstLocaleContextMock */
        $firstLocaleContextMock = $this->createMock(LocaleContextInterface::class);
        /** @var LocaleContextInterface|MockObject $secondLocaleContextMock */
        $secondLocaleContextMock = $this->createMock(LocaleContextInterface::class);
        /** @var LocaleContextInterface|MockObject $thirdLocaleContextMock */
        $thirdLocaleContextMock = $this->createMock(LocaleContextInterface::class);
        $firstLocaleContextMock->expects($this->never())->method('getLocaleCode');
        $secondLocaleContextMock->expects($this->once())->method('getLocaleCode')->willReturn('pl_PL');
        $thirdLocaleContextMock->expects($this->once())->method('getLocaleCode')->willThrowException(LocaleNotFoundException::class);
        $this->compositeLocaleContext->addContext($firstLocaleContextMock, -5);
        $this->compositeLocaleContext->addContext($secondLocaleContextMock, 0);
        $this->compositeLocaleContext->addContext($thirdLocaleContextMock, 5);
        $this->assertSame('pl_PL', $this->compositeLocaleContext->getLocaleCode());
    }
}
