<?php 
declare(strict_types=1);
namespace App\Vc\Lib;



class Theme 
{
    function __get($p_name)
    {
        $l_name=str_replace("_", "\\", $p_name);
        $l_className="\\App\\Vc\\Theme\\".$l_name;
        $this->$p_name=new $l_className($this);
        return $this->$p_name;
    }
    
    function attribute($p_name,$p_value)
    {
        echo $p_name,'="',$this->e($p_value).'" ';
    }
    
    function image($p_src,$p_class="")
    {
        echo "<img ";
        $this->attribute("src",$p_src);
        if($p_class){
            $this->attribute("class",$p_class);
        }
        echo ">";        
    }
    
    function yesNoLink($p_label,$p_route,Array $p_params,$p_value){
        ?><?=$this->e($p_label)?>:<?=$p_value?$this->e(__("Yes")):$this->e(__("No"))?> - <a href='<?=Route($p_route,array_merge($p_params,["p_flag"=>$p_value?0:1]))?>'><?=$p_value?__("Turn off"):__("Turn on")?></a> <?php
    }
    
    /**
     * After clicking a icon, a confirmation message is displayed
     * After pressing "yes"
     *
     * @param unknown $p_message
     *            Confirmation message to display
     * @param unknown $p_url
     *            Url to go after click + confirmation
     * @param unknown $p_image
     *            Url of icon/image
     */
    function iconConfirm(string $p_message, string $p_url, string $p_image): void
    {
        $l_js = $this->confirmJs($p_message, $p_url);
        ?><span class="deleteIcon" onclick="<?=$this->e($l_js)?>"><img
	src='<?=$this->e($p_image)?>'></span><?php
    }
    
    function textRouteLink($p_route,Array $p_params,$p_text,$p_class="")
    {
        $this->textLink(Route($p_route,$p_params),$p_text,$p_class);
    }
    
    function textLink($p_url,$p_text,$p_class="")
    {
        ?><a <?php if($p_class != ""){ $this->attribute("class",$p_class);}?> href="<?=$this->e($p_url)?>"><?=$this->e($p_text)?></a><?php
    }
    
    function imageTextLink($p_url,$p_image,$p_text)
    {
        ?><a href="<?=$this->e($p_url)?>"><img src="<?=$this->e($p_image)?>" /><?=$this->e($p_text)?></a><?php   
    }
    
    function iconLink(string $p_route,Array $p_data,string $p_icon):void
    {
       ?><a href="<?=$this->e(route($p_route,$p_data))?>"><?php $this->image($p_icon)?></a><?php 
    }

    function iconTextRouteLink($p_route,array $p_params,$p_image,$p_text)
    {
        ?><a href="<?=$this->e(Route($p_route,$p_params))?>"><img src="<?=$this->e($p_image)?>" /></a><a href="<?=$this->e(Route($p_route,$p_params))?>"><?=$this->e($p_text)?></a><?php
    }
    
    
    /**
     * HTML Escape string
     *
     * @param String $p_string
     * @return string
     */
    function e($p_string): string
    {
        if ($p_string === null) {
            return "";
        }
        return htmlspecialchars("$p_string", ENT_QUOTES | ENT_HTML5);
    }
    
    /**
     * Make javascript for confirm message
     *
     * @param
     *            String Message in confirmation box
     * @param
     *            String Url url location to go when confirmed
     */
    function confirmJs(String $p_message,String $p_url)
    {
        return "if(confirm(" . json_encode($p_message) . "))window.location=" . json_encode($p_url);
    }
    
    /**
     * Create tag object
     *
     * @param string $p_tag
     * @return Tag
     */
    function tag(string $p_tag):Tag
    {
        return new Tag($p_tag);
    }
    
    function makeJsCall(String $p_function ,Array $p_params):string
    {
        $l_call="";
        foreach($p_params as $l_param){
            $l_call .= ($l_call?",":"").json_encode($l_param);   
        }
        return $p_function."(".$l_call.")";
    }
    
    function div():Tag
    {
        return $this->tag("div");
    }      
}