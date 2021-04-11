<?php
/**
 * Getfit Lite Theme Customizer
 *
 * @package Getfit Lite
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function getfit_lite_customize_register( $wp_customize ) {
	
function getfit_lite_sanitize_checkbox( $checked ) {
	// Boolean check.
	return ( ( isset( $checked ) && true == $checked ) ? true : false );
}
	
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
		
	$wp_customize->add_setting('color_scheme', array(
		'default' => '#e37e12',
		'sanitize_callback'	=> 'sanitize_hex_color',
	));
	
	$wp_customize->add_control(
		new WP_Customize_Color_Control($wp_customize,'color_scheme',array(
			'label' => __('Color Scheme','getfit-lite'),
			'description'	=> __('Select color from here.','getfit-lite'),
			'section' => 'colors',
			'settings' => 'color_scheme'
		))
	);
	
	$wp_customize->add_setting('topbar-color', array(
		'default' => '#000000',
		'sanitize_callback'	=> 'sanitize_hex_color',
	));
	
	$wp_customize->add_control(
		new WP_Customize_Color_Control($wp_customize,'topbar-color',array(
			'label' => __('Topbar background color','getfit-lite'),
			'description'	=> __('Select topbar background color from here.','getfit-lite'),
			'section' => 'colors',
			'settings' => 'topbar-color'
		))
	);
	
	$wp_customize->add_setting('header-color', array(
		'default' => '#000000',
		'sanitize_callback'	=> 'sanitize_hex_color',
	));
	
	$wp_customize->add_control(
		new WP_Customize_Color_Control($wp_customize,'header-color',array(
			'label' => __('Header background color','getfit-lite'),
			'description'	=> __('Select header background color from here.','getfit-lite'),
			'section' => 'colors',
			'settings' => 'header-color'
		))
	);
	
	$wp_customize->add_setting('footer-color', array(
		'default' => '#282a2b',
		'sanitize_callback'	=> 'sanitize_hex_color',
	));
	
	$wp_customize->add_control(
		new WP_Customize_Color_Control($wp_customize,'footer-color',array(
			'label' => __('Footer background color','getfit-lite'),
			'description'	=> __('Select footer background color from here.','getfit-lite'),
			'section' => 'colors',
			'settings' => 'footer-color'
		))
	);
	
	// Slider Section Start		
	$wp_customize->add_section(
        'slider_section',
        array(
            'title' => __('Slider Settings', 'getfit-lite'),
            'priority' => null,
			'description'	=> __('Recommended image size (1420x567). Slider will be visible only when you select static front page.','getfit-lite'),	
        )
    );
	
	$wp_customize->add_setting('page-setting7',array(
			'default' => '0',
			'capability' => 'edit_theme_options',
			'sanitize_callback'	=> 'absint'
	));
	
	$wp_customize->add_control('page-setting7',array(
			'type'	=> 'dropdown-pages',
			'label'	=> __('Select page for slide one:','getfit-lite'),
			'section'	=> 'slider_section'
	));	
	
	$wp_customize->add_setting('page-setting8',array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback'	=> 'absint'
	));
	
	$wp_customize->add_control('page-setting8',array(
			'type'	=> 'dropdown-pages',
			'label'	=> __('Select page for slide two:','getfit-lite'),
			'section'	=> 'slider_section'
	));	
	
	$wp_customize->add_setting('page-setting9',array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback'	=> 'absint'
	));
	
	$wp_customize->add_control('page-setting9',array(
			'type'	=> 'dropdown-pages',
			'label'	=> __('Select page for slide three:','getfit-lite'),
			'section'	=> 'slider_section'
	));	
	
	$wp_customize->add_setting('slide_text',array(
		'default'	=> __('Join Now','getfit-lite'),
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	
	$wp_customize->add_control('slide_text',array(
		'label'	=> __('Add slider link button text.','getfit-lite'),
		'section'	=> 'slider_section',
		'setting'	=> 'slide_text',
		'type'	=> 'text'
	));
	
	$wp_customize->add_setting('hide_slider',array(
			'default' => true,
			'sanitize_callback' => 'getfit_lite_sanitize_checkbox',
			'capability' => 'edit_theme_options',
	));	 

	$wp_customize->add_control( 'hide_slider', array(
		   'settings' => 'hide_slider',
    	   'section'   => 'slider_section',
    	   'label'     => __('Check this to hide slider','getfit-lite'),
    	   'type'      => 'checkbox'
     ));	
	
	// Slider Section End
	
	
	 // Topbar		
	$wp_customize->add_section(
        'topbar_section',
        array(
            'title' => __('Topbar', 'getfit-lite'),
            'priority' => null,
			'description'	=> __('Add content for topbar','getfit-lite'),	
        )
    );
	
	$wp_customize->add_setting('phone',array(
			'default' => null,
			'capability' => 'edit_theme_options',
			'sanitize_callback'	=> 'sanitize_text_field'
	));
	
	$wp_customize->add_control('phone',array(
			'type'	=> 'text',
			'label'	=> __('Add contact number.','getfit-lite'),
			'section'	=> 'topbar_section'
	));	
	
	$wp_customize->add_setting('email',array(
			'default' => null,
			'capability' => 'edit_theme_options',
			'sanitize_callback'	=> 'sanitize_email'
	));
	
	$wp_customize->add_control('email',array(
			'type'	=> 'text',
			'label'	=> __('Add email address.','getfit-lite'),
			'section'	=> 'topbar_section'
	));
	
	$wp_customize->add_setting('hide_topbar',array(
			'default' => true,
			'sanitize_callback' => 'getfit_lite_sanitize_checkbox',
			'capability' => 'edit_theme_options',
	));	 

	$wp_customize->add_control( 'hide_topbar', array(
		   'settings' => 'hide_topbar',
    	   'section'   => 'topbar_section',
    	   'label'     => __('Check this to hide topbar','getfit-lite'),
    	   'type'      => 'checkbox'
     ));		
	
}
	
	
add_action( 'customize_register', 'getfit_lite_customize_register' );	

function getfit_lite_css(){
		?>
        <style>
				a, 
				.tm_client strong,
				.postmeta a:hover,
				#sidebar ul li a:hover,
				.blog-post h3.entry-title,
				.main-nav ul li a:hover,
				.sitenav ul li a:hover, 
				.sitenav ul li.current_page_item a, 
				.sitenav ul li:hover a.parent,
				.sitenav ul li ul.sub-menu li a:hover, 
				.sitenav ul li.current_page_item ul.sub-menu li a:hover, 
				.sitenav ul li ul.sub-menu li.current_page_item a,
				.home-section .home-left h3,
				.sitenav ul li a:after, .sitenav ul li.current_page_item a:after,
				.sitenav ul li a:before, .sitenav ul li.current_page_item a:before
				{
					color:<?php echo esc_html(get_theme_mod('color_scheme','#e37e12')); ?>;
				}
				a.blog-more:hover,
				#commentform input#submit,
				input.search-submit,
				.nivo-controlNav a.active,
				.blog-date .date,
				a.read-more,
				.header-top,
				.nivo-caption a.button,
				.home-section .home-left a.ReadMore,
				.nav-links .current, .nav-links a:hover,
				.nivo-caption a.button,
				.sitenav ul li:hover > ul{
					background-color:<?php echo esc_html(get_theme_mod('color_scheme','#e37e12')); ?>;
				}
				a.morebutton{
					border-color:<?php echo esc_html(get_theme_mod('color_scheme','#e37e12')); ?>;
				}
				.header-top{
					background-color:<?php echo esc_html(get_theme_mod('topbar-color','#000000')); ?>;
				}
				#header{
					background-color:<?php echo esc_html(get_theme_mod('header-color','#000000')); ?>;
				}
				@media screen and (max-width: 980px){
					.header_right .sitenav ul li a:hover{
							color:<?php echo esc_html(get_theme_mod('color_scheme','#e37e12')); ?> !important;
						}	
				}
				.copyright-wrapper{background-color:<?php echo esc_html(get_theme_mod('footer-color','#282a2b')); ?>;}
		</style>
	<?php }
add_action('wp_head','getfit_lite_css');

function getfit_lite_customize_preview_js() {
	wp_enqueue_script( 'getfit-lite-customize-preview', get_template_directory_uri() . '/js/customize-preview.js', array( 'customize-preview' ), '20141216', true );
}
add_action( 'customize_preview_init', 'getfit_lite_customize_preview_js' );