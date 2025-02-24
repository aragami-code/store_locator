<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php
require_once MB_PLUGIN_DIR . 'front/class-front.php';

$frontview = new MB_Front_Class();
$store_results = $frontview->get_allstores();

?>
<script type="text/javascript">
	var user_position_search = '';
</script>
<script type="text/javascript">
  jQuery(document).ready(function(){
          jQuery('#scrollbar1').tinyscrollbar();			
          
  });	
</script>

<?php
    
      $search_global = isset($_SESSION['gmap-search']);
      if($search_global != '') { 
      
      ?>
<script type="text/javascript">
        user_position_search = true;
        var pgeocoder = new google.maps.Geocoder();
        pgeocoder.geocode( { 'address': '<?php echo $search_global; ?>'}, function(results, status) { 
                      if (status == google.maps.GeocoderStatus.OK) {
                              var searched_loc = results[0].geometry.location;
                              user_position_search = searched_loc;
                              findDistanceFromClientToStores(user_position_search);
                              jQuery('#scrollbar1').tinyscrollbar_update();
                      }
        });
        
  
</script>
<?php } ?>

<?php
	$popup_secondary_image =  MB_URL.'front/image/map_popup_bottom.png'; 
?>
<h3><?php echo $this->module_settings['page_sub_heading']; ?></h3>
<div id="main">
	<div class="form" style="background-color: white;">
			


			<?php if($this->module_settings['enable_search_by_address'] == 'Yes') { ?>
		      <div class="field2" align="center">
		        <input name="s_address" id="s_address" type="text" class="input" />
		        
		        <input name="" type="button" class="button-primary" value="<?php _e('find', 'MB') ?>" onclick="searchAddress();"/>
            
		      </div>
         
	      	<?php } ?>
<a href="javascript: " onclick="clearSearchArea()" class="search-clear"><button name="reset"><?php _e('Reset', 'MB') ?></button></a>
	      	
     </div>

      	<!-- Left Side-->
      	<div class="stores" >			
          <div id="scrollbar1">
              <div class="scrollbar">
                <div class="track">
                  <div class="thumb">
                    <div class="end"></div>
                  </div>
                </div>
              </div>
          
              <div class="viewport">
                  <div class="overview"> 
                      <div class="content" id="result" style=" width: 60%;">
                          
                          
                      </div>
                  </div>
              </div>
          
          </div>
      	</div>

      	<!-- Right Side-->
      	<div class="map">
	      <div id="Map"></div>


        <div ><p style="
        cursor: auto;
        /* text-decoration: underline; */
        color: black;
        padding-left: 235px;
        font-family: Roboto,helvetica,arial,sans-serif;
        font-size: 8px;
        font-weight: 400px;
        margin-top: -100px;
    "></p>  </div>
	    </div>

	    <div style="clear:both"></div>
    	
	
</div>



<script type="text/javascript">
  var marker, i;
  var map;
  var locations = <?php echo json_encode($store_results); ?>;
  var destinationIcon = "<?php echo $this->module_settings['marker_image']; ?>";
  var client_current_latlang = '';
  var stores_from_distances = [];
  var geocoder = new google.maps.Geocoder();
  
  
  var naddress;
  var loc='';
  var result_to = 0;
  var shortest = new Array();
  var distancecounter =1;
  
  var MarkerArr = new Array();
  var PopupArr = new Array();
  
  var is_direction_on = false;
  var is_marker_numbers_enable = "<?php echo $this->module_settings['enable_marker_numbers']; ?>";
  var is_marker_sidebar_enable = "<?php echo $this->module_settings['enable_sidebar_markers']; ?>";
  var map_config_distance		= "<?php echo $this->module_settings['map_distance']; ?>";
  
  function initialize() { 
  var storemap = {
    center:new google.maps.LatLng(<?php echo $this->module_settings['standard_latitude']; ?>,<?php echo $this->module_settings['standard_longitude']; ?>),
    zoom:<?php echo $this->module_settings['map_zoom']; ?>,
    mapTypeId:google.maps.MapTypeId.ROADMAP
  };
  map=new google.maps.Map(document.getElementById("Map"),storemap);
  
  <?php if($this->module_settings['enable_search_by_address'] == 'Yes') { ?>
      var input = document.getElementById('s_address');
      var autocomplete = new google.maps.places.Autocomplete(input);
      autocomplete.bindTo('bounds', map);
  <?php } ?>
  
  is_direction_on = false;		      
  generateStoreList();
}


