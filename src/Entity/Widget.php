<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\Widget\WidgetTypeEnum;
use App\Repository\WidgetRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: WidgetRepository::class)]
#[Broadcast(template: 'broadcast/Widget/Widget.stream.html.twig')]
class Widget
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[NotBlank(message: 'widget.title')]
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[NotBlank(message: 'widget.type')]
    #[ORM\Column(enumType: WidgetTypeEnum::class)]
    private ?WidgetTypeEnum $type = null;

    /** @var array<string, mixed>|null */
    #[ORM\Column(nullable: true)]
    private ?array $content = null;

    #[ORM\Column]
    private ?int $position = null;

    #[ORM\ManyToOne(inversedBy: 'widgets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getType(): ?WidgetTypeEnum
    {
        return $this->type;
    }

    public function setType(WidgetTypeEnum $type): static
    {
        $this->type = $type;

        return $this;
    }

    /** @return array<string, mixed>|null */
    public function getContent(): ?array
    {
        return $this->content;
    }

    /** @param array<string, mixed>|null $content */
    public function setContent(?array $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
