<?php
/*
  Plugin Name: Points Group - BMI Calculator
  Description: Provides a BMI Calculator as a widget or shortcode. Can only be one calculator per page.
  Author: Peter Katsogiannos
  Version: 1.2.1.1
*/

function bmi_enqueue_scripts() {
  wp_enqueue_script( 'jquery-ui', plugins_url( '/jquery-ui.js' , __FILE__ ), array(), '1.0.0', true );

  wp_enqueue_style( 'jquery-ui', plugins_url( '/jquery-ui.css' , __FILE__ ) );
  wp_enqueue_script( 'bmi-calc-script', plugins_url( '/points-bmi-calculator.js' , __FILE__ ), array(), '1.0.0', true );
  wp_enqueue_style( 'bmi-calc-style', plugins_url( '/points-bmi-calculator.css' , __FILE__ ) );
}


//This is to update the background image selection section of the back-end
add_action( 'wp_ajax_this_ajax_action', 'ajax_callback' );
function ajax_callback()
{

    $response               = [];
    $response['data']       = $_POST;
    update_option( 'media_selector_attachment_id', $response['data']["image_attachment_id"] );

    wp_send_json( $response['data']["image_attachment_id"] );
    wp_die();
}


function bmi_ajax($hook) {
  // Load only on ?page=points_bmi_options
  if($hook != 'settings_page_points_bmi_options') {
          return;
  }
  wp_enqueue_script( 'media-library', plugins_url( './media-library.js' , __FILE__ ), array(), '1.0.0', true );


  wp_localize_script( 'media-library', 'my_ajax_object',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

}
add_action( 'admin_enqueue_scripts', 'bmi_ajax' );



function build_calc() {

  // Load in the settings fields from the settings page for the plug-in to populate the calculator's content
  settings_fields( 'points_bmi_calculator_group' );

  //Stylings for the calcuator, loaded in from PHP
  ?>

  <style type="text/css">
    #bmi-calc .calc-button input.calc-submit {
      background-color: <?php esc_html_e(get_option('points_bmi_button_color')); ?> !important;
    }

    #bmi-calc .calc-fields h2 {
      color: <?php esc_html_e(get_option('points_bmi_header1_text_color')); ?> !important;
    }

    #bmi-calc h2 {
      color: <?php esc_html_e(get_option('points_bmi_result_header_color')); ?> !important;   
    }

    #bmi-calc .top-wrapper {
      background: url(<?php echo get_option('media_selector_attachment_id'); ?>) !important;   
      background-size: cover !important;
    }

    #bmi-calc .level.active::before {
      color: <?php esc_html_e(get_option('accent_color')); ?> !important;
    }

    #bmi-calc .level.active {
      background: <?php esc_html_e(get_option('accent_color')); ?> !important;
    }

    #bmi-calc .ui-slider .ui-slider-handle {
      background-color: <?php esc_html_e(get_option('accent_color')); ?> !important;
    }

    #bmi-calc a {
      color: <?php esc_html_e(get_option('accent_color')); ?> !important;
    }

    #bmi-calc .result, #bmi-calc .result2 {
      background-color: <?php esc_html_e(get_option('accent_color')); ?> !important;
    }

    #bmi-calc .take-your-step button {
      color: <?php esc_html_e(get_option('accent_color')); ?> !important;
    }

    #bmi-calc h2 {
      font-size: <?php $h2String = (get_option('font_size_h2') . 'px'); $h2StringN = str_replace(' ','',$h2String); esc_html_e($h2StringN); ?> !important;
    }

    #bmi-calc .calc-step label {
      font-size: <?php $label = (get_option('font_size_labels') . 'px'); $labelN = str_replace(' ','',$label); esc_html_e($labelN); ?> !important;
    }

    #bmi-calc .calc-step input._text {
      font-size: <?php $pString = (get_option('font_size_p') . 'px'); $pStringN = str_replace(' ','',$pString); esc_html_e($pStringN); ?> !important;
    }

    #bmi-calc .calc-step .units {
      font-size: <?php $uString = (get_option('font_size_units') . 'px'); $uStringN = str_replace(' ','',$uString); esc_html_e($uStringN); ?> !important;
    }

    #bmi-calc .calc-button input.calc-submit {
      font-size: <?php $bString = (get_option('font_size_button') . 'px'); $bStringN = str_replace(' ','',$bString); esc_html_e($bStringN); ?> !important;
    }

    #bmi-calc .calc-levels .level p {
      font-size: <?php $pString = (get_option('font_size_p') . 'px'); $pStringN = str_replace(' ','',$pString); esc_html_e($pStringN); ?> !important;
    }

    #bmi-calc h3 {
      font-size: <?php $h3String = (get_option('font_size_h3') . 'px'); $h3StringN = str_replace(' ','',$h3String); esc_html_e($h3StringN); ?> !important;
    }

    #bmi-calc .result {
      font-size: <?php $rString = (get_option('font_size_result_number') . 'px'); $rStringN = str_replace(' ','',$rString); esc_html_e($rStringN); ?> !important;
    }

    #bmi-calc .result2 {
      font-size: <?php $rHString = (get_option('font_size_result_number_headline') . 'px'); $rHStringN = str_replace(' ','',$rHString); esc_html_e($rHStringN); ?> !important;
    }

    #bmi-calc .description-disclaimer {
      <?php $dString = ( (get_option('font_size_p') - 4) . 'px'); $dStringN = str_replace(' ','',$dString); ?>
      font-size: <?php esc_html_e(( (get_option('font_size_p') - 4) < 0 ? 1 : (get_option('font_size_p') - 4) ) . 'px'); ?> !important;
    }

    #bmi-calc .description {
      font-size: <?php $h2String = (get_option('font_size_h2') . 'px'); $h2StringN = str_replace(' ','',$h2String); esc_html_e($h2StringN); ?> !important;
      font-size: <?php esc_html_e(get_option('font_size_p') . 'px'); ?> !important;
    }

    #bmi-calc .take-your-step button {
      font-size: <?php $h2String = (get_option('font_size_h2') . 'px'); $h2StringN = str_replace(' ','',$h2String); esc_html_e($h2StringN); ?> !important;
      font-size: <?php esc_html_e(get_option('font_size_p') . 'px'); ?> !important;
    }
  </style>

    <div id="bmi-calc" class="bmi-wrapper">
      
      <div class="top-wrapper bmi-container">
        <div class="calc-fields">

          <h2><?php esc_html_e(get_option('points_bmi_header1_text')); ?></h2>
          
          <form lpformnum="20">

            <div class="calc-step weight">
              <div class="step-label">
                <label> <?php esc_html_e(get_option('points_bmi_weight_label_text')); ?> </label>
              </div> 
              <input class="_text _input calc-weight" name="calc_weight" value="" id="f_calc-weight" type="tel"> <span class="units"><?php esc_html_e(get_option('points_bmi_weight_label_measurement_text')); ?></span>
            </div>
            
            <div class="calc-step height">
              <div class="step-label">
                <label> <?php esc_html_e(get_option('points_bmi_height_label_text')); ?> </label>
              </div> 
              <input class="_text _input calc-height-ft" name="calc_height_ft" value="" id="f_calc-height-ft" type="tel"> <span class="units"><?php esc_html_e(get_option('points_bmi_height_label_measurement_text_1')); ?></span><br>
              <input class="_text _input calc-height-in" name="calc_height_in" value="" id="f_calc-height-in" type="tel"> <span class="units"><?php esc_html_e(get_option('points_bmi_height_label_measurement_text_2')); ?></span>
            </div>
            
            <div class="calc-button"><input class="_submit  _input calc-submit" name="x" value="<?php echo get_option('points_bmi_button_text'); ?>" id="f_x"  type="submit"></div>
          
          </form>
        
        </div>
      </div>
    
      <div class="result-blurb bmi-container hidden">

        <h2><?php esc_html_e(get_option('points_bmi_result_header')); ?></h2>

        <div class="calc-levels">
          <div class="level level-6"><p><b><?php esc_html_e(get_option('points_bmi_result_header_lvl6')); ?></b><br><?php esc_html_e(get_option('points_bmi_value_lvl6')); ?></p></div>
          <div class="level level-5"><p><b><?php esc_html_e(get_option('points_bmi_result_header_lvl5')); ?></b><br><?php esc_html_e(get_option('points_bmi_value_lvl5')); ?></p></div>
          <div class="level level-4"><p><b><?php esc_html_e(get_option('points_bmi_result_header_lvl4')); ?></b><br><?php esc_html_e(get_option('points_bmi_value_lvl4')); ?></p></div>
          <div class="level level-3"><p><b><?php esc_html_e(get_option('points_bmi_result_header_lvl3')); ?></b><br><?php esc_html_e(get_option('points_bmi_value_lvl3')); ?></p></div>
          <div class="level level-2"><p><b><?php esc_html_e(get_option('points_bmi_result_header_lvl2')); ?></b><br><?php esc_html_e(get_option('points_bmi_value_lvl2')); ?></p></div>
          <div class="level level-1"><p><b><?php esc_html_e(get_option('points_bmi_result_header_lvl1')); ?></b><br><?php esc_html_e(get_option('points_bmi_value_lvl1')); ?></p></div>
        </div>

        <h3><?php esc_html_e(get_option('points_bmi_your_bmi')); ?></h3>

        <div class="calc-results">
          
          <div class="result">NA</div>
          <div class="result2 hidden"></div>
          
          <div class="calc-slider-wrap">
            <div id="calc-slider" class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all ui-slider-disabled ui-state-disabled ui-disabled" aria-disabled="true">
              <div class="ui-slider-range ui-widget-header ui-slider-range-min" style="width: 0%;"></div>
              <a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: 0%;"></a>
            </div>    
          </div>
          
          <small class="blurb hidden"><?php esc_html_e(get_option('points_bmi_slider_adjust_text')); ?></small>

        </div>
        
        <div class="description level-6 hidden">
            <?php echo(get_option('points_bmi_result_text_lvl6')); ?>
        </div>

        <div class="description level-5 hidden">
            <?php echo(get_option('points_bmi_result_text_lvl5')); ?>
        </div>

        <div class="description level-4 hidden">
            <?php echo(get_option('points_bmi_result_text_lvl4')); ?>
        </div>

        <div class="description level-3 hidden">
            <?php echo(get_option('points_bmi_result_text_lvl3')); ?>
        </div>

        <div class="description level-2 hidden">
            <?php echo(get_option('points_bmi_result_text_lvl2')); ?>
        </div>

        <div class="description level-1 hidden">
          <p>
            <?php echo(get_option('points_bmi_result_text_lvl1')); ?>
          </p>
        </div>

      </div>
        
    </div>
  <?php
}

