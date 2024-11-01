(function ($) {
  jQuery(document).ready(function ($) {
    /*
    send a request to the server to store the product view in database views table
    */

    jQuery.ajax({
      type: "POST",
      data: {
        action: "update_product_views",
        product_id: ajax_object.postID,
        ajax_nonce: ajax_object.nonce,
      },
      url: ajax_object.ajax_url,
      dataType: "json",
      success: function (response) {
        //console.log(response + "success");
      },
      error: function (response) {
        //console.log(response + "error failed");
      },
    });
  });
})(jQuery);
