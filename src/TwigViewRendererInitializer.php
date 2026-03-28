<?php

declare(strict_types=1);

namespace Tamdaz\TempestTwig;

use Tempest\View\{ViewConfig, ViewRenderer};
use Tamdaz\TempestTwig\Twig\TwigViewRenderer;
use Tempest\View\Renderers\TempestViewRenderer;
use Tempest\Container\{Container, Initializer, Singleton};

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