function creatmarkers(i, loc_detail) { 
		
          //var marker_img = destinationIcon;
          
          var marker_img = {
              url: destinationIcon,
              scaledSize : new google.maps.Size(50, 50)
          };
          
          if(is_marker_numbers_enable == 'Yes'){
            
              marker = new MarkerWithLabel({
                        position: new google.maps.LatLng(loc_detail['latitude'], loc_detail['longitude']),
                        icon:	marker_img,
                        map: map,
                        draggable: false,
                        raiseOnDrag: false,
                        labelContent: i+1,
                        labelAnchor: new google.maps.Point(4, 30),
                        labelClass: "gmap-marker-labels", // the CSS class for the label
                        labelInBackground: false
                    });
          }else{ 
                
              marker = new google.maps.Marker({
                    position: new google.maps.LatLng(loc_detail['latitude'], loc_detail['longitude']),
                    icon:	marker_img,
                    animation: google.maps.Animation.DROP,
                    map: map

              });	

                
          }
          
          
          
          MarkerArr[loc_detail['store_id']] = marker;
          
         popupCreate(marker, loc_detail, i);
          
          google.maps.event.addListener(marker, 'click', (function(marker, i) {
                  return function(){				
                    
                    for(j=0; j<PopupArr.length; j++){		
                        //close all opened popups
                        if(PopupArr[j])
                          PopupArr[j].close();
                    }
                    PopupArr[loc_detail['store_id']].open(map, marker);			
                    
                    
                  }
                  
          })(marker, i));

}


