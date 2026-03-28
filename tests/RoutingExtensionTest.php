<?php

declare(strict_types=1);

namespace Tamdaz\TempestTwig\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Loader\ArrayLoader;
use Tamdaz\TempestTwig\Twig\Extensions\RoutingExtension;
use Twig\TwigFunction;

final class RoutingExtensionTest extends TestCase
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
                    new TwigFunction('current_path', [RoutingExtension::class, 'currentPath']),
                ];
            }
        });
    }

    #[Test]
    public function testCurrentPathFunctionReturnsRequestUri(): void
    {
        $originalUri = $_SERVER['REQUEST_URI'] ?? null;
        $_SERVER['REQUEST_URI'] = '/users/123';

        $result = RoutingExtension::currentPath();
        
        self::assertSame('/users/123', $result);
        
        if ($originalUri !== null) {
            $_SERVER['REQUEST_URI'] = $originalUri;
        } else {
            unset($_SERVER['REQUEST_URI']);
        }
    }

    #[Test]
    public function testCurrentPathFunctionReturnsSlashWhenMissing(): void
    {
        $originalUri = $_SERVER['REQUEST_URI'] ?? null;
        unset($_SERVER['REQUEST_URI']);

        $result = RoutingExtension::currentPath();
        
        self::assertSame('/', $result);
        
        if ($originalUri !== null) {
            $_SERVER['REQUEST_URI'] = $originalUri;
        }
    }

    #[Test]
    public function testCurrentPathFunctionReturnsSlashWhenNull(): void
    {
        $originalUri = $_SERVER['REQUEST_URI'] ?? null;
        $_SERVER['REQUEST_URI'] = null;

        $result = RoutingExtension::currentPath();
        
        self::assertSame('/', $result);
        
        if ($originalUri !== null) {
            $_SERVER['REQUEST_URI'] = $originalUri;
        } else {
            unset($_SERVER['REQUEST_URI']);
        }
    }
}
