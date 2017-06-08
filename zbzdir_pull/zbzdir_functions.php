<?php
/* functions to support zbzdir listing pull*/
	/* funtion to gen SQL and do the actual pull of the text ads */
	function zbzdir_pull_listings($blogid, $prefix,  $limit, $in_featured_only=false, $in_towns='', $in_cats='') {
		global $wpdb;
		if ($in_towns) { 
			// echo "<p> got towns: ".$in_towns.'</p>';
		}
		if ($in_cats) {
			//echo "<p> got cats: ".$in_cats.'</p>';
		}
		$blog_sql = "";
		if ($blogid) {
			$blog_sql = $blogid."_";
		}
				//echo '<p> cats:'.$in_cats.'<br>towns:'.$in_towns.'</p>';
		$calquery = 'SELECT posts.ID, posts.post_title, pm2.meta_value as thumburl ';
		//	-- $calquery .= ', (select meta_value from '.$prefix.$blog_sql.'postmeta pm where posts.ID = pm.post_id AND meta_key="_featured" ) as featured';
		$calquery .= ', (select meta_value from '.$prefix.$blog_sql.'postmeta pm3 where posts.ID = pm3.post_id AND meta_key="_job_location" ) as listing_addr';
		$calquery .= ', (select meta_value from '.$prefix.$blog_sql.'postmeta pm3 where posts.ID = pm3.post_id AND meta_key="geolocation_street_number" ) as street_number';
		$calquery .= ', (select meta_value from '.$prefix.$blog_sql.'postmeta pm3 where posts.ID = pm3.post_id AND meta_key="geolocation_street" ) as street';
		$calquery .= ', (select meta_value from '.$prefix.$blog_sql.'postmeta pm3 where posts.ID = pm3.post_id AND meta_key="geolocation_city" ) as city';
		$calquery .= ', (select meta_value from '.$prefix.$blog_sql.'postmeta pm3 where posts.ID = pm3.post_id AND meta_key="geolocation_state_short" ) as state_short';
		$calquery .= ', (select meta_value from '.$prefix.$blog_sql.'postmeta pm3 where posts.ID = pm3.post_id AND meta_key="geolocation_postcode" ) as zip';
		$calquery .= ', (select meta_value from '.$prefix.$blog_sql.'postmeta pm where posts.ID = pm.post_id AND meta_key="_phone" ) as listing_phone';
		$calquery .= ' FROM '.$prefix.$blog_sql.'posts as posts ';
		$calquery .= ' LEFT JOIN '.$prefix.$blog_sql.'postmeta pm1 ON (posts.ID = pm1.post_id AND pm1.meta_value IS NOT NULL AND pm1.meta_key = "_thumbnail_id" )';		 
  		$calquery .= ' LEFT JOIN '.$prefix.$blog_sql.'postmeta pm2 ON (pm1.meta_value = pm2.post_id AND pm2.meta_key = "_wp_attached_file" AND pm2.meta_value IS NOT NULL)';
  		if ($in_towns) {
  			$calquery .= 'LEFT JOIN '.$prefix.$blog_sql.'term_relationships tm1 ON (posts.ID = tm1.object_id)';
  			$calquery .= 'LEFT JOIN '.$prefix.$blog_sql.'term_taxonomy tt1 ON (tm1.term_taxonomy_id = tt1.term_taxonomy_id)';
  		}
  		if ($in_cats) {
			$calquery .= 'LEFT JOIN '.$prefix.$blog_sql.'term_relationships tm2 ON (posts.ID = tm2.object_id)';
  			$calquery .= 'LEFT JOIN '.$prefix.$blog_sql.'term_taxonomy tt2 ON (tm2.term_taxonomy_id = tt2.term_taxonomy_id)';
  		}
		$calquery .= ' WHERE posts.post_status="publish"';
		$calquery .= ' and posts.post_type = "job_listing" ';
		if ($in_featured_only) {
			$calquery .= ' AND EXISTS (select meta_value from '.$prefix.$blog_sql.'postmeta pm where posts.ID = pm.post_id AND meta_key="_featured" AND meta_value = 1) ';
		}
		if ($in_towns) {
			$calquery .= ' AND tt1.taxonomy = "job_listing_region" AND tt1.term_id in ('.$in_towns.')';
		}
		if ($in_cats) {
			$calquery .= ' AND tt2.taxonomy = "job_listing_category" AND tt2.term_id in ('.$in_cats.')';
		}
    	$calquery .= ' ORDER BY RAND() ';
		$calquery .= " LIMIT ".$limit; 

				// echo $calquery;
		$calresult = $wpdb->get_results($calquery); 
				//var_dump($calresult); 
		return $calresult;	
		
	} /* end pull listings */


?>