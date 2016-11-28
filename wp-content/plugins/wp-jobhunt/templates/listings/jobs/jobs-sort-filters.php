<?php
/**
 * Jobs Sort Filters
 *
 */
global $wpdb, $cs_form_fields2;
?>
<div class="filter-heading">
    <?php
    // job hunt jobs listing parameters filter
    $args = apply_filters( 'job_hunt_jobs_listing_parameters', $args, $job_sort_by );
    $loop = new WP_Query($args);
    $count_post = $loop->found_posts;
    wp_reset_postdata();
    
    echo '<h5>';
    ?><span class="result-count"><?php if (isset($count_post) && $count_post != '') echo esc_html($count_post) . " "; ?></span><?php
    if (isset($specialisms) && is_array($specialisms)) {
        echo get_specialism_headings($specialisms);
    } else {
        echo __("Jobs & Vacancies", "jobhunt");
    }
    echo "</h5>";
    ?>
    <ul class="cs-sort-sec ">
        <li id="job_filter_sort_by_div">
            <label><?php echo __("Sort by", "jobhunt");?></label>
            <div class="cs-select-holder">
                <?php
                $sortby_option = array( 'recent' => __('Most Recent', 'jobhunt'), 'featured' => __('Featured Only', 'jobhunt'), 'alphabetical' => __('Alphabet Order', 'jobhunt'));
                $sortby_option = apply_filters( 'job_hunt_jobs_sort_options', $sortby_option );
                $cs_opt_array = array(
                    'cust_id' => 'job_filter_sort_by',
                    'cust_name' => 'job_filter_sort_by',
                    'std' => $job_sort_by,
                    'desc' => '',
                    'extra_atr' => ' onchange="cs_set_sort_filter(\'' . esc_js(admin_url('admin-ajax.php')) . '\', \'job_filter_sort_by\')"',
                    'classes' => 'chosen-select-no-single',
                    'options' => $sortby_option,
                    'hint_text' => '',
                );

                $cs_form_fields2->cs_form_select_render($cs_opt_array);
                ?>
            </div>

            <?php
            $cs_opt_array = array(
                'cust_id' => 'job_filter_sort_order',
                'cust_name' => 'job_filter_sort_order',
                'std' => 'asc',
                'desc' => '',
                'hint_text' => '',
            );

            $cs_form_fields2->cs_form_hidden_render($cs_opt_array);
            ?> </li>
        <li id="job_filter_page_size_div">
            <div class="cs-select-holder">
                <?php
                $paging_options[""] = '' . __("Jobs Per Page", "jobhunt");
                $paging_options["10"] = '10 ' . __("Per Page", "jobhunt");
                $paging_options["20"] = '20 ' . __("Per Page", "jobhunt");
                $paging_options["30"] = '30 ' . __("Per Page", "jobhunt");
                $paging_options["50"] = '50 ' . __("Per Page", "jobhunt");
                $paging_options["70"] = '70 ' . __("Per Page", "jobhunt");
                $paging_options["100"] = '100 ' . __("Per Page", "jobhunt");
                $paging_options["200"] = '200 ' . __("Per Page", "jobhunt");
                $cs_opt_array = array(
                    'cust_id' => 'job_filter_page_size',
                    'cust_name' => 'job_filter_page_size',
                    'std' => $job_filter_page_size,
                    'desc' => '',
                    'extra_atr' => ' onchange="cs_set_sort_filter(\'' . esc_js(admin_url('admin-ajax.php')) . '\', \'job_filter_page_size\')"',
                    'classes' => 'chosen-select-no-single',
                    'options' => $paging_options,
                    'hint_text' => '',
                );

                $cs_form_fields2->cs_form_select_render($cs_opt_array);
                ?>
            </div>
        </li>
    </ul>
</div>
