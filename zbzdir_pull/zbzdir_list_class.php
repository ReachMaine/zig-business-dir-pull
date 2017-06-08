<?php
/*
	Modifications
	 3June15 zig - add only featured checkbox...
	 8Aug15 zig - use googleoff tag to tell google not to index the bizdir pulls.
	
*/
include('zbzdir_functions.php');

class zbzdir_list extends WP_Widget {


	public function __construct() {
		// widget actual processes
		parent::__construct(
			'zbzdir_list', // Base ID
			__('zigs business pull', 'text_domain'), // Name
			array( 'description' => __( 'A widget for pulling businesses ', 'text_domain' ), ) // Args
		);
	}
	


	public function widget( $args, $instance ) {
		// outputs the content of the widget
		 
	 	global $wpdb;
		/* set up the args */
		$title = $instance['title'];
		$blogid = ($instance['blogid']>0?$instance['blogid']:2);
		$limit_list = ($instance['number']>0?$instance['number']:3);
		$category  = $instance['category'];
		$towns = $instance['towns'];
		$tags =  $instance['tags'];
		$include_image =  !empty( $instance['include_image'] ) ? 1 : 0;
		$only_featured =  !empty( $instance['only_featured'] ) ? 1 : 0;
		$use_icon = false;

		$orig_blog_url = get_site_url();
		$biz_blog_url = get_site_url($blogid);
		
		$html = '<aside class="widget widget-zbzdir-list" >';
		
		if ($use_icon) { $html .= '<i class="icon-camera"></i>'; }
 		$html .= ($title!='')?'<h5 class="widget-title zbzdir-title prl-block-title"><a href="'.$biz_blog_url.'">'.$title.'</a></h5>':'';

 		/* $html .= '<p> blogid:'.$blogid.'</p>';
 		$html .= '<p> category:'.$category.'</p>';
 		$html .= '<p> towns:'.$towns.'</p>';
 		$html .= '<p> number:'.$limit_list.'</p>'; */

 		//echo 'blogid='.$blogid.' limit = '.$limit_list.'</br>';
		$zbzdir_ads = zbzdir_pull_listings($blogid,"ea_",$limit_list, $only_featured,  $towns, $category);
		if ($zbzdir_ads) {
			// build the html to display the biz listing.
			
			$html .= '<ul class="zbzdir-biz-list">';
			foreach ($zbzdir_ads as $ad) {

				$html .= '<li class="zbzdir-biz-listitem';
				if ($ad->featured) {
					$html .= ' featured '; 
				} 
				$html .= '">'; 
				$adlink = $biz_blog_url.'?p='.$ad->ID;
				//$html .= zbzdir_get_ad_html($ad->meta_value, $include_image /* image */, $adlink, $ad->thumburl, $ad->post_title );
			
				$html .=    '<a class="zbzdir-biz-link" href ="'.$adlink.'" data-category="Advertising" data-action="Directory SEM Linkout" data-label="'.$ad->post_title.'" data-value="2">';
				if ($blogid) {
					$html .=    '<div class="zbzdir-biz" style="background-image: url('.$orig_blog_url.'/wp-content/uploads/sites/'.$blogid."/". $ad->thumburl.');" >';
				} else {
					$html .=    '<div class="zbzdir-biz" style="background-image: url('.$biz_blog_url.'/wp-content/uploads/'.$ad->thumburl.');" >';
				}
				$html .=       	'<div class="zbdir-title">'.$ad->post_title.'</div>';

				$html .=       	'<div class="zbdir-addr">';
				if ($ad->street) {
					$html .= $ad->street_number." ".$ad->street;
				} else  {
					$html .= $ad->listing_addr; 
				}
				$html .= 		'</div>'; // zbzdir-addr
				if ($ad->street) {
					$html .= '<div class="zbdir-csz">';
					$html .= $ad->city.", ".$ad->state_short ." ".$ad->zip;
					$html .= '</div>'; // zbzdir-csz
				} 
				if ($ad->listing_phone) {
					$html .= '<div class="zbdir-phone">'.$ad->listing_phone.'</div>';
				}		
				$html .=       '</div>'; //zbzdir-biz
				$html .=     '</a>';
				$html .='</li>';
			}
			$html .= '</ul>';
			$html = '<!--googleoff: all-->'.$html.'<!--googleon: all -->';
		} else {
			$html .= "<!-- zbzdir: no results. -->";
		}
		$html .= '</aside>';
		echo $html;
		
	}
	
