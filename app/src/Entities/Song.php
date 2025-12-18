<?php


namespace App\Entities;

use App\Lib\Annotations\ORM\AutoIncrement;
use App\Lib\Annotations\ORM\Column;
use App\Lib\Annotations\ORM\Id;
use App\Lib\Annotations\ORM\ManyToOne;
use App\Lib\Annotations\ORM\ORM;
use App\Lib\Entities\AbstractEntity;
use App\Entities\Album;

#[ORM]
class Song extends AbstractEntity {

    #[Id]
    #[AutoIncrement]
    #[Column(type: 'int')]
    public int $id;
    
    #[Column(type: 'varchar', size: 255)]
    public string $name;

    #[Column(type: 'int')]
    public \DateTime $releaseDate;
    
    #[Column(type: 'int')]
    #[ManyToOne(class: Artist::class, property: 'id')]
    public int $artist;
    
    #[Column(type: 'int')]
    #[ManyToOne(class: Album::class, property: 'id')]
    public int $album;
    
    public function getId(): int
    {
        return $this->id;
    }
  
}

?>
