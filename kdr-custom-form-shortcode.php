<?php
/**
 * Plugin Name: KDR Custom Form Shortcode
 * Plugin URI: https://www.kodrindonesia.com
 * Description: Provide you custom form tag html using shortcode in a nice ways!
 * Version: 1.0.0
 * Author: Kodr Indonesia
 * Author URI: https://www.kodrindonesia.com
 * License: GPL2
 */

add_shortcode('kdr_form_open','kdr_form_open');
function kdr_form_open($atts)
{
    do_action('kdr_form_pre_form_display');

	$output = '';

	$options = shortcode_atts(array(
        'name' => '',
        'action' => '',
        'method' => 'GET',
        'class' => 'form',
    ), $atts);
 

	$output .= '<form name="'.$options['name'].'" action="'.$options['action'].'" method="'.$options['method'].'" class="'.$options['class'].'">';

	return $output;
}

add_shortcode('kdr_form_close','kdr_form_close');
function kdr_form_close()
{
    $output = '';

    //insert input hidden for identifier
    $output .= wp_nonce_field('kdr_form', 'nonce_kdr_form');
    $output .= '</form>';

	return $output;
}

add_shortcode('kdr_input','kdr_input_form');
function kdr_input_form($atts)
{
    $wrapper_start = '';
    $label = '';
    $wrapper_end = '';

	$options = shortcode_atts(array(
        'type' => 'text',
        'name' => '',
        'class' => 'form-control input-md',
        'value' => '',
        'label' => ''
    ), $atts);

    //generate automatic input name
    if($options['name'] == '')
    	$options['name'] = kdr_generate_input_name($options['name'],$options['label']);

    if($options['type'] != 'checkbox') {
        $wrapper_start .= '<div class="form-group">';
        $label   .= '<label>'.$options['label'].'</label>';
    }
    	
    $input = '';
    switch($options['type']) {
    	case 'text':
    		$input = kdr_input_text(array(
    									'name' => $options['name'],
    									'class' => $options['class'],
    									'value' => $options['value']
    								));
    		break;
        case 'password':
            $input = kdr_input_password(array(
                                        'name' => $options['name'],
                                        'class' => $options['class'],
                                        'value' => $options['value']
                                    ));
            break;
        case 'checkbox':
            $input = kdr_input_checkbox(array(
                                        'name' => $options['name'],
                                        'class' => $options['class'],
                                        'value' => $options['value'],
                                        'label' => $options['label']
                                    ));
            break;
    	default:
    		break;
    }

    if($options['type'] != 'checkbox') {
        $wrapper_end .= '</div>';
    }

    return $wrapper_start . $label . $input . $wrapper_end;
}

/**
 * Label
 **/
add_shortcode('kdr_label','kdr_label');
function kdr_label($atts)
{
	$options = shortcode_atts(array(
        'label' => ''
    ), $atts);

    return '<label>'.$options['label'].'</label>';
}

add_shortcode('kdr_form_submit','kdr_form_submit');
function kdr_form_submit($atts)
{
	$options = shortcode_atts(array(
		'type' => 'submit',
        'label' => '',
        'class' => 'btn btn-danger btn-lg btn-block uppercase'
    ), $atts);

    return '<button type="'.$options['type'].'" class="'.$options['class'].'">'.$options['label'].'</button>';
}

/**
 * Input Text
 **/
add_shortcode('kdr_input_text','kdr_input_text');
function kdr_input_text($atts) 
{
	$output = '';

	$options = shortcode_atts(array(
        'type' => 'text',
        'name' => '',
        'class' => 'form-control input-md',
        'value' => ''
    ), $atts);

    //generate automatic input name
    if($options['name'] == '')
    	$options['name'] = kdr_generate_unique_input_name();

	$output = '<input type="'.$options['type'].'" name="'.$options['name'].'" class="'.$options['class'].'" value="'.$options['value'].'">';

	return $output;
}

/**
 * Input Password
 **/
add_shortcode('kdr_input_password','kdr_input_password');
function kdr_input_password($atts) 
{
    $output = '';

    $options = shortcode_atts(array(
        'type' => 'password',
        'name' => '',
        'class' => 'form-control input-md',
        'value' => ''
    ), $atts);

    //generate automatic input name
    if($options['name'] == '')
        $options['name'] = kdr_generate_unique_input_name();

    $output = '<input type="'.$options['type'].'" name="'.$options['name'].'" class="'.$options['class'].'" value="'.$options['value'].'">';

    return $output;
}

