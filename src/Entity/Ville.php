<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\VilleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VilleRepository::class)
 * @ApiResource(
 *     collectionOperations={"get"={"normalization_context"={"groups"="ville:list"}}},
 *     itemOperations={"get"={"normalization_context"={"groups"="ville:item"}}},
 *     order={"nom"="ASC", "codePostal"="ASC"},
 *     paginationEnabled=false
 * )
 */
class Ville
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * #[Groups(['ville:list', 'ville:item'])]
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=5)
     * #[Groups(['ville:list', 'ville:item'])]
     */
    private $codePostal;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(string $codePostal): self
    {
        $this->codePostal = $codePostal;

        return $this;
    }
}
