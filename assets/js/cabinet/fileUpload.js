$(function () {
    var removeButton = "<button type='button' class='btn btn-danger btn-sm remove-file'><i class='fa fa-times' aria-hidden='true'></i></button>";

    var count = 0;
    function createAddFile (fileCount) {
        // grab the prototype template
        var newPrototype = $("#advertisement_prototype_photos").attr('data-prototype');
        // replace the "__name__" used in the id and name of the prototype
        newPrototype = newPrototype.replace(/__name__/g, fileCount);
        newPrototype = $(newPrototype).clone();
        $(newPrototype).find('input').addClass('add-photo');
        $(newPrototype).prepend(removeButton);
        $("#advertisement_prototype_photos").append(newPrototype);
        // Once the file is added
        count++;
    }
    if( $('#advertisement_prototype_photos input').length == 0){
        console.log('asdasd');
        createAddFile(count);
    }

    $('#add-photo-button').on('click', function () {
        createAddFile(count);
    })


    function handleFileSelect(evt) {
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
                $(evt.target).after(span, null);
            };
        })(f);
        // Read in the image file as a data URL.
        reader.readAsDataURL(f);
    }
    $('body').on('change','.add-photo', function (evt) {
        console.log(evt.target);
        handleFileSelect(evt);
    });


    $('body').on('click', '.remove-file', function (evt) {
        console.log($(this).parent());
        $(this).parent().remove();
    });

});
