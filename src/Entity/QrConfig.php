<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embeddable;

#[Embeddable]
class QrConfig
{
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $size = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $margin = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $codeBackground = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $codeColor = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $text = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $textColor = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $textMarginTop = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $textMarginBottom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $errorCorrectionLevel = null;

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(?string $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getMargin(): ?string
    {
        return $this->margin;
    }

    public function setMargin(?string $margin): static
    {
        $this->margin = $margin;

        return $this;
    }

    public function getCodeBackground(): ?string
    {
        return $this->codeBackground;
    }

    public function setCodeBackground(?string $codeBackground): static
    {
        $this->codeBackground = $codeBackground;

        return $this;
    }

    public function getCodeColor(): ?string
    {
        return $this->codeColor;
    }

    public function setCodeColor(?string $codeColor): static
    {
        $this->codeColor = $codeColor;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getTextColor(): ?string
    {
        return $this->textColor;
    }

    public function setTextColor(?string $textColor): static
    {
        $this->textColor = $textColor;

        return $this;
    }

    public function getTextMarginTop(): ?string
    {
        return $this->textMarginTop;
    }

    public function setTextMarginTop(?string $textMarginTop): static
    {
        $this->textMarginTop = $textMarginTop;

        return $this;
    }

    public function getTextMarginBottom(): ?string
    {
        return $this->textMarginBottom;
    }

    public function setTextMarginBottom(?string $textMarginBottom): static
    {
        $this->textMarginBottom = $textMarginBottom;

        return $this;
    }

    public function getErrorCorrectionLevel(): ?string
    {
        return $this->errorCorrectionLevel;
    }

    public function setErrorCorrectionLevel(?string $errorCorrectionLevel): static
    {
        $this->errorCorrectionLevel = $errorCorrectionLevel;

        return $this;
    }
}
