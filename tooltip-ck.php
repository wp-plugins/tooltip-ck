<?php
/**
 * Plugin Name: Tooltip CK
 * Plugin URI: http://www.wp-pluginsck.com/plugins-wordpress/tooltip-ck
 * Description: Tooltip CK allows you to put some nice tooltip effects into your content. Example : {tooltip}Text to hover{end-text}a friendly little boy{end-tooltip}
 * Version: 1.0.0
 * Author: Cédric KEIFLIN
 * Author URI: http://www.wp-pluginsck.com/
 * License: GPL2
 */

defined('ABSPATH') or die;

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

class Tooltipck {

	public $pluginname, $pluginurl, $plugindir, $options, $settings, $settings_field, $ispro, $prourl;

	public $default_settings = 
	array( 	
			'fxduration' => '300',
			'dureebulle' => '500',
			'fxtransition' => 'linear',
			'stylewidth' => '150',
			'padding' => '5',
			'tipoffsetx' => '0',
			'tipoffsety' => '0',
			'opacity' => '0.8',
			'bgcolor1' => '#f0f0f0',
			'bgcolor2' => '#e3e3e3',
			'textcolor' => '#444444',
			'roundedcornerstl' => '5',
			'roundedcornerstr' => '5',
			'roundedcornersbr' => '5',
			'roundedcornersbl' => '5',
			'shadowcolor' => '#444444',
			'shadowblur' => '3',
			'shadowspread' => '0',
			'shadowoffsetx' => '0',
			'shadowoffsety' => '0',
			'shadowinset' => '0',
			'bordercolor' => '#efefef',
			'borderwidth' => '1'
			);

	function __construct() {
		$this->pluginname = 'tooltip-ck';
		$this->pluginurl = plugins_url( '' , __FILE__ );
		$this->plugindir = WP_PLUGIN_DIR . '/' . $this->pluginname;
		$this->settings_field = 'tooltipck_options';
		$this->options = get_option( $this->settings_field );
		$this->prourl = 'http://www.wp-pluginsck.com/en/wordpress-plugins/tooltip-ck';
	}
	
	function init() {
		if (is_admin()) {
			// load settings page
			if (!class_exists("Tooltipck_Settings")) {
				require($this->plugindir . '/' . $this->pluginname . '-settings.php');
				$this->settings = new Tooltipck_Settings();
			}

			// load the pro version
			if (file_exists($this->plugindir . '/' . $this->pluginname . '-pro.php')) {
				$this->ispro = true;
				if (!class_exists('Tooltipck_Pro')) {
					require($this->plugindir . '/' . $this->pluginname . '-pro.php');
					new Tooltipck_Pro();
				}
			}
			// add the get pro link in the plugins list
			add_filter( 'plugin_action_links', array( $this, 'show_pro_message_action_links'), 10, 2);
		}

//		add_action('wp_enqueue_scripts', array( $this, 'load_assets_files'));
		add_action('wp_head', array( $this, 'load_assets'));
		add_filter('the_content',  array( $this, 'search_key'));
	}

	function load_assets_files() {
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core ');
		wp_enqueue_style('tooltipck', $this->pluginurl . '/assets/tooltipck.css');
		wp_enqueue_script('tooltipck', $this->pluginurl . '/assets/tooltipck.js');
	}

	function load_assets() {
		$this->load_assets_files();
		
		$fxduration = $this->get_option('fxduration');
		$dureebulle = $this->get_option('dureebulle');
		$opacity = $this->get_option('opacity');
		$fxtransition = 'linear';
		?>
		<script type="text/javascript">
		jQuery(document).ready(function($){
			$(this).Tooltipck({ 
				fxtransition: '<?php echo $fxtransition?>', 
				fxduration: <?php echo $fxduration ?>, 
				dureebulle: <?php echo $dureebulle ?>, 
				opacite: <?php echo $opacity ?> 
			});
		});
		</script>
		<style type="text/css">
		<?php echo $this->create_tooltip_css(); ?>
		</style>
	<?php }

	function get_option($name) {
		if (isset($this->options[$name])) {
			return $this->options[$name];
		} else if (isset($this->default_settings[$name])) {
			return $this->default_settings[$name];
		}
		return null;
	}

	function search_key($content){
		// test if the plugin is needed
		if (!stristr($content, "{tooltip}"))
			return $content;
		
		$regex = "#{tooltip}(.*?){end-tooltip}#s"; // search mask
		$content = preg_replace_callback($regex, 'Tooltipck::create_tooltip', $content);

		return $content;
	}

