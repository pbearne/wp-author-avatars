// the JS need to page avatars via ajax on shortcode

jQuery('.shortcode-author-avatars').on('click','.aa_pageList a', function (e) {
    shortCodeValues.aa_page = jQuery(this).attr("id");
    jQuery(this).parents('.shortcode-author-avatars').addClass('paging');
    jQuery.post(shortCodeValues.ajax_url, shortCodeValues, function (response) {
        jQuery('.paging').html(response);
        jQuery('.shortcode-author-avatars').removeClass('paging');
    });
    e.preventDefault();
});
