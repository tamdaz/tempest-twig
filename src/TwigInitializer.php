<?php

declare(strict_types=1);

namespace Tamdaz\TempestTwig;

use Tamdaz\TempestTwig\Twig\Components\ComponentLoader;
use Tamdaz\TempestTwig\Twig\Extensions\DebugExtension;
use Tamdaz\TempestTwig\Twig\Extensions\RoutingExtension;
use Tamdaz\TempestTwig\Twig\Extensions\ViteExtension;
use Tempest\Container\Container;
use Tempest\Container\Initializer;
use Tempest\Container\Singleton;
use Tempest\View\Renderers\TwigConfig;
use Twig\Environment;
use Twig\Extension\AttributeExtension;
use Twig\Loader\FilesystemLoader;

final class TwigInitializer implements Initializer
{
    #[Singleton]
    public function initialize(Container $container): Environment
    {
        $twigConfig = $container->get(TwigConfig::class);
        $viewPaths = array_map(strval(...), $twigConfig->viewPaths);
        $loader = new FilesystemLoader($viewPaths);
        $environment = new Environment(new ComponentLoader($loader), $twigConfig->toArray());

        foreach ($this->getTwigExtensions() as $extension) {
            $environment->addExtension(new AttributeExtension($extension));
        }

        return $environment;
    }

    /**
     * Returns all Twig extensions.
     * @return array<int, string> Twig extensions class names.
     */
    private function getTwigExtensions(): array
    {
        return [
            DebugExtension::class,
            RoutingExtension::class,
            ViteExtension::class
        ];
    }
}
