<?php if ( ! defined( 'ABSPATH' ) ) exit;  ?>
<?php
require_once MB_PLUGIN_DIR . 'front/class-front.php';

$frontview = new MB_Front_Class();
$store_detail = $frontview->store_details($_GET['store_id']);

?>
<?php $popup_secondary_image =  MB_URL.'front/image/map_popup_bottom.png';  ?>
<script type="text/javascript">
  var user_position_search = '';
</script>
<h2><?php echo $store_detail->store_name; ?></h2>
<div id="store-map" class="store-map"></div>
<div class="description"><p><?php echo html_entity_decode($store_detail->store_description, ENT_QUOTES, 'UTF-8'); ?></p></div>

<div class="store-detail">      
  <div class="info">
    <img src="<?php echo $store_detail->store_image; ?>" width="200">
  </div>
  <div class="info">        
    <h2 class="title-2"><?php _e('Contact Information', 'MB') ?></h2>
    <address><?php echo $store_detail->address.', '.$store_detail->city.', '.$store_detail->zip_code.' '.$store_detail->state.', '.$store_detail->country; ?></address>

    <?php if($store_detail->phone) { ?>  <span><?php _e('Phone:', 'MB') ?> <?php echo $store_detail->phone; ?> </span><br> <?php } ?>
    <?php if($store_detail->website) { ?>   <span> <a href="<?php echo $store_detail->$store_detail->website; ?>"><?php _e('website:', 'MB') ?> <?php echo $store_detail->website; ?> </a></span><br> <?php } ?>
    <?php if($store_detail->fax) { ?>  <span><?php _e('Fax:', 'MB') ?> <?php echo $store_detail->fax; ?> </span><br> <?php } ?>
  </div>

 
  

</div>




<script type="text/javascript">
  
  
  
  var destinationIcon = "<?php echo $this->module_settings['marker_image']; ?>";
  var map;
  var marker;
  
  
  jQuery(document).ready(function($){
      
      
      StoreMap();     
      
  });
  
  function StoreMap(){
          var map_opts = {
              zoom  : 8,
              center  : new google.maps.LatLng(<?php echo $store_detail->latitude; ?>, <?php echo $store_detail->longitude; ?>),
              mapTypeId : google.maps.MapTypeId.ROADMAP,
          }
          
          map = new google.maps.Map(document.getElementById('store-map'), map_opts);
          createMarker();
      
  }
  
  function createMarker(){
      
          var marker_img = {
              url: destinationIcon,
              scaledSize : new google.maps.Size(50, 50)
          };
          
          marker = new google.maps.Marker({
              position : new google.maps.LatLng(<?php echo $store_detail->latitude; ?>, <?php echo $store_detail->longitude; ?>),
              icon   : marker_img,
              animation: google.maps.Animation.DROP,
              map : map
          });
          
          
          google.maps.event.addListener(marker, 'click', (function(marker) {
              return function(){          
                  createPopup();      
              }
          })(marker));
          
  }
  
  function createPopup(){
              
          var boxText ="";
          var s_id = "<?php echo $store_detail->store_id; ?>";
          var s_name = "<?php echo $store_detail->store_name ?>";
          var s_description = "<?php echo substr(html_entity_decode($store_detail->store_description, ENT_QUOTES, 'UTF-8'),0,250); ?>";
          var s_distance = "";
          var s_image = "";
           
          var s_phone = "<?php echo $store_detail->phone; ?>";
           var s_website = "<?php echo $store_detail->website; ?>";
          var s_phone = "<?php echo $store_detail->phone; ?>";
          var s_fax = "<?php echo $store_detail->fax; ?>";
          var s_state = ',';
          
          boxText = document.createElement('div');
          boxText.innerHTML = '<div id="pop_div" class="map_popup_top1"><h1>'+s_name+'</h1>'+s_description+'<div class="store-more-info">'+s_phone+'</div></div>';
          var box = boxText.innerHTML + '<div class="map_popup_bottom"><img src="<?php echo $popup_secondary_image; ?>" alt="" width="290px;" /></div>';
          
          
          var popupOptions = {
              
              content : box,
              disableAutoPan : false,
              maxWidth  : 0,
              pixelOffset : new google.maps.Size(-5, -20),
              zIndex  : null,
              boxStyle: { 
                              background: "url('../css/map_popup_top.png') no-repeat"
                              ,opacity: 1
                              ,width: "295px"
                              ,height:"250px"
              },
              closeBoxMargin : "-13px -5px 2px",
              closeBoxURL: "<?php echo MB_URL; ?>front/image/close_black.png"
                ,close_onmouseover :"<?php echo MB_URL; ?>front/image/close_icon.png"
                ,close_onmouseout :"<?php echo MB_URL; ?>front/image/close_black.png",
              infoBoxClearance: new google.maps.Size(1, 1),
              isHidden: false,
              pane: "floatPane",
              enableEventPropagation: true
          }       
              var ib = new InfoBox(popupOptions);
              ib.open(map, marker);
              
  }
</script>
