<?php

declare(strict_types=1);

namespace Tamdaz\TempestTwig;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Extension\AttributeExtension;
use Tamdaz\TempestTwig\Twig\ComponentLoader;
use Tempest\Container\{Container, Initializer, Singleton};
use Tamdaz\TempestTwig\Twig\Extensions\{DebugExtension, RoutingExtension, ViteExtension};

final class TwigInitializer implements Initializer
{
    #[Singleton]
    public function initialize(Container $container): Environment
    {
        $twigConfig = $container->get(TwigConfig::class);
        $loader = new FilesystemLoader($twigConfig->viewPaths);
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
