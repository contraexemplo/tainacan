<template>
    <div class="table-container">
        <div class="table-wrapper">

            <!-- Empty result placeholder -->
            <section
                    v-if="!isLoading && items.length <= 0"
                    class="section">
                <div class="content has-text-gray4 has-text-centered">
                    <p>
                        <span class="icon is-large">
                            <i class="tainacan-icon tainacan-icon-36px tainacan-icon-items" />
                        </span>
                    </p>
                    <p>{{ $i18n.get('info_no_item_found') }}</p>
                </div>
            </section>

            <!-- SKELETON LOADING -->
            <masonry
                    v-if="isLoading"
                    :cols="{default: 4, 1919: 3, 1407: 2, 1215: 2, 1023: 1, 767: 1, 343: 1}"
                    :gutter="30"                    
                    class="tainacan-records-container">
                <div 
                        :key="item"
                        v-for="item in 12"
                        :style="{'min-height': randomHeightForRecordsItem() + 'px' }"
                        class="skeleton tainacan-record" />
            </masonry>
            
            <!-- RECORDS VIEW MODE -->
            <masonry 
                    role="list"
                    v-if="!isLoading && items.length > 0"
                    :cols="{default: 4, 1919: 3, 1407: 2, 1215: 2, 1023: 1, 767: 1, 343: 1}"
                    :gutter="30"
                    class="tainacan-records-container">
                <a 
                        role="listitem"
                        :href="item.url"
                        :key="index"
                        v-for="(item, index) of items"
                        class="tainacan-record">
                    <!-- <div :href="item.url"> -->
                        <!-- Title -->           
                        <p 
                                v-tooltip="{
                                    delay: {
                                        show: 500,
                                        hide: 300,
                                    },
                                    content: item.metadata != undefined ? renderMetadata(item.metadata, column) : '',
                                    html: true,
                                    autoHide: false,
                                    placement: 'auto-start'
                                }"
                                v-for="(column, index) in displayedMetadata"
                                :key="index"
                                class="metadata-title"
                                v-if="collectionId != undefined && column.display && column.metadata_type_object != undefined && (column.metadata_type_object.related_mapped_prop == 'title')"
                                v-html="item.metadata != undefined ? renderMetadata(item.metadata, column) : ''" />                             
                        <p 
                                v-tooltip="{
                                    delay: {
                                        show: 500,
                                        hide: 300,
                                    },
                                    content: item.title != undefined ? item.title : '',
                                    html: true,
                                    autoHide: false,
                                    placement: 'auto-start'
                                }"
                                v-for="(column, index) in tableMetadata"
                                :key="index"
                                v-if="collectionId == undefined && column.display && column.metadata_type_object != undefined && (column.metadata_type_object.related_mapped_prop == 'title')"
                                v-html="item.title != undefined ? item.title : ''" />                             

                        <!-- Remaining metadata -->  
                        <div class="media">
                            <div class="list-metadata media-body">
                                <div 
                                        class="thumbnail"
                                        v-if="item.thumbnail != undefined">
                                    <img 
                                            :alt="$i18n.get('label_thumbnail')"
                                            :src="item['thumbnail']['tainacan-medium-full'] ? item['thumbnail']['tainacan-medium-full'][0] : (item['thumbnail'].medium_large ? item['thumbnail'].medium_large[0] : thumbPlaceholderPath)">  
                                    <div 
                                            :style="{ 
                                                minHeight: getItemImageHeight(item['thumbnail']['tainacan-medium-full'] ? item['thumbnail']['tainacan-medium-full'][1] : (item['thumbnail'].medium_large ? item['thumbnail'].medium_large[1] : 120), item['thumbnail']['tainacan-medium-full'] ? item['thumbnail']['tainacan-medium-full'][2] : (item['thumbnail'].medium_large ? item['thumbnail'].medium_large[2] : 120)) + 'px',
                                                marginTop: '-' + getItemImageHeight(item['thumbnail']['tainacan-medium-full'] ? item['thumbnail']['tainacan-medium-full'][1] : (item['thumbnail'].medium_large ? item['thumbnail'].medium_large[1] : 120), item['thumbnail']['tainacan-medium-full'] ? item['thumbnail']['tainacan-medium-full'][2] : (item['thumbnail'].medium_large ? item['thumbnail'].medium_large[2] : 120)) + 'px'
                                            }"
                                            class="skeleton"/>
                                </div>
                                <span 
                                        v-for="(column, index) in tableMetadata"
                                        :key="index"
                                        v-if="collectionId == undefined && column.display && column.metadata_type_object != undefined && (column.metadata_type_object.related_mapped_prop == 'description')">
                                    <h3 class="metadata-label">{{ $i18n.get('label_description') }}</h3>
                                    <p 
                                            v-html="item.description != undefined ? item.description : ''"
                                            class="metadata-value"/>
                                </span>
                                <span 
                                        v-for="(column, index) in displayedMetadata"
                                        :key="index"
                                        v-if="renderMetadata(item.metadata, column) != '' && column.display && column.slug != 'thumbnail' && column.metadata_type_object != undefined && (column.metadata_type_object.related_mapped_prop != 'title')">
                                    <h3 class="metadata-label">{{ column.name }}</h3>
                                    <p      
                                            v-html="renderMetadata(item.metadata, column)"
                                            class="metadata-value"/>
                                </span>
                            </div>
                        </div>
                    </a>
                <!-- </div> -->
            </masonry>
        </div> 
    </div>
</template>

<script>

export default {
    name: 'ViewModeRecords',
    props: {
        collectionId: Number,
        displayedMetadata: Array,
        items: Array,
        isLoading: false
    },
    computed: {
        amountOfDisplayedMetadata() {
            return this.displayedMetadata.filter((metadata) => metadata.display).length;
        }
    },
    data () {
        return {
            thumbPlaceholderPath: tainacan_plugin.base_url + '/admin/images/placeholder_square.png'
        }
    },
    methods: {
        goToItemPage(item) {
            window.location.href = item.url;   
        },
        renderMetadata(itemMetadata, column) {

            let metadata = (itemMetadata != undefined && itemMetadata[column.slug] != undefined) ? itemMetadata[column.slug] : false;

            if (!metadata) {
                return '';
            } else {
                return metadata.value_as_html;
            }
        },
        randomHeightForRecordsItem() {
            let min = (70*this.amountOfDisplayedMetadata)*0.8;
            let max = (70*this.amountOfDisplayedMetadata)*1.2;
            return Math.floor(Math.random()*(max-min+1)+min);
        },
        getItemImageHeight(imageWidth, imageHeight) {  
            let itemWidth = 120;
            return (imageHeight*itemWidth)/imageWidth;
        },
    }
}
</script>

<style  lang="scss" scoped>
    $turquoise1: #e6f6f8;
    $turquoise2: #d1e6e6;
    $tainacan-input-color: #1d1d1d;
    $gray1: #f2f2f2; 
    $gray2: #e5e5e5;
    $gray3: #dcdcdc;
    $gray4: #555758;
    $gray5: #454647; 

    @import "../../src/admin/scss/_view-mode-records.scss";

    .tainacan-records-container .tainacan-record .metadata-title {
        padding: 0.6rem 0.75rem;
        margin-bottom: 0px;
    }
</style>


