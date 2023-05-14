<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\keyAndPass;

class HomeController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/key",
     * summary="Generate Key and Password Key",
     * description="Response a generated Key, and store Password Key for future decryption use.",
     * operationId="generateKey",
     * tags={"generate Key - encrypting"},
     * @OA\Response(
     *    response=200,
     *    description="Generated Encryption key",
     *    @OA\JsonContent(
     *       @OA\Property(property="id", type="string", example="1"),
     *       @OA\Property(property="key", type="string", example="key31$52")
     *        )
     *     )
     * )
     */

    public function generateKey()
    {
        //generate random key and password
        $keyRandom = base64_encode(random_bytes(22));

        $password = '';
        $length = 50;
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+{}[]|:;"<>,.?/';

        // Generate random password
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }

        //store
        $key = new keyAndPass;
        $key->key = $keyRandom;
        $key->password = $password;
        $key->save();

        return response([
            'id' => $key->id,
            'key' => $keyRandom
        ], 200);
    }

    /**
     * @OA\Get(
     * path="/api/get/key",
     * summary="Get Key by Password",
     * description="Sent a password and response a decryption Key.",
     * operationId="retrieveKey",
     * tags={"retrive Key - decrypting"},
     * @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="password use to retrieve the key",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             format="string"
     *         )
     *     ),
     * @OA\Response(
     *    response=200,
     *    description="Get Encryption key",
     *    @OA\JsonContent(
     *       @OA\Property(property="id", type="string", example="1"),
     *       @OA\Property(property="key", type="string", example="key31$52"),
     *       @OA\Property(property="pass", type="string", example="asdad3131sadasdada="),
     *        )
     *     ),
     * @OA\Response(
     *    response=404,
     *    description="No Matched Encryption key",
     *    @OA\JsonContent(
     *       @OA\Property(property="id", type="string", example="not found!")
     *        )
     *     )
     * )
     */

    public function getKeyByPass(Request $req)
    {
        $key = keyAndPass::where('password', $req->password);

        if(!($key->exists()))
        {
            return response(
                [
                    'id' => 'not found!'
                ], 404
                );
        } else {
            $key = $key->first();
            return response(
                [
                    'id' => $key->id,
                    'key' => $key->key,
                    'pass' => $key->password
                ], 200
            );
        }
    }
}