function generateStoreList(search_radius){ 
	      
      unsetAllMarkers();
         
      var html = '';
      var result_found = false;
      
      for (j = 0; j < locations.length; j++) {
          
          
          

          
          var store_id		=   locations[j]['store_id'];
          var store_name	=   locations[j]['store_name'];
          var address		=   locations[j]['address'];
          var district		=   locations[j]['city'];
          var state		=   locations[j]['state'] ? locations[j]['state'] : '';
          var postal_code	=   locations[j]['zip_code'];
          var website =   locations[j]['website'];
          var country		=   locations[j]['country'];
          var store_phone	=   locations[j]['phone'];
          var store_fax		=   locations[j]['fax'];
          var lat		=   locations[j]['latitude'];
          var long		=   locations[j]['longitude'];
          var distance		=   locations[j]['distance']; 
          var href    =   "<?php echo the_permalink(); ?>?store_id="+locations[j]['href'];
          var hlink    =   "<?php echo the_permalink(); ?>?store_id="+locations[j]['website']; 
          if(map_config_distance == 'km'){
              
              var new_distance_km	=   distance/1000;
              var distance_km	=   distance/1000;
              distance_km	=   distance_km.toFixed(2)+' km';
              
          }else{
              new_distance_km	=   distance/1609.34;
              distance_km	=   distance/1609.34;
              distance_km 	=   distance_km.toFixed(2)+' mile';
          }
          
          
          
          var s_number = j+1;
          
          
          if(search_radius){ // radius is in km
            if(new_distance_km <= search_radius)    {
			    result_found = true;
            
                          html += '<div class="store" id="gmap-store-'+store_id+'">';
                                
                                var store_mar = '';
                                
                                if(is_marker_sidebar_enable == 'Yes'){
                                  store_mar += '<img src="'+destinationIcon+'" width="30" style="border-radius: 25%">';
                                }
                                
                                if(is_marker_numbers_enable == 'Yes' && is_marker_sidebar_enable == 'Yes'){
                                  store_mar +='<div class="gmap-list-marker-labels">'+s_number+'</div>';			
                                }else if(is_marker_numbers_enable == 'Yes' && is_marker_sidebar_enable == 'No'){
                                  store_mar +='<div class="gmap-list-number-labels">'+s_number+'</div>';			
                                }
                                
                                html += '<a href="javascript: " onclick="showFromList('+store_id+');" >'+store_mar+store_name+'</a>';
                                html += '<p>'+address+', '+district+', '+postal_code+', '+state+', '+country+'</p>';
                                html += '<div class="left-info">';
                                  if(store_phone) html += '<span> <?php _e("Phone", "MB") ?> '+store_phone+'</span><br>';
                                  if(website) html += '<span> <?php _e("website", "MB") ?> '+website+'</span><br>';
                                  if(store_fax) html += '<span> <?php _e("Fax", "MB") ?> '+store_fax+'</span><br>';
                                  if(distance) html += '<span> <?php _e("Distance", "MB") ?> '+distance_km+'</span><br>';
                                html += '</div>';
                                
                                
                                html += '<div class="detail-link">';					  
                                  	    
                                    html += '<a href = '+href+'><?php _e("View", "MB") ?></a>';
                                    html += '<a href = '+hlink+'><?php _e("website", "MB") ?></a>';
                                  					  					  
                                html += '</div>';
                                
                                
                          html += '<div class="clear"></div>';
                          html += '</div>';
                          
                          //document.write(html);
                          creatmarkers(j, locations[j]);
            }
            
          } else {
            
            
                              html += '<div class="store" id="gmap-store-'+store_id+'">';
                                
                                var store_mar = '';
                                
                                if(is_marker_sidebar_enable == 'Yes'){
                                  store_mar += '<img src="'+destinationIcon+'" width="30" style="border-radius: 25%">';
                                }
                                
                                if(is_marker_numbers_enable == 'Yes' && is_marker_sidebar_enable == 'Yes'){
                                  store_mar +='<div class="gmap-list-marker-labels">'+s_number+'</div>';			
                                }else if(is_marker_numbers_enable == 'Yes' && is_marker_sidebar_enable == 'No'){
                                  store_mar +='<div class="gmap-list-number-labels">'+s_number+'</div>';			
                                }
                                
                                html += '<a href="javascript: " onclick="showFromList('+store_id+');" >'+store_mar+store_name+'</a>';
                                html += '<p>'+address+', '+district+', '+postal_code+', '+state+', '+country+'</p>';
                                html += '<div class="left-info">';
                                  if(store_phone) html += '<span> <?php _e("Phone", "MB") ?> '+store_phone+'</span><br>';
                                  if(store_fax) html += '<span> <?php _e("Fax", "MB") ?> '+store_fax+'</span><br>';

                                  if(website) html += '<span> <?php _e("website", "MB") ?> '+website+'</span><br>';

                                  if(distance) html += '<span> <?php _e("Distance", "MB") ?> '+distance_km+'</span><br>';
                                html += '</div>';
                                
                                
                                html += '<div class="detail-link">';					  
                                  	    
                                    html += '<a href = '+href+'><?php _e("View", "MB") ?></a>';			  
                                html += '</div>';
                                
                                
                          html += '<div class="clear"></div>';
                          html += '</div>';
                          
                          
                          creatmarkers(j, locations[j]);
            
            
          }
         
      } 
      
      if(search_radius && !result_found){
        
       document.getElementById('result').innerHTML = '<div class="store"><p><?php _e("No Result !", "MB") ?></p></div>';
      
      }else{
        
        document.getElementById('result').innerHTML = html;
        
      }
      
      jQuery('#scrollbar1').tinyscrollbar_update();
      
}

function unsetAllMarkers(){
	  
	  
  for(var i = 0; i<locations.length; i++){
      
      if(MarkerArr[locations[i]['store_id']]){
        
          MarkerArr[locations[i]['store_id']].setMap(null);		  
      }
    
  }
  
    
}

