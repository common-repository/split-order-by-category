<?php 
// Save/Update configuration value
global $wpdb;
if(sanitize_text_field(!empty($_POST['submit']))){
	  $configVal = sanitize_text_field($_POST['split_by_cat_falg']);
	  $splitorderproCondition = sanitize_text_field($_POST['splitordercategory']);
	  $optionVal = get_option( 'split_by_cat_falg' );
	  $option_name = 'split_by_cat_falg' ;
	  $option_name_split_order = 'splitordercategory' ;
      $new_value = $configVal;
      update_option( $option_name, $new_value );
      update_option( $option_name_split_order, $splitorderproCondition );
     echo "<div class='form-save-msg'>Changes Saved!</div>";
}
  $optionVal = get_option( 'split_by_cat_falg' );
  $splitorderpro = get_option( 'splitordercategory' );
?>

 <h1>General Configuration</h1>
    <div class="row">
        <div class="form-group">
            <form action="" method="post">
                <div><label for="sort" class="col-sm-2 control-label"> Enable split order </label>
                    <select class="form-control" name="split_by_cat_falg" id="sort">
                        <option value="no" <?php
                        if ($optionVal == 'no') {
                            echo 'selected';
                        }
                        ?>>No</option>
                        <option value="yes" <?php
                        if ($optionVal == 'yes') {
                            echo 'selected';
                        }
                        ?>>Yes</option>
                    </select> 
                </div> 
                <br>
                <div>

                    <label for="sort" class="col-sm-2 control-label"> SplitOrderPro Conditions </label>
                    <select class="form-control" name="splitordercategory" id="sort">
                        <option value="default" <?php
                        if ($splitorderpro == 'default') {
                            echo 'selected';
                        }
                        ?>>Default</option>
                     
						

                    </select> 
                    
                    <input type="submit" name="submit" value="save config">
                </div>
            </form>
        </div>
    </div>