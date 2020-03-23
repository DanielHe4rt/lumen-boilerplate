<?php


namespace App\Http\Controllers\Users;


use App\Entities\User\User;
use App\Http\Controllers\ApiController;
use App\Http\Requests\User\PostUserRequest;
use App\Http\Requests\User\PutUserRequest;
use App\Repositories\User\UserRepository;
use Illuminate\Http\Request;


class UserController extends ApiController
{
    public function __construct(User $model, UserRepository $repository)
    {
//        $this->middleware('auth',['except' => ['postUser']]);
//        $this->middleware('is-admin',['except' => ['postUser']]);
        $this->model = $model;
        $this->repository = $repository;
    }

    /**
     * @OA\Get(
     *     path="/users",
     *     summary="Listagem de usuários com paginação",
     *     operationId="GetUsers",
     *     tags={"users"},
     *     @OA\Response(
     *         response=200,
     *         description="...",
     *     )
     *
     * )
     */

    public function getUsers(Request $request){
        return parent::index($request);
    }

    /**
     * @OA\Post(
     *     path="/users",
     *     summary="Criação de novos usuários",
     *     operationId="PostUser",
     *     tags={"users"},
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Nome do usuário",
     *         required=true,
     *         @OA\Schema(
     *           type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="E-mail do usuário",
     *         required=true,
     *         @OA\Schema(
     *           type="string",
     *         )
     *     ),

     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="Senha do usuário",
     *         required=true,
     *         @OA\Schema(
     *           type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="password_confirmation",
     *         in="query",
     *         description="Confirmação de senha do usuário",
     *         required=true,
     *         @OA\Schema(
     *           type="string",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="..."
     *     )
     * )
     */

    public function postUser(PostUserRequest $request){
        return parent::store($request);
    }

    /**
     * @OA\Get(
     *     path="/users/{userId}",
     *     summary="Retorna um usuário",
     *     operationId="GetUser",
     *     tags={"users"},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="Id do usuário a ser pesquisado",
     *         required=true,
     *         @OA\Schema(
     *           type="string",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="...",
     *     )
     *
     * )
     */

    public function getUser(Request $request, $userId){
        return parent::show($request,$userId);
    }

    /**
     * @OA\Put(
     *     path="/users/{userId}",
     *     summary="Edição de um usuário",
     *     operationId="putUser",
     *     tags={"users"},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="Id do usuário",
     *         required=true,
     *         @OA\Schema(
     *           type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Nome do usuário",
     *         required=true,
     *         @OA\Schema(
     *           type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="E-mail do usuário",
     *         required=true,
     *         @OA\Schema(
     *           type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="role",
     *         in="query",
     *         description="Aluno = 1, Professor = 2",
     *         required=true,
     *         @OA\Schema(
     *           type="string",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="..."
     *     )
     * )
     */

    /** Atualização de usuário - Admin Only
     * @param PutUserRequest $request
     * @param int $userId
     * @return \Illuminate\Http\Response
     */

    public function putUser(PutUserRequest $request, $userId){
        return parent::update($request, $userId);
    }

    /**
     * @OA\Delete(
     *     path="/users/{userId}",
     *     summary="Deleta um usuário",
     *     operationId="DeleteUser",
     *     tags={"users"},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="Id do usuário a ser deletado",
     *         required=true,
     *         @OA\Schema(
     *           type="string",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="...",
     *     )
     * )
     */

    public function deleteUser(Request $request, int $userId){
        return parent::destroy($userId);
    }
}
