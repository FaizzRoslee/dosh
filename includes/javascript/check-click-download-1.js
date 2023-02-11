

//****************************************************************/
// linuxhouse_20220603
// filename: check-click-download-1.js
// description: checks pdf files before downloading in 
//              /inventory/chemicalView.php
//****************************************************************/


// check if file exist on server before downloading
function checkClickDownload1() {
    // root path
    var document_root = '../';
    
    // path to files folder on server
    var pdf_file_path = 'upload/chemical-prop-pdf/';
    
    // server full path to files
    var root_and_pdf_path = document_root + pdf_file_path;
    
    // search word or keyword in url
    var string_to_find = 'download1.php?file=';
    
    // gets the full path
    var href_full_path = document.getElementById("a-formula2").href;
    
    // check if search word is not found in url
    if (href_full_path.indexOf(string_to_find) < 0) { 
        // download link not properly formed or tempered with
        alert("Download link is broken.");
        return false;// stays on same button on form 
    } 
    
    // check if search word is found in url
    if (href_full_path.indexOf(string_to_find) >= 0) { 
        // split url with respect to search word
        var my_array = href_full_path.split(string_to_find);
        // gets file name from splitted results by get the last element in the array
        var file_name = my_array[my_array.length-1];
        
        // checks if file name is empty
        if (file_name.length === 0) {
            // download link not properly formed or tempered with because file name is empty 
            alert("Download link seems to be broken.");
            return false;// stays on same button on form 
        }

        // get boolen result from url
        var result = doesFileExist(root_and_pdf_path + file_name);
        if (result === true) {
            // yay, file exists!
            return true;// automatically proceeds with button click on form
        } else {
            // file does not exist!
            alert('File was not found on this server.');
            return false;// stays on same button on form 
        }
    }
}


// check if file exist on server by get the status
function doesFileExist(urlToFile) {
    var xhr = new XMLHttpRequest();
    xhr.open('HEAD', urlToFile, false);
    xhr.send();
    
    if (xhr.status == "404") {
        return false;
    } else {
        return true;
    }
}
