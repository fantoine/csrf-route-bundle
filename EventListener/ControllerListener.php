<?php

namespace Fantoine\CsrfRouteBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Persistence\Proxy;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

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
        $request    = $event->getRequest();

        // Symfony2 uses array to specify controllers
        if (!is_array($controller)) {
            return;
        }
        
        // Get parameters
        list($object, $method) = $controller;
        
        // If the object is a proxy, load it
        if ($object instanceof Proxy) {
            $object->__load();
        }
        
        // Get reflection objects
        $reflectionClass  = new \ReflectionClass($this->getClassName($object));
        $reflectionMethod = $reflectionClass->getMethod($method);
        
        // Get annotation
        /** @var \Fantoine\CsrfRouteBundle\Annotation\CsrfRoute $annotation */
        $annotation = $this->annotationReader->getMethodAnnotation(
            $reflectionMethod,
            '\\Fantoine\\CsrfRouteBundle\\Annotation\\CsrfRoute'
        );
        
        // Check annotation
        if (null === $annotation) {
            return;
        }
        
        // Check HTTP method
        if (!in_array($request->getMethod(), $annotation->getMethods())) {
            return;
        }
        
        // Validate token
        $query = $event->getRequest()->query;
        if (!$query->has($annotation->getToken())) {
            $this->accessDenied();
        }
        $token = new CsrfToken(
            $annotation->getIntention() ?: $request->attributes->get('_route'),
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
    
    /**
     * @param mixed $object
     * @return string
     */
    protected function getClassName($object)
    {
        if (class_exists('\Doctrine\Common\Util\ClassUtils')) {
            return \Doctrine\Common\Util\ClassUtils::getClass($object);
        }
        
        return get_class($object);
    }
}