/**
 * Input Hidden
 **/
add_shortcode('kdr_input_hidden','kdr_input_hidden');
function kdr_input_hidden($atts)
{
    $output = '';

    $options = shortcode_atts(array(
        'type' => 'hidden',
        'name' => '',
        'value' => ''
    ), $atts);

    //generate automatic input name
    if($options['name'] == '')
        $options['name'] = kdr_generate_unique_input_name();

    $output = '<input type="'.$options['type'].'" name="'.$options['name'].'" value="'.$options['value'].'">';

    return $output;
}

/**
 * Checkbox
 **/
add_shortcode('kdr_input_checkbox','kdr_input_checkbox');
function kdr_input_checkbox($atts)
{
    $output = '';

    $options = shortcode_atts(array(
        'type' => 'checkbox',
        'name' => '',
        'value' => '',
        'label' => ''
    ), $atts);

    //generate automatic input name
    if($options['name'] == '')
        $options['name'] = kdr_generate_unique_input_name();

    $output .= '<label class="checkbox-inline">';
    $output .= '<input type="'.$options['type'].'" name="'.$options['name'].'" value="'.$options['value'].'"> '.$options['label'];
    $output .= '</label>';

    return $output;
}

/**
 * Form Group
 **/
add_shortcode('kdr_form_group', 'kdr_form_group');
function kdr_form_group($atts , $content = null) 
{
    $output = '';

    // Attributes
    $options = shortcode_atts(array(
        'class' => 'form-group'
    ), $atts);

    if($content !== null) {
        $output .= '<div class="'.$options['class'].'">';

        //check if content has shortcode
        if(has_shortcode($content,'kdr_input') || 
           has_shortcode($content,'kdr_pull_right')) {
            $output .= do_shortcode($content);
        }
        $output .= '</div>';
    }

    return $output;
}

/**
 * Pull Right
 **/
add_shortcode('kdr_pull_right','kdr_pull_right');
function kdr_pull_right($atts, $content = null) 
{
    $output = '';

    // Attributes
    $options = shortcode_atts(array(
        'class' => 'pull-right'
    ), $atts);

    if($content !== null) {
        $output .= '<div class="'.$options['class'].'">';

        //check if content has shortcode
        if(has_shortcode($content,'kdr_input')) {
            $output .= do_shortcode($content);
        } else {
            $output .= $content;
        }
        $output .= '</div>';
    }

    return $output;
}

/**
 * Generate input name
 **/
function kdr_generate_input_name($name,$label)
{
	$input_name = kdr_generate_unique_input_name();

	//generate automatic input name
    if($name == '' && $label != '')
    	$input_name = kdr_generate_input_name_from_label($label);

    return $input_name;
}
function kdr_generate_input_name_from_label($label)
{
	return str_replace(' ','_',strtolower($label));
}

function kdr_generate_unique_input_name()
{
	return 'input_'.uniqid();
}

/**
 * Form Submit
 **/
add_action('init','kdr_form_process');
function kdr_form_process()
{
    if (!empty($_POST['nonce_kdr_form'])) {
        if (!wp_verify_nonce($_POST['nonce_kdr_form'], 'kdr_form')){
            die('You are not authorized to perform this action.');
        } else {
            do_action('kdr_form_pre_save');
        }
    }
}

/**
 * Content Filter, helps us remove unwanted <br> and <p> from the shortcode
 * @link http://wordpress.stackexchange.com/questions/130075/stop-wordpress-automatically-adding-br-tags-to-post-content
 **/
add_filter("the_content", "the_content_filter");
function the_content_filter($content) 
{
	$shortcodes = array(
		"kdr_form_open",
		"kdr_form_close",
		"kdr_input",
		"kdr_label"
	);

    $block = join("|",$shortcodes);
    $rep = preg_replace("/(<p>)?\[($block)(\s[^\]]+)?\](<\/p>|<br \/>)?/","[$2$3]",$content);
    $rep = preg_replace("/(<p>)?\[\/($block)](<\/p>|<br \/>)?/","[/$2]",$rep);
	return $rep;
}