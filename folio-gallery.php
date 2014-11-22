<?php 
// error_reporting (E_ALL ^ E_NOTICE);
// photo gallery settings
$mainFolder    = 'albums';   // folder where your albums are located - relative to root
$albumsPerPage = '6';       // number of albums per page
$itemsPerPage  = '9';       // number of images per page    
$thumb_width   = '150';      // width of thumbnails
//$thumb_height  = '85';       // height of thumbnails
$extensions    = array(".jpg",".jpeg",".png",".gif",".JPG","JPEG",".PNG",".GIF"); // allowed extensions in photo gallery
$ignore  = array('.', '..', 'thumbs'); // list of folders to ignore from album listing


// create thumbnails from images
function make_thumb($folder,$src,$dest,$thumb_width) {

		// ensure thumbnail folder exits	   
		if (!is_dir($folder.'/thumbs')) {
				mkdir($folder.'/thumbs');
				chmod($folder.'/thumbs', 0777);
		}

	$source_image = imagecreatefromjpeg($folder.'/'.$src);
	$width = imagesx($source_image);
	$height = imagesy($source_image);
	
	$thumb_height = floor($height*($thumb_width/$width));
	
	$virtual_image = imagecreatetruecolor($thumb_width,$thumb_height);
	
	imagecopyresampled($virtual_image,$source_image,0,0,0,0,$thumb_width,$thumb_height,$width,$height);
	
	imagejpeg($virtual_image,$dest,100);
	
}

// display pagination
function print_pagination($numPages,$urlVars,$currentPage) {
        
   if ($numPages > 1) {
      
	   echo '<b>Page '. $currentPage .' of '. $numPages . '</b>';
	   echo '<br />';
   
       if ($currentPage > 1) {
	       $prevPage = $currentPage - 1;
	       echo '<a href="?'. $urlVars .'p='. $prevPage.'">&laquo;&laquo; Last Page </a> ';
	   }	   
	   if ($currentPage != $numPages) {
           $nextPage = $currentPage + 1;	
		   echo ' <a href="?'. $urlVars .'p='. $nextPage.'"> Next Page &raquo;&raquo;</a>';
	   }	  	 

		echo "<div class='paginate-pages'>";

	   for( $e=0; $e < $numPages; $e++ ) {
           $p = $e + 1;
       
	       if ($p == $currentPage) {	    
		       $class = 'current-paginate';
	       } else {
	           $class = 'paginate';
	       } 
	       

		       echo '<a class="'. $class .'" href="?'. $urlVars .'p='. $p .'">'. $p .'</a> ';
		  	  
	   }

		echo "</div>";
	   
   
   }

}


