<?php
/*
 *
 * Employer Top view Searchbox
 * 
 *
 */
global $cs_plugin_options, $cs_form_fields2;
?>
<div class="cs-ag-search user-search">
    <ul class="filter-list">
        <li><a href="<?php echo esc_url(cs_remove_qrystr_extra_var($qrystr, 'alphanumaric')); ?>"><?php _e("All", "jobhunt"); ?></a></li>
        <li><a href="?alphanumaric=numeric">#</a></li> 
        <?php
        $cs_job_user_filter_character = isset($cs_plugin_options['cs_job_user_filter_character']) ? $cs_plugin_options['cs_job_user_filter_character']:'';
        $alphas_arr = explode(",", $cs_job_user_filter_character);
        $query_str_anchor_url = "";
        foreach ($alphas_arr as $char) {
            $query_str_anchor_url = cs_remove_qrystr_extra_var($qrystr, 'alphanumaric');
            if (cs_remove_qrystr_extra_var($qrystr, 'alphanumaric') != '?') {
                $query_str_anchor_url .= '&';
            }$query_str_anchor_url .='alphanumaric=' . esc_html($char);
            ?>
            <li>
                <a href="<?php echo esc_url($query_str_anchor_url); ?>" class="<?php echo esc_html($char); ?>">
                    <?php echo esc_html($char); ?>
                </a>
            </li>
            <?php
        }
        ?>
    </ul>
</div>