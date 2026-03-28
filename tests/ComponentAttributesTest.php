<?php

declare(strict_types=1);

namespace Tamdaz\TempestTwig\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Tamdaz\TempestTwig\Twig\ComponentAttributes;

final class ComponentAttributesTest extends TestCase
{
    #[Test]
    public function testFormatWithClauseReturnsEmptyWhenNoAttributes(): void
    {
        self::assertSame('', ComponentAttributes::formatWithClause(''));
    }

    #[Test]
    public function testFormatWithClauseHandlesStaticAndDynamicAttributes(): void
    {
        $attributes = 'label="Click" :disabled="isLoading" variant="primary"';

        $expected = " with { label: 'Click', disabled: isLoading, variant: 'primary' }";

        self::assertSame($expected, ComponentAttributes::formatWithClause($attributes));
    }
}
