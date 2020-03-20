<?php


/**
 * @OA\Info(
 *   title="E-School API",
 *   version="1.0.0",
 *   @OA\Contact(
 *     email="hey@danielheart.dev"
 *   )
 * )
 */

/**
 * @OA\SecurityScheme(
 *   securityScheme="api_key",
 *   type="apiKey",
 *   in="header",
 *   name="Authorization"
 * )
 */

/**
 * @OA\Get(
 *     path="/",
 *     description="Home page",
 *     @OA\Response(response="default", description="Welcome page")
 * )
 */
