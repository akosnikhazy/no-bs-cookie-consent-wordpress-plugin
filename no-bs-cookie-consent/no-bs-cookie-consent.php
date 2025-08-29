<?php
/*
Plugin Name: No BS Cookie Consent
Description: Just a simple cookie consent plugin. Until user accepts it will delete all cookies
Version: 1.0
Author: Ákos Nikházy
License: GPL2
*/

if (!defined('ABSPATH')) {
    exit;
}


add_action('wp_enqueue_scripts', 'no_bs_cookie_consent_styles');
add_action('admin_enqueue_scripts', 'no_bs_cookie_consent_admin_styles');

add_action('init','no_bs_cookie_consent_check_cookies');
add_action('wp_footer',  'no_bs_cookie_consent_html');
add_action('admin_menu', 'no_bs_cookie_consent_menu');




function no_bs_cookie_consent_menu() {
	add_options_page(
        'No BS 🍪 Consent', 
        'No BS 🍪 Consent Settings', 
        'manage_options', 
        'no-bs-cookie-consent-settings',
        'no_bs_cookie_consent_settings_page'
    );
}

function no_bs_cookie_consent_check_cookies()
{
	
	
	if(isset($_POST['no_bs_cookie_accepted']))
	{
		if (!isset($_POST['no_bs_cookie_consent_nonce']) || !wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['no_bs_cookie_consent_nonce'])), 'no_bs_cookie_consent_action')) {
			// Nonce verification failed
			wp_die('Nonce verification failed. Please try again.');
		}
		
		setcookie('no_bs_cookie_consent_cookie', 'accepted_cookies', time() + 60*60*24*365, '/', COOKIE_DOMAIN);
		if(isset($_SERVER['HTTP_REFERER']))
		{
			wp_redirect(wp_unslash( $_SERVER['HTTP_REFERER']));
		}
	}
	
	if(!isset($_COOKIE['no_bs_cookie_consent_cookie']))
	{ // we do not have the cookie, so we delete all cookies
			
		foreach ($_COOKIE as $cookieName => $cookieValue) 
		{
			setcookie($cookieName, null, time() - 3600,  '/', COOKIE_DOMAIN); 
		}
	}
	
}

function no_bs_cookie_consent_settings_page()
{
	if (!current_user_can('manage_options')) return;
	
	if (isset($_POST['submit']))
	{ // save changes
		// Verify the nonce
		if (!isset($_POST['no_bs_cookie_consent_nonce']) || !wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['no_bs_cookie_consent_nonce'])), 'no_bs_cookie_consent_action')) {
			// Nonce verification failed
			wp_die('Nonce verification failed. Please try again.');
		}
		
		
		
		if(isset($_POST['no_bs_cookie_consent_text']))
			$text	 	= sanitize_text_field(wp_unslash($_POST['no_bs_cookie_consent_text']));
		
		if(isset($_POST['no_bs_cookie_consent_accept_button']))
			$accept	 	= sanitize_text_field(wp_unslash($_POST['no_bs_cookie_consent_accept_button']));
		
		if(isset($_POST['no_bs_cookie_consent_decline_button']))
			 $decline	 	= sanitize_text_field(wp_unslash($_POST['no_bs_cookie_consent_decline_button']));
		
		if(isset($_POST['no_bs_cookie_consent_policy_label']))
			$policylabel 	= sanitize_text_field(wp_unslash($_POST['no_bs_cookie_consent_policy_label']));
		
		if(isset($_POST['no_bs_cookie_consent_policy_link']))
			$policylink	 	= sanitize_text_field(wp_unslash($_POST['no_bs_cookie_consent_policy_link']));
		
		update_option('no_bs_cookie_consent_text', $text);
		update_option('no_bs_cookie_consent_accept', $accept);
		update_option('no_bs_cookie_consent_decline', $decline);
		update_option('no_bs_cookie_consent_policy_label', $policylabel);
		update_option('no_bs_cookie_consent_policy_link', $policylink);
	}
	
	
	$text 		= get_option('no_bs_cookie_consent_text','By clicking Accept you accept that we save cookies on your computer. Clicking Decline will prevent all cookies saved on this site.');	
	$accept		= get_option('no_bs_cookie_consent_accept','Accept');	
	$decline	= get_option('no_bs_cookie_consent_decline','Decline');
	$policylabel= get_option('no_bs_cookie_consent_policy_label','Policy');	
	$policylink = get_option('no_bs_cookie_consent_policy_link','');
	/* Hidden nonce field HTML */
	$html_escaped = '<div class="wrap JDMwrap">
				<h1>No BS 🍪 Consent Settings</h1>
				
				<form method="post" action="">
					'. wp_nonce_field('no_bs_cookie_consent_action', 'no_bs_cookie_consent_nonce');
	

	
	/* Banner text */
	$html_escaped .= '<label>Text<br>
					<textarea name="no_bs_cookie_consent_text" id="no_bs_cookie_consent_text" placeholder="By clicking Accept you accept that we save cookies on your computer. Clicking Decline will prevent all cookies saved on this site.">' .  esc_attr($text) . '</textarea>';
	
	/* Button texts */
	$html_escaped .= '<hr><label for="no_bs_cookie_consent_accept_button">Accept Button</label><br>
					<input type="text" id="no_bs_cookie_consent_accept_button" name="no_bs_cookie_consent_accept_button" value="' .  esc_attr($accept) . '"><br>
					<label for="no_bs_cookie_consent_decline_button">Decline Button</label><br>
					<input type="text" id="no_bs_cookie_consent_decline_button" name="no_bs_cookie_consent_decline_button" value="' .  esc_attr($decline) . '"><br>';
	
	/* Policy data */
	$html_escaped .= '<hr><label for="no_bs_cookie_consent_policy_label">Policy Label</label><br>
					<input type="text" id="no_bs_cookie_consent_policy_label" name="no_bs_cookie_consent_policy_label" value="' .  esc_attr($policylabel) . '"><br>
					<label for="no_bs_cookie_consent_policy_link">Policy Link</label><br>
					<input type="text" id="no_bs_cookie_consent_policy_link" name="no_bs_cookie_consent_policy_link" value="' .  esc_attr($policylink) . '"><br>';
	/* Submit button HTML */
	$html_escaped .= '<br><br>
				<input type="submit" name="submit" class="button button-primary" value="Save Changes"></form>';
	
	$allowed_html = array(
					'div' => array(
						'class' => array(), 
						'id' => array(),
					),
					'textarea' => array(
						'name' => array(),
						'placeholder' => array(),
						'id' => array()
					),
					'form' => array(
						'method' => array(),
						'action' => array()
					),
					'h1' => array(),
					'h2' => array(),
					'h3' => array(),
					'p' => array(), 
					'label' => array(
						'for' => array(),
						'class'=> array(),
						'id' => array(),
					), 
					'input' => array( 
						'type' => array(),
						'name' => array(),
						'value' => array(),
						'id' => array(),
						'class' =>array(),
						'checked' => array(),
					),
					'hr' => array(),
					'br' => array(), 
					'a' => array( 
						'href' => array(),
						'target' => array(),
					)
				);
	echo wp_kses($html_escaped,$allowed_html);
}

