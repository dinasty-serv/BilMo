<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Annotations as OA;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @Hateoas\Relation(
 *     "self",
 *      href=@Hateoas\Route(
 *          "api_users_detail",
 *          parameters = {"id" = "expr(object.getId())"},
 *          absolute = true
 *      )
 * )
 *
 * @Hateoas\Relation(
 *     "update",
 *      href=@Hateoas\Route(
 *          "api_user_edit",
 *          parameters = {"id" = "expr(object.getId())"},
 *          absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *     "delete",
 *      href=@Hateoas\Route(
 *          "api_user_delete",
 *          parameters = {"id" = "expr(object.getId())"},
 *          absolute = true
 *      )
 * )
 *
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"get", "getlist"})
     *
     * @OA\Property(description="Unique ID")
     *
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Serializer\Groups({"get","write_user"})
     * @Assert\NotBlank
     * @Assert\Email(
     *     message = "l'adresse '{{ value }}' est pas un email valide !."
     * )
     *
     * @OA\Property(description="Email user")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"get","write_user"})
     * @Assert\NotBlank
     * @OA\Property(description="Username")
     */
    private $username;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="Users")
     * @Serializer\Exclude
     */
    private $client;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }
}
