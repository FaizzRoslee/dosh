	<!-- Begin
	/* This script and many more are available free online at
	The JavaScript Source!! http://javascript.internet.com
	Created by: Abraham Joffe :: http://www.abrahamjoffe.com.au/ */
	/***** CUSTOMIZE THESE VARIABLES *****/
	// width to resize large images to
	var maxWidth=140;
	// height to resize large images to
	var maxHeight=150;
	// valid file types
	var fileTypes=["bmp","gif","png","jpg","jpeg"];
	// the id of the preview image tag
	var outImage="previewField";
	// what to display when the image is not valid
	//var defaultPic="spacer.gif";
	var defaultPic="../..images/no_photo.jpg";
	/***** DO NOT EDIT BELOW *****/
	function preview(what){
		var source=what.value;
		var ext=source.substring(source.lastIndexOf(".")+1,source.length).toLowerCase();
		for (var i=0; i<fileTypes.length; i++){
			if (fileTypes[i]==ext){
				break;
			}
		}
		globalPic=new Image();
		if (i<fileTypes.length){
			//Obtenemos los datos de la imagen de firefox
			try{
				globalPic.src=what.files[0].getAsDataURL();
			}catch(err){
				globalPic.src=source;
			}
		}else {
			globalPic.src=defaultPic;
			alert("THAT IS NOT A VALID IMAGE\nPlease load an image with an extention of one of the following:\n\n"+fileTypes.join(", "));
		}
		setTimeout("applyChanges()",200);
	}
       
	var globalPic;
	function applyChanges(){
		var field=document.getElementById(outImage);
		var x=parseInt(globalPic.width);
		var y=parseInt(globalPic.height);
		if (x>maxWidth) {
			y*=maxWidth/x;
			x=maxWidth;
		}
		if (y>maxHeight) {
			x*=maxHeight/y;
			y=maxHeight;
		}
		field.style.display=(x<1 || y<1)?"none":"";
		field.src=globalPic.src;
		field.width=x;
		field.height=y;
	}
	// End -->
