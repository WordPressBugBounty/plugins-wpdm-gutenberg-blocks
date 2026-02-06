<?php
/**
 * For wpdm gutenberg add-on
 * Base: wpdmpro
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 11/7/20 21:02
 * Version: 1.0.0
 */
if(!defined("ABSPATH")) die();
//$all_downloads
if(!isset($scid)) $scid = uniqid();
//wpdmdd($scparams);
$_scparams =  \WPDM\__\Crypt::encrypt($scparams);

?>
<style>
    .card-datatable ul.pagination{
        margin:  5px auto;
        float: right;
    }
    .pagination  li a:hover{
        color:  var(--color-primary) !important;
        border: 1px solid var(--color-primary) !important;
        background: #ffffff;
    }
    .pagination  li.active a:hover,
    .pagination  li.active a{
        color:  #ffffff;
        border: 1px solid var(--color-primary-active);
        background: var(--color-primary);
    }
    #card_datatable_<?php echo $scid;  ?> td.update_date,
    #card_datatable_<?php echo $scid;  ?> th{
        white-space: nowrap;
    }
    #card_datatable_<?php echo $scid;  ?> th:last-child,
    #card_datatable_<?php echo $scid;  ?> td:last-child{
        text-align: right;
        width: 100px;
        vertical-align: middle;
    }
    #card_datatable_<?php echo $scid;  ?> th.thumb,
    #card_datatable_<?php echo $scid;  ?> td.thumb,
    #card_datatable_<?php echo $scid;  ?> th.icon,
    #card_datatable_<?php echo $scid;  ?> td.icon {
        width: 56px;
        min-width: 56px;
        max-width: 56px;
        padding-right: 0  !important;
        vertical-align: middle !important;
    }
    #card_datatable_<?php echo $scid;  ?> td.thumb .datatable-thumb,
    #card_datatable_<?php echo $scid;  ?> td.icon .datatable-icon{
        max-width: 100%;
        border-radius: 0.2rem;
    }
    #card_datatable_<?php echo $scid;  ?> td:last-child .btn{
        white-space: nowrap;
    }
    .small-txt{
        font-size: 11px;
        opacity: 0.9;
    }

     #card_datatable_<?php echo $scid;  ?> table,#card_datatable_<?php echo $scid;  ?> td,#card_datatable_<?php echo $scid;  ?> th{
         border: 0;
     }
    #card_datatable_<?php echo $scid;  ?>{
        overflow: hidden;
    }
    #card_datatable_<?php echo $scid;  ?>{
        font-size: 10pt;
        min-width: 100%;
    }
    #card_datatable_<?php echo $scid;  ?> .wpdm-download-link img{
        box-shadow: none !important;
        max-width: 100%;
    }
    #card_datatable_<?php echo $scid;  ?> .form.control,
    #card_datatable_<?php echo $scid;  ?> .btn{
        border-radius: 0.2rem;
    }
    .w3eden .pagination{
        margin: 0 !important;
    }
    #card_datatable_<?php echo $scid;  ?> td:not(:first-child){
        vertical-align: middle !important;
    }
    #card_datatable_<?php echo $scid;  ?> td.__dt_col_download_link .btn{
        width: 100%;
    }
    #card_datatable_<?php echo $scid;  ?> td.__dt_col_download_link,
    #card_datatable_<?php echo $scid;  ?> th#download_link{
        max-width: 155px !important;
        width: 155px;

    }
    #card_datatable_<?php echo $scid;  ?> th{
        background-color: rgba(0,0,0,0.04);
        border-bottom: 1px solid rgba(0,0,0,0.025);
    }

    #card_datatable_<?php echo $scid;  ?> .package-title{
        color:#36597C;
        font-size: 11pt;
        font-weight: 700;
    }
    #card_datatable_<?php echo $scid;  ?> .small-txt{
        margin-right: 7px;
    }
    #card_datatable_<?php echo $scid;  ?> td{
        min-width: 150px;
    }

    #card_datatable_<?php echo $scid;  ?> td.__dt_col_categories{
        max-width: 300px;
    }

    #card_datatable_<?php echo $scid;  ?> .search-field{
        padding-left: 32px;
        background: #ffffff url(data:image/svg+xml;base64,PHN2ZyBpZD0iTGF5ZXJfMiIgaGVpZ2h0PSI1MTIiIHZpZXdCb3g9IjAgMCAyNCAyNCIgd2lkdGg9IjUxMiIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgZGF0YS1uYW1lPSJMYXllciAyIj48bGluZWFyR3JhZGllbnQgaWQ9Ik9yYW5nZV9ZZWxsb3ciIGdyYWRpZW50VW5pdHM9InVzZXJTcGFjZU9uVXNlIiB4MT0iLTEuNzYyIiB4Mj0iMjAuOTg0IiB5MT0iMjUuMzY3IiB5Mj0iNi44ODYiPjxzdG9wIG9mZnNldD0iMCIgc3RvcC1jb2xvcj0iI2ZmZjMzYiIvPjxzdG9wIG9mZnNldD0iLjA0IiBzdG9wLWNvbG9yPSIjZmVlNzJlIi8+PHN0b3Agb2Zmc2V0PSIuMTE3IiBzdG9wLWNvbG9yPSIjZmVkNTFiIi8+PHN0b3Agb2Zmc2V0PSIuMTk2IiBzdG9wLWNvbG9yPSIjZmRjYTEwIi8+PHN0b3Agb2Zmc2V0PSIuMjgxIiBzdG9wLWNvbG9yPSIjZmRjNzBjIi8+PHN0b3Agb2Zmc2V0PSIuNjY5IiBzdG9wLWNvbG9yPSIjZjM5MDNmIi8+PHN0b3Agb2Zmc2V0PSIuODg4IiBzdG9wLWNvbG9yPSIjZWQ2ODNjIi8+PHN0b3Agb2Zmc2V0PSIxIiBzdG9wLWNvbG9yPSIjZTkzZTNhIi8+PC9saW5lYXJHcmFkaWVudD48cGF0aCBkPSJtMjIuNzA3IDIxLjI5My01LjEwNy01LjExMWE5LjM1NSA5LjM1NSAwIDEgMCAtMS40MTggMS40MThsNS4xMTEgNS4xMTFhMSAxIDAgMCAwIDEuNDE0LTEuNDE0em0tMTkuNzA3LTEwLjk2YTcuMzM0IDcuMzM0IDAgMSAxIDcuMzMzIDcuMzM0IDcuMzQyIDcuMzQyIDAgMCAxIC03LjMzMy03LjMzNHoiIGZpbGw9InVybCgjT3JhbmdlX1llbGxvdykiLz48L3N2Zz4=);
        background-repeat: no-repeat;
        background-size: 12px;
        background-position: 10px center;
    }
    #card_datatable_<?php echo $scid;  ?> .small-txt,
    #card_datatable_<?php echo $scid;  ?> small{
        font-size: 9pt;
    }
    .w3eden .table-striped tbody tr:nth-of-type(2n+1) {
        background-color: rgba(0,0,0,0.015);
    }

    /* Dark Mode Support */
    .w3eden.dark-mode #card_datatable_<?php echo $scid; ?>,
    .w3eden.dark-mode #card_datatable_<?php echo $scid; ?> .card {
        background: var(--dm-bg-secondary, #1e293b);
        border-color: var(--dm-border, rgba(255,255,255,0.1));
        color: var(--dm-text, #f1f5f9);
    }
    .w3eden.dark-mode #card_datatable_<?php echo $scid; ?> .card-header,
    .w3eden.dark-mode #card_datatable_<?php echo $scid; ?> .card-footer {
        background: var(--dm-bg-tertiary, #334155) !important;
        border-color: var(--dm-border, rgba(255,255,255,0.1));
    }
    .w3eden.dark-mode #card_datatable_<?php echo $scid; ?> th {
        background-color: var(--dm-bg-tertiary, #334155);
        color: var(--dm-text-secondary, #cbd5e1);
        border-color: var(--dm-border, rgba(255,255,255,0.1));
    }
    .w3eden.dark-mode #card_datatable_<?php echo $scid; ?> td {
        border-color: var(--dm-border, rgba(255,255,255,0.1));
    }
    .w3eden.dark-mode #card_datatable_<?php echo $scid; ?> .form-control,
    .w3eden.dark-mode #card_datatable_<?php echo $scid; ?> .custom-select {
        background-color: var(--dm-bg-secondary, #1e293b);
        border-color: var(--dm-border, rgba(255,255,255,0.15));
        color: var(--dm-text, #f1f5f9);
    }
    .w3eden.dark-mode #card_datatable_<?php echo $scid; ?> .search-field {
        background-color: var(--dm-bg-secondary, #1e293b);
        background-image: url(data:image/svg+xml;base64,PHN2ZyBpZD0iTGF5ZXJfMiIgaGVpZ2h0PSI1MTIiIHZpZXdCb3g9IjAgMCAyNCAyNCIgd2lkdGg9IjUxMiIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgZGF0YS1uYW1lPSJMYXllciAyIj48bGluZWFyR3JhZGllbnQgaWQ9Ik9yYW5nZV9ZZWxsb3ciIGdyYWRpZW50VW5pdHM9InVzZXJTcGFjZU9uVXNlIiB4MT0iLTEuNzYyIiB4Mj0iMjAuOTg0IiB5MT0iMjUuMzY3IiB5Mj0iNi44ODYiPjxzdG9wIG9mZnNldD0iMCIgc3RvcC1jb2xvcj0iI2ZmZjMzYiIvPjxzdG9wIG9mZnNldD0iLjA0IiBzdG9wLWNvbG9yPSIjZmVlNzJlIi8+PHN0b3Agb2Zmc2V0PSIuMTE3IiBzdG9wLWNvbG9yPSIjZmVkNTFiIi8+PHN0b3Agb2Zmc2V0PSIuMTk2IiBzdG9wLWNvbG9yPSIjZmRjYTEwIi8+PHN0b3Agb2Zmc2V0PSIuMjgxIiBzdG9wLWNvbG9yPSIjZmRjNzBjIi8+PHN0b3Agb2Zmc2V0PSIuNjY5IiBzdG9wLWNvbG9yPSIjZjM5MDNmIi8+PHN0b3Agb2Zmc2V0PSIuODg4IiBzdG9wLWNvbG9yPSIjZWQ2ODNjIi8+PHN0b3Agb2Zmc2V0PSIxIiBzdG9wLWNvbG9yPSIjZTkzZTNhIi8+PC9saW5lYXJHcmFkaWVudD48cGF0aCBkPSJtMjIuNzA3IDIxLjI5My01LjEwNy01LjExMWE5LjM1NSA5LjM1NSAwIDEgMCAtMS40MTggMS40MThsNS4xMTEgNS4xMTFhMSAxIDAgMCAwIDEuNDE0LTEuNDE0em0tMTkuNzA3LTEwLjk2YTcuMzM0IDcuMzM0IDAgMSAxIDcuMzMzIDcuMzM0IDcuMzQyIDcuMzQyIDAgMCAxIC03LjMzMy03LjMzNHoiIGZpbGw9InVybCgjT3JhbmdlX1llbGxvdykiLz48L3N2Zz4=);
    }
    .w3eden.dark-mode #card_datatable_<?php echo $scid; ?> .table-striped tbody tr:nth-of-type(2n+1) {
        background-color: rgba(255,255,255,0.02);
    }
    .w3eden.dark-mode #card_datatable_<?php echo $scid; ?> .package-title {
        color: var(--dm-text, #f1f5f9);
    }
    .w3eden.dark-mode #card_datatable_<?php echo $scid; ?> .pagination .page-link {
        background: var(--dm-bg-secondary, #1e293b);
        border-color: var(--dm-border, rgba(255,255,255,0.1));
        color: var(--dm-text, #f1f5f9);
    }
    .w3eden.dark-mode #card_datatable_<?php echo $scid; ?> .pagination .page-item.active .page-link {
        background: var(--color-primary);
        border-color: var(--color-primary);
    }

    /* System preference dark mode */
    @media (prefers-color-scheme: dark) {
        .w3eden:not(.light-mode) #card_datatable_<?php echo $scid; ?>,
        .w3eden:not(.light-mode) #card_datatable_<?php echo $scid; ?> .card {
            background: var(--dm-bg-secondary, #1e293b);
            border-color: var(--dm-border, rgba(255,255,255,0.1));
            color: var(--dm-text, #f1f5f9);
        }
        .w3eden:not(.light-mode) #card_datatable_<?php echo $scid; ?> .card-header,
        .w3eden:not(.light-mode) #card_datatable_<?php echo $scid; ?> .card-footer {
            background: var(--dm-bg-tertiary, #334155) !important;
            border-color: var(--dm-border, rgba(255,255,255,0.1));
        }
        .w3eden:not(.light-mode) #card_datatable_<?php echo $scid; ?> th {
            background-color: var(--dm-bg-tertiary, #334155);
            color: var(--dm-text-secondary, #cbd5e1);
            border-color: var(--dm-border, rgba(255,255,255,0.1));
        }
        .w3eden:not(.light-mode) #card_datatable_<?php echo $scid; ?> td {
            border-color: var(--dm-border, rgba(255,255,255,0.1));
        }
        .w3eden:not(.light-mode) #card_datatable_<?php echo $scid; ?> .form-control,
        .w3eden:not(.light-mode) #card_datatable_<?php echo $scid; ?> .custom-select {
            background-color: var(--dm-bg-secondary, #1e293b);
            border-color: var(--dm-border, rgba(255,255,255,0.15));
            color: var(--dm-text, #f1f5f9);
        }
        .w3eden:not(.light-mode) #card_datatable_<?php echo $scid; ?> .search-field {
            background-color: var(--dm-bg-secondary, #1e293b);
        }
        .w3eden:not(.light-mode) #card_datatable_<?php echo $scid; ?> .table-striped tbody tr:nth-of-type(2n+1) {
            background-color: rgba(255,255,255,0.02);
        }
        .w3eden:not(.light-mode) #card_datatable_<?php echo $scid; ?> .package-title {
            color: var(--dm-text, #f1f5f9);
        }
        .w3eden:not(.light-mode) #card_datatable_<?php echo $scid; ?> .pagination .page-link {
            background: var(--dm-bg-secondary, #1e293b);
            border-color: var(--dm-border, rgba(255,255,255,0.1));
            color: var(--dm-text, #f1f5f9);
        }
        .w3eden:not(.light-mode) #card_datatable_<?php echo $scid; ?> .pagination .page-item.active .page-link {
            background: var(--color-primary);
            border-color: var(--color-primary);
        }
    }

    @media (max-width: 799px) {
        #card_datatable_<?php echo $scid;  ?> tr {
            display: block;
            border: 3px solid rgba(0,0,0,0.3) !important;
            margin-bottom: 10px !important;
            position: relative;
        }
        #card_datatable_<?php echo $scid;  ?> thead{
            display: none;
        }
        #card_datatable_<?php echo $scid;  ?>,
        #card_datatable_<?php echo $scid;  ?> td:first-child {
            border: 0 !important;
        }
        #card_datatable_<?php echo $scid;  ?> td {
            display: block;
        }
        #card_datatable_<?php echo $scid;  ?> td.__dt_col_download_link {
            display: block;
            max-width: 100% !important;
            width: auto !important;

        }
    }