// if an album is not chosen, find a list of folders in the directory and select a random picture from each
if (!isset($_GET['album'])) {

    // display list of albums
    $folders = scandir($mainFolder, 0);
		  
	$albums = array();
	$captions = array();
	$random_pics = array();
	 
	// inspect each folder in directory 
    foreach($folders as $folder) {
        
		// if the folder is not in the ignore list 
	    if(!in_array($folder, $ignore)) {    
		
			// album name
			$album['name'] = $folder;

			// album caption 
			$album['caption'] = substr($album['name'],0,20);

			// loop through each item in directory and scan for a cover image
			$dir = opendir("$mainFolder/" . $album['name']);
			while ( $entry = readdir($dir) ) {
				if ( $entry != '.' && $entry != '..' ) { //Skips over . and ..
		   			$extension = strstr($entry, '.');
		   			if ( in_array($extension, $extensions)) { // skip items not in our specified supported extensions
						$album['coverImage'] = $entry;
						break;
					}
				}
			}


			// push our newly created album object into an array of all albums	
		   array_push( $albums, $album );

		  
		 } // end if folder is not in ignore array
		  
	 } // end foreach folder in dir

 
	// if there were 0 albums found 
     if( count($albums) === 0 ) {
  
        echo "There are currently no albums.  Add some to $mainFolder and they will appear here.";
  
     } else {
 
		// calculate number of albums per page  
		$numPages = ceil( count($albums) / $albumsPerPage );

		// if a page was passed in the url
        if(isset($_GET['p'])) {
     
				// fetch the requested page id 
				$currentPage = $_GET['p'];
				// if page number is above the maximum, seve the last posible page
				if($currentPage > $numPages) {
						$currentPage = $numPages;
				}

		} else {
				// if no page was specified, choose page 1
				$currentPage = 1;
		} 


		// calculate the first album to show  for the selected page
         $start = ( $currentPage * $albumsPerPage ) - $albumsPerPage;
	 

		// display titlebar  html
	     echo '<div class="titlebar">
                 <div class="float-left"><span class="title">Photo Gallery</span> - Albums</div>
			     <div class="float-right">'.count($albums).' albums</div>
              </div>';
	  
         echo '<div class="clear"></div>';
	  	  		

		// slice our album list depending on the page we are on
		$albums = array_slice($albums,$start,$albumsPerPage);	

		// loop through each album 
	     foreach($albums as $album) {
	 
				// find the filesystem location of the thumbnail we will create 
				$thumbDest = $mainFolder . '/' . $album['name'] . '/thumbs/' . $album['coverImage'];
		     	make_thumb( $mainFolder . '/' . $album['name'] . '/', $album['coverImage'], $thumbDest, $thumb_width); 

				// prepend with a slash to get the URL for this thumbnail

			 		 			 
			    echo '<div class="thumb-album shadow">
				        
						<div class="thumb-wrapper">
						   <a href="'.$_SERVER['PHP_SELF'].'?album='. urlencode($album['name']) .'">
			                 <img src="/'.$thumbDest.'" width="'.$thumb_width.'" alt="" />
						   </a>	
					    </div>
						
						<div class="p5"></div>
					    
						<a href="'.$_SERVER['PHP_SELF'].'?album='. urlencode($album['name']) .'">
							<span class="caption">'. $album['caption'] .'</span>
						</a>
		            
					  </div>';
				  
	      }
	  
	      echo '<div class="clear"></div>';
 

		// display page pagination 
          echo '<div align="center" class="paginate-wrapper">';
        	 
                 $urlVars = "";
                 print_pagination($numPages,$urlVars,$currentPage);
  
          echo '</div>';	   
   
     }
  
 
// If an album is chosen
} else {

     // display photos in album
     $src_folder = $mainFolder.'/'.$_GET['album'];
     $src_files  = scandir($src_folder);

     $files = array();


	 // filter all files by file extension
     foreach($src_files as $file) {
        
		$ext = strrchr($file, '.');
        if(in_array($ext, $extensions)) {
          
		   array_push( $files, $file );
        
		 }
      
	  }



     // slice array thumbs down to the limited ones that should be thumbnails and generate them
	 $currentPage = $_GET['p'];
	 if($currentPage <= 1){
	 	$startThumb = 0;
	 } else {
	 	$startThumb = ($currentPage - 1) * $itemsPerPage;
		$startThumb = $startThumb;
	 }
	 $makeIntoThumbs = array_slice($files,$startThumb,$itemsPerPage);
     foreach($makeIntoThumbs as $file) {

			 $thumb = $src_folder.'/thumbs/'.$file;
			 if (!file_exists($thumb)) {
					 make_thumb($src_folder,$file,$thumb,$thumb_width); 
			 } else {
			 }
	}
 

   if ( count($files) == 0 ) {

      echo 'There are no photos in this album!';
   
   } else {
   
      $numPages = ceil( count($files) / $itemsPerPage );

      if(isset($_GET['p'])) {
      
	     $currentPage = $_GET['p'];
         if($currentPage > $numPages) {
            $currentPage = $numPages;
         }

      } else {
         $currentPage=1;
      } 

   $start = ( $currentPage * $itemsPerPage ) - $itemsPerPage;

   echo '<div class="titlebar">
           <div class="float-left"><span class="title">'. $_GET['album'] .'</span> - <a href="'.$_SERVER['PHP_SELF'].'">View All Albums</a></div>
           <div class="float-right">'.count($files).' images</div>
         </div>';	  
   echo '<div class="clear"></div>';


   for( $i=$start; $i<$start + $itemsPerPage; $i++ ) {
		  
		  if( isset($files[$i]) && is_file( $src_folder .'/'. $files[$i] ) ) { 
	   
	        echo '<div class="thumb shadow">
	                <div class="thumb-wrapper">
					<a href="'. $src_folder .'/'. $files[$i] .'" class="albumpix" rel="albumpix">
				      <img src="'. $src_folder .'/thumbs/'. $files[$i] .'" width="'.$thumb_width.'" alt="" />
				    </a>
					</div>  
			      </div>'; 
      
	      } else {
		  
		    if( isset($files[$i]) ) {
			  echo $files[$i];
		    }
		   
		  }
     
    }
	   

     echo '<div class="clear"></div>';
  
     echo '<div align="center" class="paginate-wrapper">';
        	 
        $urlVars = "album=".urlencode($_GET['album'])."&amp;";
        print_pagination($numPages,$urlVars,$currentPage);
  
     echo '</div>';
	 
	 
   } // end else	 

}
?>
