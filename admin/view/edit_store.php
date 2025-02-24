<?php if ( ! defined( 'ABSPATH' ) ) exit;  ?>
<div id="MB-wrap" class="wrap MB-add-attribute">
    <?php  
        $countries = $this->my_add_country_select();

        $store_id = $_GET['store_id'] ;
        if ( $store_id ) {
            $stores = $this->get_store_data($store_id);
            
        }

        
    ?>

    <?php 
        if(isset($_SESSION['chkd'])!='') {

            $chkd = $_SESSION['chkd'];
        } else {
            $chkd = '';
        }   
    ?>

	<h2>MANAGE Store Locator</h2>
    <h3 class="MB-edit-header"><?php _e( 'Edit ', 'MB' ); if ( isset( $_POST['MB'] ) && empty( $_POST['MB']['store_name'] ) ) { echo esc_attr( stripslashes( $_POST['MB']['store_name'] ) ); } else { echo esc_attr( stripslashes( $stores->store_name ) ); } ?></h3>
    <?php settings_errors(); ?>
    
    
    <form method="post" action="" accept-charset="utf-8">
        <input type="hidden" name="MB_actions" value="update_store" />
        <input type="hidden" name="MB[checked_boxes]" id="checked_boxes" value="<?php echo $chkd; ?>">
        <?php wp_nonce_field( 'MB_update_store' ); ?>
        
        <div id="info">
             <div id="general" class="hide">
                 <div class="postbox-container" style="float:none">
                <div class="metabox-holder">
                    <div class="postbox">
                        <h3><span><?php _e( 'General Information', 'MB' ); ?></span></h3>
                        <div class="inside">
                            <p>
                                <label for="MB-store-name">
                                    <span class="tit"><?php _e( 'Store Name:', 'MB' ); ?>*</span>
                                </label>
                                <input id="MB-store-name" name="MB[store_name]" type="text" class="textinput <?php if ( isset( $_POST['MB'] ) && empty( $_POST['MB']['store_name'] ) ) { echo 'MB-error'; } ?>" value="<?php if ( isset( $_POST['MB'] ) && empty( $_POST['MB']['store_name'] ) ) { echo esc_attr( stripslashes( $_POST['MB']['store_name'] ) ); } else { echo esc_attr( stripslashes( $stores->store_name ) ); } ?>" />
                            </p>

                            <p>
                                <label for="MB-meta-title">
                                    <span class="tit"><?php _e( 'Meta Title:', 'MB' ); ?></span>
                                </label> 
                                <input type="text" value="<?php if ( isset( $_POST['MB'] ) && empty( $_POST['MB']['store_meta_title'] ) ) { echo esc_attr( stripslashes( $_POST['MB']['store_meta_title'] ) ); } else { echo esc_attr( stripslashes( $stores->store_meta_title ) ); } ?>" name="MB[meta_title]" class="textinput" id="MB-meta-title" />
                            </p>

                            <p>
                                <label for="MB-meta-keywords">
                                    <span class="tit"><?php _e( 'Meta Keywords:', 'MB' ); ?></span>
                                </label>
                                <textarea name="MB[meta_keywords]" class="textinput" rows="7"><?php if ( isset( $_POST['MB'] ) && empty( $_POST['MB']['store_meta_keywords'] ) ) { echo esc_attr( stripslashes( $_POST['MB']['store_meta_keywords'] ) ); } else { echo esc_attr( stripslashes( $stores->store_meta_keywords ) ); } ?></textarea> 
                            </p>

                            <p>
                                <label for="MB-meta-description">
                                    <span class="tit"><?php _e( 'Meta Description:', 'MB' ); ?></span>
                                </label> 
                                <textarea name="MB[meta_description]" class="textinput"  rows="7" ><?php if ( isset( $_POST['MB'] ) && empty( $_POST['MB']['store_meta_description'] ) ) { echo esc_attr( stripslashes( $_POST['MB']['store_meta_description'] ) ); } else { echo esc_attr( stripslashes( $stores->store_meta_description ) ); } ?></textarea> 
                            </p>

                            <p>
                                <label for="MB-meta-description">
                                    <span class="tit"><?php _e( 'Store Attributes:', 'MB' ); ?></span>
                                </label>
                                
                            </p>

                            <p>
                                <label for="MB-store-description">
                                    <span class="tit"><?php _e( 'Store Description:', 'MB' ); ?></span>
                                </label> 
                                <?php 
                                    $editor_id = 'addstore';
                                    if (!empty( $_POST['MB']['store_description'] ) ) { 
                                        $content = esc_attr( stripslashes( $_POST['MB']['store_description'] ) );
                                     } else
                                     {
                                        $content = esc_attr( stripslashes( $stores->store_description));
                                     }
                                    $settings = array( 
                                        'textarea_name' => 'MB[store_description]',
                                        'textarea_rows' => '10'
                                         );
                                    wp_editor( $content, $editor_id, $settings );
                                 ?> 
                            </p>
                            
                         </div>
                    </div>
                </div> 
            </div>



            <div class="postbox-container" style="float:none">
                <div class="metabox-holder">
                    <div class="postbox">
                        <h3><span><?php _e( 'Data Section', 'MB' ); ?></span></h3>
                        <div class="inside">

                            <p>
                                <label for="MB-country">
                                    <span class="tit"><?php _e( 'Country:', 'MB' ); ?>*</span>
                                </label>
                                <select name="MB[country]" class="textinput <?php if ( isset( $_POST['MB'] ) && empty( $_POST['MB']['country'] ) ) { echo 'MB-error'; } ?>" >
                                    <option value=""><?php _e('Select Country', 'MB') ?></option>
                                    <?php foreach ($countries as $country) { ?>
                                       <option value="<?php echo $country; ?>" <?php selected( $stores->country, $country); ?>><?php echo $country; ?></option>
                                    <?php } ?>
                                </select>

                            </p>

                            <p>
                                <label for="MB-city">
                                    <span class="tit"><?php _e( 'District / City:', 'MB' ); ?>*</span>
                                </label>
                                <input id="MB-city" name="MB[city]" type="text" class="textinput <?php if ( isset( $_POST['MB'] ) && empty( $_POST['MB']['city'] ) ) { echo 'MB-error'; } ?>" value="<?php if ( isset( $_POST['MB'] ) && empty( $_POST['MB']['city'] ) ) { echo esc_attr( stripslashes( $_POST['MB']['city'] ) ); } else { echo esc_attr( stripslashes( $stores->city ) ); } ?>" />
                            </p>



 <p>
                                <label for="MB-website">
                                    <span class="tit"><?php _e( 'website/:', 'MB' ); ?>*</span>
                                </label>
                                <input id="MB-website" name="MB[website]" type="text" class="textinput <?php if ( isset( $_POST['MB'] ) && empty( $_POST['MB']['website'] ) ) { echo 'MB-error'; } ?>" value="<?php if ( isset( $_POST['MB'] ) && empty( $_POST['MB']['website'] ) ) { echo esc_attr( stripslashes( $_POST['MB']['website'] ) ); } else { echo esc_attr( stripslashes( $stores->website ) ); } ?>" />
                            </p>
                            <p>
                                <label for="MB-state">
                                    <span class="tit"><?php _e( 'State:', 'MB' ); ?></span>
                                </label>
                                <input id="MB-state" name="MB[state]" type="text" class="textinput" value="<?php if ( isset( $_POST['MB'] ) && empty( $_POST['MB']['state'] ) ) { echo esc_attr( stripslashes( $_POST['MB']['state'] ) ); } else { echo esc_attr( stripslashes( $stores->state ) ); } ?>" />
                            </p>

                            <p>
                                <label for="MB-zip-code">
                                    <span class="tit"><?php _e( 'Postal / Zip Code:', 'MB' ); ?>*</span>
                                </label>
                                <input id="MB-zip-code" name="MB[zip_code]" type="text" class="textinput <?php if ( isset( $_POST['MB'] ) && empty( $_POST['MB']['zip_code'] ) ) { echo 'MB-error'; } ?>" value="<?php if ( isset( $_POST['MB'] ) && empty( $_POST['MB']['zip_code'] ) ) { echo esc_attr( stripslashes( $_POST['MB']['zip_code'] ) ); } else { echo esc_attr( stripslashes( $stores->zip_code ) ); } ?>" />
                            </p>

                            <p>
                                <label for="MB-address">
                                    <span class="tit"><?php _e( 'Address:', 'MB' ); ?>*</span>
                                </label>
                                <input id="MB-address" name="MB[address]" type="text" class="textinput <?php if ( isset( $_POST['MB'] ) && empty( $_POST['MB']['address'] ) ) { echo 'MB-error'; } ?>" value="<?php if ( isset( $_POST['MB'] ) && empty( $_POST['MB']['address'] ) ) { echo esc_attr( stripslashes( $_POST['MB']['address'] ) ); } else { echo esc_attr( stripslashes( $stores->address ) ); } ?>" />
                            </p>

                            <p>
                                <label for="MB-latitude">
                                    <span class="tit"><?php _e( 'Latitude:', 'MB' ); ?></span>
                                </label>
                                <input id="MB-latitude" name="MB[latitude]" type="text" class="textinput" value="<?php if ( isset( $_POST['MB'] ) && empty( $_POST['MB']['latitude'] ) ) { echo esc_attr( stripslashes( $_POST['MB']['latitude'] ) ); } else { echo esc_attr( stripslashes( $stores->latitude ) ); } ?>" />
                            </p>

                            <p>
                                <label for="MB-longitude">
                                    <span class="tit"><?php _e( 'Longitude:', 'MB' ); ?></span>
                                </label>
                                <input id="MB-longitude" name="MB[longitude]" type="text" class="textinput" value="<?php if ( isset( $_POST['MB'] ) && empty( $_POST['MB']['longitude'] ) ) { echo esc_attr( stripslashes( $_POST['MB']['longitude'] ) ); } else { echo esc_attr( stripslashes( $stores->longitude ) ); } ?>" />
                            </p>

                            <p>
                                <label for="MB-attribute-status">
                                    <span class="tit"><?php _e( 'Status:', 'MB' ); ?></span>
                                </label>
                                <select name="MB[status]" class="textinput" >
                                    <option value="1" <?php selected( $stores->status , 1); ?>><?php _e('Active', 'MB') ?></option>
                                    <option value="0" <?php selected( $stores->status , 0); ?>><?php _e('Inactive', 'MB') ?></option>
                                </select>
                            </p>

                            <p>
                                <label for="MB-phone">
                                    <span class="tit"><?php _e( 'Phone:', 'MB' ); ?></span>
                                </label>
                                <input id="MB-phone" name="MB[phone]" type="text" class="textinput" value="<?php if ( isset( $_POST['MB'] ) && empty( $_POST['MB']['phone'] ) ) { echo esc_attr( stripslashes( $_POST['MB']['phone'] ) ); } else { echo esc_attr( stripslashes( $stores->phone ) ); } ?>" />
                            </p>

                            <p>
                                <label for="MB-fax">
                                    <span class="tit"><?php _e( 'Fax:', 'MB' ); ?></span>
                                </label>
                                <input id="MB-fax" name="MB[fax]" type="text" class="textinput" value="<?php if ( isset( $_POST['MB'] ) && empty( $_POST['MB']['fax'] ) ) { echo esc_attr( stripslashes( $_POST['MB']['fax'] ) ); } else { echo esc_attr( stripslashes( $stores->fax ) ); } ?>" />
                            </p>

                            <p>
                                <label for="MB-store-image">
                                    <span class="tit"><?php _e( 'Store Image:', 'MB' ); ?></span>
                                </label>
                                <input type="text" name="MB[store_image]" id="new_img" value="<?php if ( isset( $_POST['MB'] ) && empty( $_POST['MB']['store_image'] ) ) { echo esc_attr( stripslashes( $_POST['MB']['store_image'] ) ); } else { echo esc_attr( stripslashes( $stores->store_image ) ); } ?>">
                                <a class="button" onclick="upload_image('new_img');">Upload Image</a>
                            </p>
                            <p>
                                <label for="MB-store-image">
                                    <span class="tit"><?php _e( 'Current Image:', 'MB' ); ?></span>
                                </label>
                                <img src="<?php echo esc_attr( stripslashes( $stores->store_image ) ); ?>" width="150">
                            </p>

                            
                             
                        </div>
                    </div>
                </div>
            </div>


             <div class="postbox-container" style="float:none">
                <div class="metabox-holder">
                    <div class="postbox">
                        <div class="inside" style="padding:3px 12px 12px;">
                            <p>
                                <input id="MB-update-store" type="submit" name="MB-update-store" class="button-primary" value="<?php _e( 'Update Store', 'MB' ); ?>" />
                            </p>
                        </div>
                    </div>
                </div>
            </div>

             </div>
           

             
             
        </div>

        <input type="hidden" name="MB[store_id]" value="<?php echo $store_id; ?>">
        
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
