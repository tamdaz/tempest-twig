<?php

declare(strict_types=1);

namespace Tamdaz\TempestTwig\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Tamdaz\TempestTwig\Twig\ComponentPreprocessor;

final class ComponentPreprocessorTest extends TestCase
{
    #[Test]
    public function testTransformsSelfClosingComponentIntoInclude(): void
    {
        $source = '<twig:Button label="Click" />';
        $expected = "{% include 'components/Button.html.twig' with { label: 'Click' } only %}";

        self::assertSame($expected, ComponentPreprocessor::process($source));
    }

    #[Test]
    public function testTransformsBlockComponentIntoEmbedWithNamedSlots(): void
    {
        $source = '<twig:Card><twig:block name="header">Title</twig:block>Body</twig:Card>';
        $processed = ComponentPreprocessor::process($source);

        self::assertStringContainsString("{% embed 'components/Card.html.twig' %}", $processed);
        self::assertStringContainsString('{% block header %}Title{% endblock %}', $processed);
        self::assertStringContainsString('{% block content %}Body{% endblock %}', $processed);
        self::assertStringContainsString('{% endembed %}', $processed);
    }
}
