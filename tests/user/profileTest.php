<?php 

use App\Vc\User\ProfilePage;
use Tests\TestCase;
use App\Vc\User\PasswordPage;
use Illuminate\Support\MessageBag;
use App\Vc\User\EditPage;
use Illuminate\Support\ViewErrorBag;
use App\Vc\User\AllUserPage;

class profileTest extends TestCase
{
    
    function testProfilePage()
    {
        $l_page=new ProfilePage($this->getAdminUser());
        $l_page->display();
        $this->assertEquals(1,1);
    }
    
    function testPasswordPage()
    {
        $l_page=new PasswordPage(new MessageBag());
        $l_page->display();
        $this->assertEquals(1,1);
    }
    
    function testEditPage()
    {
        $l_page=new EditPage(\Auth::user(), new ViewErrorBag());
        $l_page->display();
        $this->assertEquals(1,1);
    }
    
    function testAllUserPage()
    {
        $l_page=new AllUserPage();
        $l_page->display();
        $this->assertEquals(1,1);
    }
}