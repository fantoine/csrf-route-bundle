<?php

namespace Fantoine\CsrfRouteBundle\EventListener;

use Fantoine\CsrfRouteBundle\Manager\CsrfTokenManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;

/**
 * Description of ControllerListener
 *
 * @author Fabien Antoine <fabien@fantoine.fr>
 */
class ControllerListener implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
    
    /**
     * @var RouterInterface 
     */
    protected $router;
    
    /**
     * @var CsrfTokenManager
     */
    protected $tokenManager;
    
    /**
     * @param RouterInterface $router
     * @param CsrfTokenManager $tokenManager
     */
    public function __construct(
        RouterInterface $router,
        CsrfTokenManager $tokenManager)
    {
        $this->router       = $router;
        $this->tokenManager = $tokenManager;
    }
    
    /**
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();
        
        // Get route name
        $routeName = $request->attributes->get('_route');
        if (!$routeName) {
            return;
        }
        
        // Get route
        $route = $this->router->getRouteCollection()->get($routeName);
        if (null === $route) {
            return;
        }
        
        // Validate route
        $this->tokenManager->validateRoute(
            $route, $routeName, $request
        );
    }
}
