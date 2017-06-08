<?php 
/*
Plugin Name: zig's pull from bizdir listings

Description:  Plugin to pull business from business directory listings within wordpress
multi-site

Version: 0.6

Author: zig
Date: 1june15	
Author URI: http://wwww.reachmaine.com

License: GPL3

*/ 
      
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    include('zbzdir_list_class.php');

	add_action( 'widgets_init', 'load_zbzdir');
	function load_zbzdir() {
		register_widget( 'zbzdir_list' );
	}

	function zbzdir_head() {
		wp_enqueue_style('zbzdir', plugins_url().'/zbzdir_pull/zbzdir.css');
	}
	add_action( 'wp_enqueue_scripts',  'zbzdir_head' );