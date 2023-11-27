<?php

namespace App\Document;

use Symfony\Component\Security\Core\User\UserInterface;
use DateTime;
use App\Validator\PostalCodeValidator;
use App\Validator\PostalCode;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique as MongoDBUnique;




#[MongoDB\Document]
// #[UniqueEntity("email", message:"Cet e-mail est déjà utilisé.")]
#[MongoDBUnique(fields:"email")]
class User implements UserInterface , PasswordAuthenticatedUserInterface
{
    #[MongoDB\Id]
    protected string $id;

    #[MongoDB\Field(type: 'string')]
    protected ?bool $gender = false;

    #[MongoDB\Field(type: 'string')]
    protected ?string $lastname = '';

    #[MongoDB\Field(type: 'string')]
    protected ?string $firstname = '';

    #[MongoDB\Field(type: 'date')]
    protected ?DateTime $dateOfBirth = null;

    #[MongoDB\Field(type: 'string')]
    protected string $password;

    #[MongoDB\Field(type: 'string')]
    protected string $email;

    #[MongoDB\Field(type: 'string')]
    protected ?string $phone='';

    #[MongoDB\Field(type: 'string')]
    protected ?string $address = '';

    #[MongoDB\Field(type: 'int')]
    protected ?int $postalCode = 0;

    #[MongoDB\Field(type: 'string')]
    protected ?string $city = '';

    #[MongoDB\Field(type: 'string')]
    protected ?string $country = '';

    #[MongoDB\Field(type: 'int')]
    protected ?int $fidelityPoints = 0;

    #[MongoDB\Field(type: 'collection')]
    protected array $actors = [];
    
    #[MongoDB\Field(type: 'collection')]
    protected array $directors = [];

    #[MongoDB\Field(type: 'collection')]
    protected array $genres = [];

    #[MongoDB\Field(type: 'collection')]
    protected array $seats = [];

    #[MongoDB\Field(type: 'string')]
    protected string $language;

    #[MongoDB\Field(type: 'collection')]
    protected array $gifts = [];

    #[MongoDB\Field(type: 'collection')]
    protected array $roles = [];

    #[MongoDB\Field(type: 'collection')]
    protected array $locations = [];

    #[MongoDB\Field(type: 'bool')]
    protected bool $firstConnection = true;    

    public function getId(): string
    {
        return $this->id;
    }

    public function getGender(): string
    {
        return $this->email;
    }

    public function setGender(string $gender): User
    {
        $this->gender = $gender;
        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastname;
    }

    public function setLastName(string $lastname): User
    {
        $this->lastname = $lastname;
        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstname;
    }

    public function setFirstName(string $firstname): User
    {
        $this->firstname = $firstname;
        return $this;
    }

    public function getDateOfBirth(): ?DateTime
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(?DateTime $dateOfBirth): User
    {
        $this->dateOfBirth = $dateOfBirth;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): User
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): User
    {
        $this->password = $password;
        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): User
    {
        $this->phone = $phone;
        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): User
    {
        $this->address = $address;
        return $this;
    }

    public function getPostalCode(): ?int
    {
        return $this->postalCode;
    }    

    public function setPostalCode(?int $postalCode): User
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }
  
    public function setCity(string $city): User
    {
        $this->city = $city;
        return $this;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): User
    {
        $this->country = $country;
        return $this;
    }

    public function getLocation(): array
    {
        return $this->locations;
    }

    public function setLocation(array $locations): User
    {
        $this->locations = $locations;
        return $this;
    }

    public function addLocation(string $location): User
    {
        if (!in_array($location, $this->locations))
            $this->locations[] = $location;

        return $this;
    }
    
    public function getFidelityPoints(): ?int
    {
        return $this->fidelityPoints;
    }

    public function setFidelityPoints(?int $fidelityPoints): User
    {
        $this->fidelityPoints = $fidelityPoints;
        return $this;
    }

    public function getActors(): array
    {
        return $this->actors;
    }

    public function setActors(array $actors): User
    {
        $this->actors = $actors;
        return $this;
    }

    public function addActor(string $actor): User
    {
        if (!in_array($actor, $this->actors))
            $this->actors[] = $actor;

        return $this;
    }

    public function getDirectors(): array
    {
        return $this->directors;
    }

    public function setDirectors(array $director): User
    {
        $this->directors = $director;
        return $this;
    }

    public function addDirector(string $director): User
    {
        if (!in_array($director, $this->directors))
            $this->directors[] = $director;

        return $this;
    }

    public function getGenres(): array
    {
        return $this->genres;
    }

    public function setGenres(array $genres): User
    {
        $this->genres = $genres;
        return $this;
    }
    
    public function addGenre(string $genre): User
    {
        if (!in_array($genre, $this->genres))
            $this->genres[] = $genre;

        return $this;
    }

    public function getSeats(): array
    {
        return $this->seats;
    }

    public function setSeats(array $seats): User
    {
        $this->seats = $seats;
        return $this;
    }

    public function addSeat(string $seat): User
    {
        if (!in_array($seat, $this->seats))
            $this->seats[] = $seat;

        return $this;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function setLanguage(string $language): User
    {
        $this->language = $language;
        return $this;
    }
    
    public function getGifts(): array
    {
        return $this->gifts;
    }

    public function setGifts(array $gifts): User
    {
        $this->gifts = $gifts;
        return $this;
    }

    public function addGift(string $gift): User
    {
        if (!in_array($gift, $this->gifts))
            $this->gifts[] = $gift;

        return $this;
    }

    public function isFirstConnection(): bool{
        return $this->firstConnection;
    }

    public function setFirstConnection(bool $firstConnection): User {
        $this->firstConnection = $firstConnection;
        return $this;
    }

     /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}