<?php

/**
 * Cambiar la palabra por el enlace
 */
add_filter( 'the_content', 'ninja_seo_links_change_word', 10, 2 );
function ninja_seo_links_change_word( $content ) {
	global $wp;
	global $wpdb;
	$table_name = $wpdb->prefix . 'ninja_seo_links';
	$url = esc_url_raw(get_permalink());
	$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE origin=%d AND active=1", $url));
	foreach ($results as $value) {
		if(isset($results) and $value->origin === $url){
			$content = preg_replace("/$value->word/", "<a href='".esc_url_raw($value->destination)."' rel='".sanitize_text_field($value->type)."' title='".sanitize_text_field($value->title)."' target='_".sanitize_text_field($value->open)."' onclick='count(this)'>".sanitize_text_field($value->word)."</a>", $content, 1);

		}
	}
	return $content;
}

/**
 * Contar las veces que se ha abierto el enlace
 */
function ninja_seo_links_plus(){
	global $wpdb;
	$table_name = $wpdb->prefix . 'ninja_seo_links';
	$destination = esc_url_raw((isset( $_POST['href'] ) ? $_POST['href'] : ''));
	$word = sanitize_text_field((isset( $_POST['text'] ) ? $_POST['text'] : ''));
	if(empty($destination) || empty($word))return;
	$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE word=%s AND destination=%s", $word, $destination));
  	if($wpdb->num_rows >0){
		$hits=$results[0]->hits+1;
  		$wpdb->update($table_name, array('hits' => $hits), array( 'word' => sanitize_text_field($word), 'destination' => esc_url_raw($destination)), array('%s'), array('%d', '%s' ));
  	}
	die();
}
add_action('wp_ajax_ninja_seo_links_plus','ninja_seo_links_plus');
add_action('wp_ajax_nopriv_ninja_seo_links_plus','ninja_seo_links_plus');