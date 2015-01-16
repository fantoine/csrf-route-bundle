<?php

namespace Fantoine\CsrfRouteBundle\Annotation;

/**
 * Description of CsrfRoute
 *
 * @author Fabien Antoine <fantoine@fox-mind.com>
 * 
 * @Annotation
 * @Target("METHOD")
 * @Attributes({
 *   @Attribute("token", type="string"),
 *   @Attribute("intention", type="string"),
 * })
 */
class CsrfRoute
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
    protected $method;
    
    /**
     * @param array $values
     */
    public function __construct(array $values)
    {
        $options = array_merge([
            'token'     => '_token',
            'intention' => '',
            'method'    => 'GET',
        ], $values);
        
        $this->token     = $options['token'];
        $this->intention = $options['intention'];
        $this->method    = $options['method'];
    }
    
    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function getIntention()
    {
        return $this->intention;
    }
    
    /**
     * @return array
     */
    public function getMethod()
    {
        return (is_array($this->method) ? $this->method : [ (string) $this->method ]);
    }
}
