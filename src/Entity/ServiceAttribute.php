<?php

namespace App\Entity;

use Burgov\Bundle\KeyValueFormBundle\KeyValueContainer;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @UniqueEntity(fields={"name"})
 */
class ServiceAttribute {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="string", length=191, unique=true)
     * @ORM\OrderBy()
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $label;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="boolean")
     * @Serializer\Exclude()
     */
    private $isUserEditEnabled = true;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $samlAttributeName;

    /**
     * @ORM\Column(type="service_attribute_type")
     * @Assert\NotBlank()
     * @Assert\Choice(choices={"text", "select"})
     */
    private $type;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isMultipleChoice = false;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $options = [ ];

    /**
     * @ORM\ManyToMany(targetEntity="ServiceProvider", inversedBy="attributes")
     * @ORM\JoinTable(
     *  joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *  inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @Serializer\Exclude()
     */
    private $services;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->services = new ArrayCollection();
        $this->type = ServiceAttributeType::Text();
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     * @return ServiceAttribute
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel() {
        return $this->label;
    }

    /**
     * @param string $label
     * @return ServiceAttribute
     */
    public function setLabel($label) {
        $this->label = $label;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return ServiceAttribute
     */
    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUserEditEnabled() {
        return $this->isUserEditEnabled;
    }

    /**
     * @param bool $isUserEditEnabled
     * @return ServiceAttribute
     */
    public function setIsUserEditEnabled($isUserEditEnabled) {
        $this->isUserEditEnabled = $isUserEditEnabled;
        return $this;
    }

    /**
     * @return string
     */
    public function getSamlAttributeName() {
        return $this->samlAttributeName;
    }

    /**
     * @param string $samlAttributeName
     * @return ServiceAttribute
     */
    public function setSamlAttributeName($samlAttributeName) {
        $this->samlAttributeName = $samlAttributeName;
        return $this;
    }

    /**
     * @return ServiceAttributeType
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param ServiceAttributeType $type
     * @return ServiceAttribute
     */
    public function setType(ServiceAttributeType $type) {
        $this->type = $type;
        return $this;
    }

    /**
     * @return bool
     */
    public function isMultipleChoice() {
        return $this->isMultipleChoice;
    }

    /**
     * @param bool $isMultipleChoice
     * @return ServiceAttribute
     */
    public function setIsMultipleChoice($isMultipleChoice) {
        $this->isMultipleChoice = $isMultipleChoice;
        return $this;
    }

    /**
     * Set the options.
     *
     * @param array|KeyValueContainer|\Traversable $options Something that can be converted to an array.
     */
    public function setOptions($options)
    {
        $this->options = $this->convertToArray($options);
    }

    /**
     * Extract an array out of $data or throw an exception if not possible.
     *
     * @param mixed $data Something that can be converted to an array.
     *
     * @return array Native array representation of $data
     *
     * @throws \InvalidArgumentException If $data can not be converted to an array.
     */
    private function convertToArray($data)
    {
        if (is_array($data)) {
            return $data;
        }

        if ($data instanceof KeyValueContainer) {
            return $data->toArray();
        }

        if ($data instanceof \Traversable) {
            return iterator_to_array($data);
        }

        throw new \InvalidArgumentException(sprintf('Expected array, Traversable or KeyValueContainer, got "%s"', is_object($data) ? get_class($data) : gettype($data)));
    }

    /**
     * @return string[]
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * @return Collection
     */
    public function getServices(): Collection {
        return $this->services;
    }

    /**
     * @param ServiceProvider $serviceProvider
     */
    public function addService(ServiceProvider $serviceProvider) {
        $this->services->add($serviceProvider);
    }

    /**
     * @param ServiceProvider $serviceProvider
     */
    public function removeService(ServiceProvider $serviceProvider) {
        $this->services->removeElement($serviceProvider);
    }
}