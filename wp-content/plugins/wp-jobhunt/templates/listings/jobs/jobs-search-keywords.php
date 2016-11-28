<?php
/*
 *  Check if there is job alert then show '.your-search' div else hide it
 */

global $wp_filter;

$visibility = '';
if ( ! (isset($wp_filter['pre_jobhunt_jobs_listing']) && count($wp_filter['pre_jobhunt_jobs_listing']) > 0 && isset($qrystr) && $qrystr != '' ) ) {

    $visibility = 'style="display: none;"';
}
?>

<div class="your-search" <?php echo $visibility; ?>>
    <?php if ( isset($qrystr) && $qrystr != '' ) { ?>
        <ul class="filtration-tags">
            <?php
            //get all query string
            if ( isset($qrystr) ) {
                $qrystr_arr = getMultipleParameters($qrystr);
                foreach ( $qrystr_arr as $qry_var => $qry_val ) {
                    if ( $qry_val != '' ) {
                        if ( ! is_array($qry_val) )
                            if ( strpos($qry_val, ',') !== FALSE ) {
                                $qry_val = explode(",", $qry_val);
                            }
                        // only if specialism child selected
                        if ( is_array($qry_val) ) {
                            foreach ( $qry_val as $qry_val_var => $qry_val_value ) {
                                if ( $qry_val_value != '' ) {
                                    echo '<li>';
                                    if ( $qry_var == 'specialisms' ) { // only one remove the specialism
                                        if ( strpos($qrystr, ',' . $qry_val_value) !== FALSE ) {
                                            $speciliasim_new_qry = str_replace(',' . $qry_val_value, "", $qrystr);
                                        } else if ( strpos($qrystr, $qry_val_value . ',') !== FALSE ) {
                                            $speciliasim_new_qry = str_replace($qry_val_value . ',', "", $qrystr);
                                        } else {
                                            $speciliasim_new_qry = str_replace($qry_val_value, "", $qrystr);
                                        }

                                        echo '<a href="?' . $speciliasim_new_qry . '">' . str_replace("+", " ", $qry_val_value) . ' <i class="icon-cross3"></i></a>';
                                    } else {
                                        $qrystr1 = str_replace("&" . $qry_var . '[]=' . $qry_val_value, "", $qrystr);
                                        $qrystr1 = str_replace("&" . $qry_var . '=' . $qry_val_value, "", $qrystr);

                                        echo '<a href="?' . $qrystr1 . '">' . str_replace("+", " ", $qry_val_value) . ' <i class="icon-cross3"></i></a>';
                                    }

                                    echo '</li>';
                                }
                            }
                        } else {
                            echo '<li>';
                            if ( $qry_var == 'specialisms' ) { // only remove the 
                                if ( strpos($qrystr, ',' . $qry_val) !== FALSE ) {
                                    $speciliasim_new_qry = str_replace(',' . $qry_val, "", $qrystr);
                                } elseif ( strpos($qrystr, $qry_val . ",") !== FALSE ) {
                                    $speciliasim_new_qry = str_replace($qry_val . ",", "", $qrystr);
                                } else {
                                    $speciliasim_new_qry = str_replace($qry_val, "", $qrystr);
                                }
                                echo '<a href="?' . $speciliasim_new_qry . '">' . str_replace("+", " ", $qry_val) . ' <i class="icon-cross3"></i></a>';
                            } else {
                                echo '<a href="' . cs_remove_qrystr_extra_var($qrystr, $qry_var) . '">' . str_replace("+", " ", $qry_val) . ' <i class="icon-cross3"></i></a>';
                            }
                            echo '</li>';
                        }
                    }
                }
            }
            ?>
        </ul>
        <a class="clear-tags" href="<?php echo esc_url(get_permalink()); ?>"><?php _e('Clear all', 'jobhunt') ?></a>
    <?php } ?>
</div>
<?php
if ( isset($a['cs_job_alert_button']) && $a['cs_job_alert_button'] == 'enable' ) {
    // Invoke all hooks attached before listing
    do_action('pre_jobhunt_jobs_listing');
}