function showFromList(storeid){
	  
		
      unsetAllPopups();
      unsetAllMarkers();
      
      if(is_direction_on){
        initialize();
      }
      
      if(MarkerArr[storeid] && PopupArr[storeid]){ 
        map.setZoom(10);
        MarkerArr[storeid].setMap(map);
        PopupArr[storeid].open(map, MarkerArr[storeid]);
        
        
      }else{
        alert(storeid);
      }
        
}

function unsetAllMarkers(){
	  
	  
  for(var i = 0; i<locations.length; i++){
      
      if(MarkerArr[locations[i]['store_id']]){
        
          MarkerArr[locations[i]['store_id']].setMap(null);		  
      }
    
  }
  
    
}

function unsetAllPopups(){
  
  
      for(var i = 0; i<locations.length; i++){
                          
          if(PopupArr[locations[i]['store_id']]){
              PopupArr[locations[i]['store_id']].close();
          }
        
      }
  
}

function mouseoverFromList(store_id){
	  
		
      if(is_hover_marker_change == 1){
        
        if(MarkerArr[store_id]){
          
            MarkerArr[store_id].setIcon(hoverIcon);
          
        }
      }
        
}


function mouseoutFromList(store_id){
  
        
      if(is_hover_marker_change == 1){
        
        if(MarkerArr[store_id]){
          
            MarkerArr[store_id].setIcon(destinationIcon);
          
        }
      }
      
}

function popupCreate(marker, loc_detail, i) { 
	  
        var boxText ="";
        var s_distance = "";
        var s_image = "";
        var s_phone = '';
        var s_fax = '';
        var s_state= ',';
        
       
        if(loc_detail['phone'] != ''){
            s_phone = '<span><b><?php _e("Phone", "MB") ?></b></span> '+ loc_detail['phone']+'<br />';
        }
        if(loc_detail['fax'] != ''){
            s_fax = '<span><b><?php _e("Fax", "MB") ?></b></span> '+ loc_detail['fax']+'<br />';
        }
         if(loc_detail['website'] != ''){
            s_fax = '<span><link href="'+ loc_detail['website']+'"><?php _e("website", "MB") ?></link></span> <br />';
        }
        if(loc_detail['state'] == '' || loc_detail['state'] == null){
            s_state = ', ';
        }else{
            s_state = ', '+ loc_detail['state']+',';				    
        }
        
        if(loc_detail['store_image']){
             s_image_path = loc_detail['store_image'];
              s_image = "<div class='store-thumb'><img src='"+s_image_path+"' width='100'></div>";
          }
        var storeID = loc_detail['store_id'];
        var detail_link = '<a class="popup-detail-link" href=<?php echo the_permalink(); ?>?store_id='+storeID+'> ...</a>';
        
        
        if(loc_detail['store_description'].length > 150){
            s_desc = loc_detail['store_description'].substring(0, 150)+detail_link;
            
        }else{
            s_desc = loc_detail['store_description'];
        }
        
        
        
        
        
        boxText = document.createElement("div");
        boxText.innerHTML ='<div id="pop_div" class="map_popup_top1"><h1>'+loc_detail['store_name']+'</h1>'+ s_desc + '<div class="store-more-info">'+s_phone+s_fax+'<span><b><?php _e("Address", "MB") ?></b></span> '+ loc_detail['address'] +', '+ loc_detail['zip_code']+','+ loc_detail['website']+', '+ loc_detail['city']+s_state+ loc_detail['country']+'<br />'+s_distance+'</div>'+s_image+'</div>';
        var box = boxText.innerHTML + '<div class="map_popup_bottom"><img src="<?php echo $popup_secondary_image; ?>" alt="" width="290px;" /></div>';
        
        var myOptions = {
                content: box
                ,disableAutoPan: false
                ,maxWidth: 0
                ,pixelOffset: new google.maps.Size(-150, -320)
                ,zIndex: null
                ,boxStyle: { 
                        background: "url('../css/map_popup_top.png') no-repeat"
                        ,opacity: 1
                        ,width: "295px"
                        ,height:"250px"
                }
                ,closeBoxMargin: "-13px -5px 2px"
                ,closeBoxURL: "<?php echo MB_URL; ?>front/image/close_black.png"
                ,close_onmouseover :"<?php echo MB_URL; ?>front/image/close_icon.png"
                ,close_onmouseout :"<?php echo MB_URL; ?>front/image/close_black.png"
                ,infoBoxClearance: new google.maps.Size(1, 1)
                ,isHidden: false
                ,pane: "floatPane"
                ,enableEventPropagation: true
        };
        var ib = [];
        ib = new InfoBox(myOptions);
        PopupArr[loc_detail['store_id']] = ib;
}


