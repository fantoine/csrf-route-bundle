<?php

namespace Fantoine\CsrfRouteBundle\Routing\Loader;

use Sensio\Bundle\FrameworkExtraBundle\Routing\AnnotatedRouteControllerLoader;
use Symfony\Component\Routing\Route;

/**
 * Description of CsrfLoader
 *
 * @author Fabien Antoine <fabien@fantoine.fr>
 */
class CsrfLoader extends AnnotatedRouteControllerLoader
{
    /**
     * Configures the CSRF token options
     *
     * @param Route             $route  A route instance
     * @param \ReflectionClass  $class  A ReflectionClass instance
     * @param \ReflectionMethod $method A ReflectionClass method
     * @param mixed             $annot  The annotation class instance
     *
     * @throws \LogicException When the service option is specified on a method
     */
    protected function configureRoute(Route $route, \ReflectionClass $class, \ReflectionMethod $method, $annot)
    {
        parent::configureRoute($route, $class, $method, $annot);
        
        /** @var \Fantoine\CsrfRouteBundle\Annotation\CsrfRoute */
        $annotation = $this->reader->getMethodAnnotation($method, '\Fantoine\CsrfRouteBundle\Annotation\CsrfRoute');
        if (null !== $annotation) {
            // Store the CsrfRoute options on Route options
            $route->setOption('fantoine_csrf_route', $annotation->getOptions());
        }
    }
}