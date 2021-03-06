<?php
namespace Tainacan\Filter_Types;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Class TainacanMetadatumType
 */
class TaxonomyCheckbox extends Filter_Type {

    function __construct(){
        $this->set_supported_types(['term']);
        $this->set_component('tainacan-filter-taxonomy-checkbox');
        $this->set_preview_template('
            <div>
                <div>
                    <p class="has-text-gray">'. __('Selected values') . ': </p> 
                    <div class="field selected-tags is-grouped-multiline is-grouped">
                        <div>
                            <div class="tags has-addons">
                                <span class="tag"><span>'. __('Value') . ' 21</span></span> 
                                <a class="tag is-delete"></a>
                            </div>
                        </div>
                        <div>
                            <div class="tags has-addons">
                                <span class="tag"><span>'. __('Value') . ' 7</span></span> 
                                <a class="tag is-delete"></a>
                            </div>
                        </div>
                    </div> 
                    <div>
                        <label class="b-checkbox checkbox" border="" style="padding-left: 8px;">
                            <input type="checkbox" value="option1">
                            <span class="check"></span>
                            <span class="control-label">'. __('Value') . ' 1</span>
                        </label> 
                        <br>
                    </div>
                    <div>
                        <label class="b-checkbox checkbox" border="" style="padding-left: 8px;">
                            <input type="checkbox" checked value="option2">
                            <span class="check"></span> 
                            <span class="control-label">'. __('Value') . ' 2</span>
                        </label> 
                    </div>
                    <div>
                        <label class="b-checkbox checkbox" border="" style="padding-left: 8px;">
                            <input type="checkbox" checked value="option3">
                            <span class="check"></span> 
                            <span class="control-label">'. __('Value') . ' 3</span>
                        </label> 
                    </div>
                </div> 
                <a class="add-new-term">'. __('View all') . '</a>
            </div>
        ');
    }

    /**
     * @param $filter
     * @return string
     */

    public function render( $filter ){
        return '<tainacan-filter-taxonomy-checkbox name="'.$filter->get_name().'"
                                        filter_type="'.$filter->get_metadatum()->get_metadata_type().'"
                                        collection_id="'.$filter->get_collection_id().'"
                                        metadatum_id="'.$filter->get_metadatum()->get_id().'"></tainacan-filter-taxonomy-checkbox>';
    }
}