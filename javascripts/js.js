$(document).ready(function() {
    // remove success or error messages with jQuery animation when the page is loaded //
    setTimeout(function() {
        $("div.success").fadeOut(1300, function() {});
        $("div.error").fadeOut(1300, function() {});
    }, 3500);

    // background movement on mouse move //
    var movementStrength = 15;
    var height = movementStrength / $(window).height();
    var width = movementStrength / $(window).width();
    $("body").mousemove(function (e) {
        var pageX = e.pageX - ($(window).width() / 2);
        var pageY = e.pageY - ($(window).height() / 2);
        var newvalueX = width * pageX * -1;
        var newvalueY = height * pageY * -1;
        $('body').css("background-position", newvalueX + "px     " + newvalueY + "px");
    });

    // on form submit //
	$('#addnew').submit(function(event) {
		$('.messages').removeClass('error');

        // get values from form //
		var formDataValues = {
			'newnote' : $('#newnote').val(),
        };

		// ajax call and processing the form //
		$.ajax({
			type: 'POST',
			url: './includes/notes.php',
            data: formDataValues,
            dataType: 'json',
            encode: true
        })
        // success //
		.done(function(data) {
            //console.log(data); 

            // show errors if there are any //
            if (!data.success) {
                if (data.errors.newnote) {
                    $('.messages').append('<div class="message error">' + data.errors.newnote + '</div>');
                    // start fading out after 2 seconds //
                    setTimeout(function() {
                        $("div.error").fadeOut(1300, function() {})
                    }, 3500);
                }
            } else {
                $('.messages').append("<div class='message success'>" + data.message + "</div>");
                // add data to the list //
                $(".notes_list").prepend(
                    "<div class='single_note'><p class='addedontext'><span class='addedonvalue'>Added on: </span>" + data.noteadded + "</p><br /><div class='notevalue'>" + data.notetext + "</div></div>"
                );
                // clear the form //
                $('#addnew')[0].reset();

                // start fading out after 2 seconds //
                setTimeout(function() {
                    $("div.success").fadeOut(1300, function() {})
                }, 3500);
            }
        })
        // fail //
        .fail(function(data) {
            // show error data in console //
            //console.log(data);
        });

        // stop the default action (refreshing) //
        event.preventDefault();
    });
});