function clientPosition(){
	  
    jQuery('#Map').gmap({ 'zoom':<?php echo $this->module_settings['map_zoom']; ?>,'center':new google.maps.LatLng(<?php echo $this->module_settings['standard_latitude']; ?>,<?php echo $this->module_settings['standard_longitude'] ?>), 'callback': function($) {
                  
                  
                  var options = {
                    enableHighAccuracy: true,
                    timeout: 5000,
                    maximumAge: 0
                  };
                 
                  navigator.geolocation.getCurrentPosition(success_p, error_p, options);


                  
    }});

}

function success_p(pos) {
  var crd = pos.coords;

  clientlat = crd.latitude;
  clientlon = crd.longitude;
  var clientlatlng = new google.maps.LatLng(clientlat, clientlon); 
  
  user_position_search = clientlatlng;
  findDistanceFromClientToStores(user_position_search);
  jQuery('#scrollbar1').tinyscrollbar_update();

};

function error_p(err) {
  alert('Only secure origins are allowed by your browser.');
};


function findDistanceFromClientToStores(to_latlng, search_radius){ 
	  
	    
          if(to_latlng){
            
            for (j = 0; j < locations.length; j++) {
                
                var latlng = new google.maps.LatLng(locations[j]['latitude'], locations[j]['longitude']);
                
                var distance_m = google.maps.geometry.spherical.computeDistanceBetween(latlng,to_latlng);
                stores_from_distances[j] = distance_m;
                locations[j]['distance'] = stores_from_distances[j];
                
            }
            
            //sort locations by distance
            locations.sort(function(a,b){return (a['distance'] < b['distance'] ? -1 : (a['distance'] > b['distance'] ? 1 : 0)); });
            generateStoreList(search_radius);
            
            
          }

}




