<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Annotations as OA;
use Hateoas\Configuration\Annotation as Hateoas;
/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "api_product_detail",
 *           parameters = { "id" = "expr(object.getId())" },
 *           absolute = true
 *      )
 * )
 *
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"getlist", "get"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"getlist", "get"})
     * @Assert\NotBlank
     * @OA\Property(description="Product name")
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     * @Groups({"getlist", "get"})
     * @Assert\NotBlank
     * @OA\Property(description="Product description")
     */
    private $description;

    /**
     * @ORM\Column(type="float")
     * @Groups({"getlist", "get"})
     * @Assert\NotBlank
     * @OA\Property(description="Product price")
     */
    private $prix;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }
}
