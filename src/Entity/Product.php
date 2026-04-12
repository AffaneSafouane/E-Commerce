<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\Positive]
    private ?float $priceHT = null;

    #[ORM\Column]
    private ?bool $available = null;

    /**
     * @var Collection<int, Order>
     */
    #[ORM\ManyToMany(targetEntity: Order::class, inversedBy: 'products')]
    private Collection $orders;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    /**
     * @var Collection<int, Media>
     */
    #[ORM\ManyToMany(targetEntity: Media::class, mappedBy: 'products', cascade: ['persist'])]
    private Collection $media;

    /**
     * @var Collection<int, OrderLine>
     */
    #[ORM\OneToMany(targetEntity: OrderLine::class, mappedBy: 'product', orphanRemoval: true)]
    private Collection $productOrderLine;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->media = new ArrayCollection();
        $this->productOrderLine = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPriceHT(): ?float
    {
        return $this->priceHT;
    }

    public function setPriceHT(float $priceHT): static
    {
        $this->priceHT = $priceHT;

        return $this;
    }

    public function isAvailable(): ?bool
    {
        return $this->available;
    }

    public function setAvailable(bool $available): static
    {
        $this->available = $available;

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): static
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
        }

        return $this;
    }

    public function removeOrder(Order $order): static
    {
        $this->orders->removeElement($order);

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Media>
     */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    public function addMedium(Media $medium): static
    {
        if (!$this->media->contains($medium)) {
            $this->media->add($medium);
            $medium->addProduct($this);
        }

        return $this;
    }

    public function removeMedium(Media $medium): static
    {
        if ($this->media->removeElement($medium)) {
            // set the owning side to null (unless already changed)
            if ($medium->getProducts()->contains($this)) {
                $medium->removeProduct($this);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, OrderLine>
     */
    public function getProductOrderLine(): Collection
    {
        return $this->productOrderLine;
    }

    public function addProductOrderLine(OrderLine $productOrderLine): static
    {
        if (!$this->productOrderLine->contains($productOrderLine)) {
            $this->productOrderLine->add($productOrderLine);
            $productOrderLine->setProduct($this);
        }

        return $this;
    }

    public function removeProductOrderLine(OrderLine $productOrderLine): static
    {
        if ($this->productOrderLine->removeElement($productOrderLine)) {
            // set the owning side to null (unless already changed)
            if ($productOrderLine->getProduct() === $this) {
                $productOrderLine->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * Get the path of the first PHOTO media, or a placeholder image if none exists.
     */
    public function getFirstPhotoPath(): ?string
    {
        foreach ($this->media as $medium) {
            if ($medium->getType() === \App\Enum\MediaType::PHOTO) {
                return '/uploads/media/' . $medium->getPath();
            }
        }

        return '/images/placeholder.webp';
    }
}
