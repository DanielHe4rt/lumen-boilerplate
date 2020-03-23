<?php


namespace App\Repositories\User;


use App\Entities\User\User;
use App\Repositories\BaseRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

class MeRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new User();
    }

    public function update(array $data, $id)
    {
        $data = Arr::only($data, ['name', 'age', 'birthdate']);
        return parent::update($data, $id);
    }

    public function updatePassword(string $string, $id)
    {
        return $this->model->find($id)->update([
            'password' => Hash::make($string)
        ]);
    }
}
