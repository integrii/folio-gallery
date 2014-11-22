## Overview
folioGallery is an easy to use gallery written by Harry G of foliopages.com.  Just drop it on your PHP web server and edit the ``folio-gallery.php`` file to point the ``$mainFolder`` variable to the location of your pictures you want hosted.  If you are like me, this makes it super easy to turn your pile of home pictures into a browsable website in just a few minutes!

![folioGallery](/screenshot.png)


## DISCLAIMER

I did not create this!  [See the original project from foliopages.com here](http://www.foliopages.com/php-jquery-ajax-photo-gallery-no-database).

## What is this? 

Foliogallery is a php gallery that requires no database or configuration.  Simply drop the gallery into your webroot, symlink the albumbs dir to your pictures and enjoy.  I personally use this for my home family picture active which at the time of this writing is about 400GB large... Too much to use with Picasa over the LAN easily.

## Improvements
### Scale
Foliogallery will now SCALE to any size picture directory.  Previously, thumbnail generation would attempt to target the entire directory being loaded.  Now it only does the pictures being loaded.  Big difference!
### Pagination
When you have hundreds (or thousands) of page numbers, they would line up off the window and be unclickable.  This makes sure that does not happen anymore.
### Image Sizes
When you load up a very lage picture, the lightbox no longer expands off the end of your browser.  The full quality image is still loaded so you can drag it to your desktop, though.

