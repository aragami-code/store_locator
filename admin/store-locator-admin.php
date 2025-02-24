<?php 
if ( ! defined( 'ABSPATH' ) ) exit;

class Store_Locator_Admin extends MBStore_Locator
{
	public $attribute_data;
	public $store_data;

	public function __construct() {
		add_action( 'wp_loaded', array( $this, 'init' ) );
		$this->module_settings = $this->get_module_settings();
		add_action( 'plugins_loaded', array($this, 'add_MB_screen_filter' ));
		add_filter('add_country_list',array($this, 'my_add_country_select'));
        
        


	}

    

	public function init() {
		add_action( 'admin_menu', array( $this, 'create_admin_menu' ) );
        add_action( 'wp_ajax_delete_store', array( $this, 'delete_store' ) );
        add_action( 'wp_ajax_quick_edit_store', array( $this, 'quick_edit_store' ) );
        add_action( 'wp_ajax_quick_edit_store_view', array( $this, 'quick_edit_store_view' ) );	
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );	
		add_action( 'admin_enqueue_scripts', 'wp_enqueue_media' ); 
		add_action('wp_ajax__ajax_fetch_custom_list', array($this, '_ajax_fetch_custom_list_callback'));


		
	}



	public function create_admin_menu() {	
		$menu = add_menu_page('MB Store Locator', __( 'MB Store Locator', 'MB' ), apply_filters( 'MB_capability', 'manage_options' ), 'MBs_store-locator', array( $this, 'manage_stores' ) ,plugins_url( 'images/mb.jpg', dirname( __FILE__ ) ), apply_filters( 'MB_menu_position', 7 ) );

		add_submenu_page( 'MBs_store-locator', __( 'Manage Stores', 'MB' ), __( 'Manage', 'MB' ), apply_filters( 'MB_capability', 'manage_options' ), 'MBs_store-locator', array( $this, 'manage_stores' ) );
		add_submenu_page( 'MBs_store-locator', __( 'Add Store', 'MB' ), __( 'Add', 'MB' ), apply_filters( 'MB_capability', 'manage_options' ), 'MBs-add-store', array( $this, 'add_store' ) );
        add_submenu_page( 'MBs_store-locator', __( 'Settings', 'MB' ), __( 'Settings', 'MB' ), 'manage_options', 'MB_settings', array( $this, 'MBs_module_settings' ) );	

        register_setting( 'MB_settings', 'MB_settings', array( $this, 'MBs_settings' ) );
        add_action( "load-$menu", array( $this, 'add_screen_options' ) );

    }

        

    

	public function manage_stores() { 

        $this->store_actions();
        $actions = ( isset( $_GET['action'] ) ) ? $_GET['action'] : '';
        $id = ( isset( $_GET['store_id'] ) ) ? $_GET['store_id'] : '';
        
        switch ( $actions ) {
            case 'edit':
                require_once( MB_PLUGIN_DIR . 'admin/view/edit_store.php' );
                break;
            default:
                require_once( MB_PLUGIN_DIR . 'admin/view/manage_stores.php' );
                break;
        } 
	}

    

	

    public function store_actions() {
		if ( isset( $_REQUEST['MB_actions'] ) ) {
			$this->handle_store_data();
		} 
    }

    

    public function handle_store_data() {
            
        global $wpdb;
        
		if ( !current_user_can( apply_filters( 'MB_capability', 'manage_options' ) ) )
			die( '-1' );
	
		check_admin_referer( 'MB_' . $_POST['MB_actions'] );
        
        $this->store_data = $this->validate_store_data();

		if ( $this->store_data ) {
			
            
           $store_action = ( isset( $_POST['MB_actions'] ) ) ? $_POST['MB_actions'] : '';
            
            switch ( $store_action ) {
                case 'add_new_store':
                    $this->add_new_store();
                    break;
                case 'update_store':
                    $this->update_store(); 
                    break;
            } 
        }
    }

    

	public function validate_store_data() {
            
		$store_data = $_POST['MB'];
		
		if ( empty( $store_data['store_name'] ) || empty( $store_data['country'] ) || empty( $store_data['city'] ) || empty( $store_data['zip_code'] ) || empty( $store_data['address'] ) ) {	
            add_settings_error ( 'validate-store', esc_attr( 'validate-store' ), __( 'Please fill in all the required fields.', 'MB' ), 'error' );  				
		} else {
			return $store_data;
		}
	}

	

        public function admin_scripts() {	
            
            wp_enqueue_script( 'MB_script', plugins_url( '/js/MB_script.js', __FILE__ ), array( 'jquery' ), false );				
        	wp_enqueue_script( 'jquery-ui-dialog' );
        	wp_enqueue_style( 'MB-admin-css', plugins_url( '/css/MB_style.css', __FILE__ ), false );
           
        }

       
        public function add_store() {
            $this->store_actions();
            require_once( MB_PLUGIN_DIR . 'admin/view/add_store.php' ); 
        }

       
        

        

		public function add_new_store() {

            global $wpdb;


            $result = $wpdb->query( 
                        $wpdb->prepare( 
                                "
                                INSERT INTO $wpdb->MB_stores
                                (store_name, store_meta_title, store_meta_keywords, store_meta_description, store_description, country, city,website, state, zip_code, address, 
                                 latitude, longitude, status, phone, fax, store_image)
                                VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
                                ", 
                                $this->store_data['store_name'],
                                $this->store_data['meta_title'],
                                $this->store_data['meta_keywords'],
                                $this->store_data['meta_description'],
                                $this->store_data['store_description'],
                                $this->store_data['country'],
                                $this->store_data['city'],
                                $this->store_data['website'],
                                $this->store_data['state'],
                                $this->store_data['zip_code'],
                                $this->store_data['address'],
                                $this->store_data['latitude'],
                                $this->store_data['longitude'],
                                $this->store_data['status'],
                                $this->store_data['phone'],
                                $this->store_data['fax'],
                                $this->store_data['store_image']
                                
                                )

                           );
			$lastid = $wpdb->insert_id;

				


				

             
            if ( $result === false ) {
                $state = 'error';
                $msg = __( 'There was a problem saving the new store, please try again.', 'MB' );
            } else {
                $_POST = array();
                $state = 'updated';
                $msg = __( 'succesfully added.', 'MB' );
            } 
        
            add_settings_error ( 'add-store', esc_attr( 'add-store' ), $msg, $state );  
		}

        

       

        public function get_store_data($store_id) {
            
             global $wpdb;

             $result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->MB_stores." WHERE store_id = %d", $store_id ));      

             return $result;
        }

        

        
        
       

        public function delete_store() { 
            
            global $wpdb;
            
            $store_id = absint( $_POST['store_id'] );

            if ( !current_user_can( apply_filters( 'MB_capability', 'manage_options' ) ) )
                die( '-1' );
            
            check_ajax_referer( 'delete_nonce_'.$store_id );
                
            $result = $wpdb->query( $wpdb->prepare( "DELETE FROM ".$wpdb->MB_stores." WHERE store_id = %d", $store_id ) );

            if ( $result === false ) {
                wp_send_json_error();
            } else {
                wp_send_json_success();
            } 
        }


       

      

        public function quick_edit_store_view() {
            
            echo $this->single_store_get();
            exit();
        }

        protected function single_store_get()
        {
            
            global $wpdb;
             $store_id = $_POST['store_id'];
             $result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->MB_stores." WHERE store_id = %d", $store_id ));      
             echo json_encode($result);
        }


        public function update_store() {
           
            global $wpdb;
                        
            $result = $wpdb->query($wpdb->prepare("UPDATE ".$wpdb->MB_stores." SET store_name = %s, store_meta_title = %s, store_meta_keywords = %s, 
                store_meta_description = %s, store_description = %s, country = %s, city = %s, state = %s, zip_code = %s, 
                address = %s, latitude = %s, longitude = %s, status = %s, phone = %s, fax = %s, store_image = %s 
                WHERE store_id = %d", $this->store_data['store_name'], $this->store_data['meta_title'], $this->store_data['meta_keywords'], 
                $this->store_data['meta_description'], $this->store_data['store_description'], $this->store_data['country'], $this->store_data['city'], 
                $this->store_data['state'], $this->store_data['zip_code'], $this->store_data['address'], $this->store_data['latitude'], $this->store_data['longitude'], 
                $this->store_data['status'], $this->store_data['phone'], $this->store_data['fax'], $this->store_data['store_image'], $this->store_data['store_id'])); 

                


                
            
            if ( $result === false ) {
                $state = 'error';
                $msg = __( 'There was a problem updating the store, please try again.', 'MB' );
            } else {
                $_POST = array();
                $state = 'updated';
                $msg = __( 'Store updated succesfully.', 'MB' );
            } 
        
            add_settings_error ( 'update-store', esc_attr( 'update-store' ), $msg, $state );
        }

		public function MBs_module_settings() {
			require  MB_PLUGIN_DIR . 'admin/view/settings.php';
		}

		public function MBs_settings() { 

			$def_data = $this->get_module_default_settings();

			if (isset($_POST['MB_module']['page_title']) && $_POST['MB_module']['page_title']!='' )  {
				$output['page_title'] = sanitize_text_field($_POST['MB_module']['page_title']);
			} else {
				$output['page_title'] = $def_data['page_title'];
			}

			if (isset($_POST['MB_module']['meta_keywords']) && $_POST['MB_module']['meta_keywords']!='' )  {
				$output['meta_keywords'] = sanitize_text_field($_POST['MB_module']['meta_keywords']);
			} else {
				$output['meta_keywords'] = $def_data['meta_keywords'];
			}

			if (isset($_POST['MB_module']['meta_description']) && $_POST['MB_module']['meta_description']!='' )  {
				$output['meta_description'] = sanitize_text_field($_POST['MB_module']['meta_description']);
			} else {
				$output['meta_description'] = $def_data['meta_description'];
			}

			if (isset($_POST['MB_module']['page_heading']) && $_POST['MB_module']['page_heading']!='' )  {
				$output['page_heading'] = sanitize_text_field($_POST['MB_module']['page_heading']);
			} else {
				$output['page_heading'] = $def_data['page_heading'];
			}

			if (isset($_POST['MB_module']['page_sub_heading']) && $_POST['MB_module']['page_sub_heading']!='' )  {
				$output['page_sub_heading'] = sanitize_text_field($_POST['MB_module']['page_sub_heading']);
			} else {
				$output['page_sub_heading'] = $def_data['page_sub_heading'];
			}

			if (isset($_POST['MB_module']['text_get_direction_button']) && $_POST['MB_module']['text_get_direction_button']!='' )  {
				$output['text_get_direction_button'] = sanitize_text_field($_POST['MB_module']['text_get_direction_button']);
			} else {
				$output['text_get_direction_button'] = $def_data['text_get_direction_button'];
			}

			

			if (isset($_POST['MB_module']['text_header_link']) && $_POST['MB_module']['text_header_link']!='' )  {
				$output['text_header_link'] = sanitize_text_field($_POST['MB_module']['text_header_link']);
			} else {
				$output['text_header_link'] = $def_data['text_header_link'];
			}

			if (isset($_POST['MB_module']['text_footer_link']) && $_POST['MB_module']['text_footer_link']!='' )  {
				$output['text_footer_link'] = sanitize_text_field($_POST['MB_module']['text_footer_link']);
			} else {
				$output['text_footer_link'] = $def_data['text_footer_link'];
			}

			if (isset($_POST['MB_module']['standard_latitude']) && $_POST['MB_module']['standard_latitude']!='' )  {
				$output['standard_latitude'] = sanitize_text_field($_POST['MB_module']['standard_latitude']);
			} else {
				$output['standard_latitude'] = $def_data['standard_latitude'];
			}

			if (isset($_POST['MB_module']['standard_longitude']) && $_POST['MB_module']['standard_longitude']!='' )  {
				$output['standard_longitude'] = sanitize_text_field($_POST['MB_module']['standard_longitude']);
			} else {
				$output['standard_longitude'] = $def_data['standard_longitude'];
			}

			if (isset($_POST['MB_module']['api_key']) && $_POST['MB_module']['api_key']!='' )  {
				$output['api_key'] = sanitize_text_field($_POST['MB_module']['api_key']);
			} else {
				$output['api_key'] = $def_data['api_key'];
			}

			if (isset($_POST['MB_module']['marker_image']) && $_POST['MB_module']['marker_image']!='' )  {
				$output['marker_image'] = sanitize_text_field($_POST['MB_module']['marker_image']);
			} else {
				$output['marker_image'] = $def_data['marker_image'];
			}

			if (isset($_POST['MB_module']['enable_marker_numbers']) && $_POST['MB_module']['enable_marker_numbers']!='' )  {
				$output['enable_marker_numbers'] = sanitize_text_field($_POST['MB_module']['enable_marker_numbers']);
			} else {
				$output['enable_marker_numbers'] = $def_data['enable_marker_numbers'];
			}

			if (isset($_POST['MB_module']['enable_sidebar_markers']) && $_POST['MB_module']['enable_sidebar_markers']!='' )  {
				$output['enable_sidebar_markers'] = sanitize_text_field($_POST['MB_module']['enable_sidebar_markers']);
			} else {
				$output['enable_sidebar_markers'] = $def_data['enable_sidebar_markers'];
			}

			if (isset($_POST['MB_module']['map_zoom']) && $_POST['MB_module']['map_zoom']!='' )  {
				$output['map_zoom'] = sanitize_text_field($_POST['MB_module']['map_zoom']);
			} else {
				$output['map_zoom'] = $def_data['map_zoom'];
			}

			if (isset($_POST['MB_module']['map_distance']) && $_POST['MB_module']['map_distance']!='' )  {
				$output['map_distance'] = sanitize_text_field($_POST['MB_module']['map_distance']);
			} else {
				$output['map_distance'] = $def_data['map_distance'];
			}

			

			if (isset($_POST['MB_module']['enable_search_by_address']) && $_POST['MB_module']['enable_search_by_address']!='' )  {
				$output['enable_search_by_address'] = sanitize_text_field($_POST['MB_module']['enable_search_by_address']);
			} else {
				$output['enable_search_by_address'] = $def_data['enable_search_by_address'];
			}

			

			if (isset($_POST['MB_module']['enable_header_link']) && $_POST['MB_module']['enable_header_link']!='' )  {
				$output['enable_header_link'] = sanitize_text_field($_POST['MB_module']['enable_header_link']);
			} else {
				$output['enable_header_link'] = $def_data['enable_header_link'];
			}

			if (isset($_POST['MB_module']['enable_footer_link']) && $_POST['MB_module']['enable_footer_link']!='' )  {
				$output['enable_footer_link'] = sanitize_text_field($_POST['MB_module']['enable_footer_link']);
			} else {
				$output['enable_footer_link'] = $def_data['enable_footer_link'];
			}

			return $output;

		}

		

        public function add_screen_options() { 
            $option = 'per_page';
            $args = array(
                'label'   => __( 'Per Page Items', 'MB' ),
                'default' => 10,
                'option'  => 'MB_per_page'
            );

            add_screen_option( $option, $args );
        }

		public function add_MB_screen_filter() { 
		    add_filter( 'set-screen-option', array($this, 'set_MB_screen_option', 10, 3 ));
		}

		public function set_MB_screen_option( $status, $option, $value ) { 
    
		    if ( 'MB_per_page' == $option ) return $value;
		 
		    return $status;
		}

		

		function my_add_country_select(){
    $_countries = array(
      "AF" => "Afghanistan",
      "AL" => "Albania",
      "DZ" => "Algeria",
      "AS" => "American Samoa",
      "AD" => "Andorra",
      "AO" => "Angola",
      "AI" => "Anguilla",
      "AQ" => "Antarctica",
      "AG" => "Antigua And Barbuda",
      "AR" => "Argentina",
      "AM" => "Armenia",
      "AW" => "Aruba",
      "AU" => "Australia",
      "AT" => "Austria",
      "AZ" => "Azerbaijan",
      "BS" => "Bahamas",
      "BH" => "Bahrain",
      "BD" => "Bangladesh",
      "BB" => "Barbados",
      "BY" => "Belarus",
      "BE" => "Belgium",
      "BZ" => "Belize",
      "BJ" => "Benin",
      "BM" => "Bermuda",
      "BT" => "Bhutan",
      "BO" => "Bolivia",
      "BA" => "Bosnia And Herzegowina",
      "BW" => "Botswana",
      "BV" => "Bouvet Island",
      "BR" => "Brazil",
      "IO" => "British Indian Ocean Territory",
      "BN" => "Brunei Darussalam",
      "BG" => "Bulgaria",
      "BF" => "Burkina Faso",
      "BI" => "Burundi",
      "KH" => "Cambodia",
      "CM" => "Cameroon",
      "CA" => "Canada",
      "CV" => "Cape Verde",
      "KY" => "Cayman Islands",
      "CF" => "Central African Republic",
      "TD" => "Chad",
      "CL" => "Chile",
      "CN" => "China",
      "CX" => "Christmas Island",
      "CC" => "Cocos (Keeling) Islands",
      "CO" => "Colombia",
      "KM" => "Comoros",
      "CG" => "Congo",
      "CD" => "Congo, The Democratic Republic Of The",
      "CK" => "Cook Islands",
      "CR" => "Costa Rica",
      "CI" => "Cote D'Ivoire",
      "HR" => "Croatia (Local Name: Hrvatska)",
      "CU" => "Cuba",
      "CY" => "Cyprus",
      "CZ" => "Czech Republic",
      "DK" => "Denmark",
      "DJ" => "Djibouti",
      "DM" => "Dominica",
      "DO" => "Dominican Republic",
      "TP" => "East Timor",
      "EC" => "Ecuador",
      "EG" => "Egypt",
      "SV" => "El Salvador",
      "GQ" => "Equatorial Guinea",
      "ER" => "Eritrea",
      "EE" => "Estonia",
      "ET" => "Ethiopia",
      "FK" => "Falkland Islands (Malvinas)",
      "FO" => "Faroe Islands",
      "FJ" => "Fiji",
      "FI" => "Finland",
      "FR" => "France",
      "FX" => "France, Metropolitan",
      "GF" => "French Guiana",
      "PF" => "French Polynesia",
      "TF" => "French Southern Territories",
      "GA" => "Gabon",
      "GM" => "Gambia",
      "GE" => "Georgia",
      "DE" => "Germany",
      "GH" => "Ghana",
      "GI" => "Gibraltar",
      "GR" => "Greece",
      "GL" => "Greenland",
      "GD" => "Grenada",
      "GP" => "Guadeloupe",
      "GU" => "Guam",
      "GT" => "Guatemala",
      "GN" => "Guinea",
      "GW" => "Guinea-Bissau",
      "GY" => "Guyana",
      "HT" => "Haiti",
      "HM" => "Heard And Mc Donald Islands",
      "VA" => "Holy See (Vatican City State)",
      "HN" => "Honduras",
      "HK" => "Hong Kong",
      "HU" => "Hungary",
      "IS" => "Iceland",
      "IN" => "India",
      "ID" => "Indonesia",
      "IR" => "Iran (Islamic Republic Of)",
      "IQ" => "Iraq",
      "IE" => "Ireland",
      "IL" => "Israel",
      "IT" => "Italy",
      "JM" => "Jamaica",
      "JP" => "Japan",
      "JO" => "Jordan",
      "KZ" => "Kazakhstan",
      "KE" => "Kenya",
      "KI" => "Kiribati",
      "KP" => "Korea, Democratic People's Republic Of",
      "KR" => "Korea, Republic Of",
      "KW" => "Kuwait",
      "KG" => "Kyrgyzstan",
      "LA" => "Lao People's Democratic Republic",
      "LV" => "Latvia",
      "LB" => "Lebanon",
      "LS" => "Lesotho",
      "LR" => "Liberia",
      "LY" => "Libyan Arab Jamahiriya",
      "LI" => "Liechtenstein",
      "LT" => "Lithuania",
      "LU" => "Luxembourg",
      "MO" => "Macau",
      "MK" => "Macedonia, Former Yugoslav Republic Of",
      "MG" => "Madagascar",
      "MW" => "Malawi",
      "MY" => "Malaysia",
      "MV" => "Maldives",
      "ML" => "Mali",
      "MT" => "Malta",
      "MH" => "Marshall Islands",
      "MQ" => "Martinique",
      "MR" => "Mauritania",
      "MU" => "Mauritius",
      "YT" => "Mayotte",
      "MX" => "Mexico",
      "FM" => "Micronesia, Federated States Of",
      "MD" => "Moldova, Republic Of",
      "MC" => "Monaco",
      "MN" => "Mongolia",
      "MS" => "Montserrat",
      "MA" => "Morocco",
      "MZ" => "Mozambique",
      "MM" => "Myanmar",
      "NA" => "Namibia",
      "NR" => "Nauru",
      "NP" => "Nepal",
      "NL" => "Netherlands",
      "AN" => "Netherlands Antilles",
      "NC" => "New Caledonia",
      "NZ" => "New Zealand",
      "NI" => "Nicaragua",
      "NE" => "Niger",
      "NG" => "Nigeria",
      "NU" => "Niue",
      "NF" => "Norfolk Island",
      "MP" => "Northern Mariana Islands",
      "NO" => "Norway",
      "OM" => "Oman",
      "PK" => "Pakistan",
      "PW" => "Palau",
      "PA" => "Panama",
      "PG" => "Papua New Guinea",
      "PY" => "Paraguay",
      "PE" => "Peru",
      "PH" => "Philippines",
      "PN" => "Pitcairn",
      "PL" => "Poland",
      "PT" => "Portugal",
      "PR" => "Puerto Rico",
      "QA" => "Qatar",
      "RE" => "Reunion",
      "RO" => "Romania",
      "RU" => "Russian Federation",
      "RW" => "Rwanda",
      "KN" => "Saint Kitts And Nevis",
      "LC" => "Saint Lucia",
      "VC" => "Saint Vincent And The Grenadines",
      "WS" => "Samoa",
      "SM" => "San Marino",
      "ST" => "Sao Tome And Principe",
      "SA" => "Saudi Arabia",
      "SN" => "Senegal",
      "SC" => "Seychelles",
      "SL" => "Sierra Leone",
      "SG" => "Singapore",
      "SK" => "Slovakia (Slovak Republic)",
      "SI" => "Slovenia",
      "SB" => "Solomon Islands",
      "SO" => "Somalia",
      "ZA" => "South Africa",
      "GS" => "South Georgia, South Sandwich Islands",
      "ES" => "Spain",
      "LK" => "Sri Lanka",
      "SH" => "St. Helena",
      "PM" => "St. Pierre And Miquelon",
      "SD" => "Sudan",
      "SR" => "Suriname",
      "SJ" => "Svalbard And Jan Mayen Islands",
      "SZ" => "Swaziland",
      "SE" => "Sweden",
      "CH" => "Switzerland",
      "SY" => "Syrian Arab Republic",
      "TW" => "Taiwan",
      "TJ" => "Tajikistan",
      "TZ" => "Tanzania, United Republic Of",
      "TH" => "Thailand",
      "TG" => "Togo",
      "TK" => "Tokelau",
      "TO" => "Tonga",
      "TT" => "Trinidad And Tobago",
      "TN" => "Tunisia",
      "TR" => "Turkey",
      "TM" => "Turkmenistan",
      "TC" => "Turks And Caicos Islands",
      "TV" => "Tuvalu",
      "UG" => "Uganda",
      "UA" => "Ukraine",
      "AE" => "United Arab Emirates",
      "UM" => "United States Minor Outlying Islands",
      "US" => "United States",
      "GB" => "United Kingdom",
      "UY" => "Uruguay",
      "UZ" => "Uzbekistan",
      "VU" => "Vanuatu",
      "VE" => "Venezuela",
      "VN" => "Viet Nam",
      "VG" => "Virgin Islands (British)",
      "VI" => "Virgin Islands (U.S.)",
      "WF" => "Wallis And Futuna Islands",
      "EH" => "Western Sahara",
      "YE" => "Yemen",
      "YU" => "Yugoslavia",
      "ZM" => "Zambia",
      "ZW" => "Zimbabwe"
    );

	return $_countries;
}


}

new Store_Locator_Admin();

?>
