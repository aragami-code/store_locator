<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php
require_once MB_PLUGIN_DIR . 'admin/manage-stores.php';

$manage_stores = new MBs_List_Stores();
$manage_stores->prepare_items(); 



?>

<div id="MB-store-overview" class="wrap">
    <h2><?php _e(' Store Locator'); ?></h2>
    <h3><?php _e('Manage the Stores'); ?> 
	    <a class="add-new-h2" href="admin.php?page=MBs-add-store">
	    	<?php _e('Add store'); ?>
	    </a>
    </h3>
    <div class="up">
        <form method="post">
            <?php $manage_stores->views() ?>
            <?php $manage_stores->search_box( 'Search', 'search_id' ); ?>
        </form>
    </div>
    <div class="errr">
        <?php settings_errors(); ?>
    </div>
    
    <form method="post">
        <?php $manage_stores->display(); ?>
    </form>
    
</div>

