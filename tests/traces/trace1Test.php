<?php 


use Tests\TestCase;
use App\Models\RouteTrace;
use App\Vc\Trace\ListPage;
use App\Models\RouteTraceTableCollection;
use App\Vc\Trace\UploadPage;
use Illuminate\Database\Eloquent\Collection;


class trace1Test extends TestCase
{
        
    private $trace1;
    private $trace2;
    
    function setup()
    {
        parent::setup();        
        $l_content = $this->getResource(self::TRACE1);
        $this->trace1 = RouteTraceTableCollection::addGpxFile($l_content);
        $l_content = $this->getResource(self::TRACE2);
        $this->trace2 = RouteTraceTableCollection::addGpxFile($l_content);        
    }
    
    function testNewTrace()
    {
        $this->assertInstanceOf(RouteTrace::class, $this->trace1,"trace1");
        $this->assertInstanceOf(RouteTrace::class, $this->trace2,"trace2");
    }
    
    function testUserTrace()
    {
        $this->assertEquals($this->getAdminUser()->id,$this->trace1->user->id,"trace1");
        $this->assertEquals($this->getAdminUser()->id,$this->trace2->user->id,"trace2");
    }
    
    function testGetByUser()
    {
        $l_collection=RouteTraceTableCollection::getByUser(\Auth::user());
        $l_found=0;
        foreach($l_collection as $l_trace)
        {
            if($l_trace->id==$this->trace1->id){
                $l_found++;
            }
            if($l_trace->id==$this->trace2->id){
                $l_found++;
            }
        }
        $this->assertEquals(2,$l_found);
    }
    
    function testListPage()
    {
        $l_page=new ListPage(RouteTraceTableCollection::getByUser(\Auth::user()));
        $l_page->display();
        
        $this->outputContainsRoute(
            [
                ["traces.show",["id"=>$this->trace2->id]]
               ,["traces.show",["id"=>$this->trace2->id]]
            ]
        );
    }
    
    function testUploadPage()
    {
        $l_page=new UploadPage(new Collection());
        $l_page->display();
        $this->assertEquals(1,1);
    }
}