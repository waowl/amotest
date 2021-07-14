<?php


namespace App\Services\AmoCRM;


use AmoCRM\Client\AmoCRMApiClient;
use App\Models\Token;
use Illuminate\Database\Eloquent\Model;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;

class AuthService
{

    /**
     * @var AmoCRMApiClient
     */
    private $apiClient;

    public function __construct()
    {
        $this->apiClient = new AmoCRMApiClient(
            config('amo.id'),
            config('amo.secret'),
            config('amo.redirect'),
        );
    }


    public function authorize(): bool
    {

        if (isset($_GET['referer'])) {
            $this->apiClient->setAccountBaseDomain($_GET['referer']);
        }

        if (!isset($_GET['code'])) {
            $state = bin2hex(random_bytes(16));
            session(['oauth2state' => $state]) ;
            if (isset($_GET['button'])) {

                echo $this->apiClient->getOAuthClient()->getOAuthButton(
                    [
                        'title' => 'Установить интеграцию',
                        'compact' => true,
                        'class_name' => 'className',
                        'color' => 'default',
                        'error_callback' => 'handleOauthError',
                        'state' => $state,
                    ]
                );
                die;
            } else {
                $authorizationUrl = $this->apiClient->getOAuthClient()->getAuthorizeUrl([
                    'state' => $state,
                    'mode' => 'post_message',
                ]);
                header('Location: ' . $authorizationUrl);
                die;
            }
        }

        /**
         * Ловим обратный код
         */
        try {
            $accessToken = $this->apiClient->getOAuthClient()->getAccessTokenByCode($_GET['code']);

            if (! $accessToken->hasExpired()) {
                $token = new Token();
                $token->access_token = $accessToken->getToken();
                $token->refresh_token = $accessToken->getRefreshToken();
                $token->expires = $accessToken->getExpires();
                $token->base_domain = $this->apiClient->getAccountBaseDomain();
                $token->save();
            }
        } catch (Exception $e) {
            die((string)$e);
        }

        return true;
    }

    public function initApiClient(): AmoCRMApiClient
    {
        $token = $this->getSavedToken();

        return $this->setTokenToApiClient($this->apiClient, $token);

    }

    private function getSavedToken(): AccessToken
    {
        $token = Token::first();

        return new AccessToken([
            'access_token' =>  $token->access_token,
            'refresh_token' =>  $token->refresh_token,
            'expires' => $token->expires,
            'baseDomain' => $token->base_domain,
        ]);
    }


    private function setTokenToApiClient(AmoCRMApiClient $apiClient, AccessToken $accessToken):  AmoCRMApiClient
    {
        $apiClient->setAccessToken($accessToken)
            ->setAccountBaseDomain($accessToken->getValues()['baseDomain'])
            ->onAccessTokenRefresh(
                function (AccessTokenInterface $accessToken, string $baseDomain){
                    $token = Token::first();
                    $token->access_token = $accessToken->getToken();
                    $token->refresh_token = $accessToken->getRefreshToken();
                    $token->expires = $accessToken->getExpires();
                    $token->base_domain = $baseDomain;
                    $token->save();
                }
            );

        return  $apiClient;
    }
}
