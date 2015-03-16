<?php

namespace Fantoine\CsrfRouteBundle\EventListener;

use Fantoine\CsrfRouteBundle\Manager\CsrfTokenManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;
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
     * @var string
     */
    protected $cacheDirectory;
    
    /**
     * @var Filesystem
     */
    protected $filesystem;
    
    /**
     * @param RouterInterface $router
     * @param CsrfTokenManager $tokenManager
     * @param Filesystem $filesystem
     * @param string $cacheDirectory
     */
    public function __construct(
        RouterInterface $router,
        CsrfTokenManager $tokenManager,
        Filesystem $filesystem,
        $cacheDirectory)
    {
        $this->router         = $router;
        $this->tokenManager   = $tokenManager;
        $this->filesystem     = $filesystem;
        $this->cacheDirectory = $cacheDirectory;
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
        $route = $this->getRoute($routeName);
        if (null === $route) {
            return;
        }
        
        // Validate route
        $this->tokenManager->validateRoute(
            $route, $routeName, $request
        );
    }
    
    /**
     * @param string $routeName
     * @return \Symfony\Component\Routing\Route|null
     */
    protected function getRoute($routeName)
    {
        // Create cache directory
        $this->filesystem->mkdir($this->cacheDirectory);
        
        $route = null;
        
        try {
            $file = sprintf('%s/%s.php', $this->cacheDirectory, md5($routeName));
            if (!$this->filesystem->exists($file)) {
                // Get route
                $route = $this->router->getRouteCollection()->get($routeName);

                // Serialize it
                file_put_contents($file, serialize($route));
            } else {
                $route = unserialize(file_get_contents($file));
            }
        } catch (\Exception $e) {}
        
        return $route;
    }
}
