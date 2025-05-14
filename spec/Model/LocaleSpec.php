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

namespace Tests\Sylius\Component\Locale\Model;

use PHPUnit\Framework\TestCase;
use Locale;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Resource\Model\TimestampableInterface;

final class LocaleTest extends TestCase
{
    private \Sylius\Component\Locale\Model\Locale $locale;
    protected function setUp(): void
    {
        $this->locale = new \Sylius\Component\Locale\Model\Locale();
        Locale::setDefault('en');
    }

    public function testImplementsALocaleInterface(): void
    {
        $this->assertInstanceOf(LocaleInterface::class, $this->locale);
    }

    public function testTimestampable(): void
    {
        $this->assertInstanceOf(TimestampableInterface::class, $this->locale);
    }

    public function testDoesNotHaveIdByDefault(): void
    {
        $this->assertNull($this->locale->getId());
    }

    public function testHasNoCodeByDefault(): void
    {
        $this->assertNull($this->locale->getCode());
    }

    public function testItsCodeIsMutable(): void
    {
        $this->locale->setCode('de_DE');
        $this->assertSame('de_DE', $this->locale->getCode());
    }

    public function testHasAName(): void
    {
        $this->locale->setCode('pl_PL');
        $this->assertSame('Polish (Poland)', $this->locale->getName());
        $this->assertSame('polaco (Polonia)', $this->locale->getName('es'));

        $this->locale->setCode('pl');
        $this->assertSame('Polish', $this->locale->getName());
        $this->assertSame('polaco', $this->locale->getName('es'));
    }

    public function testReturnsNameWhenConvertedToString(): void
    {
        $this->locale->setCode('pl_PL');
        $this->assertSame('Polish (Poland)', $this->locale->__toString());

        $this->locale->setCode('pl');
        $this->assertSame('Polish', $this->locale->__toString());
    }

    public function testDoesNotHaveLastUpdateDateByDefault(): void
    {
        $this->assertNull($this->locale->getUpdatedAt());
    }
}
