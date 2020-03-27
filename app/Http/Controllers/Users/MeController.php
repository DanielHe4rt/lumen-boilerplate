<?php


namespace App\Http\Controllers\Users;


use App\Http\Controllers\ApiController;
use App\Http\Requests\User\Me\PutMePasswordRequest;
use App\Http\Requests\User\Me\PutMeRequest;
use App\Repositories\User\MeRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MeController extends ApiController
{
    use ApiResponse;
    public function __construct(MeRepository $repository)
    {
        $this->middleware('auth');
        $this->model = Auth::user();
        $this->repository = $repository;
    }

    /**
     * @OA\Get(
     *     path="/users/me",
     *     summary="Retorna as informações do usuário autenticado",
     *     operationId="GetMeUser",
     *     tags={"users","me"},
     *     @OA\Parameter(
     *         name="includes",
     *         in="path",
     *         description="array de relações (ORM)",
     *         required=false,
     *         @OA\Schema(
     *           type="string",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="...",
     *     ),
     *     security={{
     *          "api_key":{}
     *     }}
     * )
     */

    public function getMe(Request $request)
    {
        return parent::show($request, $this->model->id);
    }

    /**
     * @OA\Put(
     *     path="/users/me",
     *     summary="Atualiza as informações do usuário autenticado",
     *     operationId="PutMeUser",
     *     tags={"users","me"},
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Nome do usuário",
     *         required=false,
     *         @OA\Schema(
     *           type="string",
     *         )
     *     ),

     *     @OA\Parameter(
     *         name="birthdate",
     *         in="query",
     *         description="Data de nascimento do usuário",
     *         required=false,
     *         @OA\Schema(
     *           type="string",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="...",
     *     ),
     *     security={{
     *          "api_key":{}
     *     }}
     * )
     */
    public function putMe(PutMeRequest $request)
    {
        return parent::update($request, $this->model->id);
    }
    /**
     * @OA\Put(
     *     path="/users/me/password",
     *     summary="Atualiza a senha do usuário autenticado",
     *     operationId="PutMeUserPassword",
     *     tags={"users","me"},
     *     @OA\Parameter(
     *         name="old_password",
     *         in="query",
     *         description="Senha antiga do usuário",
     *         required=true,
     *         @OA\Schema(
     *           type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="Nova senha",
     *         required=true,
     *         @OA\Schema(
     *           type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="password_confirmation",
     *         in="query",
     *         description="Confirmação de senha",
     *         required=true,
     *         @OA\Schema(
     *           type="string",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="...",
     *     ),
     *     security={{
     *          "api_key":{}
     *     }}
     * )
     */
    public function putMePassword(PutMePasswordRequest $request)
    {
        $credentials = $request->only(['password','old_password']);

        if (!Hash::check($credentials['old_password'], $this->model->password)) {
            return $this->unprocessable(['password' => ['A senha atual está incorreta']]);
        }
        return $this->success($this->repository->updatePassword($credentials['password'], $this->model->id));
    }
}
