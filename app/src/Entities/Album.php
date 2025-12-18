<?php



namespace App\Entities;

use App\Lib\Annotations\ORM\AutoIncrement;
use App\Lib\Annotations\ORM\Column;
use App\Lib\Annotations\ORM\Id;
use App\Lib\Annotations\ORM\ManyToOne;
use App\Lib\Annotations\ORM\ORM;
use App\Lib\Annotations\ORM\OneToMany;
use App\Lib\Entities\AbstractEntity;

#[ORM]
class Album extends AbstractEntity {

    #[Id]
    #[AutoIncrement]
    #[Column(type: 'int')]
    public int $id;
    
    #[Column(type: 'varchar', size: 255)]
    public string $name;

    #[Column(type: 'int')]
    public int $releaseDate;
    
    #[Column(type: 'int')]
    #[ManyToOne(class: Artist::class, property: 'id')]
    public int $artist;

    #[OneToMany(class: Song::class, property: 'id')]
    public array $songs;
    
    public function getId(): int
    {
        return $this->id;
    }
  
}

?>
