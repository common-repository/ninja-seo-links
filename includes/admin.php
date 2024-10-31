<?php

/**
 * Crea el menu y submenu en el panel de admin
 */
function ninja_seo_links_admin_menu(){
    add_menu_page('Ninja SEO Links', 'Ninja SEO Links', 'activate_plugins', 'ninja-seo-links', 'ninja_seo_links_page_handler');
    add_submenu_page('ninja-seo-links', 'Ninja SEO Links', 'Ninja SEO Links', 'activate_plugins', 'ninja-seo-links', 'ninja_seo_links_page_handler');
    add_submenu_page('ninja-seo-links', 'Add new', 'Add new', 'activate_plugins', 'ninja-seo-links-add', 'ninja_seo_links_add_page_handler');
}
add_action('admin_menu', 'ninja_seo_links_admin_menu');


/**
 * Imprime la tabla con los resultados
 */
function ninja_seo_links_page_handler() {
    global $wpdb;

    $table = new ninja_seo_links_List_Table();
    $table->prepare_items();

    $message = '';
    if ('delete' === $table->current_action()) {
        $message = '<div class="updated below-h2" id="message"><p>' . sprintf('Items deleted: %d', esc_attr(count($_REQUEST['id']))) . '</p></div>';
    } ?>
    <div class="wrap">
        <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
        <h2>SEO Links<a class="add-new-h2" href="<?php echo esc_url(get_admin_url(get_current_blog_id(), 'admin.php?page=ninja-seo-links-add'));?>">Add new</a></h2>
        <?php echo $message; ?>

        <form id="ninja-seo-links-table" method="GET">
            <input type="hidden" name="page" value="<?php echo esc_html($_REQUEST['page']) ?>"/>
            <?php $table->search_box('Search', 'search_id');
             $table->display(); ?>
        </form>
    </div>
    <?php
}

/**
 * Crear la pagina para insertar un nuevo registro
 */
function ninja_seo_links_form_page_handler(){ 
    ?>
    <div class="wrap">
        <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
        <h2><?php echo 'Add new';?> <a class="add-new-h2" href="<?php echo esc_url(get_admin_url(get_current_blog_id(), 'admin.php?page=ninja-seo-links'));?>"><?php echo 'Back to list';?></a></h2>
        <?php if (!empty($notice)){ ?>
        <div id="notice" class="error"><p><?php echo $notice; ?></p></div>
        <?php } ?>
        <?php if (!empty($message)){ ?>
        <div id="message" class="updated"><p><?php echo $message; ?></p></div>
        <?php } ?>

        <form id="form" method="POST">
            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
            <input type="hidden" name="id" value="<?php echo esc_attr($item['id']); ?>"/>

            <div class="metabox-holder" id="poststuff">
                <div id="post-body">
                    <div id="post-body-content">
                        <?php do_meta_boxes('ninja-seo-links', 'normal', $item); ?>
                        <input type="submit" value="<?php echo 'Guardar';?>" id="submit" class="button-primary" name="submit">
                    </div>
                </div>
            </div>
        </form>
    </div>
    <?php
}


/**
 * Inserta y actualiza los nuevos registros
 */
function ninja_seo_links_add_page_handler() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ninja_seo_links';

    $message = '';
    $notice = '';
    $default = array(
        'id' => 0,
        'origin' => '',
        'word' => '',
        'destination' => '',
        'title' => '',
        'type' => '',
        'open' => '',
        'created' => time(),
        'hits' => '0',
        'active' => '0',
    );

    if (isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
        $item = shortcode_atts($default, $_REQUEST);
        $item_valid = ninja_seo_links_validate($item);
        if ($item_valid === true) {
            if ($item['id'] == 0) {
                $result = $wpdb->insert($table_name, $item);
                $item['id'] = 0;//$wpdb->insert_id;
                $item=$default;
                if ($result) {
                    $message = 'Saved successfully';
                } else {
                    $notice = 'There was an error saving the link';
                }
            } else {
                $result = $wpdb->update($table_name, $item, array('id' => $item['id']));
                if ($result) {
                    $message = 'Successfully updated';
                } else {
                    $notice = 'There was an error updating the link';
                }
            }
        } else {
            $notice = $item_valid;
        }
    }else {
        $item = $default;
        if (isset($_REQUEST['id'])) {
            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $_REQUEST['id']), ARRAY_A);
            if (!$item) {
                $item = $default;
                $notice = 'Link not found';
            }
        }
    }

    add_meta_box('ninja_seo_links_add_meta_box', 'New', 'ninja_seo_links_add_meta_box_handler', 'ninja_seo_links', 'normal', 'default');

    ?>
    <div class="wrap">
        <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
        <h2><?php echo 'Add new link';?> <a class="add-new-h2" href="<?php echo esc_url(get_admin_url(get_current_blog_id(), 'admin.php?page=ninja-seo-links'));?>"><?php echo 'Back to list';?></a></h2>
        <?php if (!empty($notice)) { ?>
        <div id="notice" class="error"><p><?php echo $notice; ?></p></div>
        <?php } ?>
        <?php if (!empty($message)) { ?>
        <div id="message" class="updated"><p><?php echo $message; ?></p></div>
        <?php } ?>

        <form id="form" method="POST">
            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
            <input type="hidden" name="id" value="<?php echo (isset($item['id']) ? $item['id']:'0'); ?>"/>
            <div class="metabox-holder" id="poststuff">
                <div id="post-body">
                    <div id="post-body-content">
                        <?php do_meta_boxes('ninja_seo_links', 'normal',  (isset($item) ? $item:'0')); ?>
                        <input type="submit" value="<?php echo 'Save';?>" id="submit" class="button-primary" name="submit">
                    </div>
                </div>
            </div>
        </form>
    </div>
    <?php
}

