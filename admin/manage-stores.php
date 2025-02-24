<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
if ( !class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
if ( !class_exists( 'Store_Locator_Admin' ) ) {
    require_once MB_PLUGIN_DIR . 'admin/store-locator-admin.php';
}

class MBs_List_Stores extends WP_List_Table {
    private $_per_page;
    function __construct(){
        global $status, $page;
                
        parent::__construct( array(
            'singular'  => 'store',   
            'plural'    => 'stores',    
            'ajax'      => false        
        ) );
        
        $this->_per_page = $this->get_per_page();

            
    }




    function get_per_page() {
        
        $user     = get_current_user_id();
        $screen   = get_current_screen();
        $option   = $screen->get_option( 'per_page', 'option' );
        $per_page = get_user_meta( $user, $option, true );
        
        if ( empty( $per_page ) || $per_page < 1 ) {
            $per_page = $screen->get_option( 'per_page', 'default' );
        }
        
        return $per_page;
    }

    function no_items() {
        _e( 'No store found', 'MB' );
    }

    function column_default($item, $column_name){ 
        switch($column_name){
            case 'thumb':
            return "<a href='?page=".$_REQUEST['page']."&action=edit&store_id=".$item->store_id."'><img src='".$item->store_image."' width='50'>";
            case 'store_name':
            return $item->store_name;
            case 'country':
            return $item->country;
            case 'city':
            return $item->city;
            case 'address':
            return $item->address;
            case 'status':
                return ( $item->status ) ? __( 'Active', 'MB' ) : __( 'Inactive', 'MB' );
            default:
                return $item->store_id;
        }

    }

    function ed($id)
    {
        $data = $this->getStore($id);
        if(isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'excerpt')
        {
            return substr($data->store_description,0,100);
        } 
        elseif(isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'list')
        {
            return '';
        }
        else
        {
            return '';
        }
    }

    function column_store_name($item){
        
        //Build row actions
        $actions = array(
            'edit'      => sprintf('<a href="?page=%s&action=%s&store_id=%s">Edit</a>',$_REQUEST['page'],'edit',$item->store_id),
            
            'delete'    => sprintf('<a href="?page=%s&action=%s&store_id=%s&delete_nonce" class="MB_del">Delete</a>
                <input name="store_id" type="hidden" value="' . $item->store_id . '" />
                <input name="delete_nonce" type="hidden" value="' . wp_create_nonce( 'delete_nonce_'.$item->store_id ) . '" />
            ',$_REQUEST['page'],'delete',$item->store_id),
            'view'    => sprintf('<a href="'.get_site_url().'/index.php/g-map-store-locator/?store_id=%s">View</a>',$item->store_id),
        );


        
        //Return the title contents
        return sprintf('<strong><a href="?page='.$_REQUEST['page'].'&action=edit&store_id='.$item->store_id.'">%1$s</a></strong><br />
            <span style="color:silver"></span>%3$s
            <span style="color:silver"></span>%4$s',
            /*$1%s*/ $item->store_name,
            /*$2%s*/ $item->store_id,
            $this->ed($item->store_id),
            /*$3%s*/ $this->row_actions($actions)
        );
    }

    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
           $this->_args['singular'],   $item->store_id                //
        );
    }

    function get_sortable_columns() {
        
        $sortable_columns = array(
            'store_name'   => array( 'store_name', false ),
            'country'   => array( 'country', false ),
            'city'   => array( 'city', false ),
            'address'   => array( 'address', false )
            
        );

        return $sortable_columns;
    }

    function get_columns() {
        
        $columns = array(
            'cb'      => '<input type="checkbox" />',
            'thumb'   => "<span class='wc-image tips'></span>",
            'store_name'   => __( 'Store Name', 'MB' ),
            'country'   => __( 'Country', 'MB' ),
            'city'   => __( 'City', 'MB' ),
            'address'   => __( 'Address', 'MB' ),
            'status'  => __( 'Status', 'MB' )
        );

        return $columns;
    }


    function get_bulk_actions() {
        
        $actions = array(
            'delete'     => __( 'Delete', 'wpsl' ),
            'activate'   => __( 'Activate', 'wpsl' ),
            'deactivate' => __( 'Deactivate', 'wpsl' )
        );

        return $actions;
    }
    protected function getAllStores() {
         global $wpdb;

         $result = $wpdb->get_var( "SELECT COUNT(*) AS count FROM $wpdb->MB_stores" );      

         return $result;
    }

    protected function getAllPublished() {
         global $wpdb;

         $result = $wpdb->get_var( "SELECT COUNT(*) AS count, status FROM $wpdb->MB_stores WHERE status = 1" );      

         return $result;
    }

    protected function getAllTrash() {
         global $wpdb;

         $result = $wpdb->get_var( "SELECT COUNT(*) AS count, status FROM $wpdb->MB_stores WHERE status = 0" );      

         return $result;
    }


    function get_views(){
       $views = array();
       $current = ( !empty($_REQUEST['status']) ? $_REQUEST['status'] : 'all');

       //All link
       $class = ($current == 'all' ? ' class="current"' :'');
       $all_url = remove_query_arg('status');
       $views['all'] = "<a href='{$all_url }' {$class} >All <span class='count'>(".$this->getAllStores().")</span></a>";

       //Foo link
       $foo_url = add_query_arg('status','active');
       $class = ($current == 'active' ? ' class="current"' :'');
       $views['active'] = "<a href='{$foo_url}' {$class} >Active <span class='count'>(".$this->getAllPublished().")</span></a>";

       //Bar link
       $bar_url = add_query_arg('status','inactive');
       $class = ($current == 'inactive' ? ' class="current"' :'');
       $views['inactive'] = "<a href='{$bar_url}' {$class} >Inactive <span class='count'>(".$this->getAllTrash().")</span></a>";

       return $views;
    }



    protected function view_switcher( $current_mode ) {
?>
        <input type="hidden" name="mode" value="<?php echo esc_attr( $current_mode ); ?>" />
        <div class="view-switch">
<?php
            foreach ( $this->modes as $mode => $title ) {
                $classes = array( 'view-' . $mode );
                if ( $current_mode == $mode )
                    $classes[] = 'current';
                printf(
                    "<a href='%s' class='%s' id='view-switch-$mode'><span class='screen-reader-text'>%s</span></a>\n",
                    esc_url( add_query_arg( 'mode', $mode ) ),
                    implode( ' ', $classes ),
                    $title
                );
            }
        ?>
        </div>
<?php

    }



    function update_store_status( $store_ids, $status ) { 
        
        global $wpdb;

        if ( $status === 'deactivate' ) {
            $active_status       = 0;
            $success_action_desc = __( 'deactivated', 'MB' );
            $fail_action_desc    = __( 'deactivating', 'MB' );
        } else {
            $active_status       = 1;
            $success_action_desc = __( 'activated', 'MB' );
            $fail_action_desc    = __( 'activating', 'MB' );
        }
        
        $result = $wpdb->query( $wpdb->prepare( "UPDATE $wpdb->MB_stores SET status = %d WHERE store_id IN ( $store_ids )", $active_status ) );    
        
        if ( $result === false ) {
            $state = 'error';
            $msg = sprintf( __( 'There was a problem %s the Store(s), please try again.', 'MB' ), $fail_action_desc );

        } else {
            $state = 'updated';
            $msg = sprintf( __( 'Store(s) successfully %s.', 'MB' ), $success_action_desc );
        } 
        
        add_settings_error ( 'bulk-state', esc_attr( 'bulk-state' ), $msg, $state );
    }
    

    function remove_stores( $store_ids ) {

        global $wpdb;

        $result = $wpdb->query( "DELETE FROM $wpdb->MB_stores WHERE store_id IN ( $store_ids )" );
        
        if ( $result === false ) {
            $state = 'error';
            $msg   = __( 'There was a problem removing the Store(s), please try again.', 'MB' );
        } else {
            $state = 'updated';
            $msg   = __( 'Store(s) successfully removed.' , 'MB' );
        } 
        
        add_settings_error ( 'bulk-remove', esc_attr( 'bulk-remove' ), $msg, $state );
    }
    

    function process_bulk_action() {
        
        if ( !current_user_can( apply_filters( 'MB_capability', 'manage_options' ) ) )
            die( '-1' );
        
        if ( isset( $_POST['_wpnonce'] ) && ! empty( $_POST['_wpnonce'] ) ) {
            $nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
            $action = 'bulk-' . $this->_args['plural'];

            if ( !wp_verify_nonce( $nonce, $action ) )
                wp_die( 'Nope! Security check failed!' );
            
            $action = $this->current_action();

            /* If an action is set continue, otherwise reload the page */
            if ( !empty( $action ) ) {
                $id_list = array();

                foreach ( $_POST['store'] as $store_id ) {
                    $id_list[] = $store_id;
                }

                /* Before checking which type of bulk action to run, we make sure we actually have some ids to process */
                if ( !empty( $id_list ) ) {
                    $store_ids = esc_sql( implode( ',', wp_parse_id_list( $id_list ) ) );

                    switch ( $action ) {
                        case 'activate':
                            $this->update_store_status( $store_ids, 'activate' );
                            break;
                        case 'deactivate':
                            $this->update_store_status( $store_ids, 'deactivate' );
                            break;                 
                         case 'delete':
                            $this->remove_stores( $store_ids );
                            break;                
                    }   
                }
            }
        }
    }
    


    function get_store_list() { 
        
        global $wpdb;
        
        $total_items = 0;
        if(isset($_REQUEST['status']) && $_REQUEST['status']=='active')
        {
            $status = 1;
        }
        if(isset($_REQUEST['status']) && $_REQUEST['status']=='inactive')
        {
            $status = 0;
        } else {
            $status = 1;
        } 
                
        if ( isset( $_POST['s'] ) && ( !empty( $_POST['s'] ) ) ) {
            $search = trim( $_POST['s'] );
            $result = $wpdb->get_results( 
                            $wpdb->prepare( "SELECT store_id, store_name, store_image, country, city, address, status
                                             FROM $wpdb->MB_stores
                                             WHERE status = $status AND store_name LIKE %s", 
                                             '%' . like_escape( $search ). '%', '%' . like_escape( $search ). '%', '%' . like_escape( $search ). '%', '%' . like_escape( $search ). '%', '%' . like_escape( $search ). '%'
                                          ) 
                            );
        } else {
            $orderby   = !empty ( $_GET["orderby"] ) ? esc_sql( $_GET["orderby"] ) : 'store_name';
            $order     = !empty ( $_GET["order"] ) ? esc_sql( $_GET["order"] ) : 'ASC';
            $order_sql = $orderby.' '.$order; 

            $total_items = $wpdb->get_var( "SELECT COUNT(*) AS count, status FROM $wpdb->MB_stores WHERE status = $status" );
            $paged       = !empty ( $_GET["paged"] ) ? esc_sql( $_GET["paged"] ) : '';
            
            if ( empty( $paged ) || !is_numeric( $paged ) || $paged <= 0 ) { 
                $paged = 1; 
            }

            $totalpages = ceil( $total_items / $this->_per_page );
            
            if ( !empty( $paged ) && !empty( $this->_per_page ) ){
                $offset    = ( $paged - 1 ) * $this->_per_page;
                $limit_sql = (int)$offset.',' . (int)$this->_per_page;
            }
            
            $result = $wpdb->get_results( "SELECT store_id, store_name, store_image, country, city, address, status FROM ".$wpdb->MB_stores." WHERE status = $status ORDER BY $order_sql LIMIT $limit_sql" );
        }

        
        $i = 0;
        foreach ( $result as $k => $store_details ) {
            
            
            $i++;
        }
        
        $response = array(
            "data"  => stripslashes_deep( $result ),
            "count" => $total_items
        );
        
        return $response;
    }   

    function prepare_items() {
        
        $columns  = $this->get_columns();
        $hidden   = array();
        $sortable = $this->get_sortable_columns();
        
        $this->process_bulk_action();        
        $response = $this->get_store_list();

        $current_page = $this->get_pagenum();
        $total_items  = $response['count'];
        $view = ( isset($_REQUEST['status']) ? $_REQUEST['status'] : 'all');
        
        
        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $this->_per_page,
            'total_pages' => ceil( $total_items / $this->_per_page ) 
        ) );

        $this->items = $response['data'];
        $this->_column_headers = array( $columns, $hidden, $sortable );       
    }



    public function display_rows_or_placeholder() {
        if ( $this->has_items() ) {
            $this->display_rows();

        } else {
            echo '<tr class="no-items"><td class="colspanchange" colspan="' . $this->get_column_count() . '">';
            $this->no_items();
            echo '</td></tr>';
        }
    }

    protected function getStore($id)
        {
            global $wpdb;
            $result = $wpdb->get_row( 
                            $wpdb->prepare( "SELECT * FROM $wpdb->MB_stores 
                                WHERE store_id = %d", $id
                                          )
                            );

            return $result; 
        }

    public function single_row( $item ) { 
        echo '<tr class="qes2" id=MB-wrrap'.$item->store_id.'>';
            $this->single_row_columns( $item );
        echo '</tr>';
            $store = $this->getStore($item->store_id);
            $cc = new Store_Locator_Admin();
            $countries = $cc->my_add_country_select();
       
    }

    protected function extra_tablenav( $which ) {
        $mode = ( isset($_REQUEST['mode']) ? $_REQUEST['mode'] : 'list');
        $this->view_switcher($mode);
    }

   function display() {
        $singular = $this->_args['singular'];

        $this->display_tablenav( 'top' );
        
    ?>
        <table class="wp-list-table <?php echo implode( ' ', $this->get_table_classes() ); ?>" cellspacing="0">
                <thead>
                <tr>
                        <?php $this->print_column_headers(); ?>
                </tr>
                </thead>

                <tfoot>
                <tr>
                        <?php $this->print_column_headers( false ); ?>
                </tr>
                </tfoot>

                <tbody id="the-list"<?php if ( $singular ) echo " data-wp-lists='list:$singular'"; ?>>
                        <?php $this->display_rows_or_placeholder(); ?>
                        
                </tbody>

        </table>
        <div id="MB-delete-confirmation">
            <p><?php _e( 'Are you sure you want to delete this store?', 'MB' ); ?></p>
            <p>
                <input class="button-primary" type="submit" name="delete" value="<?php _e( 'Delete', 'MB' ); ?>" />
                <input class="button-secondary dialog-cancel" type="reset" value="<?php _e( 'Cancel', 'MB' ); ?>" />
            </p>
        </div>
        <?php
            $this->display_tablenav( 'bottom' );
    }

    /**
     * Send required variables to JavaScript land
     */
    function _js_vars() {

        $args = array(
            'url'    => MB_URL
        );

        printf( "<script type='text/javascript'>var MB_data = %s;</script>\n", json_encode( $args ) );
    }
    
}
