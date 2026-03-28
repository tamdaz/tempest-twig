<?php

declare(strict_types=1);

namespace Tamdaz\TempestTwig\Tests;

use Twig\Error\LoaderError;
use Twig\Loader\ArrayLoader;
use Tamdaz\TempestTwig\Twig\ComponentLoader;
use PHPUnit\Framework\{TestCase, Attributes\Test};

final class ComponentLoaderTest extends TestCase
{
    /**
     * @throws LoaderError
     */
    #[Test]
    public function testLoaderPreprocessesComponentSyntax(): void
    {
        $loader = new ArrayLoader([
            'index.html.twig' => '<twig:Button label="Click" />',
            'components/Button.html.twig' => '<button>{{ label }}</button>',
        ]);

        $componentLoader = new ComponentLoader($loader);
        $source = $componentLoader->getSourceContext('index.html.twig')->getCode();

        self::assertStringContainsString("{% include 'components/Button.html.twig' with { label: 'Click' } only %}", $source);
    }
}
