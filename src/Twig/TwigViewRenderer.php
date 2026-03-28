<?php

declare(strict_types=1);

namespace Tamdaz\TempestTwig\Twig;

use Twig\Environment;
use Tempest\View\{View, ViewRenderer};
use Twig\Error\{LoaderError, RuntimeError, SyntaxError};

final readonly class TwigViewRenderer implements ViewRenderer
{
    /**
     * Wraps the Twig environment and ensures the component loader is enabled.
     *
     * @param Environment $twig Twig environment used for rendering.
     */
    public function __construct(
        private Environment $twig
    ) {
        if (! $twig->getLoader() instanceof ComponentLoader) {
            $twig->setLoader(new ComponentLoader($twig->getLoader()));
        }
    }

    /**
     * Renders a Twig view or template name to HTML.
     *
     * @param View|string $view View instance or template name to render.
     * @return string Rendered HTML.
     * @throws LoaderError|RuntimeError|SyntaxError
     */
    public function render(View|string $view): string
    {
        if (is_string($view)) {
            return trim($this->twig->render($view, []));
        }

        return trim($this->twig->render($view->path, $view->data));
    }
}
