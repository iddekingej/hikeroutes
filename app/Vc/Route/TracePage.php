<?php 
declare(strict_types=1);
namespace App\Vc\Route;
use App\Vc\Trace\OpenLayer;
use App\Vc\Lib\TopMenu;

class TracePage extends DisplayPage
{
    function setup():void
    {
        $this->currentCode="trace";
        parent::setup();
    }
    
    function setupTopMenu():void
    {
        $this->topMenu->addMenuitem("routes.trace.edit", ["id"=>$this->route->id],  __("Upload new gpx file"));
    }
    function content():void
    {
        $l_trace=new OpenLayer($this->route->routeTrace);
        $l_trace->display();
        
    }
}