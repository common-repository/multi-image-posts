<?php
/*
Plugin Name: Multi Image Posts
Author: Tani_Afi
Author URI: https://www.fiverr.com/tani_afi
Description: This plugin will provide you the facility to create multiple post at once by selecting images from plugin page (Auto Posts) and you can set information of each post.
Version: 1.3
License: GPLv2 or later
*/

// create custom plugin settings menu
add_action('admin_menu', 'mip_plugin_menu');
function mip_plugin_menu() {
	add_menu_page('Multi Image Posts', 'Multi Image Posts', 'administrator', __FILE__, 'mip_plugin_page' , plugins_url('/images/icon.png', __FILE__) );
	// Include js files
	wp_enqueue_script('jquery');	
	wp_register_script('mip-upload', plugin_dir_url(__FILE__) . 'js/admin_script.js',array('jquery','media-upload','thickbox'));
	wp_enqueue_script('mip-upload');
}
//Load media files needed for Uploader
function mip_load_wp_media_files() {
  wp_enqueue_media();
}
add_action( 'admin_enqueue_scripts', 'mip_load_wp_media_files' );
function mip_plugin_page() {
?>
<style>
.mip_input{
	width: 400px;
}
</style>
<?php if( current_user_can('administrator')) { ?>
<h2>Multi Image Posts</h2>
<div class="uploader">
   	<input id="mip_image_select" class="mip_image_button" name="image_select" type="button" value="Select Image(s)" />
    <?php
	//Get categories
	$categories = get_categories(); 
	$groups = array();
	foreach ($categories as $category) {
		  //$tests = array($category->cat_ID."-".$category->cat_name);
		$tests = array($category->cat_name); 
		 foreach ($tests as $trys){
			  $groups[] = $trys;
			}
	} 
	  $cList = implode(',', $groups);
	?>
    <input type="hidden" name="mip_CategoreisList" id="mip_CategoreisList" value="<?php echo $cList;?>" />
</div>
<br />
<div id="preview">
</div>
<?php } ?>
<?php
if( current_user_can('administrator')) {
$submit = sanitize_text_field( $_POST['submit'] );
if( isset($submit) && $submit == 'Submit' ) {
	$no_of_imgs = intval( $_POST['no_of_imgs'] );
	
	if(!empty($no_of_imgs)){
		
		for($i=0;$i<$no_of_imgs;$i++) {
			$url_id = intval( $_POST['url_id'][$i] );
			$alt_text = sanitize_text_field( $_POST['alt_text'][$i] );
			$post_title =  sanitize_text_field( $_POST['post_title'][$i] );
			$image_title = sanitize_text_field( $_POST['title'][$i]);
			$image_caption = sanitize_text_field( $_POST['caption'][$i]);
			$tags = $_POST['tags'][$i];
			$attachId=$url_id;
			$image = wp_get_attachment_image_src( $attachId, 'large');
			$image_tag = "<br>".'<img src="'.esc_url($image[0]).'" alt="'.$alt_text.'" />'."<br><center>".$image_caption."</center>";
			$categories = $_POST['categories'][$i];
			if(!empty($categories)){
				$category_ids_array = array();
				foreach($categories as $category){
				// get category ids 
				$category_ids_array[] = get_cat_ID($category);
				}
			}
	
 	$postData = array(
        'post_title' => $post_title,
        'post_type' => 'post',
        'post_content' => $image_tag,
		'post_excerpt' => $image_caption,
        'post_category' => $category_ids_array,
        'post_status' => 'draft'
    );

    $post_id = wp_insert_post($postData);
	if(!empty($tags)){
		// Add post tags
		wp_add_post_tags($post_id,$tags);
	}
	//set image title as a tag
	wp_set_post_tags( $post_id, array($image_title), true );
	
	//sets the given post to the 'gallery' format
	set_post_format($post_id, 'image' ); 
	
    // Update title, attach media to post into the database
    wp_update_post(array(
        'ID' => $attachId,
        'post_parent' => $post_id,
		'post_title'   =>  $image_title,
		'post_excerpt' => $image_caption
    ));

    set_post_thumbnail($post_id, $attachId);
	update_post_meta( $attachId, '_wp_attachment_image_alt', $alt_text );
	//set seo focus keyword
	add_post_meta( $post_id, '_yoast_wpseo_focuskw_text_input', $image_caption );
	add_post_meta( $post_id, '_yoast_wpseo_focuskw', $image_title );
	
	//ping image to post
	//extracing thumbnail by attachid
	$post_thumbnail_url = wp_get_attachment_url( $attachId );
	$img=$post_thumbnail_url;
		if (trim($img) != '' ){
			$pin_images=array(($img));
			add_post_meta( $post_id, 'pin_manual', 'automatic' );
			add_post_meta($post_id,'pin_images',$pin_images);
			}
	
		}
	}
	$message = $no_of_imgs." Post(s) Successfully Created with Draft status!!";
}
}

if (isset($message) ){
        echo '<div class="updated"><p>"'.$message.'"</p></div>';
    }
?>
<br /><br />
<div id="guide"> <!--instructions for user-->
    <strong>How to use:</strong>
 <ol>
     <li>Start by &quot;Select Image(s)&quot; button for adding (selecting) image to upload from plugin page. If user selects multiple images, multiple box will create for posts.</li>
     <li>A box (or multiple ones) create for a post, with image preview on the left and post options on the right.</li>
     <li>Post options includes:     
      <ul type="square">
       <li>Image name display (minus file extension).</li>
       <li>Image TITLE, Image ALT TEXT, Caption and Post Title auto-filled with original file name (user can edit).</li>
       <li>Categories are selectable via available Categories in a blog.</li>
      </ul>
     </li>
 </ol>
<strong>Upon submitting:</strong>
 <ol>
  <li>uploaded Image into Media Library, with TITLE and ALT TEXT being populated properly.</li>
  <li>created Post with Type Image.</li>
  <li>Set Uploaded image as &quot;Featured Image&quot;. It will automatically insert into the post content with Caption.</li>
  <li>Set Image TITLE as Post(s) Title.</li>
  <li>Set Image TITLE as &quot;FOCUS KEYWORD&quot; field of the plugin Yoast SEO.</li>
  <li>Set Tags with user entered data and image title for the post(s).</li>
  <li>Set Image as a Ping for the post(s).</li>
  <li>Set Image Caption as a excerpt for the post(s).</li>
  <li>Post(s) will automatically be created as DRAFT status.</li>
 </ol>
 <p><strong><em><u>Note:</u></em></strong> For demo please see <a href="https://wordpress.org/plugins/multi-image-posts/screenshots/" target="_blank"><strong><em>screenshots</em></strong></a></p>
</div>
<?php }
function mip_add_post_formats() {
    add_theme_support( 'post-formats', array( 'gallery', 'quote', 'video', 'aside', 'image', 'link' ) );
}
 
add_action( 'after_setup_theme', 'mip_add_post_formats', 20 );