jQuery(function()  {
    var login_url = 'https://api.marketingoptimizer.com/auth';
    var logout_url = 'https://api.marketingoptimizer.com/auth/logout';
    jQuery("#auth_mo").click(function(){
            window.open(login_url, '_blank', 'location=yes,height=700,width=700,scrollbars=no,status=yes');
    });
    var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
    var eventer = window[eventMethod];


    var messageEvent = eventMethod == "attachEvent" ? "onmessage" : "message";

    // Listen to message from child window
    eventer(messageEvent, function (e) {
            var tokens_obj = JSON.parse(e.data);
            if (typeof tokens_obj.access_token == 'undefined') {
                return;
            }
            var tokens_data = {
                'access_token'  : tokens_obj.access_token,
                'refresh_token' : tokens_obj.refresh_token,
                'id_token'      : tokens_obj.id_token
            };
            jQuery.ajax({
               type : "post",
               dataType : "json",
               url : authObj.ajaxurl,
               data : {
                   action: "mo_save_auth_tokens", 
                   nonce: authObj.nonce,
                   account_action : 'auth_update_tokens',
                   tokens_data : tokens_data
               }
            })
              .done(function(data) {
                alert(data.message);
              })
              .fail(function(data) {
                alert(data.message);
              })
              .always(function() {
                location.reload();
              });

    }, false);
    
    jQuery("#revoke_auth_mo").click(function() {
        var win = window.open(logout_url, '_blank', 'location=yes,height=700,width=700,scrollbars=no,status=yes');
                setInterval(function () {
                    win.close();
                }, 1000);
        jQuery.ajax({
               type : "post",
               dataType : "json",
               url : authObj.ajaxurl,
               data : {
                   action: "mo_save_auth_tokens", 
                   nonce: authObj.nonce,
                   account_action : 'auth_remove_token'
               }
            })
            .done(function(data) {

                    //alert(data.message);
                
              })
              .fail(function(data) {
                    alert(data.message);
              })
              .always(function() {
                location.reload();
              });
    });
});
