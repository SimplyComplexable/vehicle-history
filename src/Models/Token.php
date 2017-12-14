<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 10/7/2015
 * Time: 7:55 PM
 */

namespace VehicleHistory\Models;

use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use \Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use VehicleHistory\Http\StatusCodes;

class Token
{
    const ROLE_STUDENT = "Student";
    const ROLE_FACULTY = "Faculty";
    private static $KEY = "cdf97907258bb76aebaa7d435992f6b94f6f8886de4d725036e38cc17420625dc23fc2856519a1b51937ce89502cbb309b3501dd3908b4ff0966ff49c8747dfc";   //TODO: Extract key to config file that is loaded on-run.
    private static $lengthValid = 3600; // 1 Hour

    /**
     * @var string Contains a standard JWT.
     */
    public static $token = "";

    public static function buildToken($id, $username)
    {
        $tokenId = uniqid("", true);//TODO: Reset with MCrypt Enabled. //base64_encode(mcrypt_create_iv(32));
        $issuedAt = time();
        $notBefore = $issuedAt;                   //Adding 10 seconds to compensate for clock-skew
        $expire = $notBefore + self::$lengthValid;  // Expiration time
        $serverName = "http://icarus.cs.weber.edu";
        $data = [
            'iat' => $issuedAt,         // Issued at: time when the token was generated
            'jti' => $tokenId,          // Json Token Id: an unique identifier for the token
            'iss' => $serverName,       // Issuer
            'nbf' => $notBefore,        // Not before
            'exp' => $expire,           // Expire
            'data' => [                  // Data related to the signer user
                'id' => $id,
                'username' => $username  // User name
            ]
        ];

        static::$token = JWT::encode($data, self::$KEY, 'HS256');

        return JWT::encode($data, self::$KEY, 'HS256');
    }

    private static function extractTokenData($jwt)
    {
        try {
            $tokenData = (array)JWT::decode($jwt, self::$KEY, array('HS256'));
        } catch (Exception $e) {
            http_response_code(StatusCodes::UNAUTHORIZED);
            return false;
        } catch (BeforeValidException $e) {
            http_response_code(StatusCodes::UNAUTHORIZED);
            return false;
        } catch (ExpiredException $e) {
            http_response_code(StatusCodes::UNAUTHORIZED);
            return false;
        } catch (SignatureInvalidException $e) {
            http_response_code(StatusCodes::UNAUTHORIZED);
            return false;
        }

        return $tokenData;
    }
    public static function getUsernameFromToken($jwt = null)
    {
        if ($jwt == null)
            $jwt = self::getBearerTokenFromHeader();
        $tokenData = static::extractTokenData($jwt);
        if (!$tokenData) {
            return false;
        }
        $data = (array)$tokenData['data'];
        return $data['username'];
    }
    public static function getIDFromToken($jwt = null)
    {
        if ($jwt == null)
            $jwt = self::getBearerTokenFromHeader();
        $tokenData = static::extractTokenData($jwt);
        if (!$tokenData) {
            return false;
        }
        $data = (array)$tokenData['data'];
        return $data['id'];
    }

    private static function getBearerTokenFromHeader()
    {
        $headers = apache_request_headers();

        if (!isset($headers)) {
            http_response_code(StatusCodes::BAD_REQUEST);
            exit("No headers set.");
        }

        if (!array_key_exists("Authorization", $headers)) {
            http_response_code(StatusCodes::UNAUTHORIZED);
            exit("No credentials provided.");
        }

        $authHeader = $headers['Authorization'];
        list($jwt) = sscanf($authHeader, 'Bearer %s');

        if ($jwt == null)
        {
            http_response_code(StatusCodes::UNAUTHORIZED);
            exit("No credentials provided.");
        }

        return $jwt;
    }
}
