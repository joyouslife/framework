<?php
namespace franken\LolitaFramework\Widgets\Carousel;

use \franken\LolitaFramework\Widgets\AbstractWithControls\AbstractWithControls;
use \franken\LolitaFramework\Core\View;
use \franken\LolitaFramework\Core\HelperArray;
use \franken\LolitaFramework\Core\HelperImage;
use \franken\LolitaFramework as LolitaFramework;

class Carousel extends AbstractWithControls
{
    /**
     * Register widget with WordPress.
     */
    public function __construct()
    {
        parent::__construct(
            __('Lolita carousel widget', 'lolita'),
            array('description' => __('Lolita carousel widget', 'lolita'))
        );
    }

    /**
     * This function run before widgets_init hook
     * @return void
     */
    public static function beforeInit()
    {
        parent::beforeInit();
        add_action('admin_enqueue_scripts', array(__CLASS__, 'adminAddScriptsAndStyles'));
        add_action('wp_enqueue_scripts', array(__CLASS__, 'wpAddScriptsAndStyles'));
    }

    /**
     * Add scripts and styles to wp admin
     */
    public static function wpAddScriptsAndStyles()
    {
        $assets = LolitaFramework::getURLByDirectory(__DIR__) . DS . 'assets' . DS;
        // ==============================================================
        // Scripts
        // ==============================================================
        wp_enqueue_script('jquery');
        wp_enqueue_script(
            'jquery.sly',
            $assets . 'js' . DS . 'jquery.sly.min.js',
            array('jquery'),
            false,
            true
        );
        wp_enqueue_script(
            'lolita_widget_carousel',
            $assets . 'js' . DS . 'lolita_widget_carousel.js',
            array('jquery', 'jquery.sly'),
            false,
            true
        );
    }

    /**
     * Add scripts and styles to wp admin
     */
    public static function adminAddScriptsAndStyles()
    {
        $assets = LolitaFramework::getURLByDirectory(__DIR__) . DS . 'assets' . DS;
        // ==============================================================
        // Scripts
        // ==============================================================
        wp_enqueue_script('jquery');
        wp_enqueue_script(
            'lolita-widget-carousel',
            $assets . 'js' . DS . 'admin_lolita_widget_carousel.js',
            array('jquery'),
            false,
            true
        );
    }

    /**
     * Get controls data
     * @return array data to generate controls.
     */
    public static function getControlsData()
    {
        return array(
            array(
                'name'     => 'carousel_type',
                '__TYPE__' => 'Select',
                'label'    => __('Carousel type', 'lolita'),
                'options'  => array(
                    '1' => __('Style 1', 'lolita'),
                ),
            ),
            array(
                'name'     => 'style_1',
                '__TYPE__' => 'Repeater',
                'label'    => __('Style 1', 'lolita'),
                'controls' => array(
                    array(
                        'name'     => 'title',
                        '__TYPE__' => 'Input',
                        'type'     => 'text',
                        'label'    => __('Title', 'lolita'),
                    ),
                    array(
                        'name'     => 'content',
                        '__TYPE__' => 'Textarea',
                        'rows'     => '7',
                        'label'    => __('Content', 'lolita'),
                    ),
                    array(
                        'name'     => 'img',
                        '__TYPE__' => 'Media',
                        'label'    => __('Image', 'lolita'),
                    ),
                ),
            ),
        );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance)
    {
        echo View::make(
            sprintf(
                '%sstyle_%s.php',
                dirname(__FILE__) . DS . 'views' . DS,
                $this->getStyleType($instance)
            ),
            array(
                'args'     => $args,
                'instance' => $this->prepareInstance($instance),
            )
        );
    }

    /**
     * Prepare data per style
     * @param  [type] $instance [description]
     * @return [type]           [description]
     */
    private function prepareInstance($instance)
    {
        $key  = 'style_' . $this->getStyleType($instance);
        $data = HelperArray::get($instance, $key, array());
        foreach ($data as &$list) {
            if (array_key_exists('img', $list)) {
                $list['img_src'] = HelperImage::getURL(
                    (int) $list['img'],
                    'full'
                );
            }
        }
        return $data;
    }

    /**
     * Get carousel type
     * @param  array $instance instance.
     * @return integer carousel type.
     */
    private function getStyleType($instance)
    {
        return max(1, (int) HelperArray::get($instance, 'carousel_type', 1));
    }
}