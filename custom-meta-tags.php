<?php
/*
Plugin Name: Custom Meta Tags
Description: Плагин для добавления кастомных мета-тегов в head.
Version: 1.0
Author: Ваше Имя
*/

// Подключение стилей для админки, только на странице плагина
add_action('admin_enqueue_scripts', 'cmt_admin_styles');
function cmt_admin_styles($hook_suffix)
{
  // Проверка, что это страница настроек нашего плагина
  if ($hook_suffix == 'toplevel_page_custom_meta_tags') {
    wp_enqueue_style('cmt_admin_styles', plugin_dir_url(__FILE__) . 'admin/css/admin-style.css');
  }
}

// Создание таблицы в базе данных при активации плагина
register_activation_hook(__FILE__, 'cmt_create_meta_tags_table');
function cmt_create_meta_tags_table()
{
  global $wpdb;
  $table_name = $wpdb->prefix . 'meta_tags';
  $charset_collate = $wpdb->get_charset_collate();

  $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        tag_name varchar(255) NOT NULL,
        tag_content text NOT NULL,
        pages text NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  dbDelta($sql);
}

// Добавление мета-тегов в head на выбранных страницах
add_action('wp_head', 'cmt_add_meta_tags');
function cmt_add_meta_tags()
{
  global $wpdb;
  $table_name = $wpdb->prefix . 'meta_tags';
  $meta_tags = $wpdb->get_results("SELECT * FROM $table_name");

  if ($meta_tags) {
    foreach ($meta_tags as $tag) {
      if (is_page() || is_single()) {
        $current_page = get_queried_object_id();
        $pages = explode(',', $tag->pages);
        if (!in_array($current_page, $pages)) {
          continue;
        }
      }
      echo '<meta name="' . esc_attr($tag->tag_name) . '" content="' . esc_attr($tag->tag_content) . '" />' . "\n";
    }
  }
}

// Добавление страницы настроек в админку
add_action('admin_menu', 'cmt_add_admin_menu');
function cmt_add_admin_menu()
{
  add_menu_page(
    'Custom Meta Tags',
    'Meta Tags',
    'manage_options',
    'custom_meta_tags',
    'cmt_settings_page'
  );
}

// Функция для вывода страницы настроек
function cmt_settings_page()
{
  global $wpdb;
  $table_name = $wpdb->prefix . 'meta_tags';

  // Сохранение и удаление мета-тегов
  if (isset($_POST['submit'])) {
    $tag_name = sanitize_text_field($_POST['tag_name']);
    $tag_content = sanitize_textarea_field($_POST['tag_content']);
    $pages = sanitize_text_field($_POST['pages']);

    $wpdb->insert($table_name, array(
      'tag_name' => $tag_name,
      'tag_content' => $tag_content,
      'pages' => $pages
    ));
  }

  if (isset($_POST['delete'])) {
    $tag_id = intval($_POST['tag_id']);
    $wpdb->delete($table_name, array('id' => $tag_id));
  }

  // Получаем существующие мета-теги из базы данных
  $meta_tags = $wpdb->get_results("SELECT * FROM $table_name");

  // Подключение HTML-шаблона страницы настроек
  include plugin_dir_path(__FILE__) . 'admin/views/settings-view.php';
}