function showStoresByLatLang(arr, to_latlng){ 
	    
	    
      var is_matched = false;
      initialize();
      unsetAllMarkers(); 
      document.getElementById('result').innerHTML = '';
        
      resulting_stores = new Array();  
       
      
          
      for (j = 0; j < locations.length; j++) {
        
          for(var i=0; i<arr.length; i++){	
                          var cur_latlang = arr[i]['latitude']+','+arr[i]['longitude'];
                          var s_latlang = locations[j]['latitude']+','+locations[j]['longitude'];
                          
                          var latlng = new google.maps.LatLng(locations[j]['latitude'], locations[j]['longitude']);
                          
                          if(cur_latlang == s_latlang){					
                                  
                                      is_matched = true;
                                      createSingleStore(j);
                          }
          }
          
      }
      
      
      
      if(is_matched == false){ 
          
          document.getElementById('result').innerHTML = '<div class="store"><p><?php _e("No Result !", "MB") ?></p></div>';		
      }
      
      jQuery('#scrollbar1').tinyscrollbar_update();
  }
  
  
  function createSingleStore(j){
		
      var html = '';
    
      var store_id		=   locations[j]['store_id'];
          var store_name	=   locations[j]['store_name'];
          var address		=   locations[j]['address'];
          var district		=   locations[j]['city'];
          var state		=   locations[j]['state'] ? locations[j]['state'] : '';
          var postal_code	=   locations[j]['zip_code'];
          var website =   locations[j]['website'];
          var country		=   locations[j]['country'];
          var store_phone	=   locations[j]['phone'];
          var store_fax		=   locations[j]['fax'];
          var lat		=   locations[j]['latitude'];
          var long		=   locations[j]['longitude'];
          var distance		=   locations[j]['distance'];
          var href    =   "<?php echo the_permalink(); ?>?store_id="+locations[j]['href'];var hlink    =   "<?php echo the_permalink(); ?>?store_id="+locations[j]['website']; 
      
      var distance_km	=   distance/1000;
          distance_km	=   distance_km.toFixed(2)+' km';
      
      if(map_config_distance == 'mile'){
          
          distance_km	= distance/1609.34;
          distance_km	= distance_km.toFixed(2)+' mile';	      
      }
      
      
      var s_number = j+1;		  
      
                      html += '<div class="store" id="gmap-store-'+store_id+'">';
                                
                                var store_mar = '';
                                
                                if(is_marker_sidebar_enable == 'Yes'){
                                  store_mar += '<img src="'+destinationIcon+'" width="50">';
                                }
                                
                                if(is_marker_numbers_enable == 'Yes' && is_marker_sidebar_enable == "Yes"){
                                  store_mar +='<div class="gmap-list-marker-labels">'+s_number+'</div>';			
                                }else if(is_marker_numbers_enable == 'Yes' && is_marker_sidebar_enable == 'No'){
                                  store_mar +='<div class="gmap-list-number-labels">'+s_number+'</div>';			
                                }
                                
                                html += '<a href="javascript: " onclick="showFromList('+store_id+');" >'+store_mar+store_name+'</a>';
                                html += '<p>'+address+', '+district+', '+postal_code+','+website+' ,'+state+', '+country+'</p>';
                                html += '<div class="left-info">';
                                  if(store_phone) html += '<span> <?php _e("Phone", "MB") ?> '+store_phone+'</span><br>';

                                  html += '<div class="left-info">';
                                  if(website) html += '<span> <?php _e("website", "MB") ?> '+website+'</span><br>';

                                  if(store_fax) html += '<span> <?php _e("Fax", "MB") ?> '+store_fax+'</span><br>';
                                  if(distance) html += '<span> <?php _e("Distance", "MB") ?> '+distance_km+'</span><br>';
                                html += '</div>';
                                
                                
                                html += '<div class="detail-link">';					  
                                  	    
                                    html += '<a href = '+href+'><?php _e("View", "MB") ?></a>';

                                  					  					  
                                html += '</div>';
                                 
                                
                          html += '<div class="clear"></div>';
                          html += '</div>';
                      
                      
                      
                      creatmarkers(j, locations[j]);
      
      document.getElementById('result').innerHTML = document.getElementById('result').innerHTML+html;	  
}

