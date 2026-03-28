<?php

declare(strict_types=1);

namespace Tamdaz\TempestTwig\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use Twig\Extension\AbstractExtension;
use Twig\Loader\ArrayLoader;
use Tamdaz\TempestTwig\Twig\Extensions\DebugExtension;
use Twig\TwigFunction;
use stdClass;

final class DebugExtensionTest extends TestCase
{
    private Environment $twig;

    protected function setUp(): void
    {
        $loader = new ArrayLoader([]);
        $this->twig = new Environment($loader);
        
        $this->twig->addExtension(new class extends AbstractExtension {
            public function getFunctions(): array
            {
                return [
                    new TwigFunction('dump', [DebugExtension::class, 'dump']),
                    new TwigFunction('class', [DebugExtension::class, 'getClass']),
                    new TwigFunction('is_empty', [DebugExtension::class, 'isEmpty']),
                    new TwigFunction('get_type', [DebugExtension::class, 'getType']),
                    new TwigFunction('env', [DebugExtension::class, 'env']),
                    new TwigFunction('to_json', [DebugExtension::class, 'toJson']),
                    new TwigFunction('count', [DebugExtension::class, 'count']),
                    new TwigFunction('current_url', [DebugExtension::class, 'currentUrl']),
                    new TwigFunction('now', [DebugExtension::class, 'now']),
                ];
            }
        });
    }

    /**
     * @throws SyntaxError|LoaderError
     */
    #[Test]
    public function testClassFunctionReturnsClassName(): void
    {
        $template = $this->twig->createTemplate('{{ class(obj) }}');
        $result = $template->render(['obj' => new stdClass()]);
        
        self::assertSame('stdClass', $result);
    }

    /**
     * @throws SyntaxError|LoaderError
     */
    #[Test]
    public function testIsEmptyFunctionReturnsTrueForEmpty(): void
    {
        $template = $this->twig->createTemplate('{{ is_empty(value) ? "empty" : "not_empty" }}');
        
        self::assertSame('empty', $template->render(['value' => '']));
        self::assertSame('empty', $template->render(['value' => 0]));
        self::assertSame('empty', $template->render(['value' => []]));
        self::assertSame('empty', $template->render(['value' => null]));
    }

    /**
     * @throws SyntaxError|LoaderError
     */
    #[Test]
    public function testIsEmptyFunctionReturnsFalseForNonEmpty(): void
    {
        $template = $this->twig->createTemplate('{{ is_empty(value) ? "empty" : "not_empty" }}');
        
        self::assertSame('not_empty', $template->render(['value' => 'text']));
        self::assertSame('not_empty', $template->render(['value' => 1]));
        self::assertSame('not_empty', $template->render(['value' => [1, 2]]));
    }

    /**
     * @throws SyntaxError|LoaderError
     */
    #[Test]
    public function testGetTypeFunctionReturnsCorrectTypes(): void
    {
        $tests = [
            'string' => ['value' => 'text'],
            'integer' => ['value' => 42],
            'double' => ['value' => 3.14],
            'array' => ['value' => []],
            'boolean' => ['value' => true],
            'NULL' => ['value' => null],
        ];

        $template = $this->twig->createTemplate('{{ get_type(value) }}');

        foreach ($tests as $expectedType => $context) {
            $result = $template->render($context);
            self::assertSame($expectedType, $result);
        }
    }

    /**
     * @throws SyntaxError|LoaderError
     */
    #[Test]
    public function testEnvFunctionReturnsEnvironmentVariable(): void
    {
        $_ENV['TEST_TWIG_VAR'] = 'test_value';
        
        $template = $this->twig->createTemplate('{{ env("TEST_TWIG_VAR", "default") }}');
        $result = $template->render();
        
        self::assertSame('test_value', $result);
        
        unset($_ENV['TEST_TWIG_VAR']);
    }

    /**
     * @throws SyntaxError|LoaderError
     */
    #[Test]
    public function testEnvFunctionReturnsDefaultWhenMissing(): void
    {
        $template = $this->twig->createTemplate('{{ env("MISSING_TWIG_VAR", "fallback") }}');
        $result = $template->render();
        
        self::assertSame('fallback', $result);
    }

    /**
     * @throws SyntaxError|LoaderError
     */
    #[Test]
    public function testCountFunctionReturnsCorrectCount(): void
    {
        $template = $this->twig->createTemplate('{{ count(items) }}');
        
        self::assertSame('0', $template->render(['items' => []]));
        self::assertSame('3', $template->render(['items' => [1, 2, 3]]));
        self::assertSame('2', $template->render(['items' => ['a' => 1, 'b' => 2]]));
    }

    /**
     * @throws SyntaxError|LoaderError
     */
    #[Test]
    public function testCountFunctionReturnsZeroForNonCountable(): void
    {
        $template = $this->twig->createTemplate('{{ count(value) }}');
        
        self::assertSame('0', $template->render(['value' => 'string']));
        self::assertSame('0', $template->render(['value' => 42]));
    }

    /**
     * @throws SyntaxError|LoaderError
     */
    #[Test]
    public function testNowFunctionReturnsValidTimestamp(): void
    {
        $template = $this->twig->createTemplate('{% if now() > 0 %}valid{% endif %}');
        $result = $template->render();
        
        self::assertSame('valid', $result);
    }

    /**
     * @throws SyntaxError|LoaderError
     */
    #[Test]
    public function testToJsonFunctionConvertsArrayToJson(): void
    {
        $template = $this->twig->createTemplate('{{ to_json(data)|length > 0 ? "has_content" : "empty" }}');
        $result = $template->render(['data' => ['name' => 'John', 'age' => 30]]);
        
        self::assertSame('has_content', $result);
    }
}
