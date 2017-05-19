<?php 
declare(strict_types=1);
namespace App\Vc\User;


use App\Lib\Icons;
use App\Vc\Lib\HtmlMenuPage;
use App\Models\User;

class ProfilePage extends HtmlMenuPage
{
    private $user;
    
    function __construct(User $p_user)
    {
        $this->user=$p_user;
        parent::__construct();
    }
    
    function setup()
    {
        $this->setCurrentTag("profile");
        $this->title=__("User profile");
        parent::setup();
    }
    function content()
    {
        $this->theme->user_Profile->profileHeader();
        $this->theme->user_Profile->profileRow(__("Nick name"), $this->user->name);
        $this->theme->user_Profile->profileRow(__("First name"), $this->user->firstname);
        $this->theme->user_Profile->profileRow(__("Last name"), $this->user->lastname);
        $this->theme->user_Profile->profileRow(__("Email address"), $this->user->email);
        $this->theme->user_Profile->profileEnd();
        $this->theme->imageTextLink(route("user.editprofile"),Icons::EDIT ,__("Edit profile"));
        $this->theme->imageTextLink(route("user.editpassword"),Icons::EDIT ,__("Edit password"));
        $this->theme->user_Profile->profileFooter();
    }
}