<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;


#[MongoDB\Document]
class Movie {
    
    #[MongoDB\Id]
    protected string $id;

    #[MongoDB\Field(type: 'string')]
    protected string $name;

    #[MongoDB\Field(type: 'string')]
    protected string $url;

    #[MongoDB\Field(type: 'string')]
    protected string $date;
    
    #[MongoDB\Field(type: 'string')]
    protected string $date_fr;

    #[MongoDB\Field(type: 'collection')]
    protected array $cinemas;

    #[MongoDB\Field(type: 'float')]
    protected float $tmdbVoteAvg = 0;

    #[MongoDB\Field(type: 'string')]
    protected string $tmdbOverview;

    #[MongoDB\Field(type: 'string')]
    protected string $tmdbPoster;

    #[MongoDB\Field(type: 'string')]
    protected string $tmdbGenre;

    #[MongoDB\Field(type: 'string')]
    protected string $tmdbDirector;

    #[MongoDB\Field(type: 'collection')]
    protected array $tmdbActor = [];

    public function __construct(array $datas)
    {
        $this->name     = isset($datas['name'])     ? $datas['name'] : '' ;
        $this->url      = isset($datas['url'])      ? $datas['url'] : '' ;
        $this->date     = isset($datas['date'])     ? $datas['date'] : '' ;
        $this->date_fr  = isset($datas['date_fr'])  ? $datas['date_fr'] : '' ;
        $this->cinemas  = isset($datas['cinemas'])  ? $datas['cinemas'] : [];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Movie
    {
        $this->name = $name;

        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): Movie
    {
        $this->url = $url;

        return $this;
    }

    public function setDate(string $date): Movie
    {
        $this->date = $date;

        return $this;
    }

    public function getDate(): string
    {
        return $this->date;
    }   

    public function setDateFr(string $date_fr): Movie
    {
        $this->date_fr = $date_fr;

        return $this;
    }

    public function getCinemas()
    {
        return $this->cinemas;
    }

    public function setCinemas(array $cinemas): Movie
    {
        $this->cinemas = $cinemas;

        return $this;
    }

    public function getDateFr(): string
    {
        return $this->date_fr;
    }

    public function getTmdbVoteAvg()
    {
        return $this->tmdbVoteAvg;
    }

    public function setTmdbVoteAvg($tmdbVoteAvg)
    {
        $this->tmdbVoteAvg = $tmdbVoteAvg;
    }

    public function getTmdbOverview()
    {
        return $this->tmdbOverview;
    }

    public function setTmdbOverview($tmdbOverview)
    {
        $this->tmdbOverview = $tmdbOverview;
    }

    public function getTmdbPoster()
    {
        return $this->tmdbPoster;
    }

    public function setTmdbPoster($tmdbPoster)
    {
        $this->tmdbPoster = $tmdbPoster;
    }

    public function getTmdbGenre()
    {
        return $this->tmdbGenre;
    }

    public function setTmdbGenre($tmdbGenre)
    {
        $this->tmdbGenre = $tmdbGenre;
    }

    public function getTmdbDirector()
    {
        return $this->tmdbDirector;
    }

    public function setTmdbDirector($tmdbDirector)
    {
        $this->tmdbDirector = $tmdbDirector;
    }

    public function getTmdbActor()
    {
        return $this->tmdbActor;
    }

    public function setTmdbActor($tmdbActor)
    {
        $this->tmdbActor = $tmdbActor;
    }
}
