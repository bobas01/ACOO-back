<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\ApiProperty;
use App\Controller\AuthController;
use App\Controller\RegisterController;
use App\Repository\AdminRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AdminRepository::class)]
#[ORM\Table(name: '`admin`')]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/admin/{id}',
            controller: AuthController::class . '::show',
            normalizationContext: ['groups' => ['admin:read']]
        ),
        new GetCollection(
            uriTemplate: '/admin',
            controller: AuthController::class . '::index',
            normalizationContext: ['groups' => ['admin:read']]
        ),
        new Post(
            uriTemplate: '/admin/register',
            controller: RegisterController::class . '::register',
            deserialize: false,
            denormalizationContext: ['groups' => ['admin:write']]
        ),
        new Post(
            uriTemplate: '/admin/login',
            controller: AuthController::class . '::login',
            deserialize: false
        ),
        new Post(
            uriTemplate: '/admin/{id}',
            controller: AuthController::class . '::update',
            deserialize: false,
            denormalizationContext: ['groups' => ['admin:write']]
        ),
        new Delete(
            uriTemplate: '/admin/{id}',
            controller: AuthController::class . '::delete'
        )
    ],
    formats: ['json', 'multipart' => ['multipart/form-data']]
)]
class Admin implements UserInterface, PasswordAuthenticatedUserInterface
{

#[ORM\Id]
#[ORM\GeneratedValue]
#[ORM\Column]
#[Groups(['admin:read'])]
private ?int $id = null;

#[ORM\Column(length: 255)]
#[Assert\NotBlank]
#[Assert\Length(min: 3)]
#[Groups(['admin:read', 'admin:write'])]
private ?string $username = null;

#[ORM\Column(length: 255)]
#[Assert\NotBlank]
#[Assert\Email]
#[Groups(['admin:read', 'admin:write'])]
private ?string $email = null;

#[ORM\Column(length: 255)]
#[Assert\NotBlank]
#[Assert\Length(min: 12)]
#[Groups(['admin:write'])]
private ?string $password = null;

    /**
     * @var Collection<int, News>
     */
    #[ORM\OneToMany(targetEntity: News::class, mappedBy: 'id_admin')]
    private Collection $news;

    public function __construct()
    {
        $this->news = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return Collection<int, News>
     */
    public function getNews(): Collection
    {
        return $this->news;
    }

    public function addNews(News $news): static
    {
        if (!$this->news->contains($news)) {
            $this->news->add($news);
            $news->setIdAdmin($this);
        }

        return $this;
    }

    public function removeNews(News $news): static
    {
        if ($this->news->removeElement($news)) {
            // set the owning side to null (unless already changed)
            if ($news->getIdAdmin() === $this) {
                $news->setIdAdmin(null);
            }
        }

        return $this;
    }

    public function getRoles(): array
    {
        return ['ROLE_ADMIN'];
    }

    public function getSalt(): ?string
    {
        
        return null; 
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function eraseCredentials(): void
    {
        
    }
}
