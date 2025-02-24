<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div id="MB-wrap" class="wrap MB-settings">
    <h2><?php _e( 'MB Store Locator Settings', 'MB' ); ?></h2>
    <?php 
        global $wpdb;
        settings_errors();

    ?>

    <form id="MB-settings-form" method="post" action="options.php" accept-charset="utf-8">
        <h2></h2>
        
        <div id="info">
            <div id="general" class="hide">
                <br /><br />
                <h3><?php _e('General Information', 'MB'); ?></h3>
                <?php _e('Enter your settings below:', 'MB'); ?>

                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <?php _e('Page Title:','MB'); ?>
                            </th>
                            <td>
                                <input type="text" value="<?php if($this->module_settings['page_title']!='') echo esc_attr( $this->module_settings['page_title'] ); ?>" name="MB_module[page_title]" placeholder="<?php _e( 'Page Title', 'MB' ); ?>" class="textinput" id="MB-page-title" />
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <?php _e('Meta Keywords:','MB'); ?>
                            </th>
                            <td>
                                <textarea name="MB_module[meta_keywords]" class="textinput" rows="7" placeholder="<?php _e( 'Meta Keywords', 'MB' ); ?>"><?php if($this->module_settings['meta_keywords']!='') echo esc_attr( $this->module_settings['meta_keywords'] ); ?></textarea> 
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <?php _e('Meta Description:','MB'); ?>
                            </th>
                            <td>
                                <textarea name="MB_module[meta_description]" class="textinput" rows="7" placeholder="<?php _e( 'Meta Description', 'MB' ); ?>"><?php if($this->module_settings['meta_description']!='') echo esc_attr( $this->module_settings['meta_description'] ); ?></textarea> 
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <?php _e('Page Heading:','MB'); ?>
                                <p class="description">(<?php _e('Main heading  store locator page', 'MB'); ?>)</p>
                            </th>
                            <td>
                                <input type="text" value="<?php if($this->module_settings['page_heading']!='') echo esc_attr( $this->module_settings['page_heading'] ); ?>" name="MB_module[page_heading]" placeholder="<?php _e( 'Page Heading', 'MB' ); ?>" class="textinput" id="MB-page-heading" />
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <?php _e('Page Sub Heading:','MB'); ?>
                                <p class="description">(<?php _e('Sub heading  store locator page', 'MB'); ?>)</p>
                            </th>
                            <td>
                                <input type="text" value="<?php if($this->module_settings['page_sub_heading']!='') echo esc_attr( $this->module_settings['page_sub_heading'] ); ?>" name="MB_module[page_sub_heading]" placeholder="<?php _e( 'Page Sub Heading', 'MB' ); ?>" class="textinput" id="MB-page-sub-heading" />
                            </td>
                        </tr>

                        

                        

                    </tbody>
                </table>


                 <br /><br />
                 

                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <?php _e('Standard Latitude:','MB'); ?>
                            </th>
                            <td>
                                <input type="text" value="<?php if($this->module_settings['standard_latitude']!='') echo esc_attr( $this->module_settings['standard_latitude'] ); ?>" name="MB_module[standard_latitude]" placeholder="<?php _e( 'Standard Latitude', 'MB' ); ?>" class="textinput" id="MB-standard-latitude" />
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <?php _e('Standard Longitude:','MB'); ?>
                            </th>
                            <td>
                                <input type="text" value="<?php if($this->module_settings['standard_longitude']!='') echo esc_attr( $this->module_settings['standard_longitude'] ); ?>" name="MB_module[standard_longitude]" placeholder="<?php _e( 'Standard Longitude', 'MB' ); ?>" class="textinput" id="MB-standard-longitude" />
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <?php _e('API Key:','MB'); ?>
                                <p class="description">(<?php _e('Google Map API Key(v3)', 'MB'); ?>)</p>
                            </th>
                            <td>
                                <input type="text" value="<?php if($this->module_settings['MB']!='') echo esc_attr( $this->module_settings['api_key'] ); ?>" name="MB_module[api_key]" placeholder="<?php _e( 'API Key', 'MB' ); ?>" class="textinput" id="MB-api-key" />
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <?php _e('Map Marker Image:','MB'); ?>
                            </th>
                            <td>
                                <input type="text" name="MB_module[marker_image]" id="new_img" value="<?php if($this->module_settings['marker_image']!='') echo esc_attr( $this->module_settings['marker_image'] ); ?>">
                                <a class="button" onclick="upload_image('new_img');">Upload Image</a>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <?php _e('Current Marker Image:','MB'); ?>
                            </th>
                            <td>
                                <img src="<?php echo esc_attr( $this->module_settings['marker_image'] ); ?>" width="50" height="50" />
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <?php _e('Enable Marker Numbers:','MB'); ?>
                            </th>
                            <td>
                                <select name="MB_module[enable_marker_numbers]" class="textinput" id="MB-enable-marker-numbers">
                                    <option value="Yes" <?php selected( $this->module_settings['enable_marker_numbers'] , 'Yes'); ?>>Yes</option>
                                    <option value="No" <?php selected( $this->module_settings['enable_marker_numbers'] , 'No'); ?>>No</option>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <?php _e('Enable Sidebar Markers:','MB'); ?>
                            </th>
                            <td>
                                <select name="MB_module[enable_sidebar_markers]" class="textinput" id="MB-enable-sidebar-markers">
                                    <option value="Yes" <?php selected( $this->module_settings['enable_sidebar_markers'] , 'Yes'); ?>>Yes</option>
                                    <option value="No" <?php selected( $this->module_settings['enable_sidebar_markers'] , 'No'); ?>>No</option>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <?php _e('Map Zoom:','MB'); ?>
                                <p class="description">(<?php _e('Enter digit for map zoom. i.e(6, 8, 11) etc', 'MB'); ?>)</p>
                            </th>
                            <td>
                                <input type="text" value="<?php echo esc_attr( $this->module_settings['map_zoom'] ); ?>" name="MB_module[map_zoom]" placeholder="<?php _e( 'Map Zoom', 'MB' ); ?>" class="textinput" id="MB-map-zoom" />
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <?php _e('Map Distance:','MB'); ?>
                            </th>
                            <td>
                                <select name="MB_module[map_distance]" class="textinput" id="MB-map-distance">
                                    <option value="km" <?php selected( $this->module_settings['map_distance'] , 'Km'); ?>>Km</option>
                                    <option value="Mile" <?php selected( $this->module_settings['map_distance'] , 'Mile'); ?>>Mile</option>
                                </select>
                            </td>
                        </tr>

                        
                        <tr>
                            <th scope="row">
                                <?php _e('Enable Search By Address, Zip Code, State:','MB'); ?>
                            </th>
                            <td>
                                <select name="MB_module[enable_search_by_address]" class="textinput" id="MB-enable-search-by-address">
                                    <option value="Yes" <?php selected( $this->module_settings['enable_search_by_address'] , 'Yes'); ?>>Yes</option>
                                    <option value="No" <?php selected( $this->module_settings['enable_search_by_address'] , 'No'); ?>>No</option>
                                </select>
                            </td>
                        </tr>

                        


                    </tbody>
                 </table>



            </div>
            <p class="submit">
                <input type="submit" value="<?php _e( 'Save Changes', 'MB' ); ?>" class="button-primary" name="MB-save-settings" id="MB-save-settings">
            <?php settings_fields( 'MB_settings' ); ?>
            </p>
        </div>
    </form>


</div>

<script type="text/javascript">
    jQuery(document).ready(function($){
      $( '#info #data' ).hide();
      
      $('#info-nav li').click(function(e) {
        $('#info .hide').hide();
        $('#info-nav .current').removeClass("current");
        $(this).addClass('current');
        
        var clicked = $(this).find('a:first').attr('href');
        $('#info ' + clicked).fadeIn('fast');
        e.preventDefault();
      }).eq(0).addClass('current');
    });

</script>
