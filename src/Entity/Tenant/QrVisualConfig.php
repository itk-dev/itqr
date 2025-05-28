<?php

namespace App\Entity\Tenant;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\QrVisualConfigRepository;
use Doctrine\ORM\Mapping as ORM;
use Endroid\QrCode\ErrorCorrectionLevel;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: QrVisualConfigRepository::class)]
#[ApiResource]
class QrVisualConfig
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(length: 5)]
    private ?int $size = 400;

    #[ORM\Column(length: 5)]
    private ?int $margin = 15;

    #[ORM\Column(length: 10)]
    private ?string $backgroundColor = '#ffffff';

    #[ORM\Column(length: 10)]
    private ?string $foregroundColor = '#000000';

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $labelText = null;

    #[ORM\Column(length: 5, nullable: true)]
    private ?int $labelSize = 15;

    #[ORM\Column(length: 10)]
    private ?string $labelTextColor = '#000000';

    #[ORM\Column(length: 5)]
    private ?int $labelMarginTop = 0;

    #[ORM\Column(length: 5)]
    private ?int $labelMarginBottom = 0;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    #[ORM\Column(enumType: ErrorCorrectionLevel::class)]
    private ?ErrorCorrectionLevel $errorCorrectionLevel = ErrorCorrectionLevel::Low;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function setSize(string $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getMargin(): ?string
    {
        return $this->margin;
    }

    public function setMargin(string $margin): static
    {
        $this->margin = $margin;

        return $this;
    }

    public function getBackgroundColor(): ?string
    {
        return $this->backgroundColor;
    }

    public function setBackgroundColor(string $backgroundColor): static
    {
        $this->backgroundColor = $backgroundColor;

        return $this;
    }

    public function getForegroundColor(): ?string
    {
        return $this->foregroundColor;
    }

    public function setForegroundColor(string $foregroundColor): static
    {
        $this->foregroundColor = $foregroundColor;

        return $this;
    }

    public function getLabelText(): ?string
    {
        return $this->labelText;
    }

    public function setLabelText(?string $labelText): static
    {
        $this->labelText = $labelText;

        return $this;
    }

    public function getLabelSize(): ?int
    {
        return $this->labelSize;
    }

    public function setLabelSize(int $labelSize): static
    {
        $this->labelSize = $labelSize;

        return $this;
    }

    public function getLabelTextColor(): ?string
    {
        return $this->labelTextColor;
    }

    public function setLabelTextColor(string $labelTextColor): static
    {
        $this->labelTextColor = $labelTextColor;

        return $this;
    }

    public function getLabelMarginTop(): ?string
    {
        return $this->labelMarginTop;
    }

    public function setLabelMarginTop(string $labelMarginTop): static
    {
        $this->labelMarginTop = $labelMarginTop;

        return $this;
    }

    public function getLabelMarginBottom(): ?string
    {
        return $this->labelMarginBottom;
    }

    public function setLabelMarginBottom(string $labelMarginBottom): static
    {
        $this->labelMarginBottom = $labelMarginBottom;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo($logo): static
    {
        if ($logo instanceof File) {
            $this->logo = $logo->getFilename();
        } else {
            $this->logo = $logo;
        }

        return $this;
    }

    public function getErrorCorrectionLevel(): ?ErrorCorrectionLevel
    {
        return $this->errorCorrectionLevel;
    }

    public function setErrorCorrectionLevel(ErrorCorrectionLevel $errorCorrectionLevel): static
    {
        $this->errorCorrectionLevel = $errorCorrectionLevel;

        return $this;
    }
}
