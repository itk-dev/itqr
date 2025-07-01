<?php

namespace App\DTO;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\Font\Font;
use Endroid\QrCode\Label\Font\FontInterface;
use Endroid\QrCode\Label\Margin\Margin;

class DownloadSettingsDTO
{
    private const string OPEN_SANS = __DIR__.'/../../vendor/endroid/qr-code/assets/open_sans.ttf';

    public function __construct(
        public readonly int $size = 400,
        public readonly int $margin = 0,
        public readonly Color $backgroundColor = new Color(255, 255, 255),
        public readonly Color $foregroundColor = new Color(0, 0, 0),
        public readonly string $labelText = '',
        public readonly FontInterface $labelFont = new Font(self::OPEN_SANS, 12),
        public readonly Color $labelTextColor = new Color(0, 0, 0),
        public readonly Margin $labelMargin = new Margin(0, 0, 0, 0),
        public readonly ErrorCorrectionLevel $errorCorrectionLevel = ErrorCorrectionLevel::Low,
    ) {
    }
}
