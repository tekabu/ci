<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Smartyci extends Smarty
{
    public function __construct()
    {
        parent::__construct();
        
        $this->caching = 0;
        
        $this->setTemplateDir( VIEWPATH );
        
        $this->setCompileDir( VIEWPATH.'smarty/templates_c' );
        
        $this->setConfigDir( VIEWPATH.'smarty/configs' );

        $this->addPluginsDir( APPPATH.'libraries/smarty/plugins' );
        
        #$this->setCacheDir( APPPATH . 'cache' );

        $this->setCaching(Smarty::CACHING_OFF);

        $this->clearAllCache(3600);

        $this->clearCompiledTemplate(null, null, 10);
        
        $this->clearCompiledTemplate();
    }
    public function _()
    {
        return new self();
    }
    public function dumpTplVars()
    {
        dj( $this->getTemplateVars() );
    }
}