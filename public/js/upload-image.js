$(function() {
    var croppie = null;
    var el = document.getElementById('resizer');
    var host = document.location.origin;

    $.base64ImageToBlob = function(str) {
        // extract content type and base64 payload from original string
        var pos = str.indexOf(';base64,');
        var type = str.substring(5, pos);
        var b64 = str.substr(pos + 8);
      
        // decode base64
        var imageContent = atob(b64);
      
        // create an ArrayBuffer and a view (as unsigned 8-bit)
        var buffer = new ArrayBuffer(imageContent.length);
        var view = new Uint8Array(buffer);
      
        // fill the view, using the decoded base64
        for (var n = 0; n < imageContent.length; n++) {
          view[n] = imageContent.charCodeAt(n);
        }
      
        // convert ArrayBuffer to Blob
        var blob = new Blob([buffer], { type: type });
      
        return blob;
    }

    $.getImage = function(input, croppie) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {  
                croppie.bind({
                    url: e.target.result,
                });
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#file-upload").on("change", function(event) {
        $("#myModal").modal();
        // Initailize croppie instance and assign it to global variable
        croppie = new Croppie(el, {
                viewport: {
                    width: 200,
                    height: 200,
                    type: 'circle'
                },
                boundary: {
                    width: 250,
                    height: 250
                },
                enableOrientation: true
            });
        $.getImage(event.target, croppie); 
    });

    $("#upload").on("click", function() {
        croppie.result('base64').then(function(base64) {
            $("#myModal").modal("hide"); 
            $("#profile-pic").attr("src",host+"/eventos/public/img/spinner.gif");

            var url = host+"/eventos/public/palestrante/perfil/foto-upload";
            var formData = new FormData();
            formData.append("profile_picture", $.base64ImageToBlob(base64));

            // This step is only needed if you are using Laravel
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: url,
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    if (data.msg == "success") {
                        $("#profile-pic").attr("src", base64); 
                        $("#path_imagem_pal").val(data.file);
                    } else {
                        $("#profile-pic").attr("src",host+"/eventos/public/img/icon-cam.png"); 
                        console.log(data['profile_picture']);
                    }
                },
                error: function(error) {
                    console.log(error);
                    $("#profile-pic").attr("src",host+"/eventos/public/img/icon-cam.png"); 
                }
            });
        });
    });

    // To Rotate Image Left or Right
    $(".rotate").on("click", function() {
        croppie.rotate(parseInt($(this).data('deg'))); 
    });

    $('#myModal').on('hidden.bs.modal', function (e) {
        // This function will call immediately after model close
        // To ensure that old croppie instance is destroyed on every model close
        setTimeout(function() { croppie.destroy(); }, 100);
    })

});