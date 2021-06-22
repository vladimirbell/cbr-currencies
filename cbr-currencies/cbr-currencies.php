<?php
/**
 * Plugin Name: Курс валют ЦБ
 * Description: Вывод валют с сайта ЦБ России
 * Version:     1.0
 * Text Domain: cbr-currencies
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function show_currencies () {
    $url = simplexml_load_file("http://www.cbr.ru/scripts/XML_daily.asp?date_req=".date("d/m/Y"));
    $usd_xml = $url->xpath("//Valute[@ID='R01235']"); 
    $eur_xml = $url->xpath("//Valute[@ID='R01239']"); 
    $usd = strval($usd_xml[0]->Value);
    $eur = strval($eur_xml[0]->Value);

    return '€:'.$eur.' $:'.$usd;
}

add_shortcode( 'currencies', 'show_currencies' );

// Вывод курса валют при помощи шорткода:
// echo do_shortcode( '[currencies]' ); in header.php

/**
 * Вывод курса валют при помощи виджета:
 */

class Currencies_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'currencies_widget', // Base ID
			esc_html__( 'Курсы валют', 'cbr-currencies' ), // Name
			array( 'description' => esc_html__( 'Курсы валют с сайта ЦБ', 'cbr-currencies' ), ) // Args
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
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

        $currencies = show_currencies();
		echo esc_html__( $currencies, 'cbr-currencies' );
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Заголовок', 'cbr-currencies' );
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'cbr-currencies' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';

		return $instance;
	}

} // class Currencies_Widget

// register Currencies_Widget widget
function register_curr_widget() {
    register_widget( 'Currencies_Widget' );
}
add_action( 'widgets_init', 'register_curr_widget' );