<?php


/**
 * function: code125_get_size_array
 *
 * @param none
 * @return array of the image sizes you register to use
 */
function register_c5_images_plugin() {
  $images_size_array=array(
		
		array(
		   'slug' => 'compare-post-thumb',
		   'width' => 200,
		   'height' => 200,
		   'crop' => true,
		),
		array(
		   'slug' => 'compare-post-table',
		   'width' => 99999,
		   'height' => 200,
		   'crop' => false,
		)
	);
	foreach ($images_size_array as $image ) {
		code125_add_image_size($image['slug'] , $image['width'] , $image['height'], $image['crop']);
	}
	
}

add_action('init' , 'register_c5_images_plugin');
?>