	function create_tooltip(&$matches) {
		$ID = (int) (microtime() * 100000); // unique ID
		$stylewidth = $this->get_option('stylewidth');
		$fxduration = $this->get_option('fxduration');
		$dureebulle = $this->get_option('dureebulle');
		$tipoffsetx = $this->get_option('tipoffsetx');
		$tipoffsety = $this->get_option('tipoffsety');

		// get the text
		$patterns = "#{tooltip}(.*){(.*)}(.*){end-tooltip}#Uis";
		$result = preg_match($patterns, $matches[0], $results);

		// check if there is some custom params
		$relparams = Array();
		$params = explode('|', $results[2]);
		$parmsnumb = count($params);
		for ($i = 1; $i < $parmsnumb; $i++) {
			$fxduration = stristr($params[$i], "mood=") ? str_replace('mood=', '', $params[$i]) : $fxduration;
			$dureebulle = stristr($params[$i], "tipd=") ? str_replace('tipd=', '', $params[$i]) : $dureebulle;
			$tipoffsetx = stristr($params[$i], "offsetx=") ? str_replace('offsetx=', '', $params[$i]) : $tipoffsetx;
			$tipoffsety = stristr($params[$i], "offsety=") ? str_replace('offsety=', '', $params[$i]) : $tipoffsety;
			$stylewidth = stristr($params[$i], "w=") ? str_replace('px', '', str_replace('w=', '', $params[$i])) : $stylewidth;
		}

		// compile the rel attribute to inject the specific params
		$relparams['mood'] = 'mood=' . $fxduration;
		$relparams['tipd'] = 'tipd=' . $dureebulle;
		$relparams['offsetx'] = 'offsetx=' . $tipoffsetx;
		$relparams['offsety'] = 'offsety=' . $tipoffsety;

		$tooltiprel = '';
		if (count($relparams)) {
			$tooltiprel = ' rel="' . implode("|", $relparams) . '"';
		}

		// output the code
		$result = '<span class="infotip" id="tooltipck' . $ID . '"' . $tooltiprel . '>'
					. $results[1]
					. '<span class="tooltipck_tooltip" style="width:' . $stylewidth . 'px;">'
						. '<span class="tooltipck_inner">'
						. $results[3]
						. '</span>'
					. '</span>'
				. '</span>';

		return $result;
	}

	function create_tooltip_css() {
		$padding = $this->get_option('padding') . 'px';
		$tipoffsetx = $this->get_option('tipoffsetx') . 'px';
		$tipoffsety = $this->get_option('tipoffsety') . 'px';
		$bgcolor1 = $this->get_option('bgcolor1');
		$bgcolor2 = $this->get_option('bgcolor2');
		$textcolor = $this->get_option('textcolor');
		$roundedcornerstl = $this->get_option('roundedcornerstl') . 'px';
		$roundedcornerstr = $this->get_option('roundedcornerstr') . 'px';
		$roundedcornersbr = $this->get_option('roundedcornersbr') . 'px';
		$roundedcornersbl = $this->get_option('roundedcornersbl') . 'px';
		$shadowcolor = $this->get_option('shadowcolor');
		$shadowblur = $this->get_option('shadowblur') . 'px';
		$shadowspread = $this->get_option('shadowspread') . 'px';
		$shadowoffsetx = $this->get_option('shadowoffsetx') . 'px';
		$shadowoffsety = $this->get_option('shadowoffsety') . 'px';
		$bordercolor = $this->get_option('bordercolor');
		$borderwidth = $this->get_option('borderwidth') . 'px';
		$shadowinset = $this->get_option('shadowinset');

		$shadowinset = $shadowinset ? 'inset ' : '';

		$css = '.tooltipck_tooltip {'
				. 'padding: ' . $padding . ';'
				. 'border: ' . $bordercolor . ' ' . $borderwidth . ' solid;'
				. '-moz-border-radius: ' . $roundedcornerstl . ' ' . $roundedcornerstr . ' ' . $roundedcornersbr . ' ' . $roundedcornersbl . ';'
				. '-webkit-border-radius: ' . $roundedcornerstl . ' ' . $roundedcornerstr . ' ' . $roundedcornersbr . ' ' . $roundedcornersbl . ';'
				. 'border-radius: ' . $roundedcornerstl . ' ' . $roundedcornerstr . ' ' . $roundedcornersbr . ' ' . $roundedcornersbl . ';'
				. 'background: ' . $bgcolor1 . ';'
				. 'background: -moz-linear-gradient(top, ' . $bgcolor1 . ', ' . $bgcolor2 . ');'
				. 'background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(' . $bgcolor1 . '), to(' . $bgcolor2 . '));'
				. 'color: ' . $textcolor . ';'
				. 'margin: ' . $tipoffsety . ' 0 0 ' . $tipoffsetx . ';'
				. '-moz-box-shadow: ' . $shadowinset . $shadowoffsetx . ' ' . $shadowoffsety . ' ' . $shadowblur . ' ' . $shadowspread . ' ' . $shadowcolor . ';'
				. '-webkit-box-shadow: ' . $shadowinset . $shadowoffsetx . ' ' . $shadowoffsety . ' ' . $shadowblur . ' ' . $shadowspread . ' ' . $shadowcolor . ';'
				. 'box-shadow: ' . $shadowinset . $shadowoffsetx . ' ' . $shadowoffsety . ' ' . $shadowblur . ' ' . $shadowspread . ' ' . $shadowcolor . ';'
				. '}';

		return $css;
	}
	
	function show_pro_message_action_links($links, $file) {
		if ($file == plugin_basename(__FILE__)) {
			array_push($links, '<a href="options-general.php?page=' . $this->pluginname . '">'. __('Settings'). '</a>');
			if (!$this->ispro) {
				array_push($links, '<br /><img class="iconck" src="' .$this->pluginurl . '/images/star.png" /><a target="_blank" href="' . $this->prourl .'">' . __('Get the PRO Version') . '</a>');
			} else {
				array_push($links, '<br /><img class="iconck" src="' .$this->pluginurl . '/images/tick.png" /><span style="color: green;">' . __('You are using the PRO Version. Thank you !') . '</span>' );
			}
		}
		return $links;
	}
	
	function show_pro_message_settings_page() { ?>
		<div class="ckcheckproversion">
			<?php if (!file_exists($this->plugindir . '/' . $this->pluginname . '-pro.php')) : ?>
				<img class="iconck" src="<?php echo $this->pluginurl ?>/images/star.png" />
				<a target="_blank" href="<?php echo $this->prourl ?>"><?php _e('Get the PRO Version'); ?></a>
			<?php endif; ?>
		</div>
	<?php }
}

$tooltipckClass = new Tooltipck();
$tooltipckClass->init();