/**************************************************************
 *
 * javascript for admin page
 *
 **************************************************************/



/////////////
(function($){
    "use strict";
    
	$(document).ready(function(){
        
        // parse dateformat
        var parseDate = function(d){
            if(d){
                var t = d.split("/");
                return t[2] + "-" + t[1] + "-" + t[0] + " 00:00:00";
            } else {
                return;
            }
        };
        
        // datepicker
        $("#timeline-date").datepicker({
            showAnim: "slide", 
            showButtonPanel: true,
            dateFormat: "dd/mm/yy"
        });
        
        //
        $("body").on("click", "[data-upload]", function(e){
            e.preventDefault();

            var uploadBtn = $(this);

            var image = wp.media({ 
                title: uploadBtn.prev().val(),
                multiple: false // mutiple: true if you want to upload multiple files at once
            })
            .open().on("select", function(e){

                // This will return the selected image from the Media Uploader, the result is an object
                var uploaded_image = image.state().get("selection").first();

                // We convert uploaded_image to a JSON object to make accessing it easier
                // Output to the console uploaded_image
                var image_url = uploaded_image.toJSON().url;

                // Let's assign the url value to the input field
                uploadBtn.prev().val(image_url);

                if($(uploadBtn.attr("data-preview")).length){
                    var preview = $(uploadBtn.attr("data-preview")).children("img").length? $(uploadBtn.attr("data-preview")).children("img"): $("<img>").appendTo(uploadBtn.attr("data-preview"));
                    preview.attr({src: image_url});
                }			
            });
        });
        
                
        // save updated a timeline
        $("#timeline-save").click(function(){
            var timeline_post = {
                date: parseDate($("#timeline-date").val()),
                dotname: $("#timeline-dotname").val(),
                title: $("#timeline-title").val(),
                thumbnail: $("#timeline-thumbnail").val(),
                content: tinymce.editors['timeline-content'].getContent()
            }
            
            // validation
            for(var i in timeline_post){
                if(!timeline_post[i]){
                    alert("Please fill up all fields");
                    return;
                }
            }
            
            // check update/insert
            if($("#timeline-id").val() > 0){
                timeline_post.id = $("#timeline-id").val();
                timeline_post.action = "timeline_edit";
                timeline_post.the_issue_key = global_var.the_issue_key;
            } else {
                timeline_post.action = "timeline_new";
                timeline_post.the_issue_key = global_var.the_issue_key;
            }
            
            // ajax post
            var loadingbar = $("<div></div").appendTo("body").addClass("preloader").fadeIn(300); // create preloader
            $.post(
                global_var.ajaxurl, 
                timeline_post, 
                function(response){
                    // remove preloader
                    loadingbar.fadeOut(300, function(){
                        loadingbar.remove();
                    });

                    /////////////
                    response = $.parseJSON(response);

                    // success
                    if(response.success){
                        alert(response.success_txt);
                        
                        if(!timeline_post.id){
                            $("#timeline-date").val("");
                            $("#timeline-title").val("");
                            tinymce.editors['timeline-content'].setContent("");                           
                        }
                    } else {
                        // error
                        if(response.error){						
                            alert(response.error_txt);
                        } else {
                            alert("AJAX ERROR!");
                        }
                    }
                }
            );

            return false;
        });
        ///////////////

	});
    
})(jQuery);

        
// remove timeline in list
var removeTimeline = function(msg, id, reload){
    (function($){
        if(id > 0){
            if(confirm(msg)){
                // ajax 
                var loadingbar = $("<div></div").appendTo("body").addClass("preloader").fadeIn(300); // create preloader
                
                $.post(
                    global_var.ajaxurl, 
                    {
                        action:			"timeline_del",
                        the_issue_key:	global_var.the_issue_key,

                        id: id
                    }, 
                    function(response){
                        // remove preloader
                        loadingbar.fadeOut(300, function(){
                            loadingbar.remove();
                        });

                        /////////////
                        response = $.parseJSON(response);

                        // success
                        if(response.success){
                            alert(response.success_txt);
                            
                            if($("#timeline-list-page").length){
                                location.href = $("#timeline-list-page").attr("href");
                            } else {
                                location.reload();
                            }
                            
                        } else {
                            // error
                            if(response.error){						
                                alert(response.error_txt);
                            } else {
                                alert("AJAX ERROR!");
                            }
                        }
                    }
                );
            }
        } else {
            alert("Sorry, couldn't find timeline info.");
        }

    })(jQuery);

    return false;
};