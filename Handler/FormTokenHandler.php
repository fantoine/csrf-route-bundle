<?php

namespace Fantoine\CsrfRouteBundle\Handler;

use Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfProviderInterface;

/**
 * Description of FormTokenHandler
 *
 * @author Fabien Antoine <fabien@fantoine.fr>
 */
class FormTokenHandler implements TokenHandlerInterface
{
    /**
     * @var CsrfProviderInterface 
     */
    protected $csrfProvider;
    
    /**
     * @param CsrfProviderInterface $csrfProvider
     */
    public function __construct(CsrfProviderInterface $csrfProvider)
    {
        $this->csrfProvider = $csrfProvider;
    }
    
    /**
     * @param string $intention
     * @return string
     */
    public function getToken($intention)
    {
        return $this->csrfProvider->generateCsrfToken($intention);
    }
    
    /**
     * @param string $intention
     * @param string $token
     * @return boolean
     */
    public function isTokenValid($intention, $token)
    {
        return $this->csrfProvider->isCsrfTokenValid($intention, $token);
    }
}
