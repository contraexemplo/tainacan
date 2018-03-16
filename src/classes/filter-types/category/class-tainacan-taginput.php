<?php
namespace Tainacan\Filter_Types\Category;
use Tainacan\Filter_Types;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Class Taginput
 */
class Taginput extends Filter_Types\Filter_Type {

    function __construct(){
        parent::set_supported_types(['term']);
        $this->component = 'tainacan-filter-category-taginput';
    }

    /**
     * @param $filter
     * @return string
     */

    public function render( $filter ){
        return '<tainacan-filter-category-taginput name="'.$filter->get_name().'"
                                        filter_type="'.$filter->get_field()->get_field_type().'"
                                        collection_id="'.$filter->get_collection_id().'"
                                        field_id="'.$filter->get_field()->get_id().'"></tainacan-filter-taginput>';
    }
}