<?php

namespace App\Controller;

use FOS\OAuthServerBundle\Model\ClientManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as FOSRest;

/**
 * Brand controller.
 *
 * @Route("/")
 */
class UserController extends Controller
{
    /** @var ClientManagerInterface */
    private $clientManager;

    public function __construct(ClientManagerInterface $clientManager)
    {
        $this->clientManager = $clientManager;
    }

    /**
     * Create Client.
     * @FOSRest\Get("/createClient")
     *
     * @return Response
     */
    public function AuthenticationAction(Request $input)
    {
                // Create a new client
                $client = $this->clientManager->createClient();

                $client->setRedirectUris([$input->get('redirect-uri')]);
                $client->setAllowedGrantTypes(['token', 'authorization_code', 'password']);

                // Save the client
                $this->clientManager->updateClient($client);

                // Give the credentials back to the user
                $headers = ['Client ID', 'Client Secret'];
                $rows = [
                    [$client->getPublicId(), $client->getSecret()],
                ];

        return $this->redirect($this->generateUrl('fos_oauth_server_authorize', array(
            'client_id'     => $client->getPublicId(),
            'redirect_uri'  => '/api/articles',
            'response_type' => 'code'
        )));
    }

}