<?php if ( ! defined( 'ABSPATH' ) ) exit;  ?>
<div id="MB-wrap" class="wrap MB-add-attribute">
    <?php  
        $countries = $this->my_add_country_select();

    ?>
	<h2>MANAGE Store Locator</h2>
    <h3>Add Store</h3>
    <?php settings_errors(); ?>
    
    <?php 
        if(isset($_SESSION['chkd'])!='') {

            $chkd = $_SESSION['chkd'];
        } else {
            $chkd = '';
        }   
    ?>
    
    <form method="post" action="" accept-charset="utf-8">
        <input type="hidden" name="MB_actions" value="add_new_store" />
        <input type="hidden" name="MB[checked_boxes]" id="checked_boxes" value="<?php echo $chkd; ?>">
        <?php wp_nonce_field( 'MB_add_new_store' ); ?>
        
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
                                    <input id="MB-store-name" name="MB[store_name]" type="text" class="textinput <?php if ( isset( $_POST['MB'] ) && empty( $_POST['MB']['store_name'] ) ) { echo 'MB-error'; } ?>" value="<?php if ( !empty( $_POST['MB']['store_name'] ) ) { echo esc_attr( stripslashes( $_POST['MB']['store_name'] ) );  } ?>" />
                                </p>

                                <p>
                                    <label for="MB-meta-title">
                                        <span class="tit"><?php _e( 'Meta Title:', 'MB' ); ?></span>
                                    </label> 
                                    <input type="text" value="<?php if ( !empty( $_POST['MB']['meta_title'] ) ) { echo esc_attr( stripslashes( $_POST['MB']['meta_title'] ) );  } ?>" name="MB[meta_title]" class="textinput" id="MB-meta-title" />
                                </p>

                                <p>
                                    <label for="MB-meta-keywords">
                                        <span class="tit"><?php _e( 'Meta Keywords:', 'MB' ); ?></span>
                                    </label>
                                    <textarea name="MB[meta_keywords]" class="textinput" rows="7"><?php if ( !empty( $_POST['MB']['meta_keywords'] ) ) { echo esc_attr( stripslashes( $_POST['MB']['meta_keywords'] ) );  } ?></textarea> 
                                </p>

                                <p>
                                    <label for="MB-meta-description">
                                        <span class="tit"><?php _e( 'Meta Description:', 'MB' ); ?></span>
                                    </label> 
                                    <textarea name="MB[meta_description]" class="textinput"  rows="7" ><?php if ( !empty( $_POST['MB']['meta_description'] ) ) { echo esc_attr( stripslashes( $_POST['MB']['meta_description'] ) );  } ?></textarea> 
                                </p>

                               

                                <p>
                                    <label for="MB-store-description">
                                        <span class="tit"><?php _e( 'Store Description:', 'MB' ); ?></span>
                                    </label> 
                                    <?php 
                                        $editor_id = 'addstore';
                                        if (!empty( $_POST['MB']['store_description'] ) ) { 
                                            $content = esc_attr( stripslashes( $_POST['MB']['store_description'] ) );
                                         } else {
                                            $content = '';
                                         }
                                        $settings = array( 
                                            'textarea_name' => 'MB[store_description]',
                                            'textarea_rows' => '10'
                                             );
                                        wp_editor( $content, $editor_id, $settings );
                                     ?> 
                                </p>



                                <div class="postbox">
                            <h3></h3>
                            <div class="inside">

                                <p>
                                    <label for="MB-country">
                                        <span class="tit"><?php _e( 'Country:', 'MB' ); ?>*</span>
                                    </label>
                                    <select name="MB[country]" class="textinput <?php if ( isset( $_POST['MB'] ) && empty( $_POST['MB']['country'] ) ) { echo 'MB-error'; } ?>" >
                                        <option value=""><?php _e('Select Country', 'MB') ?></option>
                                        <?php foreach ($countries as $country) { ?>
                                           <option value="<?php echo $country; ?>"><?php echo $country; ?></option>
                                        <?php } ?>
                                    </select>

                                </p>

                                <p>
                                    <label for="MB-city">
                                        <span class="tit"><?php _e( 'District / City:', 'MB' ); ?>*</span>
                                    </label>
                                    <input id="MB-city" name="MB[city]" type="text" class="textinput <?php if ( isset( $_POST['MB'] ) && empty( $_POST['MB']['city'] ) ) { echo 'MB-error'; } ?>" value="<?php if ( !empty( $_POST['MB']['city'] ) ) { echo esc_attr( stripslashes( $_POST['MB']['city'] ) );  } ?>" />
                                </p>


                                <p>
                                    <label for="MB-website">
                                        <span class="tit"><?php _e( 'website/:', 'MB' ); ?>*</span>
                                    </label>
                                    <input id="MB-website" name="MB[website]" type="text" class="textinput <?php if ( isset( $_POST['MB'] ) && empty( $_POST['MB']['website'] ) ) { echo 'MB-error'; } ?>" value="<?php if ( !empty( $_POST['MB']['website'] ) ) { echo esc_attr( stripslashes( $_POST['MB']['website'] ) );  } ?>" />
                                </p>

                                <p>
                                    <label for="MB-state">
                                        <span class="tit"><?php _e( 'State:', 'MB' ); ?></span>
                                    </label>
                                    <input id="MB-state" name="MB[state]" type="text" class="textinput" value="<?php if ( !empty( $_POST['MB']['state'] ) ) { echo esc_attr( stripslashes( $_POST['MB']['state'] ) );  } ?>" />
                                </p>

                                <p>
                                    <label for="MB-zip-code">
                                        <span class="tit"><?php _e( 'Postal / Zip Code:', 'MB' ); ?>*</span>
                                    </label>
                                    <input id="MB-zip-code" name="MB[zip_code]" type="text" class="textinput <?php if ( isset( $_POST['MB'] ) && empty( $_POST['MB']['zip_code'] ) ) { echo 'MB-error'; } ?>" value="<?php if ( !empty( $_POST['MB']['zip_code'] ) ) { echo esc_attr( stripslashes( $_POST['MB']['zip_code'] ) );  } ?>" />
                                </p>

                                <p>
                                    <label for="MB-address">
                                        <span class="tit"><?php _e( 'Address:', 'MB' ); ?>*</span>
                                    </label>
                                    <input id="MB-address" name="MB[address]" type="text" class="textinput <?php if ( isset( $_POST['MB'] ) && empty( $_POST['MB']['address'] ) ) { echo 'MB-error'; } ?>" value="<?php if ( !empty( $_POST['MB']['address'] ) ) { echo esc_attr( stripslashes( $_POST['MB']['address'] ) );  } ?>" />
                                </p>

                                <p>
                                    <label for="MB-latitude">
                                        <span class="tit"><?php _e( 'Latitude:', 'MB' ); ?></span>
                                    </label>
                                    <input id="MB-latitude" name="MB[latitude]" type="text" class="textinput" value="<?php if ( !empty( $_POST['MB']['latitude'] ) ) { echo esc_attr( stripslashes( $_POST['MB']['latitude'] ) );  } ?>" />
                                </p>

                                <p>
                                    <label for="MB-longitude">
                                        <span class="tit"><?php _e( 'Longitude:', 'MB' ); ?></span>
                                    </label>
                                    <input id="MB-longitude" name="MB[longitude]" type="text" class="textinput" value="<?php if ( !empty( $_POST['MB']['longitude'] ) ) { echo esc_attr( stripslashes( $_POST['MB']['longitude'] ) );  } ?>" />
                                </p>

                                <p>
                                    <label for="MB-attribute-status">
                                        <span class="tit"><?php _e( 'Status:', 'MB' ); ?></span>
                                    </label>
                                    <select name="MB[status]" class="textinput" >
                                        <option value="1"><?php _e('Active', 'MB') ?></option>
                                        <option value="0"><?php _e('Inactive', 'MB') ?></option>
                                    </select>
                                </p>

                                <p>
                                    <label for="MB-phone">
                                        <span class="tit"><?php _e( 'Phone:', 'MB' ); ?></span>
                                    </label>
                                    <input id="MB-phone" name="MB[phone]" type="text" class="textinput" value="<?php if ( !empty( $_POST['MB']['phone'] ) ) { echo esc_attr( stripslashes( $_POST['MB']['phone'] ) );  } ?>" />
                                </p>

                                <p>
                                    <label for="MB-fax">
                                        <span class="tit"><?php _e( 'Fax:', 'MB' ); ?></span>
                                    </label>
                                    <input id="MB-fax" name="MB[fax]" type="text" class="textinput" value="<?php if ( !empty( $_POST['MB']['fax'] ) ) { echo esc_attr( stripslashes( $_POST['MB']['fax'] ) );  } ?>" />
                                </p>

                                <p>
                                    <label for="MB-store-image">
                                        <span class="tit"><?php _e( 'Store Image:', 'MB' ); ?></span>
                                    </label>
                                    <input type="text" name="MB[store_image]" id="new_img" value="<?php if ( !empty( $_POST['MB']['store_image'] ) ) { echo esc_attr( stripslashes( $_POST['MB']['store_image'] ) );  } ?>">
                                    <a class="button-primary" onclick="upload_image('new_img');">Upload Image</a>
                                </p>

                               
                                 
                            </div>
                        </div>


                                
                             </div>
                        </div>
                    </div> 
                </div>
            </div>
            <div id="general" class="hide">
                <div class="postbox-container" style="float:none">
                    <div class="metabox-holder">
                        
                    </div>
                </div>

                <div class="postbox-container" style="float:none">
                    <div class="metabox-holder">
                        <div class="postbox">
                            <div class="inside" style="padding:3px 12px 12px;" align="center">
                                <p>
                                    <input id="MB-add-store" type="submit" name="MB-add-store" class="button-primary" value="<?php _e( 'Add Store', 'MB' ); ?>" />
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
        

            </div>

            
            
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
