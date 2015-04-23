<?php

namespace Fantoine\CsrfRouteBundle\Manager;

use Fantoine\CsrfRouteBundle\Handler\TokenHandlerInterface;
use Fantoine\CsrfRouteBundle\Model\CsrfToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Description of CsrfTokenManager
 *
 * @author Fabien Antoine <fabien@fantoine.fr>
 */
class CsrfTokenManager
{
    /**
     * CsrfToken route option name
     */
    const OPTION_NAME = 'csrf_token';
    
    /**
     * @var TokenHandlerInterface
     */
    protected $tokenHandler;
    
    /**
     * @param TokenHandlerInterface $tokenHandler
     */
    public function __construct(TokenHandlerInterface $tokenHandler)
    {
        $this->tokenHandler = $tokenHandler;
    }
    
    /**
     * @return CsrfToken
     */
    public function getDefaultToken()
    {
        return (new CsrfToken())
            ->setToken('_token')
            ->setIntention(null)
            ->setMethods('GET')
        ;
    }
    
    /**
     * @param mixed $option
     * @return CsrfToken
     */
    public function getTokenFromOption($option)
    {
        if (true === $option) {
            return $this->getDefaultToken();
        }
        
        if (!is_array($option)) {
            return null;
        }
        
        return (new CsrfToken())
            ->setToken(array_key_exists('token', $option) ? $option['token'] : '_token')
            ->setIntention(array_key_exists('intention', $option) ? $option['intention'] : null)
            ->setMethods(array_key_exists('methods', $option) ? $option['methods'] : 'GET')
        ;
    }
    
    /**
     * @param Route $route
     */
    public function getTokenFromRoute(Route $route)
    {
        // Check if route has the option
        if (!$route->hasOption(self::OPTION_NAME)) {
            return null;
        }
        
        // Get option
        $option = $route->getOption(self::OPTION_NAME);
        if (!$option) {
            return null;
        }
        
        // Get token
        return $this->getTokenFromOption($option);
    }
    
    /**
     * @param Route $route
     * @param string $name
     * @param array $parameters
     */
    public function updateRoute(Route $route, $name, array &$parameters)
    {
        // Get token
        $token = $this->getTokenFromRoute($route);
        if (null === $token) {
            return;
        }
        
        // Add token
        $parameters[$token->getToken()] = $this->tokenHandler
            ->getToken($token->getIntention() ?: $name)
        ;
    }
    
    /**
     * @param Route $route
     * @param string $routeName
     * @param Request $request
     */
    public function validateRoute(Route $route, $routeName, Request $request)
    {
        // Find token
        $token = $this->getTokenFromRoute($route);
        if (null === $token) {
            return;
        }
        
        // Check HTTP method
        if (!in_array($request->getMethod(), $token->getMethods())) {
            return;
        }
        
        // Validate token
        $query = $request->query;
        if (!$query->has($token->getToken())) {
            $this->accessDenied();
        }
        $valid = $this->tokenHandler->isTokenValid(
            $token->getIntention() ?: $routeName,
            $query->get($token->getToken())
        );
        if (!$valid) {
            $this->accessDenied();
        }
    }
    
    /**
     * @throws AccessDeniedHttpException
     */
    protected function accessDenied()
    {
        throw new AccessDeniedHttpException('Invalid CSRF token');
    }
}