function points_display_bmi_calculator($type, $instance = null) {
  static $sm_bmi_count = 1;
  if ($sm_bmi_count == 1) {
    bmi_enqueue_scripts();
  }

  $sm_bmi_count++;
  return build_calc();
}

function points_bmi_register_settings() {

  //Adding options for the back-end of the calculator, client requested maximum customization options

   add_option( 'media_selector_attachment_id', ' ');
   register_setting( 'points_bmi_calculator_group', 'media_selector_attachment_id', 'points_bmi_callback' );

   add_option( 'accent_color', '#1d95c9 ');
   register_setting( 'points_bmi_calculator_group', 'accent_color', 'points_bmi_callback' );

   add_option( 'font_size_p', '16 ');
   register_setting( 'points_bmi_calculator_group', 'font_size_p', 'points_bmi_callback' );

   add_option( 'font_size_button', '14 ');
   register_setting( 'points_bmi_calculator_group', 'font_size_button', 'points_bmi_callback' );

   add_option( 'font_size_labels', '18 ');
   register_setting( 'points_bmi_calculator_group', 'font_size_labels', 'points_bmi_callback' );

   add_option( 'font_size_units', '18 ');
   register_setting( 'points_bmi_calculator_group', 'font_size_units', 'points_bmi_callback' );

   add_option( 'font_size_h1', '12');
   register_setting( 'points_bmi_calculator_group', 'font_size_h1', 'points_bmi_callback' );

   add_option( 'font_size_h2', '36 ');
   register_setting( 'points_bmi_calculator_group', 'font_size_h2', 'points_bmi_callback' );

   add_option( 'font_size_h3', '24 ');
   register_setting( 'points_bmi_calculator_group', 'font_size_h3', 'points_bmi_callback' );

   add_option( 'font_size_result_number', '48');
   register_setting( 'points_bmi_calculator_group', 'font_size_result_number', 'points_bmi_callback' );

   add_option( 'font_size_result_number_headline', '15');
   register_setting( 'points_bmi_calculator_group', 'font_size_result_number_headline', 'points_bmi_callback' );

   add_option( 'points_bmi_your_bmi', 'Your BMI');
   register_setting( 'points_bmi_calculator_group', 'points_bmi_your_bmi', 'points_bmi_callback' );

   add_option( 'points_bmi_header1_text_color', '#ffffff');
   register_setting( 'points_bmi_calculator_group', 'points_bmi_header1_text_color', 'points_bmi_callback' );

   add_option( 'points_bmi_header1_text', 'Calculate Your BMI');
   register_setting( 'points_bmi_calculator_group', 'points_bmi_header1_text', 'points_bmi_callback' );

   add_option( 'points_bmi_button_color', '#ffffff');
   register_setting( 'points_bmi_calculator_group', 'points_bmi_button_color', 'points_bmi_callback' );

   add_option( 'points_bmi_button_text', 'CALCULATE MY BMI');
   register_setting( 'points_bmi_calculator_group', 'points_bmi_button_text', 'points_bmi_callback' );

   add_option( 'points_bmi_weight_label_measurement_text', 'lbs');
   register_setting( 'points_bmi_calculator_group', 'points_bmi_weight_label_measurement_text', 'points_bmi_callback' );

   add_option( 'points_bmi_weight_label_text', 'Weight');
   register_setting( 'points_bmi_calculator_group', 'points_bmi_weight_label_text', 'points_bmi_callback' );

   add_option( 'points_bmi_height_label_text', 'Height');
   register_setting( 'points_bmi_calculator_group', 'points_bmi_height_label_text', 'points_bmi_callback' );

   add_option( 'points_bmi_height_label_measurement_text_1', 'ft');
   register_setting( 'points_bmi_calculator_group', 'points_bmi_height_label_measurement_text_1', 'points_bmi_callback' );

   add_option( 'points_bmi_height_label_measurement_text_2', 'in');
   register_setting( 'points_bmi_calculator_group', 'points_bmi_height_label_measurement_text_2', 'points_bmi_callback' );

   add_option( 'points_bmi_result_header', 'Your Personalized Report');
   register_setting( 'points_bmi_calculator_group', 'points_bmi_result_header', 'points_bmi_callback' );

   add_option( 'points_bmi_result_header_color', '#5f6062');
   register_setting( 'points_bmi_calculator_group', 'points_bmi_result_header_color', 'points_bmi_callback' );


   add_option( 'points_bmi_result_header_lvl1', 'Underweight');
   register_setting( 'points_bmi_calculator_group', 'points_bmi_result_header_lvl1', 'points_bmi_callback' );

   add_option( 'points_bmi_result_text_lvl1', ' ');
   register_setting( 'points_bmi_calculator_group', 'points_bmi_result_text_lvl1', 'points_bmi_callback' );

   add_option( 'points_bmi_value_lvl1', '&lt;18.5');
   register_setting( 'points_bmi_calculator_group', 'points_bmi_value_lvl1', 'points_bmi_callback' );


   add_option( 'points_bmi_result_header_lvl2', 'Healthy');
   register_setting( 'points_bmi_calculator_group', 'points_bmi_result_header_lvl2', 'points_bmi_callback' );

   add_option( 'points_bmi_result_text_lvl2', ' ');
   register_setting( 'points_bmi_calculator_group', 'points_bmi_result_text_lvl2', 'points_bmi_callback' );

   add_option( 'points_bmi_value_lvl2', '18.6 - 24.9');
   register_setting( 'points_bmi_calculator_group', 'points_bmi_value_lvl2', 'points_bmi_callback' );


   add_option( 'points_bmi_result_header_lvl3', 'Overweight');
   register_setting( 'points_bmi_calculator_group', 'points_bmi_result_header_lvl3', 'points_bmi_callback' );

   add_option( 'points_bmi_result_text_lvl3', ' ');
   register_setting( 'points_bmi_calculator_group', 'points_bmi_result_text_lvl3', 'points_bmi_callback' );

   add_option( 'points_bmi_value_lvl3', '25 - 29.9');
   register_setting( 'points_bmi_calculator_group', 'points_bmi_value_lvl3', 'points_bmi_callback' );


   add_option( 'points_bmi_result_header_lvl4', 'Obese');
   register_setting( 'points_bmi_calculator_group', 'points_bmi_result_header_lvl4', 'points_bmi_callback' );

   add_option( 'points_bmi_result_text_lvl4', ' ');
   register_setting( 'points_bmi_calculator_group', 'points_bmi_result_text_lvl4', 'points_bmi_callback' );

   add_option( 'points_bmi_value_lvl4', '30 - 34.9');
   register_setting( 'points_bmi_calculator_group', 'points_bmi_value_lvl4', 'points_bmi_callback' );


   add_option( 'points_bmi_result_header_lvl5', 'Severely Obese');
   register_setting( 'points_bmi_calculator_group', 'points_bmi_result_header_lvl5', 'points_bmi_callback' );

   add_option( 'points_bmi_result_text_lvl5', ' ');
   register_setting( 'points_bmi_calculator_group', 'points_bmi_result_text_lvl5', 'points_bmi_callback' );

   add_option( 'points_bmi_value_lvl5', '35 - 39.9');
   register_setting( 'points_bmi_calculator_group', 'points_bmi_value_lvl5', 'points_bmi_callback' );


   add_option( 'points_bmi_result_header_lvl6', 'Morbidly Obese');
   register_setting( 'points_bmi_calculator_group', 'points_bmi_result_header_lvl6', 'points_bmi_callback' );

   add_option( 'points_bmi_result_text_lvl6', ' ');
   register_setting( 'points_bmi_calculator_group', 'points_bmi_result_text_lvl6', 'points_bmi_callback' );

   add_option( 'points_bmi_value_lvl6', '&gt;40');
   register_setting( 'points_bmi_calculator_group', 'points_bmi_value_lvl6', 'points_bmi_callback' );
}