</style>
<div  class="w3eden">
<div class="card card-datatable" id="card_datatable_<?php echo $scid;  ?>">
    <div class="card-header bg-white">
        <form method="get" id="datatable_filter_<?php echo $scid; ?>">
            <input type="hidden" name="_scparams"  value="<?php echo $_scparams; ?>" />
            <input type="hidden" name="cp" id="dtcp"  value="1" />
        <div class="row">
            <div class="col-md-3">
                <input type="search"  placeholder="<?php echo __( "Search...", "download-manager" ); ?>" class="form-control search-field" onclick="this.select()" name="skw">
            </div>
            <div class="col-md-3">
                <?php wp_dropdown_categories(['taxonomy' => 'wpdmcategory', 'hide_empty' => true, 'name'  => 'category', 'id' => 'category_'.$scid, 'class' => 'form-control custom-select', 'show_option_all' => __( "All Categories", "download-manager" )]) ?>
            </div>
            <div class="col-md-2">
                <select class="form-control custom-select" id="orderby"  name="orderby">
                    <option value="title"><?php echo __( "Title", "download-manager" ); ?></option>
                    <option value="download_count"><?php echo __( "Download Count", "download-manager" ); ?></option>
                    <option value="date"><?php echo __( "Publish Date", "download-manager" ); ?></option>
                    <option value="update_date"><?php echo __( "Update Date", "download-manager" ); ?></option>
                    <option value="package_size_b"><?php echo __( "Package Size", "download-manager" ); ?></option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-control custom-select" id="order"  name="order">
                    <option value="asc"><?php echo __( "Asc", "download-manager" ); ?></option>
                    <option value="desc"><?php echo __( "Desc", "download-manager" ); ?></option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary btn-block">Apply Filter</button>
            </div>
        </div>
        </form>
    </div>
    <table class="table table-striped">
        <thead>
        <tr>
            <?php  foreach ($colheads as $index => $colhead){ if(!isset($cols[$index]) || trim($cols[$index]) === '') continue; ?>
                <th class="<?php echo esc_attr($cols[$index]); ?>" id="<?php echo esc_attr($cols[$index]); ?>"><?php echo esc_html__($colhead, 'download-manager'); ?></th>
            <?php  } ?>
        </tr>
        </thead>
        <tbody id="wpdm_datatable_<?php echo $scid;  ?>">
        <tr v-for="package in packages">
            <?php  foreach ($cols as $col){ if(trim($col) === '') continue; ?>
                <td  class="__dt_col_<?php echo esc_attr($col); ?>"><span v-html="package.<?php echo esc_js(str_replace(",", "__", $col)); ?>"></span></td>
            <?php  } ?>
        </tr>
        </tbody>
    </table>
    <div class="card-footer bg-white" id="card_footer_<?php echo $scid;  ?>">
        <div class="float-right"  id="__pginate">
            <div id="_paginate">

            </div>
        </div>
        <div id="_total"  style="line-height: 38px">
            Total {{total}} items found
        </div>
    </div>