function calculateRoute(storeid) {
	  
      var start = '';
      
      if(MarkerArr[storeid]){
        
        start = user_position_search;
        var stop = MarkerArr[storeid].getPosition();
        getDirection(stop, start);
            
      }
      
  }
  
  function getDirection(stop_latlng, start) {
	  
      var stop = stop_latlng;
          var map;
          var directionsDisplay;
          var directionsService;
          var stepDisplay;
          var markerArray = [];
          
          directionsService = new google.maps.DirectionsService();
          
          /******  Standard Latitude and Longitude from Config  ********/
          
            // Create a map and center .
            var india = new google.maps.LatLng(<?php echo $this->module_settings['standard_latitude']; ?>,<?php echo $this->module_settings['standard_longitude']; ?>);
            var myOptions = {
              zoom: <?php echo $this->module_settings['map_zoom']; ?>,
              mapTypeId: google.maps.MapTypeId.ROADMAP,
              center: india
            }
            
            
            
            map = new google.maps.Map(document.getElementById("Map"), myOptions);
          
            // Create a renderer for directions and bind it to the map.
            var rendererOptions = {
              map: map
            }
            directionsDisplay = new google.maps.DirectionsRenderer(rendererOptions)
          
            // Instantiate an info window to hold step text.
            stepDisplay = new google.maps.InfoWindow();
          
          
          
          var request = {
              origin:start, 
              destination:stop,
              travelMode: google.maps.DirectionsTravelMode.DRIVING
          };
            // Route the directions and pass the response to a
            // function to create markers for each step.
            directionsService.route(request, function(response, status) { 
              if (status == google.maps.DirectionsStatus.OK) {
                //updateMessage(1 , locations[index]['district']);
                  directionsDisplay.setDirections(response);
                  is_direction_on = true;
                  var route = response.routes[0];
                  
                  var 	href = 'saddr=';
                          href +=route.legs[0].start_address;
                          href +='&daddr=';
                          href +=route.legs[0].end_address;				
                          href +='&pw=2';
                  var nhref ='http://maps.google.com/maps?'+href;
                  var MainDiv = document.createElement('div');
                  var directinControl = new DirectionControl(MainDiv, map , nhref);
                  
                  MainDiv.index = 1;
                  map.controls[google.maps.ControlPosition.TOP_RIGHT].push(MainDiv);
              } else if (status == google.maps.DirectionsStatus.ZERO_RESULTS) {
              alert('error<?php ?>');
              initialize();
              } else {
                  
                  return;
                  initialize();
            }
            });
          
  }
  
  function DirectionControl(maincontrolDiv, map , href) {
		
          maincontrolDiv.style.padding = '5px';
        
          var directioncontrolUI = document.createElement('div');
          directioncontrolUI.style.backgroundColor = 'white';
          directioncontrolUI.style.borderStyle = 'solid';
          directioncontrolUI.style.borderWidth = '1px';
          directioncontrolUI.style.cursor = 'pointer';
          directioncontrolUI.style.textAlign = 'center';
          directioncontrolUI.title = 'Click here to print directions';
          maincontrolDiv.appendChild(directioncontrolUI);
        
          var controlText = document.createElement('div');
          controlText.style.fontFamily = 'Arial,sans-serif';
          controlText.style.fontSize = '12px';
          controlText.style.padding = '2px 4px';
          controlText.innerHTML = '<strong>Print directions<strong>';
          directioncontrolUI.appendChild(controlText);
        
          google.maps.event.addDomListener(directioncontrolUI, 'click', function() {
            window.open(href); 
          });
  }
  
  function clearSearchArea(){
	  
      //clear the field of search
      document.getElementById('s_address').value = '';
      
      //regenerate all the options
      initialize();
      
    }
    
  function searchAddress(search_qry_create){
	  
    var qrystr = document.getElementById('s_address').value;
    
        if(qrystr == '' || !qrystr || qrystr == 'Enter a location'){
                
                alert('<?php _e("Enter Address", "MB") ?>');
                return false;
        }
    initialize();      
    var s_radius = document.getElementById('store_radius').value;
    
    
        var  pgeocoder = new google.maps.Geocoder();
        pgeocoder.geocode( { 'address': qrystr}, function(results, status) { 
              if (status == google.maps.GeocoderStatus.OK) {
                      var qryLatlng = results[0].geometry.location;
                      user_position_search = qryLatlng;
                      findDistanceFromClientToStores(user_position_search,s_radius);
                      map.setCenter(qryLatlng);
                      jQuery('#scrollbar1').tinyscrollbar_update();
              }
        });	  
  }
  

    
    function removeItem(array, item){
        for(var i in array){
            if(array[i]==item){
                array.splice(i,1);
                break;
                }
        }
    }
    
    function in_array(array, id) {
          
          for(var i=0;i<array.length;i++) {
              if(array[i] === id) {
                  return true;
              }
          }
          return false;
    }
    
  
	 

if(!user_position_search)
  {
    clientPosition();	  
  }

initialize();
</script>
