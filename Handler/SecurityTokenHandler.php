<?php

namespace Fantoine\CsrfRouteBundle\Handler;

use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * Description of SecurityTokenHandler
 *
 * @author Fabien Antoine <fabien@fantoine.fr>
 */
class SecurityTokenHandler implements TokenHandlerInterface
{
    /**
     * @var CsrfTokenManagerInterface 
     */
    protected $tokenManager;
    
    /**
     * @param CsrfTokenManagerInterface $tokenManager
     */
    public function __construct(CsrfTokenManagerInterface $tokenManager)
    {
        $this->tokenManager = $tokenManager;
    }
    
    /**
     * @param string $intention
     * @return string
     */
    public function getToken($intention)
    {
        return $this->tokenManager
            ->getToken($intention)
            ->getValue()
        ;
    }
    
    /**
     * @param string $intention
     * @param string $token
     * @return boolean
     */
    public function isTokenValid($intention, $token)
    {
        $securityToken = new CsrfToken($intention, $token);
        return $this->tokenManager->isTokenValid($securityToken);
    }
}
