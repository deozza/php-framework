<?php


namespace App\Repositories;

use App\Entities\Album;
use App\Lib\Repositories\AbstractRepository;

class AlbumRepository extends AbstractRepository {
    
    public function __construct()
    {
        parent::__construct(Album::class);
    }
}
    


?>
