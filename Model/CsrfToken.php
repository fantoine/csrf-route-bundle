<?php

namespace Fantoine\CsrfRouteBundle\Model;

/**
 * Description of CsrfToken
 *
 * @author Fabien Antoine <fabien@fantoine.fr>
 */
class CsrfToken
{
    /**
     * @var string
     */
    protected $token;
    
    /**
     * @var string
     */
    protected $intention;
    
    /**
     * @var array
     */
    protected $methods;
    
    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }
    
    /**
     * @param string $token
     * @return CsrfToken
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getIntention()
    {
        return $this->intention;
    }
    
    /**
     * @param string $intention
     * @return CsrfToken
     */
    public function setIntention($intention)
    {
        $this->intention = $intention;
        return $this;
    }
    
    /**
     * @param boolean $raw
     * @return array
     */
    public function getMethods()
    {
        return (null === $this->methods ?
            null :
            (is_array($this->methods) ?
                $this->methods :
                [ (string) $this->methods ]
            )
        );
    }
    
    /**
     * @param string|array $methods
     * @return CsrfToken
     */
    public function setMethods($methods)
    {
        $this->methods = $methods;
        return $this;
    }
}
