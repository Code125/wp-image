<?php
/**
 * function: code125_get_size_array
 *
 * @param none
 * @return array of the image sizes you register to use
 */
function code125_get_size_array() {
	$images_size_array=array(
		array(
		   'slug' => 'full',
		   'width' => 999999,
		   'height' => 9999,
		   'crop' => false,
		),
		array(
		   'slug' => 'large',
		   'width' => 640,
		   'height' => 640,
		   'crop' => false,
		),
		array(
		   'slug' => 'medium',
		   'width' => 300,
		   'height' => 300,
		   'crop' => false,
		),
		array(
		   'slug' => 'thumbnail',
		   'width' => 150,
		   'height' => 150,
		   'crop' => false,
		),
		
		
		array(
		   'slug' => 'image-size-1',
		   'width' => 640,
		   'height' => 300,
		   'crop' => true,
		)
	);
	return $images_size_array;
}
/**
* function: code125_images_size_array
*
* @param name srting
* @return array of the image properties to use in the crop/resize function
*/
function code125_images_size_array($name) {
		

	$return = array();
	$images_size_array = code125_get_size_array();
	foreach ($images_size_array as $array) {
	
		if($array['slug']== $name){
			$return['width'] = $array['width'];
			$return['height'] = $array['height'];
			$return['crop'] = $array['crop'];
		}
		
	}
	
	if(count($return) == 0){
		$return = array(
		   'slug' => 'full',
		   'width' => 99999,
		   'height' => 99999,
		   'crop' => false,
		);
	
	}
	return $return;
}
/**
* function: get_attachment_id_from_src
*
* @param attachment_src srting
* @return id of the image attachment, if dont' exist it will return the given url
*/
function get_attachment_id_from_src ($attachment_src) {
	global $wpdb;
	$query = "SELECT ID FROM {$wpdb->posts} WHERE guid='$attachment_src'";
	$id = $wpdb->get_var($query);
	if($id == ''){
		$id = $attachment_src;
	}		
	return $id;
}

/**
* function: code125_wp_get_attachment_image_src
*
* @param id_link srting, the attachment id
* @param size string, size name
* @return array that contains the source and dimensions of the image and if it is retina or not.
*/
function code125_wp_get_attachment_image_src($id_link='', $size='') {
	
	$data = code125_images_size_array($size);
	
	if($data){
	$width=$data['width'];
	$height=$data['height'];
	$crop=$data['crop'];
	
	if( is_numeric($id_link)){
	$image_url = wp_get_attachment_image_src( $id_link, 'full');
	
	
	
		$old_height  = $image_url[2];
	
		$old_width  = $image_url[1];
	
		if($height > $old_height &&  $width > $old_width ){
			$height = $old_height;
			$width = $old_width;
		}	
	
	
		if($crop==false){
			if ($height > $width){
				$height = $width * $old_height / $old_width;
			
			}
			elseif ($width > $height){
				$width = $height * $old_width / $old_height;
			
			}
			else{
				if ($old_height > $old_width) {
					$height = $width * $old_height / $old_width;
				}elseif ($old_width > $old_height) {
					$width = $height * $old_width / $old_height;
				} 
			}
		}else {
			if ($height > $old_height) {
				$height = $old_height;
			}elseif ($width > $old_width) {
				$width = $old_width;
			
			} 
		}
		$width = round($width);
		$height = round($height);
		$write_height =  $height;
		$write_width =  $width;
		
	
		$retina = false;
		if( isset($_COOKIE["device_pixel_ratio"]) ){
			$test_height =  2*$height;
			$test_width =  2*$width;
			if( $test_height < $old_height && $test_width < $old_width ){
				$height = 2*$height;
				$width = 2*$width;
				$retina = true;
			}
	
		}
	
		$base_url = wp_upload_dir();
		$url = str_replace($base_url['baseurl'],$base_url['basedir'],$image_url[0]);
		$image = wp_get_image_editor($url );
		if ( ! is_wp_error( $image ) ) {
		
		    if( !file_exists($image->generate_filename($width .'x' . $height))){
		    $image->resize($width, $height, $crop);
		    $image->set_quality(80);
		    $saved_file = $image->save();
			$new_image_url = str_replace($base_url['basedir'],$base_url['baseurl'],$saved_file['path'] );
			
			}else {
				$new_image_url = str_replace($base_url['basedir'],$base_url['baseurl'], $image->generate_filename($width .'x' . $height) );
			}
			if($retina){
				return array($new_image_url, $width/2, $height/2,$retina);
			
			}else{
				return array($new_image_url, $width, $height,$retina);
			}
		
		
		
		
		}else {
			return false;
		}
	
	}else {
		list($width, $height, $type, $attr) = getimagesize($id_link);
		$image_url = array($id_link,$width,$height,false);
		return $image_url;
		
	}
	
	}else {
		return false;
	}
	
}
/**
* function: code125_the_post_thumbnail
*
* @param post_id srting, the post id
* @param size string, image size name
* @return html echo the featured image of this post in this size.
*/
function code125_the_post_thumbnail($post_id = null, $size = 'post-thumbnail') {
	
	echo  code125_get_the_post_thumbnail($post_id,$size);
	
}
/**
* function: code125_get_the_post_thumbnail
*
* @param post_id srting, the post id
* @param size string, image size name
* @return html  the featured image of this post in this size.
*/
function code125_get_the_post_thumbnail($post_id = null, $size = 'post-thumbnail') {
	$post_id = ( null === $post_id ) ? get_the_ID() : $post_id;
	
	$id_link = get_post_thumbnail_id($post_id);
	
	if ( $id_link ) {
		$image_url = code125_wp_get_attachment_image_src($id_link,$size );
		if($image_url[0]!=''){
		$the_post_thumbnail = '<img src="'.$image_url[0].'" width="'.$image_url[1].'" height="'.$image_url[2].'" alt="" class="thumb-'.$size .'" />';
		}else {
			$the_post_thumbnail ='';
		}
		
	}else {
		$the_post_thumbnail = '';
	}
	
	return $the_post_thumbnail;
}

?>