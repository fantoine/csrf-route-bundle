<?php

namespace Fantoine\CsrfRouteBundle\Handler;

/**
 * Description of TokenHandlerInterface
 *
 * @author Fabien Antoine <fabien@fantoine.fr>
 */
interface TokenHandlerInterface
{
    /**
     * @param string $intention
     * @return string
     */
    public function getToken($intention);
    
    /**
     * @param string $intention
     * @param string $token
     * @return boolean
     */
    public function isTokenValid($intention, $token);
}
