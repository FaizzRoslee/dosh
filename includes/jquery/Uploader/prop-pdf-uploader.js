


//****************************************************************/
// linuxhouse_20220603
// filename: prop-pdf-uploader.js
// description: checks if only pdf file is uploaded in 
//              /inventory/chemicalAddEdit.php
//****************************************************************/


// old prop-pdf validatation script for file input type in same page with form
//     $('#fpath2').on('change', function() {
//         var input_file_name = $(this)[0].files[0].name;
//         if ( /\.(pdf)$/i.test(input_file_name) === false ) 
//         { 
//             alert(input_file_name + " has invalid extension. Only pdf is allowed."); 
//             reset the contents of file input
//             $("#fpath2").val('');
//         }
//         else
//         {
//             var numb = $(this)[0].files[0].size / 1024 / 1024;
//             numb = numb.toFixed(2);
//             if (numb > 2) 
//             {
//                 alert(input_file_name + ' is too big, maximum size is 2MiB. Your file size is: ' + numb + ' MiB');
//                 reset the contents of file input
//                 $("#fpath2").val('');
//             } 
//         }
//         });



// checks file type, size to make sure it is the accepted
function checkFile(e) {
    // FOR SINGLE FILE UPLOADS
    var file_list = e.target.files;

    // loops through list of files to get their extentsions or file types and their sizes
    for (var i = 0, file; file = file_list[i]; i++) {
        // gets file name
        var input_original_file_name = file.name
        // gets file name without extension
        var input_file_name_new = file.name.split('.')[0]
        // get file size
        var file_size = file.size;
        // set maximum file size
        // var max_file_size = 1 * (1024);  // 1 Kb
        // var max_file_size = 1 * (1024 * 1024);  // 1 Mb
        var max_file_size = 2 * (1024 * 1024);  // 2 Mb
        
        // checks file extension to make sure is pdf
        if (/\.(pdf)$/i.test(input_original_file_name) === false ) 
        { 
            // display message for wrong file extension
            alert(input_original_file_name + " has invalid extension. Only pdf is allowed."); 
            // reset the contents of file input
            document.getElementById('fpath2').value = '';
        }
        else
        {
            // check if file size exceeds the maximum file size
            if (file_size > max_file_size)
            {
                // displays message if file size exceeds the maximum file size
                var file_size_error = input_original_file_name + ' is too big. Maximum file size is: ' + bytesToSize(max_file_size) + '. Uploaded file size is: ' + bytesToSize(file_size) + '.';
                alert(file_size_error);
                // reset the contents of file input
                document.getElementById('fpath2').value = '';
            }
        }
    }
    
}


// gets file size in 'Bytes', 'KB', 'MB', 'GB', 'TB'
function bytesToSize(bytes) {
  const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB']
  if (bytes === 0) return 'n/a'
  const i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)), 10)
  if (i === 0) return `${bytes} ${sizes[i]})`
  return `${(bytes / (1024 ** i)).toFixed(1)} ${sizes[i]}`
}