/**
 * Imprimir formulario para añadir una nueva url
 *
 * @param $item
 */
function ninja_seo_links_add_meta_box_handler($item) {
    ?>
    <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
        <tbody>
        <tr class="form-field">
            <input id="created" name="created" type="hidden" value="<?php echo esc_attr((isset($item['created']) ? $item['created']:'0'));?>">
            <input id="hits" name="hits" type="hidden" value="<?php echo esc_attr((isset($item['hits']) ? $item['hits']:'0'));?>">
            <th valign="top" scope="row">
                <label for="origin"><?php echo 'URL Source';?></label>
            </th>
            <td>
                <input id="origin" name="origin" type="text" style="width: 95%" value="<?php echo esc_attr((isset($item['origin']) ? $item['origin']:'')); ?>"
                       maxlength="200" class="code" placeholder="" required>
            </td>
        </tr>
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="word"><?php echo 'Keyword';?></label>
            </th>
            <td>
                <input id="word" name="word" type="text" style="width: 95%" value="<?php echo esc_attr((isset($item['word']) ? $item['word']:'')); ?>"
                       maxlength="200" class="code" placeholder="" required>
            </td>
        </tr>
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="destination"><?php echo 'URL Destination';?></label>
            </th>
            <td>
                <input id="destination" name="destination" type="text" style="width: 95%" value="<?php echo esc_attr((isset($item['destination']) ? $item['destination']:'')); ?>"
                       maxlength="200" class="code" placeholder="" required>
            </td>
        </tr>
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="title"><?php echo 'Title';?></label>
            </th>
            <td>
                <input id="title" name="title" type="text" style="width: 95%" value="<?php echo esc_attr((isset($item['title']) ? $item['title']:'')); ?>"
                       maxlength="200" class="code" placeholder="" required>
            </td>
        </tr>
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="type"><?php echo 'Type';?></label>
            </th>
            <td>
                <select class="form-control" id="type" name="type" value="<?php echo esc_attr((isset($item['type']) ? $item['type']:'')); ?>">
                    <option value="follow">follow</option>
                    <option value="nofollow">nofollow</option>
                </select>
            </td>
        </tr>
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="open"><?php echo 'Open';?></label>
            </th>
            <td>
            <select class="form-control" id="open" name="open" value="<?php echo esc_attr((isset($item['open']) ? $item['open']:'')); ?>">
                        <option value="blank">Blank</option>
                        <option value="parent">Parent</option>
                        <option value="self">Self</option>
                        <option value="top">Top </option>
                    </select>
                
            </td>
        </tr>
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="active"><?php echo 'Active';?></label>
            </th>
            <td>
                <input id="active" name="active" type="checkbox" value="1" <?php echo esc_attr((isset($item['active'])))==1? 'checked': '' ?>>
            </td>
        </tr>
        </tbody>
    </table>
    <?php
}

/**
 * Validar datos del formulario
 *
 * @param $item
 * @return bool|string
 */
function ninja_seo_links_validate($item) {
    $messages = array();
    if (empty(esc_url_raw($item['origin'])) || strlen( $esc_url_raw($item['origin']) ) > 200) $messages[] = 'URL source is required and must be valid';
    if (empty(sanitize_text_field($item['word'])) || strlen( $esc_url_raw($item['origin']) ) > 200) $messages[] = 'Keyword is required and must be valid';
    if (empty(esc_url_raw($item['destination'])) || strlen( $esc_url_raw($item['origin']) ) > 200) $messages[] = 'URL destination is required and must be valid';
    if (empty(sanitize_text_field($item['title'])) || strlen( $esc_url_raw($item['origin']) ) > 200) $messages[] = 'Title is required and must be valid';
    if (empty(sanitize_text_field($item['type'])) || strlen( $esc_url_raw($item['origin']) ) > 60) $messages[] = 'Type is required and must be valid';
    if (empty(sanitize_text_field($item['open'])) || strlen( $esc_url_raw($item['origin']) ) > 60) $messages[] = 'Open is required and must be valid';
    if (empty($messages)) return true;
    return implode('<br />', $messages);
}

