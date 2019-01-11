$(function () {
    var removeButton = "<button type='button' class='btn btn-danger btn-sm remove-file'><i class='fa fa-times' aria-hidden='true'></i></button>";
    var fileCount = '{{ advertisement.photos|length }}';
   // console.log(fileCount);
    var count = $('#count-photos').val();

    function createAddFile (fileCount) {
        // grab the prototype template
        var newPrototype = $("#advertisement_prototype_photos").attr('data-prototype');
        // replace the "__name__" used in the id and name of the prototype
        newPrototype = newPrototype.replace(/__name__/g, fileCount);
       /* newPrototype = $(newPrototype).clone();
        $(newPrototype).find('input').addClass('add-photo');*/
        newPrototype = "<div style='display:none'>" + newPrototype + "</div>";

        var myFileUploadButton =  "<div  class = 'my-button pr-2' id='myFileUploadButton" + fileCount + "'>";
        myFileUploadButton += "Вибрати файл...</div>";

        var removeButtonCopy = "<div id = 'remove-file-" +fileCount+ "'>" + removeButton + "</div>";
        newPrototype = "<div class = 'p-2 mb-2'>" + removeButtonCopy + newPrototype + myFileUploadButton + "</div>";
       // $(newPrototype).prepend(removeButtonCopy);
        $("#advertisement_prototype_photos").append(newPrototype);


        $("#myFileUploadButton" + fileCount).on('click', function(e){
            $('#advertisement_photos_' + fileCount + '_file').trigger('click');
        });

        $('#advertisement_photos_' + fileCount + '_file').on('change', function(evt) {
            // Show its name
            var fileName = $(this).prop('files')[0].name;
            $("#myFileUploadButton" + fileCount).html(fileName);
            handleFileSelect(evt, fileCount);
        });


        // Once the file is added
        count++;
    }

    $('.my-button').each(function () {
       $(this).on('click', function () {
           $(this).parent().find('input[type=file]').trigger('click');
       });
        $(this).parent().find('input[type=file]').on('change', function(evt) {
            // Show its name
            var fileName = $(this).prop('files')[0].name;
            var fileId = $(this).attr('id');
            var fileNumber = fileId.replace ( /[^\d.]/g, '' )

            $("#myFileUploadButton" + fileNumber).html(fileName);
            handleFileSelect(evt, fileNumber);
        });
    });

    if($('#advertisement_prototype_photos').length > 0 && $('#advertisement_prototype_photos input').length == 0){
        createAddFile(count);
    }

    $('#add-photo-button').on('click', function () {
        createAddFile(count);
    })

    function handleFileSelect(evt, fileCount) {
        var file = evt.target.files; // FileList object
        var f = file[0];
        // Only process image files.
        if (!f.type.match('image.*')) {
            alert("Image only please....");
        }
        var reader = new FileReader();
        // Closure to capture the file information.
        reader.onload = (function(theFile) {
            return function(e) {
                // Render thumbnail.
                var span = document.createElement('span');
                span.innerHTML = ['<img class="thumb" title="', escape(theFile.name), '" src="', e.target.result, '" />'].join('');
                $('#myFileUploadButton'+fileCount).next('span').remove();
                $('#myFileUploadButton'+fileCount).after(span, null);
            };
        })(f);
        // Read in the image file as a data URL.
        reader.readAsDataURL(f);
    }

    $('body').on('click', '.remove-file', function (evt) {
        $(this).parent().parent().remove();
    });


});
