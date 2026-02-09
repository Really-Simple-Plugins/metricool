<?php

namespace Metricool\Features\Onboarding\Services;

use GuzzleHttp\Exception\GuzzleException;
use Metricool\Http\Metricool\MetricoolApi;
use Metricool\Http\RSPAL\RspalApiClient;

class CreateAccountService
{
    private RspalApiClient $rspalClient;
    private MetricoolApi $metricoolApi;

    public function __construct(RspalApiClient $rspalClient, MetricoolApi $metricoolApi)
    {
        $this->rspalClient = $rspalClient;
        $this->metricoolApi = $metricoolApi;
    }

    /**
     * Creates a new Metricool account and authenticate the MetricoolClient.
     *
     * @param array{
     *     email: string,
     *     password: string,
     *     newsletters: bool,
     *     captcha: string} $data
     * @throws GuzzleException
     */
    public function createAccount(array $data): array
    {
        $signupData = [
            'username' => $data['email'],
            'newsletters' => $data['newsletters'],
        ];

        // todo: implement sign-up endpoint when API is ready
        // $signupResponse = $this->rspalClient->signUp($signupData, [
        //     'RSPAL-RecaptchaV3Token' => $data['captcha']
        // ]);

        // Authenticate the user
        // Todo: Implement API Authentication when oAuth2 is implemented
        // $this->metricoolApi->authenticate(
        //     $signupResponse->data->userId,
        //     $signupResponse->data->accessToken,
        //     $signupResponse->data->refreshToken
        // );

        // Store mock-up
        $this->metricoolApi->authenticate(
            '3864308',
            'RCGXYAHRFQXWRXODYNGCBUMHKTSQRDJQSWWLXDCCBIKHHDEAOLQJAGEDQBPIZINX',
            'test_refresh_token'
        );

        // Update the user password
        // Todo: Implement password change when API Authentication works
        // $this->metricoolApi->userCredentials()
        //    ->updatePassword('', $data['password']);

        $blogs = $this->metricoolApi->brands()->get();

        if (empty($blogs)) {
            throw new \RuntimeException('Something went wrong. No blogs found.');
        }

        if (count($blogs) == 1) {
            $this->metricoolApi->storeBlogId($blogs[0]->id);

            return [
                'success' => true,
                'ask_for_blog_id' => false,
            ];
        } else {
            return [
                'success' => false,
                'ask_for_blog_id' => true,
                'connected_brand' => $blogs,
            ];
        }
    }
}
