<?php

namespace App\Entity;

use App\Repository\LinkRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LinkRepository::class)]
class Link
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $originalUrl = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $shortUrl = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $numberOfClicks = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createAt = null;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $useAt = null;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $deleteAt = null;

    #[ORM\Column]
    private bool $isDeleted = false;

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getOriginalUrl(): ?string
    {
        return $this->originalUrl;
    }

    public function setOriginalUrl(string $originalUrl): static
    {
        $this->originalUrl = $originalUrl;

        return $this;
    }


    public function getShortUrl(): ?string
    {
        return $this->shortUrl;
    }

    public function getFullShortUrl(): string
    {
        return "http://" . $_SERVER['HTTP_HOST'] . '/short/' . $this->shortUrl;
    }

    public function setShortUrl(string $shortUrl): static
    {
        $this->shortUrl = $shortUrl;

        return $this;
    }

    public function setGenericShortUrl(): static
    {
        $shortUrl = '';

        $chars = '0123456789abcdefghijklmnopqrstuvwxyz';

        for ($i = 0; $i < 6; $i++) {
            $shortUrl .= $chars[rand(0, strlen($chars) - 1)];
        }

        $this->shortUrl = $shortUrl;

        return $this;
    }


    public function getNumberOfClicks(): ?int
    {
        return $this->numberOfClicks;
    }

    public function setNumberOfClicks(int $numberOfClicks): static
    {
        $this->numberOfClicks = $numberOfClicks;

        return $this;
    }

    public function addOneClick(): static
    {
        $this->numberOfClicks++;

        return $this;
    }


    public function getCreateAt(): ?DateTimeImmutable
    {
        return $this->createAt;
    }

    public function setCreateAt(DateTimeImmutable $createAt): static
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function setCreateNow(): static
    {
        $this->createAt = new DateTimeImmutable();

        return $this;
    }


    public function getUseAt(): ?DateTimeImmutable
    {
        return $this->useAt;
    }

    public function setUseAt(DateTimeImmutable $useAt): static
    {
        $this->useAt = $useAt;

        return $this;
    }

    public function setUseNow(): static
    {
        $this->useAt = new DateTimeImmutable();

        return $this;
    }


    public function getDeleteAt(): ?DateTimeImmutable
    {
        return $this->deleteAt;
    }

    public function setDeleteAt(?DateTimeImmutable $deleteAt): static
    {
        $this->deleteAt = $deleteAt;

        return $this;
    }

    public function setDeleteNow(): static
    {
        $this->deleteAt = new DateTimeImmutable();

        return $this;
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): static
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }
}
