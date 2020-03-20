<?php


namespace App\Repositories\Classes;


use App\Entities\Classes\Classes;
use App\Repositories\BaseRepository;

class ClassesRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new Classes();
    }
}
