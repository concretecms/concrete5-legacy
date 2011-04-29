$(document).ready(function(){
       $("#ccm-guestbook-closed-comments").css("display","none");
        if ($("#ccm-guestbook-closed-comments-on").is(":checked"))
        {
            $("#ccm-guestbook-closed-comments").show("fast");
        }
       $("#ccm-guestbook-closed-comments-on").click(function(){
        if ($("#ccm-guestbook-closed-comments-on").is(":checked"))
        {
            $("#ccm-guestbook-closed-comments").show("fast");
        }
        else
        {      
            $("#ccm-guestbook-closed-comments").hide("fast");
        }
      });
    });
