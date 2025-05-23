<?php

namespace App\Entity\Tenant;

use ApiPlatform\Metadata\ApiResource;
use Endroid\QrCode\ErrorCorrectionLevel;
use App\Repository\QrVisualConfigRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

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
    private ?int $size = null;

    #[ORM\Column(length: 5)]
    private ?int $margin = null;

    #[ORM\Column(length: 10)]
    private ?string $backgroundColor = null;

    #[ORM\Column(length: 10)]
    private ?string $foregroundColor = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $labelText = null;

    #[ORM\Column(length: 5, nullable: true)]
    private ?int $labelSize = null;

    #[ORM\Column(length: 10)]
    private ?string $labelTextColor = null;

    #[ORM\Column(length: 5)]
    private ?string $labelMarginTop = null;

    #[ORM\Column(length: 5)]
    private ?string $labelMarginBottom = null;

    #[ORM\Column(type: Types::BLOB, nullable: true)]
    private $logo = null;

    #[ORM\Column(enumType: ErrorCorrectionLevel::class)]
    private ?ErrorCorrectionLevel $errorCorrectionLevel = null;

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

    public function setLabelText(string $labelText): static
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

    public function getLogo()
    {
        return $this->logo;
    }

    public function setLogo($logo): static
    {
        $this->logo = $logo;

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
