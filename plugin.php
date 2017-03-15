<?php
/*
Plugin Name: Genesis Simple Love
Plugin URI: https://wordpress.org/plugins/genesis-simple-love
Description: Add Love feature on Genesis Simple Share Plugin. <strong>This plugin requires Genesis Simple Share. Thanks!</strong>
Version: 2.0
Author: Phpbits Creative Studio
Author URI: https://phpbits.net/
License: GPL2
*/

class PHPBITS_GenesisSimpleLove{
	function __construct(){
		add_action( 'wp_enqueue_scripts', array( &$this,'enqueue_scripts'), 99 );
		add_action( 'wp_ajax_genesis_simple_love', array( $this, 'ajax_love' ));
		add_action( 'wp_ajax_nopriv_genesis_simple_love', array( $this, 'ajax_love' ));
		add_filter( 'the_content', array( $this, 'archive_fix' ), 15 );
		add_filter( 'the_excerpt', array( $this, 'archive_fix' ), 15 );
	}

	function enqueue_scripts(){
		wp_enqueue_style( 'simple-love-css', plugins_url( 'css/simple_love.css' , __FILE__ ) , array(), null );
		wp_enqueue_script(
			'jquery-simple-love',
			plugins_url( 'js/simple_love.js' , __FILE__ ),
			array( 'jquery', 'genesis-simple-share-plugin-js' ),
			'',
			true
		);
		$front_end = array(
			'fe' => $this->front_end(),
			'nonce' => wp_create_nonce( 'phpbits_love_nonce' ),
			'ajaxurl' =>  admin_url('admin-ajax.php')
		);
		wp_localize_script( 'jquery-simple-love', 'simple_love', $front_end );
	}

	function front_end(){
		global $post;
		$postid = $post->ID;
		$position = 'before';
		switch( genesis_get_option( 'general_position', 'genesis_simple_share' ) ){

			case 'before_content':
				$position = 'before';
				break;

			case 'after_content':
				$position = 'after';
				break;

			case 'both':
				$position = 'before';
				break;

		}
		$post_love = (int) get_post_meta($post->ID, '_genesis_simple_love_', true);
		if(!is_single()){
			$post_love = '<!--__love__-->';
			$postid = '--__id__--';
		}

		//check if already loved
		$loved = array();
		if( isset( $_COOKIE['genesis_simple_love'] ) ){
            $loved = @unserialize( base64_decode ($_COOKIE['genesis_simple_love'] ) );
        }

		$love_txt = apply_filters( 'genesis_simple_love_loved', __( 'Loved', 'genesis-simple-love' ) );

		if ( is_array( $loved ) && !in_array( $postid, $loved ) ){
			$love_txt = apply_filters( 'genesis_simple_love_text', __( 'Love', 'genesis-simple-love' ) );
		}

		$code = '<div class="simple-love sharrre" id="simple-'. $position .'-'. $postid .'" data-id="'. $postid .'" ><div class="box"><a class="count" href="#">'. $post_love .'</a><a class="share" href="#">'. $love_txt .'</a></div></div>';

		return $code;
	}

	function ajax_love() {
		$result = array();
		$nonce = $_POST['nonce'];
		$post_id = $_POST['post_id'];
		$loved = array();

        if ( !wp_verify_nonce( $nonce, 'phpbits_love_nonce' )) {
			exit( 'You don\'t have any power here!' );
		}

        $handle = '';
        if( isset( $_COOKIE['genesis_simple_love'] ) ){
            $loved = @unserialize( base64_decode ($_COOKIE['genesis_simple_love'] ) );
        }

        //save love
        if ( is_array($loved) && !in_array( $post_id, $loved ) ){
        		$loved[] = $post_id;
                $post_loved = (int) get_post_meta($post_id, '_genesis_simple_love_', true);
                $post_loved++;
                update_post_meta($post_id, '_genesis_simple_love_', $post_loved);

                $_COOKIE['genesis_simple_love']  = base64_encode(serialize($loved));
                setcookie('genesis_simple_love', $_COOKIE['genesis_simple_love'] , time()+(10*365*24*60*60),'/');

                $result['type'] = 'success';
                $result['message'] = apply_filters('genesis_simple_love_message', __('Thank You for loving this!', 'genesis-simple-love') );
                $result['count'] = $post_loved;
        }else{
        	$result['type'] = 'error';
        	$result['message'] = apply_filters('genesis_simple_loved', __( 'You already loved this. Thanks!', 'genesis-simple-love' ) );
        }

		$result['loved'] = apply_filters( 'genesis_simple_love_loved', __( 'Loved', 'genesis-simple-love' ) );

		echo $result = json_encode($result);
		die();
	}

	function archive_fix($content){
		if(!is_single()){
			$post_love = (int) get_post_meta(get_the_ID(), '_genesis_simple_love_', true);
			$content = '<input type="hidden" class="simple-love-fix" data-id="'. get_the_ID() .'" value="'. $post_love .'" />' . $content;
		}
		return $content;
	}
}

new PHPBITS_GenesisSimpleLove();

//add widget, @since 2.0
require_once plugin_dir_path( __FILE__ ) . 'includes/widget.php';