</div>
</div>
<script src="<?= WPDM_BASE_URL ?>assets/js/vue.min.js"></script>
<script>
    // Vue 3 app for datatable
    var datatable_<?php echo $scid; ?> = Vue.createApp({
        data() {
            return {
                packages: []
            };
        }
    }).mount('#wpdm_datatable_<?php echo $scid; ?>');

    var scparams = '<?php echo $_scparams; ?>';
    var pages = parseInt('<?php echo $pages; ?>') || 1;
    var currentPage = 1;

    // Pure JS pagination (Vue 3 compatible)
    function createPagination() {
        var container = jQuery('#card_footer_<?php echo $scid; ?> #__pginate');
        container.html('<ul class="pagination" id="_paginate_<?php echo $scid; ?>"></ul>');

        var html = '';
        var maxVisible = 5;
        var startPage = Math.max(1, currentPage - Math.floor(maxVisible / 2));
        var endPage = Math.min(pages, startPage + maxVisible - 1);

        if (endPage - startPage < maxVisible - 1) {
            startPage = Math.max(1, endPage - maxVisible + 1);
        }

        // Previous button
        if (currentPage > 1) {
            html += '<li class="page-item"><a class="page-link" href="#" data-page="' + (currentPage - 1) + '">&laquo;</a></li>';
        }

        // First page + ellipsis
        if (startPage > 1) {
            html += '<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>';
            if (startPage > 2) {
                html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        // Page numbers
        for (var i = startPage; i <= endPage; i++) {
            var activeClass = (i === currentPage) ? ' active' : '';
            html += '<li class="page-item' + activeClass + '"><a class="page-link" href="#" data-page="' + i + '">' + i + '</a></li>';
        }

        // Last page + ellipsis
        if (endPage < pages) {
            if (endPage < pages - 1) {
                html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            html += '<li class="page-item"><a class="page-link" href="#" data-page="' + pages + '">' + pages + '</a></li>';
        }

        // Next button
        if (currentPage < pages) {
            html += '<li class="page-item"><a class="page-link" href="#" data-page="' + (currentPage + 1) + '">&raquo;</a></li>';
        }

        container.find('ul').html(html);
    }

    // Handle pagination clicks
    jQuery(document).on('click', '#_paginate_<?php echo $scid; ?> a.page-link', function(e) {
        e.preventDefault();
        var pageNum = parseInt(jQuery(this).data('page'));
        if (pageNum && pageNum !== currentPage) {
            currentPage = pageNum;
            jQuery('#dtcp').val(pageNum);
            jQuery('#datatable_filter_<?php echo $scid; ?>').submit();
        }
    });

    jQuery(function ($) {
        // Initial data load
        $.get('<?php echo wpdm_rest_url('alldownloads'); ?>', {_scparams: '<?php echo $_scparams; ?>'}, function(response) {
            datatable_<?php echo $scid; ?>.packages = response.packages;
            pages = parseInt(response.pages) || 1;
            $('#_total').html("Total <b>" + response.total + "</b> items found");
            createPagination();
        });

        // Reset to page 1 on filter change
        $('body').on('change', '#datatable_filter_<?php echo $scid; ?> .form-control', function() {
            $('#dtcp').val(1);
            currentPage = 1;
        });
        $('body').on('click', '#datatable_filter_<?php echo $scid; ?> .btn', function() {
            $('#dtcp').val(1);
            currentPage = 1;
        });

        // Form submission handler
        $('#datatable_filter_<?php echo $scid; ?>').submit(function(e) {
            e.preventDefault();
            WPDM.blockUI('#card_datatable_<?php echo $scid; ?>');
            $(this).ajaxSubmit({
                url: '<?php echo wpdm_rest_url('alldownloads'); ?>',
                success: function(response) {
                    datatable_<?php echo $scid; ?>.packages = response.packages;
                    currentPage = parseInt($('#dtcp').val()) || 1;

                    if (currentPage < 2) {
                        pages = parseInt(response.pages) || 1;
                        $('#_total').html('Total <b>' + response.total + '</b> items found');
                    }
                    createPagination();
                    WPDM.unblockUI('#card_datatable_<?php echo $scid; ?>');
                }
            });
        });
    });
</script>

