<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document]
class Cinemas {

    #[MongoDB\Id]
    protected string $id;

    #[MongoDB\Field(type: 'string')]
    protected ?string $name ='';

    #[MongoDB\Field(type: 'string')]
    protected ?string $address ='';

    #[MongoDB\Field(type: 'string')]
    protected ?string $zipcode ='';

    #[MongoDB\Field(type: 'string')]
    protected string $city ='';

    #[MongoDB\Field(type: 'string')]
    protected string $image ='';

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Cinemas
    {
        $this->name = $name;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): Cinemas
    {
        $this->address = $address;
        return $this;

    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(string $zipcode): Cinemas
    {
        $this->zipcode = $zipcode;
        return $this;
        
    }

    public function getCity(): string
    {
        return $this->city;
    }
  
    public function setCity(string $city): Cinemas
    {
        $this->city = $city;
        return $this;
    }

    public function getImage(): string
    {
        return $this->image;
    }
  
    public function setImage(string $image): Cinemas
    {
        $this->image = $image;
        return $this;
    }

}