function no_bs_cookie_consent_html()
{
	$text 		= get_option('no_bs_cookie_consent_text','By clicking Accept you accept that we save cookies on your computer. Clicking Decline will prevent all cookies saved on this site.');	
	$accept		= get_option('no_bs_cookie_consent_accept','Accept');	
	$decline	= get_option('no_bs_cookie_consent_decline','Decline');	
	$policylabel= get_option('no_bs_cookie_consent_policy_label','Policy');	
	$policylink = get_option('no_bs_cookie_consent_policy_link','');
	
	if(!isset($_COOKIE['no_bs_cookie_consent_cookie']))
	{
		$allowed_html = array(
					'input' => array( 
						'type' => array(),
						'name' => array(),
						'value' => array(),
						'id' => array(),
						'class' =>array(),
						'checked' => array(),
					)
					
				);
		?>
		<aside id="no_bs_cookie_consent">
			<?php echo wp_kses($text,$allowed_html); ?> <a href="<?php echo wp_kses($policylink,$allowed_html); ?>"><?php echo  wp_kses($policylabel,$allowed_html); ?></a><br><form method="post" action="" class="no_bs_cookie_forms"><?php echo  wp_kses(wp_nonce_field('no_bs_cookie_consent_action', 'no_bs_cookie_consent_nonce'),$allowed_html);?><input type="submit" class="btn btngreen" name="no_bs_cookie_accepted" value="<?php echo  wp_kses($accept,$allowed_html); ?>"></form> <form method="post" action="" class="no_bs_cookie_forms"><input type="submit" class="btn btnred" name="no_bs_cookie_declined" value="<?php echo wp_kses($decline,$allowed_html); ?>"></form>
		
 		</aside>
	<?php
	}
}


function no_bs_cookie_consent_styles()
{
	wp_enqueue_style('no-bs-cookie-consent-styles', plugin_dir_url(__FILE__) . 'css/no-bs-cookie-consent.css',array(),time());
}


function no_bs_cookie_consent_admin_styles($hook)
{
	if ($hook != 'settings_page_no-bs-cookie-consent-settings') return;
	
	wp_enqueue_style('no-bs-cookie-consent-admin-styles', plugin_dir_url(__FILE__) . 'css/no-bs-cookie-consent-admin.css',array(),time());
}