<?php

namespace WoganMay\DomoPHP;

class Client {
    
    private $DataSet;
    private $DomoWeb;
    
    public function __construct($instance, $token)
    {
        // New DomoWeb object for underlying API calls
        $this->DomoWeb = new \WoganMay\DomoPHP\DomoWeb($instance, $token);
        
        // Function libraries
        $this->DataSet = new \WoganMay\DomoPHP\DataSet($this->DomoWeb);
    }
    
    public function DataSet()
    {
        return $this->DataSet;
    }
    
}