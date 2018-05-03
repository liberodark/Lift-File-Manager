/**
 *   I don't recommend using this plugin on large tables, I just wrote it to make the demo useable. It will work fine for smaller tables
 *   but will likely encounter performance issues on larger tables.
 *
 *		<input type="text" class="form-control" id="dev-table-filter" data-action="filter" data-filters="#dev-table" placeholder="Filter Developers" />
 *		$(input-element).filterTable()
 *
 *	The important attributes are 'data-action="filter"' and 'data-filters="#table-selector"'
 */
(function(){
    'use strict';
    var $ = jQuery;
    $.fn.extend({
        filterTable: function(){
            return this.each(function(){
                $(this).on('keyup', function(e){
                    $('.filterTable_no_results').remove();
                    var $this = $(this), search = $this.val().toLowerCase(), target = $this.attr('data-filters'), $target = $(target), $rows = $target.find('tbody tr');
                    if(search == '') {
                        $rows.show();
                    } else {
                        $rows.each(function(){
                            var $this = $(this);
                            $this.text().toLowerCase().indexOf(search) === -1 ? $this.hide() : $this.show();
                        })
                        if($target.find('tbody tr:visible').size() === 0) {
                            var col_count = $target.find('tr').first().find('td').size();
                            var no_results = $('<tr class="filterTable_no_results"><td colspan="'+col_count+'">No results found</td></tr>')
                            $target.find('tbody').append(no_results);
                        }
                    }
                });
            });
        }
    });
    $('[data-action="filter"]').filterTable();
})(jQuery);

$(function(){
    // attach table filter plugin to inputs
    $('[data-action="filter"]').filterTable();

    $('.container').on('click', '.panel-heading span.filter', function(e){
        var $this = $(this),
            $panel = $this.parents('.panel');

        $panel.find('.panel-body').slideToggle();
        if($this.css('display') != 'none') {
            $panel.find('.panel-body input').focus();
        }
    });
    $('[data-toggle="tooltip"]').tooltip();
})

check_all_method = "all";

function set_selected(value, id, checker)
{
    if(checker == true)
    {
        if(!in_array(selected, value))
        {
            selected.push(value);
        }
    }
    else
    {
        if(in_array(selected, value))
        {
            removeItem(selected, value);
        }
    }
}

function select_all( start, end )
{
    if( check_all_method == "all" ) {
        check_all_method = "none";
        for( var i = start; i < end; i++ ) {
            document.getElementById("check_"+i).checked = true;
        }
        selected = all_loaded_files;
    }
    else {
        check_all_method = "all";
        for( var i = start; i < end; i++ ) {
            document.getElementById("check_"+i).checked = false;
        }
        selected = new Array();
    }
}

function in_array(array, id)
{
    for(var i=0;i<array.length;i++)
    {
        if(array[i] === id)
        {
            return true;
        }
    }
    return false;
}

function removeItem(array, item)
{
    for(var i in array)
    {
        if(array[i] == item)
        {
            array.splice( i, 1 );
            break;
        }
    }
}

function show_this_dir_file( is_file, is_zip, is_img, path, download, search )
{
    if( search != '' ) {
        return false;
    }
    if(is_file == 0)
    {
        page = 1;
        showFileManager( path );
    }
    else
    {
        if(is_img == 0) window.open("download.php?show="+download, "download.php?show="+download);
    }
}

function show_user_dir_file( is_file, is_zip, is_img, path, download, search, active )
{
    if( search != '' ) {
        return false;
    }
    if(is_file == 0)
    {
        page = 1;
        showFileManager( path );
    }
    else
    {
        if(is_img == 0) window.open("filemanager_user/download.php?show="+download+"&switch="+active, "filemanager_user/download.php?show="+download+"&switch="+active);
    }
}

function validateEmail(email)
{
    var atpos = email.indexOf("@");
    var dotpos = email.lastIndexOf(".");
    if (atpos<1 || dotpos<atpos+2 || dotpos+2>=email.length)
    {
        return false;
    }
    else
    {
        return true;
    }
}

$(".image_show").fancybox({
    'hideOnContentClick': true,
    "type": "image"
});

$('a[data-type="user"]').click(function(){
    var reload_href = $(this).attr( "href" );
    document.location.href = reload_href;
});