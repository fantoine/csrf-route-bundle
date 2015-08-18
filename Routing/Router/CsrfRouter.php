<?php

namespace Fantoine\CsrfRouteBundle\Routing\Router;

use Fantoine\CsrfRouteBundle\Manager\CsrfTokenManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;

/**
 * Description of CsrfRouter
 *
 * @author Fabien Antoine <fabien@fantoine.fr>
 */
class CsrfRouter extends Router
{
    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @var Router
     */
    protected $parent;
    
    /**
     * @var CsrfTokenManager
     */
    protected $tokenManager;

    /**
     * @param bool $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }
    
    /**
     * @param Router $router
     */
    public function setParentRouter(Router $router)
    {
        $this->parent = $router;
    }
    
    /**
     * @param CsrfTokenManager $tokenManager
     */
    public function setTokenManager(CsrfTokenManager $tokenManager)
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
     * @param bool|string $referenceType
     * @return string
     */
    public function generate($name, $parameters = [], $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        // Add Csrf token if required
        if ($this->enabled) {
            $route = $this->getRouteCollection()->get($name);
            if (null !== $route) {
                // Apply token if required
                $this->tokenManager->updateRoute(
                    $route, $name, $parameters
                );
            }
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
