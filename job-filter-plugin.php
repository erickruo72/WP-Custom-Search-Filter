<?php
/*
Plugin Name: Job Filter Redirect
Description: Job filter with unified search term submission and custom scrollable dropdowns.
Version: 2.5
Author: Erick Ruo
*/


function job_filter_form() {
    $parents = array('Company', 'Qualification', 'Location', 'Experience', 'Job Field', 'Job Type');
    $base_url = home_url('/');
    
    ?>
    <form method="get" action="<?php echo $base_url; ?>" class="job-filter-form" onsubmit="return handleFilterSubmission(event);">

        <input type="hidden" name="s" id="consolidated-s-field" value="">

        <?php foreach ($parents as $parent_name):
            $param_key = strtolower(str_replace(' ', '_', $parent_name));
            $parent = get_term_by('name', $parent_name, 'category');
            $children = [];

            if ($parent && !is_wp_error($parent)) {
                $children = get_terms(array(
                    'taxonomy' => 'category',
                    'parent'   => $parent->term_id,
                    'hide_empty' => false,
                ));
            }
        ?>
            <?php if (!empty($children) && !is_wp_error($children)): ?>
                <div class="filter-group">
                    <label for="filter-<?php echo $param_key; ?>"><?php echo $parent_name; ?>:</label>
                    
                    <div class="custom-select-container" data-filter-id="<?php echo $param_key; ?>">
                        <input type="hidden" id="value-<?php echo $param_key; ?>" class="filter-hidden-value" value="" />
                        
                        <div class="select-styled" id="display-<?php echo $param_key; ?>">All <?php echo $parent_name; ?></div>
                        
                        <ul class="select-options-list" id="options-<?php echo $param_key; ?>">
                            <li data-value="" data-display-name="All <?php echo $parent_name; ?>">All <?php echo $parent_name; ?></li>
                            <?php foreach ($children as $child): ?>
                                <li 
                                    data-value="<?php echo $child->slug; ?>" 
                                    data-display-name="<?php echo $child->name; ?>"
                                >
                                    <?php echo $child->name; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <button type="submit">Filter</button>
        <button type="button" onclick="clearFilters()" class="clear-filters-button">Clear Filters</button>
    </form>
    
    <script>
    const filterIds = ['company', 'qualification', 'location', 'experience', 'job_field', 'job_type'];

    document.addEventListener('DOMContentLoaded', () => {
        document.addEventListener('click', (e) => {
            const containers = document.querySelectorAll('.custom-select-container');
            containers.forEach(container => {
                if (!container.contains(e.target)) {
                    container.classList.remove('active');
                }
            });
        });

        document.querySelectorAll('.select-styled').forEach(display => {
            display.addEventListener('click', function() {
                document.querySelectorAll('.custom-select-container.active').forEach(activeContainer => {
                    if (activeContainer !== this.closest('.custom-select-container')) {
                        activeContainer.classList.remove('active');
                    }
                });
                this.closest('.custom-select-container').classList.toggle('active');
            });
        });

        document.querySelectorAll('.select-options-list li').forEach(option => {
            option.addEventListener('click', function() {
                const container = this.closest('.custom-select-container');
                const hiddenValueInput = container.querySelector('.filter-hidden-value');
                const displayDiv = container.querySelector('.select-styled');

                const value = this.getAttribute('data-value');
                const displayName = this.getAttribute('data-display-name') || this.textContent.trim();

                displayDiv.textContent = displayName;
                hiddenValueInput.value = value;
                
                container.classList.remove('active');
            });
        });
    });


    function handleFilterSubmission(event) {
        event.preventDefault(); 
        
        const filters = [];
        
        document.querySelectorAll('.custom-select-container').forEach(container => {
            const hiddenValueInput = container.querySelector('.filter-hidden-value');
            if (hiddenValueInput && hiddenValueInput.value) {
                filters.push(hiddenValueInput.value);
            }
        });

        const consolidatedTerm = filters.join(',');
        
        const baseUrl = event.target.getAttribute('action').split('?')[0];
        const newUrl = `${baseUrl}?s=${encodeURIComponent(consolidatedTerm)}`;
        
        window.location.href = newUrl;
        return false;
    }
    

    function clearFilters() {
        document.querySelectorAll('.custom-select-container').forEach(container => {
            const hiddenValueInput = container.querySelector('.filter-hidden-value');
            const displayDiv = container.querySelector('.select-styled');
            
            if (hiddenValueInput) {
                 hiddenValueInput.value = '';
            }
            
            const defaultOption = container.querySelector('.select-options-list li[data-value=""]');
            if (displayDiv && defaultOption) {
                displayDiv.textContent = defaultOption.textContent.trim();
            }
            container.classList.remove('active');
        });
    }
    

    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            clearFilters();
        }
    });
    </script>

    <style>

    .job-filter-form {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: flex-end;
        font-family: Inter, Arial, sans-serif;
        font-size: 14px;
        background-color: #f7f7f7;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .job-filter-form .filter-group {
        display: flex;
        flex-direction: column;
        min-width: 120px;
        max-width: 180px;
        flex: 1;
    }
    
    .job-filter-form label {
        font-weight: 600;
        margin-bottom: 4px;
        color: #333;
    }

    .custom-select-container {
        position: relative;
        cursor: pointer;
        border-radius: 4px;
        border: 1px solid #ddd;
        background-color: white;
        transition: border-color 0.2s;
        height: 34px; 
        box-sizing: border-box;
    }

    .custom-select-container:hover {
        border-color: #aaa;
    }
    .custom-select-container.active {
        border-color: #0073aa;
        border-radius: 4px 4px 0 0;
    }

    .select-styled {
        padding: 8px 30px 8px 10px;
        font-size: 14px;
        width: 100%;
        box-sizing: border-box;
        display: block;
        color: #333;
        white-space: nowrap; 
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 18px;
    }

    .select-styled::after {
        content: '▼';
        position: absolute;
        top: 9px;
        right: 10px;
        font-size: 10px;
        color: #555;
        transition: transform 0.2s;
    }

    .custom-select-container.active .select-styled::after {
        transform: rotate(180deg);
    }

    .select-options-list {
        position: absolute;
        top: 100%; 
        left: -1px; 
        z-index: 10;
        max-height: 300px; 
        overflow-y: auto; 
        list-style: none;
        padding: 0;
        margin: 0;
        background-color: white;
        border: 1px solid #0073aa;
        border-top: none;
        border-radius: 0 0 4px 4px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        display: none;
        
        min-width: 100%;
        width: max-content; 
    }

    .custom-select-container.active .select-options-list {
        display: block;
    }

    .select-options-list li {
        padding: 8px 10px;
        cursor: pointer;
        transition: background-color 0.1s;
        font-size: 14px;

        white-space: nowrap; 
        overflow: visible; 
        text-overflow: clip; 
    }

    .select-options-list li:hover {
        background-color: #f0f0f0;
    }

    .job-filter-form button {
        padding: 9px 18px;
        font-size: 14px;
        cursor: pointer;
        border: none;
        border-radius: 4px;
        transition: background-color 0.2s, transform 0.1s;
        font-weight: 600;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    
    .job-filter-form button[type="submit"] {
        background-color: #0073aa;
        color: white;
    }
    
    .job-filter-form button[type="submit"]:hover {
        background-color: #005a87;
        box-shadow: 0 2px 4px rgba(0,0,0,0.15);
    }

    .job-filter-form .clear-filters-button {
        background-color: #f3f4f6;
        color: #4b5563;
    }
    .job-filter-form .clear-filters-button:hover {
        background-color: #e5e7eb;
        color: #1f2937;
    }

    @media (max-width: 600px) {
        .job-filter-form {
            flex-direction: column;
            align-items: stretch;
            gap: 15px;
        }
        .job-filter-form .filter-group {
            min-width: 100%;
            max-width: 100%;
        }
        .job-filter-form button {
            width: 100%;
        }
    }
    </style>
    <?php
}
add_shortcode('job_filter', 'job_filter_form');
function job_filter_query($query) {
    if (!is_admin() && $query->is_main_query() && $query->is_search() && !empty($_GET['s'])) {
        
        $raw_s = sanitize_text_field($_GET['s']);
        
        $search_terms = array_map('trim', explode(',', $raw_s));

        $search_terms = array_filter($search_terms);

        if (!empty($search_terms)) {
            $tax_query = array(
                'relation' => 'AND',
                array(
                    'taxonomy' => 'category',
                    'field'    => 'slug',
                    'terms'    => $search_terms,
                )
            );
            
            $query->set('tax_query', $tax_query);
        }

        $query->set('post_type', 'post');
    }
}
add_action('pre_get_posts', 'job_filter_query');


add_filter('get_the_archive_title', 'job_filter_clean_title');
function job_filter_clean_title($title) {
    if (is_search()) {
        
        $raw_s_term = get_query_var('s');
        
        if (!empty($raw_s_term)) {
            $formatted_term = str_replace([',', '-', '_', '+'], ' ', $raw_s_term);
            $formatted_term = ucwords($formatted_term);

            $title = 'Search Results for: ' . $formatted_term;
        } else {
             $title = 'All Job Listings'; 
        }

        global $paged;
        if ($paged && $paged > 1) {
            $title .= ' - Page ' . $paged;
        }
    }
    return $title;
}
