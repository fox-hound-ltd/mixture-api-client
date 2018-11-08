<?php

namespace MixtureApiClient;

use PHPUnit\Framework\TestCase;

/**
 * Class JWT
 *
 * @package MixtureApiClient
 */
class JWTTest extends TestCase
{
    const CHECK_STATUS_OK = 1;
    const CHECK_STATUS_UNMATCHED = 2;
    const CHECK_STATUS_EXPIRED = 3;

    const CHECK_STATUS_RENEW_TOKEN_SUCCESS = 4;
    const CHECK_STATUS_RENEW_TOKEN_FAILED = 5;

    private $jwt;

    public function setUp()
    {
        $this->jwt = new JWT();
    }

    /**
     * Bearer不正1
     *
     * @dataProvider dataTokenInjustice
     * @param $token
     */
    public function testBearer不整合CheckToken($token)
    {
        $check_token = $this->jwt->checkToken('secret', $token);

        $this->assertFalse($check_token);
    }

    /**
     * Bearer不正2
     *
     * @dataProvider dataTokenInjustice
     * @param $token
     */
    public function testBearer不整合CheckExp($token)
    {
        $check_token = $this->jwt->getExpTime($token);
        $this->assertFalse($check_token);
    }

    public function dataTokenInjustice()
    {
        return [
            '複合できる' => [
                'Bearer eyJzZWNyZXRfa2V5IjoiNzIwOTFiZWVjMWU5NThhNmM1ZGQzMTMzNjI0MjYwN2MwODg3NzU0YjFkNDNlYmYwNzFmYjI4OTA1MzNkNjExYSJ9.sC0PVPfBiXwDZ96VuM7Hti4DTIsf475nVsEUeWFn6lE',
            ],
        ];
    }

    /**
     *
     * @dataProvider dataMakeToken
     * @param $token
     */
    public function testMakeToken($token)
    {
        $secret_word = 'secret';
        $payload['secret_key'] = '72091beec1e958a6c5dd31336242607c0887754b1d43ebf071fb2890533d611a';

        $make_token = $this->jwt->makeToken($secret_word, $payload);

        $check_token = $this->jwt->checkToken($secret_word, $make_token);

        $this->assertEquals($check_token['payload'], $this->jwt->checkToken($secret_word, $token)['payload']);
    }

    public function dataMakeToken()
    {
        return [
            '複合できる' => [
                'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCIsImV4cCI6MTU0MTQwMjI3MH0.eyJzZWNyZXRfa2V5IjoiNzIwOTFiZWVjMWU5NThhNmM1ZGQzMTMzNjI0MjYwN2MwODg3NzU0YjFkNDNlYmYwNzFmYjI4OTA1MzNkNjExYSJ9.sC0PVPfBiXwDZ96VuM7Hti4DTIsf475nVsEUeWFn6lE',
            ],
        ];
    }

    /**
     * @param $encryption_secret_word 暗号化key
     * @param $uncryption_secret_word 複合化key
     * @param $secret_key 一部内容
     * @param $status ステータス
     * @dataProvider dataCheckToken
     */
    public function testCheckToken($encryption_secret_word, $uncryption_secret_word, $secret_key, $status)
    {
        $make_token = $this->jwt->makeToken($encryption_secret_word, $secret_key);
        $check_token = $this->jwt->checkToken($uncryption_secret_word, $make_token);
        $this->assertEquals($check_token['status'], $status);
    }

    public function dataCheckToken()
    {
        return [
            'マッチ' => [
                'secret',
                'secret',
                '72091beec1e958a6c5dd31336242607c0887754b1d43ebf071fb2890533d611a',
                JWT::CHECK_STATUS_OK,
            ],
            'アンマッチ' => [
                'secret',
                'secret1',
                '72091beec1e958a6c5dd31336242607c0887754b1d43ebf071fb2890533d611a',
                JWT::CHECK_STATUS_UNMATCHED,
            ],
        ];
    }

    /**
     * @param $secret_word
     * @param $is_exp_time
     * @param $exp_time
     * @dataProvider dataGetExpTime
     */
    public function testGetExpTime($secret_word, $is_exp_time, $exp_time)
    {
        $token = $this->jwt->makeToken($secret_word, [], $exp_time);
        $exp = $this->jwt->getExpTime($token);
        $this->assertEquals($exp, $exp_time);
    }

    public function dataGetExpTime()
    {
        return [
            'マッチ' => [
                'secret',
                true,
                (new \DateTime(date('Y-m-d H:i:s', strtotime('+1 hour'))))->getTimestamp(),
            ],
        ];
    }

    /**
     * @param $secret_word
     * @param $exp_time
     * @param $status
     * @dataProvider dataRefresh
     */
    public function testRefresh($secret_word, $exp_time, $status)
    {
        // token確認
        $token = $this->jwt->makeToken($secret_word, [], $exp_time);
        $checkToken = $this->jwt->checkToken($secret_word, $token);
        $checkResult = $this->jwt->refresh($secret_word, $token, $checkToken['payload']);
        $this->assertEquals($checkResult['status'], $status);
    }

    public function dataRefresh()
    {
        return [
            'リフレッシュ成功' => [
                'secret',
                (new \DateTime(date('Y-m-d H:i:s', strtotime('-1 hour'))))->getTimestamp(),
                JWT::CHECK_STATUS_RENEW_TOKEN_SUCCESS,
            ],
            'リフレッシュ失敗' => [
                'secret',
                (new \DateTime(date('Y-m-d H:i:s', strtotime('+1 hour'))))->getTimestamp(),
                JWT::CHECK_STATUS_RENEW_TOKEN_FAILED,
            ],
        ];
    }
}