add_action( 'admin_init', 'points_bmi_register_settings' );

function points_register_options_page() {
  add_options_page('Points BMI Calculator Options', 'Points BMI Calculator', 'manage_options', 'points_bmi_options', 'points_bmi_options_page');
}

add_action('admin_menu', 'points_register_options_page');

function points_bmi_options_page() {
//This is the HTML for the settings page
  wp_enqueue_media();
?>
  <div class="points-card">
    <div class="points-section-header">
      <div class="points-section-header__label">
        <span><?php esc_html_e( 'Points Group BMI Calculator Settings'); ?></span>
      </div>
    </div>
    
    <div class="inside">
      <form id='optionsForm' method="post" action="options.php">

        <table cellspacing="0" class="points-settings">
          <tbody>

                <tr>
                  <th align="left" scope="row"><?php esc_html_e('Background Image'); ?></th>
                  <td align="left">
                    <form method='post'>
                      <div class='image-preview-wrapper'>
                        <img id='image-preview' src='<?php echo get_option( 'media_selector_attachment_id' ); ?>' height='100'>
                      </div>
                      <input id="upload_image_button" type="button" class="button" value="<?php _e( 'Upload image' ); ?>" />
                      <input type='hidden' name='image_attachment_id' id='image_attachment_id' value='<?php echo get_option( 'media_selector_attachment_id' ); ?>'>
                      <input type="submit" name="submit_image_selector" value="Save Background Image" id="saveMediaButton" class="button-primary">
                      <p>After choosing an image, press save to lock in the selected background image.</p>
                      <H2 id='sMessage' style="display: none; color: green;">Image Saved</H2>
                    </form>
                  </td>
                </tr>

                <tr>
                  <th align="left" scope="row"><?php esc_html_e('Accent Color'); ?></th>
                  <td align="left">
                    <p>
                      <label title="<?php esc_attr_e( 'Accent Color' ); ?>">
                        <input type="color" id="accent_color" name="accent_color" value="<?php echo get_option('accent_color'); ?>" />
                      </label>
                    </p>
                  </td>
                </tr>


                <tr>
                  <th align="left" scope="row"><?php esc_html_e('Main Header'); ?></th>
                  <td align="left">
                    <p>
                      <label title="<?php esc_attr_e( 'Main Header' ); ?>">
                        <input type="color" id="points_bmi_header1_text_color" name="points_bmi_header1_text_color" value="<?php echo get_option('points_bmi_header1_text_color'); ?>" />
                        <input type="hidden" id="media_selector_attachment_id" name="media_selector_attachment_id" value="<?php echo get_option('media_selector_attachment_id'); ?>" />
                        <input type="text" id="points_bmi_header1_text" name="points_bmi_header1_text" value="<?php echo get_option('points_bmi_header1_text'); ?>" />
                      </label>
                    </p>
                  </td>
                </tr>

                <tr>
                  <th align="left" scope="row"><?php esc_html_e('Button');?></th>
                  <td align="left">
                    <p>
                      <label title="<?php esc_attr_e( 'Calculate Button' ); ?>">
                        <input type="color" id="points_bmi_button_color" name="points_bmi_button_color" value="<?php echo get_option('points_bmi_button_color'); ?>" />
                        <input type="text" id="points_bmi_button_text" name="points_bmi_button_text" value="<?php echo get_option('points_bmi_button_text'); ?>" />
                      </label>
                    </p>
                  </td>
                </tr>

                <tr>
                  <th align="left" scope="row"><?php esc_html_e('Input Labels');?></th>
                  <td align="left">
                    <p>
                      <label title="<?php esc_attr_e( 'Weight Label' ); ?>">
                        <input type="text" id="points_bmi_weight_label_text" name="points_bmi_weight_label_text" value="<?php echo get_option('points_bmi_weight_label_text'); ?>" />
                        <input type="text" id="points_bmi_weight_label_measurement_text" name="points_bmi_weight_label_measurement_text" value="<?php echo get_option('points_bmi_weight_label_measurement_text'); ?>" />
                      </label>
                    </p>
                    <p>
                      <label title="<?php esc_attr_e( 'Height Label' ); ?>">
                        <input type="text" id="points_bmi_height_label_text" name="points_bmi_height_label_text" value="<?php echo get_option('points_bmi_height_label_text'); ?>" />
                        <input type="text" id="points_bmi_height_label_measurement_text_1" name="points_bmi_height_label_measurement_text_1" value="<?php echo get_option('points_bmi_height_label_measurement_text_1'); ?>" />
                        <input type="text" id="points_bmi_height_label_measurement_text_2" name="points_bmi_height_label_measurement_text_2" value="<?php echo get_option('points_bmi_height_label_measurement_text_2'); ?>" />
                      </label>
                    </p>
                  </td>
                </tr>
                <tr>
                  <th align="left" scope="row"><?php esc_html_e('Calculator Header');?></th>
                  <td>
                    <p>
                      <label title="<?php esc_attr_e( 'Calculator Labels' ); ?>">
                        <input type="color" id="points_bmi_result_header_color" name="points_bmi_result_header_color" value="<?php echo get_option('points_bmi_result_header_color'); ?>" />
                        <input type="text" id="points_bmi_result_header" name="points_bmi_result_header" value="<?php echo get_option('points_bmi_result_header'); ?>" />
                      </label>
                    </p>
                  </td>
                </tr>

          </tbody>
        </table>

        <table cellspacing="0" width="100%" class="points-settings">
          <tbody>
                <tr width="100%">
                    <th colspan="2" width="100%" align="center" scope="row"><?php esc_html_e('Calculator Font Sizes');?></th>
                </tr>
                <tr>
                  <td>
                    <p width="40" align="right" scope="row"><strong><?php esc_html_e('Paragraph');?></strong></p>
                  </td>
                  <td>
                    <input type="text" id="font_size_p" name="font_size_p" value="<?php echo get_option('font_size_p'); ?>" />
                  </td>
                </tr>
                <tr>
                  <td>
                    <p width="40" align="right" scope="row"><strong><?php esc_html_e('Button');?></strong></p>
                  </td>
                  <td>
                    <input type="text" id="font_size_button" name="font_size_button" value="<?php echo get_option('font_size_button'); ?>" />
                  </td>
                </tr>
                <tr>
                  <td>
                    <p width="40" align="right" scope="row"><strong><?php esc_html_e('Labels');?></strong></p>
                  </td>
                  <td>
                    <input type="text" id="font_size_labels" name="font_size_labels" value="<?php echo get_option('font_size_labels'); ?>" />
                  </td>
                </tr>
                <tr>
                  <td>
                    <p width="40" align="right" scope="row"><strong><?php esc_html_e('Units');?></strong></p>
                  </td>
                  <td>
                    <input type="text" id="font_size_units" name="font_size_units" value="<?php echo get_option('font_size_units'); ?>" />
                  </td>
                </tr>
                <tr>
                  <td>
                    <p width="40" align="right" scope="row"><strong><?php esc_html_e('H1');?></strong></p>
                  </td>
                  <td>
                    <input type="text" id="font_size_h1" name="font_size_h1" value="<?php echo get_option('font_size_h1'); ?>" />
                  </td>
                </tr>
                <tr>
                  <td>
                    <p width="40" align="right" scope="row"><strong><?php esc_html_e('H2');?></strong></p>
                  </td>
                  <td>
                    <input type="text" id="font_size_h2" name="font_size_h2" value="<?php echo get_option('font_size_h2'); ?>" />
                  </td>
                </tr>
                <tr>
                  <td>
                    <p width="40" align="right" scope="row"><strong><?php esc_html_e('H3');?></strong></p>
                  </td>
                  <td>
                    <input type="text" id="font_size_h3" name="font_size_h3" value="<?php echo get_option('font_size_h3'); ?>" />
                  </td>
                </tr>
                <tr>
                  <td>
                    <p width="40" align="right" scope="row"><strong><?php esc_html_e('Result Number');?></strong></p>
                  </td>
                  <td>
                    <input type="text" id="font_size_result_number" name="font_size_result_number" value="<?php echo get_option('font_size_result_number'); ?>" />
                  </td>
                </tr>
                <tr>
                  <td>
                    <p width="40" align="right" scope="row"><strong><?php esc_html_e('Result Number Headline');?></strong></p>
                  </td>
                  <td>
                    <input type="text" id="font_size_result_number_headline" name="font_size_result_number_headline" value="<?php echo get_option('font_size_result_number_headline'); ?>" />
                  </td>
                </tr>
          </tbody>
        </table>

        <table cellspacing="0" class="points-settings">
          <tbody>

                <tr>
                  <th align="center" scope="row"><?php esc_html_e('Calculator Levels Content');?></th>
                </tr>

                <tr>
                  <th align="left" scope="row"><?php esc_html_e('Your BMI'); ?></th>
                  <td align="left">
                    <p>
                      <label title="<?php esc_attr_e( 'Your BMI' ); ?>">
                        <input type="text" id="points_bmi_your_bmi" name="points_bmi_your_bmi" value="<?php echo get_option('points_bmi_your_bmi'); ?>" />
                      </label>
                    </p>
                  </td>
                </tr>

                <tr>
                  <th align="left" scope="row"><?php esc_html_e('Calculator Level 1');?></th>
                </tr>
                <tr>
                  <td align="left">
                    <p>
                      <label title="<?php esc_attr_e( 'points_bmi_result_text_lvl1' ); ?>">
                      <input type="text" id="points_bmi_result_header_lvl1" name="points_bmi_result_header_lvl1" value="<?php echo get_option('points_bmi_result_header_lvl1'); ?>" />
                      <input type="textarea" id="points_bmi_value_lvl1" name="points_bmi_value_lvl1" value="<?php echo get_option('points_bmi_value_lvl1'); ?>" />
                        <?php 
                          $content = get_option('points_bmi_result_text_lvl1');
                          wp_editor( $content, 'points_bmi_result_text_lvl1', $settings = array('textarea_rows'=> '5') ); ?>
                      </label>
                    </p>
                  </td>
                </tr>

                <tr>
                  <th align="left" scope="row"><?php esc_html_e('Calculator Level 2');?></th>
                </tr>
                <tr>
                  <td align="left">
                    <p>
                      <label title="<?php esc_attr_e( 'points_bmi_result_text_lvl2' ); ?>">
                      <input type="text" id="points_bmi_result_header_lvl2" name="points_bmi_result_header_lvl2" value="<?php echo get_option('points_bmi_result_header_lvl2'); ?>" />
                      <input type="textarea" id="points_bmi_value_lvl2" name="points_bmi_value_lvl2" value="<?php echo get_option('points_bmi_value_lvl2'); ?>" />
                        <?php 
                          $content = get_option('points_bmi_result_text_lvl2');
                          wp_editor( $content, 'points_bmi_result_text_lvl2', $settings = array('textarea_rows'=> '5') ); ?>
                      </label>
                    </p>
                  </td>
                </tr>

                <tr>
                  <th align="left" scope="row"><?php esc_html_e('Calculator Level 3');?></th>
                </tr>
                <tr>
                  <td align="left">
                    <p>
                      <label title="<?php esc_attr_e( 'points_bmi_result_text_lvl3' ); ?>">
                      <input type="text" id="points_bmi_result_header_lvl3" name="points_bmi_result_header_lvl3" value="<?php echo get_option('points_bmi_result_header_lvl3'); ?>" />
                      <input type="textarea" id="points_bmi_value_lvl3" name="points_bmi_value_lvl3" value="<?php echo get_option('points_bmi_value_lvl3'); ?>" />
                        <?php 
                          $content = get_option('points_bmi_result_text_lvl3');
                          wp_editor( $content, 'points_bmi_result_text_lvl3', $settings = array('textarea_rows'=> '5') ); ?>
                      </label>
                    </p>
                  </td>
                </tr>

                <tr>
                  <th align="left" scope="row"><?php esc_html_e('Calculator Level 4');?></th>
                </tr>
                <tr>
                  <td align="left">
                    <p>
                      <label title="<?php esc_attr_e( 'points_bmi_result_text_lvl4' ); ?>">
                      <input type="text" id="points_bmi_result_header_lvl4" name="points_bmi_result_header_lvl4" value="<?php echo get_option('points_bmi_result_header_lvl4'); ?>" />
                      <input type="textarea" id="points_bmi_value_lvl4" name="points_bmi_value_lvl4" value="<?php echo get_option('points_bmi_value_lvl4'); ?>" />
                        <?php 
                          $content = get_option('points_bmi_result_text_lvl4');
                          wp_editor( $content, 'points_bmi_result_text_lvl4', $settings = array('textarea_rows'=> '5') ); ?>
                      </label>
                    </p>
                  </td>
                </tr>

                <tr>
                  <th align="left" scope="row"><?php esc_html_e('Calculator Level 5');?></th>
                </tr>
                <tr>
                  <td align="left">
                    <p>
                      <label title="<?php esc_attr_e( 'points_bmi_result_text_lvl5' ); ?>">
                      <input type="text" id="points_bmi_result_header_lvl5" name="points_bmi_result_header_lvl5" value="<?php echo get_option('points_bmi_result_header_lvl5'); ?>" />
                      <input type="textarea" id="points_bmi_value_lvl5" name="points_bmi_value_lvl5" value="<?php echo get_option('points_bmi_value_lvl5'); ?>" />
                        <?php 
                          $content = get_option('points_bmi_result_text_lvl5');
                          wp_editor( $content, 'points_bmi_result_text_lvl5', $settings = array('textarea_rows'=> '5') ); ?>
                      </label>
                    </p>
                  </td>
                </tr>

                <tr>
                  <th align="left" scope="row"><?php esc_html_e('Calculator Level 6');?></th>
                </tr>
                <tr>
                  <td align="left">
                    <p>
                      <label title="<?php esc_attr_e( 'points_bmi_result_text_lvl6' ); ?>">
                      <input type="text" id="points_bmi_result_header_lvl6" name="points_bmi_result_header_lvl6" value="<?php echo get_option('points_bmi_result_header_lvl6'); ?>" />
                      <input type="textarea" id="points_bmi_value_lvl6" name="points_bmi_value_lvl6" value="<?php echo get_option('points_bmi_value_lvl6'); ?>" />
                        <?php 
                          $content = get_option('points_bmi_result_text_lvl6');
                          wp_editor( $content, 'points_bmi_result_text_lvl6', $settings = array('textarea_rows'=> '6') ); ?>
                      </label>
                    </p>
                  </td>
                </tr>
                <tr>
                  <td>
                    <h2>Shortcode for BMI Calculator: [points_bmi_calculator]</h2>
                  </td>
                </tr>
          </tbody>
        </table>

        <?php settings_fields( 'points_bmi_calculator_group' ); ?>
        <?php submit_button(); ?>
      </form>
    </div>
  </div>
<?php
}

