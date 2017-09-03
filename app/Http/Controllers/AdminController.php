<?php
declare(strict_types = 1);
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\User;
use App\Models\UserRight;
use App\Models\Right;
use App\Models\RightTableCollection;

/**
 * Controller for site administration
 * Can only be used by users with administrator rights.
 */

class AdminController extends Controller
{
/**
 * Setup authentication middleware.
 * User must be logged in and must have administration rights
 */
    function __construct()
    {
        $this->middleware('auth');
        $this->middleware("admin");        
    }

    /**
     * Displays all users
     *
     * Call view "admin.userlist" which contains a list with all users
     */
    
    function listUsers()
    {
        return view("admin.userlist", [
            "users" => \App\Models\User::orderBy("name")->get()
        ]);
    }

    /**
     * Edit user.
     *
     * Calls "admin.user" view with user id in "$id"
     * This view contains a user edit form.
     *
     * @param int $id
     *            User id to edit.
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory view to display
     */
    
    function editUser($p_id_user)
    {
 
        $this->checkInteger($p_id_user);
        $l_rights = RightTableCollection::getRightsSelectionArray();
        
        $l_user = User::findOrFail($p_id_user);
        $l_userRights = $l_user->userRights;
        foreach ($l_userRights as $l_userRight) {
            $l_rights[$l_userRight->id_right][1] = true;
        }
        
        return view("admin.user", [
            "id" => $l_user->id,
            "name" => $l_user->name,
            "firstname" => $l_user->firstname,
            "lastname" => $l_user->lastname,
            "email" => $l_user->email,
            "enabled" => $l_user->enabled,
            "title" => "Edit user",
            "rights" => $l_rights,
            "user"=>$l_user,
            "cmd" => "edit"
        ]);
    }

    /**
     * Handles adding a new users.
     * This method displays the admin.user view.
     * In this view a form is displayed for entering a new user
     *
     * @return View view to display
     */
    
    function newUser()
    {        
        $l_rights = RightTableCollection::getRightsSelectionArray();
        return view("admin.user", [
            "id" => "",
            "name" => "",
            "firstname" => "",
            "lastname" => "",
            "email" => "",
            "enabled" => "",
            "title" => "New user",
            "cmd" => "add",
            "user"=>null,
            "rights" => $l_rights
        ]);
    }

    /**
     * Deletes an user.
     * A user can't delete their own user record.
     *
     * @param User  $p_user  DThis user is going to be deleted          
     * @return redirect redirects to the user overview
     */
    
    function deleteUser(User $p_user)
    {

        if ($p_user->id != \Auth::user()->id) {
            $p_user->deleteDepended();
        }
        return Redirect::route("admin.users");
    }

    /**
     * Save rights belonging to user.
     * First all existing rights are deleted and 
     * the new rights are inserted again.
     *
     * @param Request $p_request
     *            Post request from form (contains data to save)
     * @param \App\User $p_user
     *            Update the rights of this user
     */
    
    private function saveRights(Request $p_request, User $p_user)
    {
        $p_user->deleteRights();
        foreach (Right::all() as $l_right) {
            if ($p_request->has("right_" . $l_right->id)) {
                UserRight::addUserRight($p_user, $l_right);
            }
        }
    }

    /**
     * After submitting a new user, this method
     * validates the data and inserts the user in the "user" table.
     *
     * @param Request $p_request  Post request from the admin.user form.
     * @return Redirect           Redirect to the users overview
     */
    
    function saveUserAdd(Request $p_request)
    {
        $l_validator = User::validateRequest($p_request, - 1, true);
        
        if ($l_validator->fails()) {
            
            return Redirect::Route("admin.users.new")->withErrors($l_validator)->withInput($p_request->all());
        }
        
        $l_user = User::create([
            "name" => $p_request->input("name"),
            "firstname" => $p_request->input("firstname"),
            "lastname" => $p_request->input("lastname"),
            "email" => $p_request->input("email"),
            "enabled" => $p_request->input("enabled") ? 1 : 0,
            "password" => bcrypt($p_request->input("password"))            
        ]);
        $this->saveRights($p_request, $l_user);
        return Redirect::Route("admin.users");
    }

    /**
     * This method is called after submitting "edit user" form.
     * The user data is saved in the "User" table
     *
     * @param Request $p_request
     *            Data posted from the "edit user" form.
     * @return unknown
     */
    
    function saveUserEdit(Request $p_request)
    {
        $l_id = $p_request->input("id");
        $this->checkInteger($l_id);
        
        $l_validator = User::validateRequest($p_request, (int) $l_id, $p_request->has("resetpassword"));
        if ($l_validator->fails()) {
            return Redirect::Route("admin.users.edit",["p_id_user"=>$l_id])->withErrors($l_validator)->withInput($p_request->all());
        }
        $l_user = User::findOrFail($l_id);
        $l_user->name = $p_request->input("name");
        $l_user->firstname = $p_request->input("firstname");
        $l_user->lastname = $p_request->input("lastname");
        $l_user->email = $p_request->input("email");
        $l_user->enabled = $p_request->input("enabled") ? 1 : 0;
        if ($p_request->has("resetpassword")) {
            $l_user->password = bcrypt($p_request->input("password"));
        }
        $l_user->save();
        $this->saveRights($p_request, $l_user);
        return Redirect::Route("admin.users");
    }
}