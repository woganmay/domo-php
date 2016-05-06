<?php

namespace WoganMay\DomoPHP;

class DomoWeb {
    
    private $DataSet;
    
    public function __construct($instance, $token)
    {
        // Create internal Guzzle adapter
        
        $this->DataSet = new \WoganMay\DomoPHP\DataSet(this);
    }
    
    public function DataSet()
    {
        return $this->DataSet;
    }
    
    public function get()
    {
        
    }
    
    public function post()
    {
        
    }
    
    public function put()
    {
        
    }
    
}