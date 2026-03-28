<?php

declare(strict_types=1);

namespace Tamdaz\TempestTwig;

use Tamdaz\TempestTwig\Twig\TwigViewRenderer;
use Tempest\Container\Container;
use Tempest\Container\Initializer;
use Tempest\Container\Singleton;
use Tempest\View\Renderers\TempestViewRenderer;
use Tempest\View\ViewConfig;
use Tempest\View\ViewRenderer;

final class TwigViewRendererInitializer implements Initializer
{
    #[Singleton]
    public function initialize(Container $container): ViewRenderer
    {
        $viewConfig = $container->get(ViewConfig::class);
        $rendererClass = $viewConfig->rendererClass;

        if ($rendererClass === TempestViewRenderer::class) {
            $rendererClass = TwigViewRenderer::class;
        }

        return $container->get($rendererClass);
    }
}
