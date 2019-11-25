<?php
namespace App\Test\Utils;

use Cake\ORM\TableRegistry;
use Cake\Utility\Security;
use Firebase\JWT\JWT;

class TokenGenerator
{
    /**
     * Should run User.fixture first before this
     */
    public static function getToken(int $id = 200002)
    {
        $usersModel = TableRegistry::getTableLocator()->get('Users');
        $user = $usersModel->get($id);
        $payload = $user;
        $payload['sub'] = $id;
        return self::encode($payload);
    }

    /**
     * Encodes a payload into a JWTToken
     * 
     * @return string - jwt token
     */
    private static function encode($payload) {
        $time = time();
        $payload['iss'] = "Pipz";
        $payload['aud'] = "Microblog";
        $payload['iat'] = $time;
        $payload['nbf'] = $time;
        $payload['exp'] = $time + 86400;// One day expiration
        return JWT::encode($payload, Security::salt());
    }
}
