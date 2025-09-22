<?php

namespace App\Twig;

use App\Helper\DownloadHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class QrCodeExtension extends AbstractExtension
{
    public function __construct(
        private readonly DownloadHelper $downloadHelper,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('create_color_from_hex', [$this->downloadHelper, 'createColorFromHex']),
            new TwigFunction('create_label_margin', [$this->downloadHelper, 'createLabelMargin']),
            new TwigFunction('create_font_interface', [$this->downloadHelper, 'createFontInterface']),
        ];
    }
}
