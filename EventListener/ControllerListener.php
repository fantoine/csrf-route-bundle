<?php

namespace Fantoine\CsrfRouteBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Doctrine\Common\Annotations\Reader;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Description of ControllerListener
 *
 * @author Fabien Antoine <fantoine@fox-mind.com>
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
     * @var Reader 
     */
    protected $annotationReader;
    
    /**
     * @var CsrfTokenManagerInterface
     */
    protected $tokenManager;
    
    /**
     * @param Reader $annotationReader
     * @param CsrfTokenManagerInterface $tokenManager
     */
    public function __construct(
        Reader $annotationReader,
        CsrfTokenManagerInterface $tokenManager)
    {
        $this->annotationReader = $annotationReader;
        $this->tokenManager     = $tokenManager;
    }
    
    /**
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        // Symfony2 uses array to specify controllers
        if (!is_array($controller)) {
            return;
        }
        
        // Get parameters
        list($object, $method) = $controller;
        
        // Get reflection objects
        $reflectionClass  = new \ReflectionClass(get_class($object));
        $reflectionMethod = $reflectionClass->getMethod($method);
        
        // Get annotation
        /** @var \Fantoine\CsrfRouteBundle\Annotation\CsrfRoute $annotation */
        $annotation = $this->annotationReader->getMethodAnnotation(
            $reflectionMethod,
            'Fantoine\CsrfRouteBundle\Annotation\CsrfRoute'
        );
        
        // Check annotation
        if (null === $annotation) {
            return;
        }
        
        // Check HTTP method
        if (!in_array($event->getRequest()->getMethod(), $annotation->getMethod())) {
            return;
        }
        
        // Validate token
        $query = $event->getRequest()->query;
        if (!$query->has($annotation->getToken())) {
            $this->accessDenied();
        }
        $token = new CsrfToken(
            $annotation->getIntention(),
            $query->get($annotation->getToken())
        );
        if (!$this->tokenManager->isTokenValid($token)) {
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