 	public function form( $instance ) {
		// outputs the options form on admin
		$blogid = isset($instance['blogid']) ? esc_attr( $instance['blogid'] ) : 2;
		$title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 6;
		$category = isset($instance['category']) ? esc_attr( $instance['category'] ) : '';
		$towns = isset($instance['towns']) ? esc_attr( $instance['towns'] ) : '';
		$tags = isset($instance['tags']) ? esc_attr( $instance['tags'] ) : '';
		$include_image =  isset($instance['include_image']) ? esc_attr( $instance['include_image'] ) : 0;
		$only_featured =  isset($instance['only_featured']) ? esc_attr( $instance['only_featured'] ) : 0;
		?> 
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title (optional):' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p> 
        	<label for="<?php echo $this->get_field_id( 'blogid' ); ?>"><?php _e( 'Blog ID' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'blogid' ); ?>" name="<?php echo $this->get_field_name( 'blogid' ); ?>" type="text" value="<?php echo $blogid; ?>" size="3" />
		</p>
		<p> 
        	<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of biz listings to show:' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" />
		</p>
		<?php /*  comes with images - not an option....
		<p>
			<input id="<?php echo $this->get_field_id('include_image'); ?>" name="<?php echo $this->get_field_name('include_image'); ?>" type="checkbox" <?php checked(isset($instance['include_image']) ? $instance['include_image'] : 0); ?>/>
			<label for="<?php echo $this->get_field_id('include_image'); ?>"><?php _e('Include Image'); ?></label>
		</p>	*/ ?>
		<p>
			<input id="<?php echo $this->get_field_id('only_featured'); ?>" name="<?php echo $this->get_field_name('only_featured'); ?>" type="checkbox" <?php checked(isset($instance['only_featured']) ? $instance['only_featured'] : 0); ?>/>
			<label for="<?php echo $this->get_field_id('only_featured'); ?>"><?php _e('Only Featured Listings'); ?></label>
		</p>			
		
		<p>  
    		<label for="<?php echo $this->get_field_id( 'category' ); ?>"><?php _e('Category (IDs - comma separated):'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'category' ); ?>" name="<?php echo $this->get_field_name( 'category' ); ?>" type="text" value="<?php echo esc_attr( $category ); ?>" />
		</p>   
	    <p> 
	    	<label for="<?php echo $this->get_field_id( 'towns' ); ?>"><?php _e('Region (IDs - comma separated):'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'towns' ); ?>" name="<?php echo $this->get_field_name( 'towns' ); ?>" type="text" value="<?php echo esc_attr( $towns ); ?>" />
		</p>
		<?php /* save for later, hopefully never....
		<p> 
	    	<label for="<?php echo $this->get_field_id( 'tags' ); ?>"><?php _e('tags filter - comma separated slugs:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'tags' ); ?>" name="<?php echo $this->get_field_name( 'tags' ); ?>" type="text" value="<?php echo esc_attr( $tags ); ?>" />
		</p> */ ?>
		<?php 
	}

	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
		
		$instance = array();
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['blogid'] = strip_tags($new_instance['blogid']);
		$instance['number'] = (int) $new_instance['number'];
		$instance['category'] = strip_tags($new_instance['category']);
		$instance['towns'] = strip_tags($new_instance['towns']);
		$instance['tags'] = strip_tags($new_instance['tags']);
		$instance['include_image'] = !empty($new_instance['include_image']) ? 1 : 0;
		$instance['only_featured'] = !empty($new_instance['only_featured']) ? 1 : 0;
		
		return $instance;
		
	}
} /* end class */

?>