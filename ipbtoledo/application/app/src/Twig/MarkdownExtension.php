<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Twig;

use Parsedown;
use Twig_Extension;
use Twig_Function;

class MarkdownExtension extends Twig_Extension
{
    public function getFunctions()
    {
        return [
            new Twig_Function(
                'markdown',
                [$this, 'markdownParser']
            )
        ];
    }

    public function markdownParser($text)
    {
        $parser = new Parsedown();
        return $parser->text($text);
    }
}
