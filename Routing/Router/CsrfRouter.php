<?php

namespace Fantoine\CsrfRouteBundle\Routing\Router;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * Description of CsrfRouter
 *
 * @author Fabien Antoine <fabien@fantoine.fr>
 */
class CsrfRouter extends Router
{
    /**
     * @var Router
     */
    protected $parent;
    
    /**
     * @var CsrfTokenManagerInterface 
     */
    protected $tokenManager;
    
    /**
     * @param Router $router
     */
    public function setParentRouter(Router $router)
    {
        $this->parent = $router;
    }
    
    /**
     * @param CsrfTokenManagerInterface $tokenManager
     */
    public function setTokenManager(CsrfTokenManagerInterface $tokenManager)
    {
        $this->tokenManager = $tokenManager;
    }
    
    /**
     * @param RequestContext $context
     */
    public function setContext(RequestContext $context)
    {
        if (null !== $this->parent) {
            $this->parent->setContext($context);
        }
        
        parent::setContext($context);
    }
    
    /**
     * @return \Symfony\Component\Routing\RouteCollection
     */
    public function getRouteCollection()
    {
        if (null !== $this->parent) {
            return $this->parent->getRouteCollection();
        }
        
        return parent::getRouteCollection();
    }
    
    /**
     * @param string $pathinfo
     * @return boolean
     */
    public function match($pathinfo)
    {
        if (null !== $this->parent) {
            return $this->parent->match($pathinfo);
        }
        
        return parent::match($pathinfo);
    }
    
    /**
     * @param string $name
     * @param array $parameters
     * @param string $referenceType
     */
    public function generate($name, $parameters = [], $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        // Add Csrf token if required
        $route = $this->getRouteCollection()->get($name);
        if (null !== $route && $route->hasOption('fantoine_csrf_route')) {
            $option = $route->getOption('fantoine_csrf_route');
            
            // Create token
            $parameters[$option['token']] = $this->tokenManager
                ->getToken($option['intention'] ?: $name)
                ->getValue()
            ;
        }
        
        if (null !== $this->parent) {
            return $this->parent->generate(
                $name, $parameters, $referenceType
            );
        }
        
        return parent::generate(
            $name, $parameters, $referenceType
        );
    }
    
    /**
     * @param Request $request
     * @return array
     */
    public function matchRequest(Request $request)
    {
        if (null !== $this->parent) {
            return $this->parent->matchRequest($request);
        }
        
        return parent::matchRequest($request);
    }
}
