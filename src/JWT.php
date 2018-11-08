<?php

namespace MixtureApiClient;

/**
 * Class JWT
 *
 * @package MixtureApiClient
 */
class JWT
{
    const CHECK_STATUS_OK = 1;
    const CHECK_STATUS_UNMATCHED = 2;
    const CHECK_STATUS_EXPIRED = 3;

    const CHECK_STATUS_RENEW_TOKEN_SUCCESS = 4;
    const CHECK_STATUS_RENEW_TOKEN_FAILED = 5;

    /**
     * JWT作成
     *
     * @param  string $secret
     * @param  array  $payload
     * @param  int    $exp_time
     * @return string
     */
    public function makeToken($secret, $payload, $exp_time = 0)
    {
        if ($exp_time === 0 || !is_int($exp_time)) {
            // 一時間後
            $exp_time = (new \DateTime(date('Y-m-d H:i:s', strtotime('+1 hour'))))->getTimestamp();
        }

        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT',
            'exp' => $exp_time,
        ];

        $header = json_encode($header);
        $payload = json_encode($payload);

        $header_base64 = $this->base64UrlEncode($header);
        $payload_base64 = $this->base64UrlEncode($payload);

        $unsignedToken = $header_base64 . '.' . $payload_base64;
        $sig = hash_hmac('sha256', $unsignedToken, $secret, true);

        return 'Bearer ' . $header_base64 . '.' . $payload_base64 . '.' . $this->base64UrlEncode($sig);
    }

    /**
     * TOKENの整合性チェック
     *
     * @param  string     $secret
     * @param  string     $bearer
     * @return array|bool
     */
    public function checkToken($secret, $bearer)
    {
        $status = JWT::CHECK_STATUS_OK;
        $trace = '';
        $token = str_replace('Bearer ', '', $bearer);
        $arr = explode('.', $token);
        if (count($arr) != 3) {
            return false;
        }
        $header = json_decode($this->base64UrlDecode($arr[0]), true);
        $payload = json_decode($this->base64UrlDecode($arr[1]), true);
        //$sig = $arr[2];

        $checkToken = $this->makeToken($secret, $payload, $header['exp']);

        if ($bearer != $checkToken) {
            $status = JWT::CHECK_STATUS_UNMATCHED;
        } elseif ($header['exp'] < (new \DateTime())->getTimestamp()) {
            $status = JWT::CHECK_STATUS_EXPIRED;
        } else {
            // TODO フロント側のtokenも都度保存し直す必要アリ
            // 生存時間更新
            $checkToken = $this->makeToken($secret, $payload);
        }

        return [
            'token' => $checkToken,
            'header' => $header,
            'payload' => $payload,
            'status' => $status,
            'trace' => $trace,
        ];
    }

    /**
     * expire time取得
     *
     * @param $bearer
     * @return bool
     */
    public function getExpTime($bearer)
    {
        $token = str_replace('Bearer ', '', $bearer);
        $arr = explode('.', $token);
        if (count($arr) != 3) {
            return false;
        }
        $header = json_decode($this->base64UrlDecode($arr[0]), true);
        return $header['exp'];
    }

    /**
     * JWTトークンの更新を行う
     *
     * @param  string $secret
     * @param  string $token
     * @param  array  $payload
     * @return array
     */
    public function refresh($secret, $token, $payload)
    {
        // token確認
        $checkResult = $this->checkToken($secret, $token);
        if (!empty($checkResult) && $checkResult['status'] === JWT::CHECK_STATUS_EXPIRED) {
            // 新しいトークンの作成
            return [
                'token' => $this->makeToken($secret, $payload),
                'status' => JWT::CHECK_STATUS_RENEW_TOKEN_SUCCESS,
            ];
        }
        // 失敗
        return [
            'status' => JWT::CHECK_STATUS_RENEW_TOKEN_FAILED,
        ];
    }

    /**
     * URLエンコード
     *
     * @param  string $data
     * @return string
     */
    public function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * URLデコード
     *
     * @param  string $data
     * @return string
     */
    public function base64UrlDecode($data)
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}
