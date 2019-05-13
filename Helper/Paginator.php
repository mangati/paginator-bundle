<?php

namespace App\Helper;

/**
 * Paginator
 *
 * @author rogerio
 */
class Paginator
{
    /**
     * @var string
     */
    private $html;
    
    /**
     * @var array
     */
    private $result;
    
    /**
     * @var string
     */
    private $routeName;
    
    public function __construct($result, $html, string $routeName)
    {
        $this->result     = $result;
        $this->html       = $html;
        $this->routeName  = $routeName;
    }
    
    public function getHtml(): string
    {
        return $this->html;
    }

    public function getResult()
    {
        return $this->result;
    }
    
    public function getRouteName(): string
    {
        return $this->routeName;
    }
}
