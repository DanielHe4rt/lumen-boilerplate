<?php


namespace App\Http\Controllers;


use App\Entities\User\Token;
use App\Entities\User\User;
use App\Enums\Auth\TokenTypes;
use App\Http\Requests\Auth\PostForgotPasswordRequest;
use App\Http\Requests\Auth\PostResetPasswordRequest;
use App\Mail\Auth\ForgotMail;
use App\Repositories\User\MeRepository;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Ramsey\Uuid\Uuid;


class AuthController extends Controller
{
    use ApiResponse;

    public function __construct()
    {
        $this->middleware('auth', ['except' => ['postAuthenticate', 'postForgot', 'postReset']]);
    }

    /**
     * @OA\Post(
     *     path="/auth/login",
     *     summary="Autenticação de usuário",
     *     operationId="AuthLogin",
     *     tags={"auth"},
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="E-mail para autenticação",
     *         required=true,
     *         @OA\Schema(
     *           type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="Senha para autenticação",
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

    public function postAuthenticate(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|exists:users',
            'password' => 'required'
        ]);

        $data = [
            'grant_type' => 'password',
            'client_id' => env('PASSPORT_CLIENT_ID'),
            'client_secret' => env('PASSPORT_CLIENT_SECRET'),
            'username' => $request->input('email'),
            'password' => $request->input('password'),
            'scope' => ''
        ];

        $request = Request::create('/oauth/token', 'POST', $data);
        $response = App::dispatch($request);

        if ($response->getStatusCode() === 200) {
            $authContent = json_decode($response->getContent());
            return $this->success([$authContent]);
        }
        if ($response->getStatusCode() === 400 || $response->getStatusCode() === 401) {
            return $this->unauthorized(['unauthorized']);
        }
        if ($response->getStatusCode() >= 500) {
            return $this->internalError(['chama o eduardo q deu merda']);
        }

        return $this->internalError();
    }

    /**
     * @OA\Post(
     *     path="/auth/refresh",
     *     summary="Refresh de token do usuário",
     *     operationId="AuthRefresh",
     *     tags={"auth"},
     *     @OA\Parameter(
     *         name="refresh_token",
     *         in="query",
     *         description="Token de atualização",
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
    public function postRefresh(Request $request)
    {
        $this->validate($request, [
            'refresh_token' => 'required'
        ]);
        $data = [
            'grant_type' => 'refresh_token',
            'client_id' => env('PASSPORT_CLIENT_ID'),
            'client_secret' => env('PASSPORT_CLIENT_SECRET'),
            'refresh_token' => $request->input('refresh_token'),
            'scope' => ''
        ];

        $request = Request::create('/oauth/token', 'POST', $data);
        $response = App::dispatch($request);

        if ($response->getStatusCode() === 200) {
            $authContent = json_decode($response->getContent());
            return $this->success([$authContent]);
        }
        if ($response->getStatusCode() === 400 || $response->getStatusCode() === 401) {
            return $this->unauthorized(['unauthorized']);
        }
        if ($response->getStatusCode() >= 500) {
            return $this->internalError(['chama o eduardo q deu merda']);
        }

        return $this->internalError();
    }

    /**
     * @OA\Post(
     *     path="/auth/forgot",
     *     summary="Envia e-mail para recuperação de senha do usuário",
     *     operationId="AuthForgot",
     *     tags={"auth"},
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="E-mail do usuário",
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
    public function postForgot(PostForgotPasswordRequest $request)
    {
        $email = $request->input('email');
        $token = Token::create([
            'id' => Uuid::uuid4()->toString(),
            'type' => TokenTypes::EMAIL,
            'data' => $email,
            'expires_at' => Carbon::now()->addHours(env('APP_TOKENS_TIME'))
        ]);
        Mail::to($email)->send((new ForgotMail($token->id)));
        return $this->success();
    }

    //TODO: rota pro eduardo checar se tá tudo bonito com o reset


    /**
     * @OA\Post(
     *     path="/auth/reset",
     *     summary="Reseta a senha do usuário e retorna o bearer para autenticação",
     *     operationId="AuthReset",
     *     tags={"auth"},
     *     @OA\Parameter(
     *         name="token",
     *         in="query",
     *         description="Token do usuário",
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
     *         description="Confirmação do usuário",
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
    public function postReset(PostResetPasswordRequest $request)
    {
        $token = Token::find($request->input('token'));

        if (Carbon::parse($token->expires_at)->isPast() || $token->used) {
            $token->update(['used' => true]);
            return $this->unauthorized();
        }
        $token->update(['used' => true]);
        $user = User::where('email', $token->data)->first();
        $request->merge(['email' => $token->data]);
        (new MeRepository())->updatePassword($request->input('password'), $user->id);
        return $this->postAuthenticate($request);
    }
}
