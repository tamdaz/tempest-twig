<?php

declare(strict_types=1);

namespace Tamdaz\TempestTwig\Tests;

use ReflectionClass;
use ReflectionMethod;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Tamdaz\TempestTwig\Twig\Extensions\ViteExtension;

final class ViteExtensionTest extends TestCase
{
    #[Test]
    public function testViteAssetReturnsExpectedFormat(): void
    {
        $reflection = new ReflectionClass(ViteExtension::class);

        $method = $reflection->getMethod('viteAsset');

        self::assertTrue($method->isPublic());
        self::assertTrue($method->isStatic());
    }

    #[Test]
    public function testViteTagsMethodExists(): void
    {
        $reflection = new ReflectionClass(ViteExtension::class);
        
        $method = $reflection->getMethod('viteTags');

        self::assertTrue($method->isPublic());
        self::assertTrue($method->isStatic());
    }

    #[Test]
    public function testExtensionHasTwigFunctionAttributes(): void
    {
        $reflection = new ReflectionClass(ViteExtension::class);
        
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_STATIC);
        $methodNames = array_map(fn($m) => $m->getName(), $methods);
        
        self::assertContains('viteTags', $methodNames);
        self::assertContains('viteAsset', $methodNames);
    }

    #[Test]
    public function testViteAssetWithRelativePath(): void
    {
        $reflection = new ReflectionClass(ViteExtension::class);
        $method = $reflection->getMethod('viteAsset');
        
        $parameters = $method->getParameters();
        self::assertCount(1, $parameters);
        self::assertSame('path', $parameters[0]->getName());
    }

    #[Test]
    public function testViteTagsWithMultipleEntrypoints(): void
    {
        $reflection = new ReflectionClass(ViteExtension::class);
        $method = $reflection->getMethod('viteTags');
        
        $parameters = $method->getParameters();
        self::assertCount(1, $parameters);
        self::assertTrue($parameters[0]->isVariadic());
    }
}
