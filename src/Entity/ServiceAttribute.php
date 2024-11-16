<?php

namespace App\Entity;

use App\Form\DataTransformer\KeyValueContainer;
use Traversable;
use InvalidArgumentException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[UniqueEntity(fields: ['name'])]
class ServiceAttribute {

    use IdTrait;
    use UuidTrait;

    #[ORM\Column(type: 'string', length: 191, unique: true)]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    private string $label;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description;

    #[ORM\Column(type: 'boolean')]
    #[Serializer\Exclude]
    private bool $isUserEditEnabled = true;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    private string $samlAttributeName;

    #[ORM\Column(type: 'string', enumType: ServiceAttributeType::class)]
    #[Assert\NotNull]
    private ?ServiceAttributeType $type;

    #[ORM\Column(type: 'boolean')]
    private bool $isMultipleChoice = false;

    #[ORM\Column(type: 'json', nullable: true)]
    private array $options = [ ];

    /**
     * @var Collection<SamlServiceProvider>
     */
    #[ORM\JoinTable]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: SamlServiceProvider::class, inversedBy: 'attributes')]
    #[Serializer\Exclude]
    private Collection $services;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->services = new ArrayCollection();
        $this->type = ServiceAttributeType::Text;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }

    public function getLabel(): string {
        return $this->label;
    }

    public function setLabel(string $label): self {
        $this->label = $label;
        return $this;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): self {
        $this->description = $description;
        return $this;
    }

    public function isUserEditEnabled(): bool {
        return $this->isUserEditEnabled;
    }

    public function setIsUserEditEnabled(bool $isUserEditEnabled): self {
        $this->isUserEditEnabled = $isUserEditEnabled;
        return $this;
    }

    public function getSamlAttributeName(): string {
        return $this->samlAttributeName;
    }

    public function setSamlAttributeName(string $samlAttributeName): self {
        $this->samlAttributeName = $samlAttributeName;
        return $this;
    }

    public function getType(): ?ServiceAttributeType {
        return $this->type;
    }

    public function setType(ServiceAttributeType $type): self {
        $this->type = $type;
        return $this;
    }

    public function isMultipleChoice(): bool {
        return $this->isMultipleChoice;
    }

    public function setIsMultipleChoice(bool $isMultipleChoice): self {
        $this->isMultipleChoice = $isMultipleChoice;
        return $this;
    }

    /**
     * Set the options.
     *
     * @param iterable|KeyValueContainer $options Something that can be converted to an array.
     */
    public function setOptions(KeyValueContainer|iterable $options): void {
        $this->options = $this->convertToArray($options);
    }

    /**
     * Extract an array out of $data or throw an exception if not possible.
     *
     * @param iterable|KeyValueContainer $data Something that can be converted to an array.
     *
     * @return array Native array representation of $data
     *
     * @throws InvalidArgumentException If $data can not be converted to an array.
     */
    private function convertToArray(KeyValueContainer|iterable $data): array {
        if (is_array($data)) {
            return $data;
        }

        if ($data instanceof KeyValueContainer) {
            return $data->toArray();
        }

        if ($data instanceof Traversable) {
            return iterator_to_array($data);
        }

        throw new InvalidArgumentException(sprintf('Expected array, Traversable or KeyValueContainer, got "%s"', get_debug_type($data)));
    }

    /**
     * @return string[]
     */
    public function getOptions(): array {
        return $this->options;
    }

    /**
     * @return Collection<SamlServiceProvider>
     */
    public function getServices(): Collection {
        return $this->services;
    }

    public function addService(ServiceProvider $serviceProvider): void {
        $this->services->add($serviceProvider);
    }

    public function removeService(ServiceProvider $serviceProvider): void {
        $this->services->removeElement($serviceProvider);
    }
}