// Creating the widget 

class points_health_bmi_widget extends WP_Widget {
  
  function __construct() {
    parent::__construct('points_health_bmi_widget', __('BMI Calculator', 'points_health_bmi_widget_domain'), array( 'description' => __( 'Points Group - BMI Calculator', 'points_health_bmi_widget_domain' ), ));
  }

  // Creating widget front-end
  // This is where the action happens
  public function widget( $args, $instance ) {
    $title = apply_filters( 'widget_title', $instance['title'] );

    // before and after widget arguments are defined by themes
    echo $args['before_widget'];

    if ( ! empty( $title ) )
      echo $args['before_title'] . $title . $args['after_title'];

    echo points_display_bmi_calculator('widget', $instance);
    echo $args['after_widget'];
}

// Widget Backend 

public function form( $instance ) {
  if ( isset( $instance[ 'title' ] ) ) {
    $title = $instance[ 'title' ];
  }
  else {
    $title = __( 'BMI Calculator', 'points_health_bmi_widget_domain' );
  }
  if ( isset( $instance[ 'color' ] ) ) {
    $color = $instance[ 'color' ];
  }
  else {
    $color = __( '#2EA3F2', 'points_health_bmi_widget_domain' );
  }
}


} // Class sm_health_bmi_widget ends here

// Register and load the widget
function points_health_bmi_load_widget() {
  register_widget( 'points_health_bmi_widget' );
}

add_action( 'widgets_init', 'points_health_bmi_load_widget' );

// Add shortcode
function get_points_health_bmi_shortcode($atts) {
  return points_display_bmi_calculator('shortcode');
}

add_shortcode('points_bmi_calculator', 'get_points_health_bmi_shortcode');
