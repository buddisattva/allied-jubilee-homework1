<?php

namespace App\Services\User;

use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Facebook\GraphNodes\GraphNode;
use Illuminate\Http\Request;

class FacebookService
{
    private $fb;

    public function __construct(Facebook $fb)
    {
        $this->fb = $fb;
    }

    /**
     * Get Facebook login URL.
     *
     * @return string
     */
    public function getLoginUrl(): string
    {
        $helper = $this->fb->getRedirectLoginHelper();

        return $helper->getLoginUrl(
            route('facebook-login-callback'),
            ['email']
        );
    }

    /**
     * @param Request $request
     * @return string|null
     * @throws FacebookSDKException
     */
    public function getAccessToken(Request $request): ?string
    {
        $helper = $this->fb->getRedirectLoginHelper();
        if ($state = $request->get('state')) {
            $helper->getPersistentDataHandler()->set('state', $state);
        }

        $accessToken = $helper->getAccessToken();

        if (isset($accessToken)) {
            // Logged in!
            return $accessToken;
        }

        return null;
    }

    /**
     * @param string $accessToken
     * @return GraphNode
     * @throws FacebookSDKException
     */
    public function getUser(string $accessToken): GraphNode
    {
        $request = $this->fb->request(
            'GET',
            '/me',
            ['fields' => 'id,name,email'],
            $accessToken
        );


        $response = $this->fb->getClient()->sendRequest($request);

        return $response->getGraphNode();
    }
}