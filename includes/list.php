<?php

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

/**
 * Crea la classe para contruir la tabla de registros
 */
class Ninja_seo_links_List_Table extends WP_List_Table {
    /**
     * [REQUIRED] You must declare constructor and give some basic params
     */
    function __construct(){
        global $status, $page;

        parent::__construct(array(
            'singular' => 'links',
            'plural' => 'links',
        ));
    }

    /**
     * Imprime la columna
     *
     * @param $item - row (key, value array)
     * @param $column_name - string (key)
     * @return HTML
     */
    function column_default($item, $column_name) {
        return esc_attr($item[$column_name]);
    }

    /**
     * Imprime Si o No segun si esta el enlace activo
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_active($item) {
        if($item['active'] == 0){$activo = "No";}else{$activo = "Yes";}
        return esc_attr($activo);
    }

    /**
     * Imprime Si o No segun si esta el enlace activo
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_destination($item) {
        return esc_url_raw($item['destination']);
    }

    /**
     * Imprime la fecha
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_created($item) {
        return esc_attr(date('d/m/Y', $item['created']));
    }

    /**
     * Imprime las opciones Editar y Borrar con hover
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_origin($item) {
        $actions = array(
            'edit' => sprintf('<a href="?page=ninja-seo-links-add&id=%s">%s</a>', esc_attr($item['id']), 'Edit'),
            'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', esc_attr($_REQUEST['page']), esc_attr($item['id']), 'Delete'),
        );
        return sprintf('%s %s',
            esc_url_raw($item['origin']),
            $this->row_actions($actions)
        );
    }

    /**
     * Imprime el checkbox
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            esc_attr($item['id'])
        );
    }

    /**
     * Devuelve las columnas de la tabla
     *
     * @return array
     */
    function get_columns() {
        $columns = array(
            'cb'      => '<input type="checkbox" />',
            'origin'    => 'URL Source',
            'word' => 'Keyword',
            'destination'    => 'URL Destination',
            'title'    => 'Title',
            'type' => 'Type',
            'open'    => 'Open',
            'created' => 'Created',
            'hits'    => 'Opened',
            'active' => 'Active'
        );
        return $columns;
    }

    /**
     * Devuelve las columnas de la tabla para ordenar
     *
     * @return array
     */
    function get_sortable_columns() {
        $sortable_columns = array(
            'origin' => array( 'origin', true ),
            'word' => array( 'word', false ),
            'destination' => array( 'destination', false ),
            'title' => array( 'title', false ),
            'type' => array( 'type', false ),
            'open' => array( 'open', false ),
            'open' => array( 'open', false ),
            'created' => array( 'created', false ),
            'hits' => array( 'hits', false ),
            'active' => array( 'active', false ),
        );

        return $sortable_columns;
    }

    /**
     * Devuelve el array para bulk action
     *
     * @return array
     */
    function get_bulk_actions() {
        $actions = array(
            'delete' => 'Delete'
        );
        return $actions;
    }

    /**
     * Borra el array de registros que devuelve get_bulk_actions
     */
    function process_bulk_action() {
        global $wpdb;
        $tp=0;
        $table_name = $wpdb->prefix . 'ninja_seo_links';

        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                $ids = array_map( 'intval', explode( ',', $ids ) );
                foreach ($ids as $id) {
                    if (!is_int($id)) {
                        $tp = 1;
                    }
                }
                if($tp==0){
                    $ids = implode(',', $ids);
                    $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");
                }
                
            }
        }
    }

    /**
     * Obtiene los datos de la base de datos y los prepara para mostrarlos en la tabla
     */
    function prepare_items() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ninja_seo_links';

        $per_page = 5;
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        $paged = sanitize_text_field((isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0));
        $orderby = sanitize_text_field((isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'origin');
        $order = sanitize_text_field((isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc');

        if(isset($_GET['s'])){
            $s = "%".sanitize_text_field($_GET['s'])."%";
            $total_items = $wpdb->get_var($wpdb->prepare("SELECT COUNT(id) FROM $table_name WHERE origin LIKE '%s' OR word LIKE '%s' OR destination LIKE '%s'", $s,$s,$s));
            $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE origin LIKE '%s' OR word LIKE '%s' OR destination LIKE '%s' ORDER BY $orderby $order, id LIMIT %d OFFSET %d", $s,$s,$s,$per_page, $paged*$per_page), ARRAY_A);
        } else {
            $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");
            $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY $orderby $order, id LIMIT %d OFFSET %d", $per_page, $paged*$per_page), ARRAY_A);
            
        }

        $this->set_pagination_args(array(
                'total_items' => $total_items,
                'per_page' => $per_page,
                'total_pages' => ceil($total_items / $per_page)
            ));

        
    }
}