<?php

class JWT
{
  public static function encode($payload, $secret, $algorithm = 'HS256')
  {
    $header = [
      'alg' => $algorithm,
      'typ' => 'JWT'
    ];
    $encodedHeader = self::base64UrlEncode(json_encode($header));
    $encodedPayload = self::base64UrlEncode(json_encode($payload));
    $signature = self::generateSignature($encodedHeader, $encodedPayload, $secret, $algorithm);

    return $encodedHeader . '.' . $encodedPayload . '.' . $signature;
  }

  public static function decode($token, $secret)
  {
    list($encodedHeader, $encodedPayload, $signature) = explode('.', $token);

    $header = json_decode(self::base64UrlDecode($encodedHeader), true);
    $payload = json_decode(self::base64UrlDecode($encodedPayload), true);

    $algorithm = isset($header['alg']) ? $header['alg'] : 'HS256';

    if (!self::verifySignature($encodedHeader, $encodedPayload, $signature, $secret, $algorithm)) {
      return null;
    }

    return $payload;
  }

  private static function generateSignature($encodedHeader, $encodedPayload, $secret, $algorithm)
  {
    $data = $encodedHeader . '.' . $encodedPayload;

    switch ($algorithm) {
      case 'HS256':
        return self::base64UrlEncode(hash_hmac('sha256', $data, $secret, true));
      default:
      throw new Exception('Unsupported algorithm');
    }
  }

  private static function verifySignature($encodedHeader, $encodedPayload, $signature, $secret, $algorithm)
  {
    $expectedSignature = self::generateSignature($encodedHeader, $encodedPayload, $secret, $algorithm);
    return hash_equals($signature, $expectedSignature);
  }

  private static function base64UrlEncode($data)
  {
    return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
  }

  private static function base64UrlDecode($data)
  {
    $paddedData = str_pad($data, strlen($data) % 4, '=', STR_PAD_RIGHT);
    return base64_decode(str_replace(['-', '_'], ['+', '/'], $paddedData));
  }

  public static function isValid($token) {
    if (!$token) {
      return false;
    }
    $payload = JWT::decode($token, getenv("SECRET_KEY"));
    if (!$payload) {
      return false;
    }
    if (!$payload["expiration"] || strtotime($payload["expiration"]) > time()) {
      return false;
    }
    if (!array_key_exists("userId", $payload)) {
      return false;
    }
    if (filter_var($payload["userId"], FILTER_VALIDATE_INT) === false) {
      return false;
    }
    return true;
  }
}
