<?php


namespace App\Http\Controllers\Users;


use App\Enums\User\GenderTypes;
use App\Http\Controllers\ApiController;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class GenderController extends ApiController
{
    use ApiResponse;
    public function getGenders(Request $request){
        return $this->success(GenderTypes::LIST);
    }
}
