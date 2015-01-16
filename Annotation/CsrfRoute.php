<?php

namespace Fantoine\CsrfRouteBundle\Annotation;

/**
 * Description of CsrfRoute
 *
 * @author Fabien Antoine <fabien@fantoine.fr>
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
    protected $methods;
    
    /**
     * @param array $values
     */
    public function __construct(array $values)
    {
        $options = array_merge([
            'token'     => '_token',
            'intention' => '',
            'methods'   => 'GET',
        ], $values);
        
        $this->token     = $options['token'];
        $this->intention = $options['intention'];
        $this->methods   = $options['methods'];
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
    public function getMethods()
    {
        return (is_array($this->methods) ? $this->methods : [ (string) $this->methods ]);
    }
    
    /**
     * @return array
     */
    public function getOptions()
    {
        return [
            'token'     => $this->getToken(),
            'intention' => $this->getIntention(),
            'methods'   => $this->getMethods(),
        ];
    }
}
