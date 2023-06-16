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
}
