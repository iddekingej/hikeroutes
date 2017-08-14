<?php 
declare(strict_types=1);
namespace App\Vc\Lib\Widgets\Form;

use App\Vc\Lib\Engine\Data\DynamicValue;
use App\Vc\Lib\Engine\Data\DataStore;

/**
 * Class used for a form section title
 *
 */
class FormSection extends Widget
{
    /**
     * Section title
     * 
     * @var string 
     */
    private $title;
    
    /**
     * Set the section title. The text is html escaped before
     * it is displayed
     * 
     * @param string $p_title Section title
     */
    function setTitle(DynamicValue $p_title):void
    {
        $this->title=$p_title;
    }
    
    /**
     * Get the section title.
     * 
     * @return string
     */
    
    function getTitle():string
    {
        return $this->title;
    }
    
    /**
     * Display the section title
     * @param DataStore $p_store   Data store used for displaying elements
     */
    
    function displayContent(?DataStore $p_store=null)
    {
        $this->theme->base_Form->sectionTitle($this->title->getValue($p_store));        
    }
}