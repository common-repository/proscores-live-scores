<?php
if (! defined('ABSPATH')) exit; // Exit if accessed directly
/*
Plugin Name: ProScores - Live Scores
Plugin URI: https://widgets.proscores.app
Description: ProScores provides a fully customizable and responsive live scores page, free of ads and iframes. Developed by Livescores.pro
Version: 1.0.7
Author: LiveScores.pro
Author URI: https://livescores.pro
License: GPLv2 or later
Text Domain: proscores-live-scores
Domain Path: /languages
*/


/**
 * Main Class
 */
class ProScores_Live_Scores {
    private static $instance = null;
    private $plugin;
    public $options;
    public $options_standings;
    public $defs = array(
            'tar'=>"#555555",'fa'=>"arial",'fs'=>"11.5px",'lbg'=>"#ecece9",'dbg'=>"#fafafa",'m_tt'=>"capitalize",'md_tt'=>"capitalize",'clr'=>"#555555",'c_fw'=>"bold",'c_clr'=>"#555555",'c_bg'=>"#abd8ea",'c_tt'=>"capitalize",'l_fw'=>"normal",'l_bg'=>"#e1edf3",'l_clr'=>"#00699e",'l_tt'=>"capitalize",'m_bg2'=>"#f5f5f5",'m_bg'=>"#f8f8f8",'m_clr'=>"#555555",'live_clr'=>"#ff0000",'score_bg'=>"#b4eae3",'score_bg2'=>"#fdfbc5",'flagw'=>"14px",'inc_bg'=>"#ebf3f4",'inc_color'=>"#333333",'inc_score'=>"#009eb3",'inc_score_color'=>"#ffffff",'inc_fs'=>"12px",'inc_img'=>"16px"
        );
    public $defs_standings = array(
        'mainCLR' => '#222222', 'filterSBG' => '#00699e', 'filterBG' => '#f1f5fb', 'filterSCLR' => '#ffffff',
        'rowBgOdd' => '#f0f0f0','rowBgEven' => '#f8fcff',
        'ptsBG' =>'#f7f7c1','ptsBRD' =>'#f7c617','ptsCLR' =>"#222222",
        'nuw'=>'#1f8000','nud'=>'#005aff','nul'=>'#ff2b00',
        'mainBRD'=>'#e8e8e8'
    );
    public $widgetURI= "https://widgets.proscores.app";
    public $authorIS= "livescores.pro";
    public $authorURI= "https://livescores.pro";
    public $jsver='1.0';
    public static function get_instance() {
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }
    private function __construct() {
        add_action('admin_init', array(&$this, 'proscores_handle_reset'));
        add_action('admin_menu', array(&$this, 'proscores_add_page'));
        add_action('admin_init', array(&$this, 'proscores_register_page_options'));
        add_action('admin_enqueue_scripts', array($this, 'proscores_enqueue_admin_js'));

        $this->plugin = plugin_basename(__FILE__);
        add_filter("plugin_action_links_$this->plugin", array($this, 'proscores_setting_links'));

        // Get registered option
        if ( !function_exists('get_plugin_data') ) {
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            $plugin_data = get_plugin_data( __FILE__ );
            $this->jsver = $plugin_data['Version'];
        }
        $this->options = get_option('proscores_settings_options');
        $this->options_standings = get_option('proscores_settings_options_standings');
        add_action('wp_enqueue_scripts', array($this, 'proscores_conditionally_prefetch'));
        register_uninstall_hook( __FILE__, array($this, 'proscores_uninstall') );
        add_shortcode( 'proscores', array($this, 'proscores_plugin_code') );
        add_shortcode( 'prostandings', array($this, 'proscores_plugin_code_standings') );
    }
    public function proscores_conditionally_prefetch() {
        if (is_singular() && has_shortcode(get_post()->post_content, 'proscores')) {
            // Add the link tag to the head with 'as="style"' for preloading the stylesheet
            $langIs = $this->options['proscores_lang'] ?? "en";
            wp_enqueue_script('prolivewidget', $this->widgetURI . '/njs/' . $langIs . '/prolivewidget.js', array(), $this->jsver, true);
            add_action('wp_head', function() use ($langIs) {
                $widgetURI = esc_url($this->widgetURI);
                $jsver = esc_attr($this->jsver);
                echo '<link rel="prefetch" href="' . esc_url($widgetURI . '/njs/' . $langIs . '/prolivewidget.js?ver=' . $jsver) . '" as="script">';
            });
        }
        if (is_singular() && has_shortcode(get_post()->post_content, 'prostandings')) {
            $langIs = $this->options['proscores_lang'] ?? "en";
            wp_enqueue_script('prowidgetdeep', $this->widgetURI . '/njs/' . $langIs . '/prowidgetdeep.js', array(), $this->jsver, true);
            add_action('wp_head', function() use ($langIs) {
                $widgetURI = esc_url($this->widgetURI);
                $jsver = esc_attr($this->jsver);
                echo '<link rel="prefetch" href="' . esc_url($widgetURI . '/njs/' . $langIs . '/prowidgetdeep.js?ver=' . $jsver) . '" as="script">';
            });
        }
    }
    public function proscores_uninstall() {
        delete_option( 'proscores_settings_options' );
    }

    public function proscores_handle_reset()
    {
        $retrieved_nonce = isset($_REQUEST['proscores_reset_settings_nonce']) ? sanitize_text_field(wp_unslash($_REQUEST['proscores_reset_settings_nonce'])) : '';
        if (isset($_POST['proscores_reset']) && !wp_verify_nonce($retrieved_nonce, 'proscores_plugin_reset' ) ) {
            add_settings_error( 'proscores_settings_options', 'proscores_bg_error', 'Reset to default failed due to a security nonce check.'.$retrieved_nonce );
        } else if(isset($_POST['proscores_reset']) && wp_verify_nonce($retrieved_nonce, 'proscores_plugin_reset' ) ) {
            $this->proscores_reset_settings();
            add_action('admin_notices', array(&$this, 'proscores_reset_notice'));
        }
    }

    public function proscores_reset_notice() {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php esc_html_e('Settings reset to default.', 'proscores-live-scores'); ?></p>
        </div>
        <?php
    }
    public function proscores_reset_settings() {
        delete_option( 'proscores_settings_options' );
        delete_option( 'proscores_settings_options_standings' );
        $this->options = get_option('proscores_settings_options');
        $this->options_standings = get_option('proscores_settings_options_standings');
    }
    public function proscores_add_page() {
        // $page_title, $menu_title, $capability, $menu_slug, $callback_function
        add_options_page('Theme Options', 'ProScores', 'manage_options', "proscores_plugin", array($this, 'proscores_display_page'));
        //add_menu_page('ProScores Settings', 'ProScores', 'manage_options', 'proscores_plugin', array($this, 'display_page'));
    }
    public function proscores_setting_links($links) {
        $setting_links="<a href='options-general.php?page=proscores_plugin'>Settings</a>";
        array_push($links, $setting_links);
        return $links;
    }
    public function proscores_display_page() {
        ?>
        <div class="wrap">
            <div style="display: flex;flex-wrap: wrap;">
                <div style="flex:1 1 50%">      
                    <h2><?php esc_html_e('ProScores Settings', 'proscores-live-scores'); ?></h2>
                    <form method="post" action="options.php">
                    <?php
                        submit_button();
                        wp_nonce_field('proscores_plugin', 'proscores_plugin_nonce');
                        settings_fields("proscores_plugin" );
                        do_settings_sections("proscores_plugin");
                        submit_button();
                    ?>
                    </form>                    
                </div>
                <div style="flex:1 1 50%">
                    <h2><?php esc_html_e('Live Preview', 'proscores-live-scores'); ?></h2>
                    <div id="livepreview" style="height: 100vh;position: sticky;top: 40px;">
                        <onizle></onizle>
                    </div>
                </div>
            </div>
            <div style="display: flex;flex-wrap: wrap;">
                <div style="flex:1 1 50%">
                    <h2><?php esc_html_e('Standings Settings', 'proscores-live-scores'); ?></h2>
                    <form method="post" action="options.php">
                    <?php
                        wp_nonce_field('proscores_plugin', 'proscores_plugin_nonce');
                        settings_fields("proscores_plugin_standings" );
                        do_settings_sections("proscores_plugin_standings");
                        submit_button();
                    ?>
                    </form>
                    <form method="post">
                        <?php wp_nonce_field('proscores_plugin_reset','proscores_reset_settings_nonce'); ?>
                        <input type="hidden" name="proscores_reset" value="true">
                        <?php submit_button(__('Reset to Default','proscores-live-scores'), 'secondary'); ?>
                    </form>
                </div>
                <div style="flex:1 1 50%">
                    <h2><?php esc_html_e('Live Preview', 'proscores-live-scores'); ?></h2>
                    <div id="livepreview" style="height: 100vh;position: sticky;top: 40px;">
                        <onizle2></onizle2>
                    </div>
                </div>
            </div>
            <h2><?php esc_html_e('Instructions for Using the ProScores', 'proscores-live-scores'); ?></h2>
            <ul style="list-style: inside;">
                <li><b><?php esc_html_e('Basic Usage', 'proscores-live-scores'); ?></b>
                    <blockquote>
                        <li><?php esc_html_e('Use the [proscores] shortcode to display today\'s scores.', 'proscores-live-scores'); ?></li>
                        <li><?php esc_html_e('Live matches: [proscores list="live"] Yesterday\'s results: [proscores list="yesterday"] Tomorrow\'s fixtures: [proscores list="tomorrow"] ', 'proscores-live-scores'); ?></li>
                        <li><?php esc_html_e('You can set font size and font family attributes. For example: [proscores font-family="sans-serif"].', 'proscores-live-scores'); ?></li>
                    </blockquote>
                </li>
                <li><b><?php esc_html_e('Shortcode for Leagues', 'proscores-live-scores'); ?></b>
                    <blockquote>
                        <li><?php esc_html_e('To display scores for the England - Premier League, use the following shortcode: [proscores list="league" path="/a/50/league/england-premier-league/"]', 'proscores-live-scores'); ?></li>
                        <li><?php esc_html_e('For other leagues, visit our site at Livescores.pro and navigate to the desired league page. In the address bar, you will see a URL like https://livescores.pro/a/50/league/england-premier-league/. Copy the path part, which in this example is /a/50/league/england-premier-league/, and use it in the shortcode.', 'proscores-live-scores'); ?></li>
                    </blockquote>
                </li>
                <li><b><?php esc_html_e('Shortcode for Standings', 'proscores-live-scores'); ?></b>
                    <blockquote>
                        <li><?php esc_html_e('To display standings for the England - Premier League, use the following shortcode: ', 'proscores-live-scores'); ?> <code>[prostandings path="/a/50/league/england-premier-league/standings"]</code></li>
                        <li>Narrow display: <code>[prostandings compact="1" path="/a/50/league/england-premier-league/standings"]</code></li>
                        <li>Without Promotion Informations: <code>[prostandings legends="0" path="/a/50/league/england-premier-league/standings"]</code></li>
                        <li>Without Team Logos: <code>[prostandings logos="0" path="/a/50/league/england-premier-league/standings"]</code></li>
                        <li><?php esc_html_e('For other leagues, visit our site at Livescores.pro and navigate to the desired league\'s standings page. In the address bar, you will see a URL like https://livescores.pro/a/50/league/england-premier-league/standings. Copy the path part, which in this example is /a/50/league/england-premier-league/standings, and use it in the shortcode.', 'proscores-live-scores'); ?></li>
                    </blockquote>
                </li>
                <li><?php esc_html_e('When you set "Credit Author" to "Off" means:', 'proscores-live-scores'); ?>
                    <blockquote>
                        * <?php esc_html_e('You are not linking back to author site.', 'proscores-live-scores'); ?><br>
                        * <?php esc_html_e('ProScores will work in an iframe that sized 100% x 8000px .', 'proscores-live-scores'); ?><br>
                        * <?php esc_html_e('Color and Font customizations will be default setting.', 'proscores-live-scores'); ?><br>
                    </blockquote>
                </li>
                <li><?php esc_html_e('When you set "Credit Author" to "On" means:', 'proscores-live-scores'); ?>
                    <blockquote>
                        * <?php esc_html_e('You are linking back to author site.', 'proscores-live-scores'); ?><br>
                        * <?php esc_html_e('ProScores will work without an iframe.', 'proscores-live-scores'); ?><br>
                        * <?php esc_html_e('Color and Font customizations will be your setting.', 'proscores-live-scores'); ?><br>
                    </blockquote>
                </li>
            </ul>
        </div> <!-- /wrap -->
        <?php
    }
    public function proscores_register_page_options() {
        // Add Section for option fields
        add_settings_section( 'Smain', __('Main Settings','proscores-live-scores'), array( $this, 'proscores_display_section' ),  "proscores_plugin" );
        add_settings_field( 'proscores_plugin_lang', __('Language','proscores-live-scores'), array( $this, 'proscores_lang_settings_fieldv2' ),  "proscores_plugin" , 'Smain');
        add_settings_field( 'proscores_plugin_credit', __('Credit Author','proscores-live-scores'), array( $this, 'proscores_select_field' ),  "proscores_plugin" , 'Smain',array('key' => 'proscores_credit','options' => array('off' => __('OFF','proscores-live-scores'),'on' => __('ON','proscores-live-scores') , 'notes' => __('Turn it ON to use your custom colors, or keep it OFF for default colors. For more details, see Notes at the bottom of the page.','proscores-live-scores')) ));
        add_settings_field( 'proscores_plugin_fs', __('Font Size','proscores-live-scores'), array( $this, 'proscores_select_field' ),  "proscores_plugin" , 'Smain',array('key' => 'fs','listen' => 'var','options' => array('10px' => '10px','10.5px' => '10.5px','11px' => '11px','11.5px' => '11.5px','12px' => '12px','12.5px' => '12.5px','13px' => '13px','13.5px' => '13.5px','14px' => '14px','14.5px' => '14.5px' ) ));

        add_settings_section( 'Smatch_row', __('Match Row','proscores-live-scores'), array( $this, 'proscores_display_section' ),  "proscores_plugin" );
            add_settings_field( 'proscores_plugin_lmt_text', __('Live Match Tracker (Text)','proscores-live-scores'), array( $this, 'proscores_select_field' ),  "proscores_plugin" , 'Smatch_row',array('key' => 'lmt_text','options' => array('true' => __('ON','proscores-live-scores'),'false' => __('OFF','proscores-live-scores') ) ));
            add_settings_field( 'proscores_plugin_m_bg', __('Background (1)','proscores-live-scores'), array( $this, 'proscores_plugin_colorcb' ),  "proscores_plugin", 'Smatch_row',array('o_name' => 'm_bg') );
            add_settings_field( 'proscores_plugin_m_bg2', __('Background (2)','proscores-live-scores'), array( $this, 'proscores_plugin_colorcb' ), "proscores_plugin", 'Smatch_row',array('o_name' => 'm_bg2') );
            add_settings_field( 'proscores_plugin_m_tt', __('Font Transform','proscores-live-scores'), array( $this, 'proscores_select_field' ),  "proscores_plugin" , 'Smatch_row',array('key' => 'm_tt','listen' => 'var','options' => array('capitalize' => __('Capitalize','proscores-live-scores'),'uppercase' => __('Uppercase','proscores-live-scores') ) ));
            add_settings_field( 'proscores_plugin_m_clr', __('Font Color','proscores-live-scores'), array( $this, 'proscores_plugin_colorcb' ), "proscores_plugin", 'Smatch_row',array('o_name' => 'm_clr') );
            add_settings_field( 'proscores_plugin_live_clr', __('Live Color','proscores-live-scores'), array( $this, 'proscores_plugin_colorcb' ), "proscores_plugin", 'Smatch_row',array('o_name' => 'live_clr') );
            add_settings_field( 'proscores_plugin_score_bg', __('Scored Team Background','proscores-live-scores'), array( $this, 'proscores_plugin_colorcb' ), "proscores_plugin", 'Smatch_row',array('o_name' => 'score_bg') );
            add_settings_field( 'proscores_plugin_score_bg2', __('Scored Match Score Background','proscores-live-scores'), array( $this, 'proscores_plugin_colorcb' ), "proscores_plugin", 'Smatch_row',array('o_name' => 'score_bg2') );

        add_settings_section( 'Sinc', __('Match Details','proscores-live-scores'), array( $this, 'proscores_display_section' ), "proscores_plugin" );
            add_settings_field( 'proscores_plugin_inc_bg', __('Background','proscores-live-scores'), array( $this, 'proscores_plugin_colorcb' ), "proscores_plugin", 'Sinc',array('o_name' => 'inc_bg') );
            add_settings_field( 'proscores_plugin_inc_color', __('Font Color','proscores-live-scores'), array( $this, 'proscores_plugin_colorcb' ), "proscores_plugin", 'Sinc',array('o_name' => 'inc_color') );
            add_settings_field( 'proscores_plugin_inc_fs', __('Font Size','proscores-live-scores'), array( $this, 'proscores_select_field' ),  "proscores_plugin" , 'Sinc',array('key' => 'inc_fs','listen' => 'var','options' => array('10px' => '10px','10.5px' => '10.5px','11px' => '11px','11.5px' => '11.5px','12px' => '12px','12.5px' => '12.5px','13px' => '13px','13.5px' => '13.5px','14px' => '14px','14.5px' => '14.5px' ) ));
            add_settings_field( 'proscores_plugin_md_tt', __('Font Transform','proscores-live-scores'), array( $this, 'proscores_select_field' ),  "proscores_plugin" , 'Sinc',array('key' => 'md_tt','listen' => 'var','options' => array('capitalize' => __('Capitalize','proscores-live-scores'),'uppercase' => __('Uppercase','proscores-live-scores') ) ));
            add_settings_field( 'proscores_plugin_inc_score', __('Score Background','proscores-live-scores'), array( $this, 'proscores_plugin_colorcb' ), "proscores_plugin", 'Sinc',array('o_name' => 'inc_score') );
            add_settings_field( 'proscores_plugin_inc_score_color', __('Score Font Color','proscores-live-scores'), array( $this, 'proscores_plugin_colorcb' ), "proscores_plugin", 'Sinc',array('o_name' => 'inc_score_color') );

        add_settings_section( 'Sc_row', __('Country','proscores-live-scores'), array( $this, 'proscores_display_section' ), "proscores_plugin" );
            add_settings_field( 'proscores_plugin_c_bg', __('Background','proscores-live-scores'), array( $this, 'proscores_plugin_colorcb' ), "proscores_plugin", 'Sc_row',array('o_name' => 'c_bg') );
            add_settings_field( 'proscores_plugin_c_clr', __('Font Color','proscores-live-scores'), array( $this, 'proscores_plugin_colorcb' ), "proscores_plugin", 'Sc_row',array('o_name' => 'c_clr') );
            add_settings_field( 'proscores_plugin_c_tt', __('Font Transform','proscores-live-scores'), array( $this, 'proscores_select_field' ),  "proscores_plugin" , 'Sc_row',array('key' => 'c_tt','listen' => 'var','options' => array('capitalize' => __('Capitalize','proscores-live-scores'),'uppercase' => __('Uppercase','proscores-live-scores') ) ));

        add_settings_section( 'Sl_row', __('League','proscores-live-scores'), array( $this, 'proscores_display_section' ), "proscores_plugin" );
            add_settings_field( 'proscores_plugin_l_bg', __('Background','proscores-live-scores'), array( $this, 'proscores_plugin_colorcb' ), "proscores_plugin", 'Sl_row',array('o_name' => 'l_bg') );
            add_settings_field( 'proscores_plugin_l_clr', __('Font Color','proscores-live-scores'), array( $this, 'proscores_plugin_colorcb' ), "proscores_plugin", 'Sl_row',array('o_name' => 'l_clr') );
            add_settings_field( 'proscores_plugin_l_tt', __('Font Transform','proscores-live-scores'), array( $this, 'proscores_select_field' ),  "proscores_plugin" , 'Sl_row',array('key' => 'l_tt','listen' => 'var','options' => array('capitalize' => __('Capitalize','proscores-live-scores'),'uppercase' => __('Uppercase','proscores-live-scores') ) ));

        add_settings_section( 'Sprem', __('Premium Features','proscores-live-scores'), array( $this, 'proscores_display_section' ), "proscores_plugin" );
            //add_settings_field( 'premium_info', 'What is it?', array( $this, 'proscores_premium_info' ), "proscores_plugin", 'Sprem' );
            add_settings_field( 'proscores_plugin_lmt_ani', __('Live Match Tracker (Animated)','proscores-live-scores'), array( $this, 'proscores_select_field' ),  "proscores_plugin" , 'Sprem',array('key' => 'lmt_ani','options' => array('false' => __('OFF','proscores-live-scores'),'true' => __('ON','proscores-live-scores') ) ));
            //add_settings_field( 'lmtani', 'Animated Live Match Tracker', array( $this, 'proscores_plugin_title_settings_field' ), "proscores_plugin", 'Sprem' );
            add_settings_field( 'proscores_plugin_token', __('Your Domain Token','proscores-live-scores'), array( $this, 'proscores_text_field' ), "proscores_plugin", 'Sprem' , array('key' => 'token', 'attr' => 'id=proscores_token disabled=disabled','bf' => '<div id=proscores_token_text></div>','af' => '<div id=proscores_activate></div>', 'type' => 'hidden'));
        register_setting( "proscores_plugin", 'proscores_settings_options', array( $this, 'proscores_plugin_validate_options' ) );

        add_settings_section( 'Smain', __('Main Settings','proscores-live-scores'), array( $this, 'proscores_display_section' ),  "proscores_plugin_standings" );
            add_settings_field( 'proscores_plugin_mainCLR', __('Font Color','proscores-live-scores'), array( $this, 'proscores_plugin_colorcb_standings' ), "proscores_plugin_standings", 'Smain',array('o_name' => 'mainCLR') );
            add_settings_field( 'proscores_plugin_mainBRD', __('Border Color','proscores-live-scores'), array( $this, 'proscores_plugin_colorcb_standings' ), "proscores_plugin_standings", 'Smain',array('o_name' => 'mainBRD') );
            add_settings_field( 'proscores_plugin_filterBG', __('Label Background','proscores-live-scores'), array( $this, 'proscores_plugin_colorcb_standings' ), "proscores_plugin_standings", 'Smain',array('o_name' => 'filterBG') );
            add_settings_field( 'proscores_plugin_filterSBG', __('Selected Label Background','proscores-live-scores'), array( $this, 'proscores_plugin_colorcb_standings' ), "proscores_plugin_standings", 'Smain',array('o_name' => 'filterSBG') );
            add_settings_field( 'proscores_plugin_filterSCLR', __('Selected Label Font Color','proscores-live-scores'), array( $this, 'proscores_plugin_colorcb_standings' ), "proscores_plugin_standings", 'Smain',array('o_name' => 'filterSCLR') );
            add_settings_field( 'proscores_plugin_rowBgOdd', __('Background (1)','proscores-live-scores'), array( $this, 'proscores_plugin_colorcb_standings' ), "proscores_plugin_standings", 'Smain',array('o_name' => 'rowBgOdd') );
            add_settings_field( 'proscores_plugin_rowBgEven', __('Background (2)','proscores-live-scores'), array( $this, 'proscores_plugin_colorcb_standings' ), "proscores_plugin_standings", 'Smain',array('o_name' => 'rowBgEven') );
            add_settings_field( 'proscores_plugin_ptsBG', __('Points Background','proscores-live-scores'), array( $this, 'proscores_plugin_colorcb_standings' ), "proscores_plugin_standings", 'Smain',array('o_name' => 'ptsBG') );
            add_settings_field( 'proscores_plugin_ptsBRD', __('Points Border Color','proscores-live-scores'), array( $this, 'proscores_plugin_colorcb_standings' ), "proscores_plugin_standings", 'Smain',array('o_name' => 'ptsBRD') );
            add_settings_field( 'proscores_plugin_ptsClR', __('Points Font Color','proscores-live-scores'), array( $this, 'proscores_plugin_colorcb_standings' ), "proscores_plugin_standings", 'Smain',array('o_name' => 'ptsCLR') );
            add_settings_field( 'proscores_plugin_nuw', __('Wins Font Color','proscores-live-scores'), array( $this, 'proscores_plugin_colorcb_standings' ), "proscores_plugin_standings", 'Smain',array('o_name' => 'nuw') );
            add_settings_field( 'proscores_plugin_nud', __('Draws Font Color','proscores-live-scores'), array( $this, 'proscores_plugin_colorcb_standings' ), "proscores_plugin_standings", 'Smain',array('o_name' => 'nud') );
            add_settings_field( 'proscores_plugin_nul', __('Loses Font Color','proscores-live-scores'), array( $this, 'proscores_plugin_colorcb_standings' ), "proscores_plugin_standings", 'Smain',array('o_name' => 'nul') );


        register_setting( "proscores_plugin_standings", 'proscores_settings_options_standings', array( $this, 'proscores_plugin_validate_options_standings' ) );
    }
    public function proscores_enqueue_admin_js() {
        $handle = 'proscores_custom_js';
        $src = plugins_url('jquery.custom.js', __FILE__);
        $deps = array();
        $ver = '1.0.1';
        $in_footer = true;
        wp_enqueue_script($handle, $src, $deps, $ver, $in_footer);
    }
    public function proscores_plugin_validate_options($fields) {
        $valid_fields = array();
        $retrieved_nonce = isset($_REQUEST['proscores_plugin_nonce']) ? sanitize_text_field(wp_unslash($_REQUEST['proscores_plugin_nonce'])) : '';
        if (!wp_verify_nonce($retrieved_nonce, 'proscores_plugin' ) ) {
            add_settings_error( 'proscores_settings_options', 'proscores_bg_error', 'Update failed due to a security nonce check.'.$retrieved_nonce );
        } else {
            $title = trim( $fields['proscores_lang'] );
            $valid_fields['proscores_lang'] = wp_strip_all_tags( stripslashes( $title ) );
            foreach ($fields as $key => $val) {
               $position = strpos($key, "_bg"); $position2 = strpos($key, "_clr");
               if ($position == true || $position2 == true || $key==="inc_color" || $key=='inc_score') {
                if( FALSE === $this->proscores_plugin_check_color( $val ) ) {
                    add_settings_error( 'proscores_settings_options', 'proscores_bg_error', 'Insert a valid color for Background '. $key, 'error' );
                    // Get the previous valid value 
                    $valid_fields[$key] = $this->options[$key];
                } else {
                    $valid_fields[$key] = sanitize_text_field($val);  
                }
               } else {
                $valid_fields[$key]=sanitize_text_field($val);//wp_strip_all_tags( stripslashes( $val ) );
               }
            }
        }
        return apply_filters( 'proscores_plugin_validate_options', $valid_fields, $fields);
    }
    public function proscores_plugin_validate_options_standings($fields) {
        $valid_fields = array();
        $retrieved_nonce = isset($_REQUEST['proscores_plugin_nonce']) ? sanitize_text_field(wp_unslash($_REQUEST['proscores_plugin_nonce'])) : '';
        if (!wp_verify_nonce($retrieved_nonce, 'proscores_plugin' ) ) {
            add_settings_error( 'proscores_settings_options_standings', 'proscores_bg_error', 'Update failed due to a security nonce check.'.$retrieved_nonce );
        } else {
            $title = trim( $fields['proscores_lang'] );
            $valid_fields['proscores_lang'] = wp_strip_all_tags( stripslashes( $title ) );
            foreach ($fields as $key => $val) {
               $position = strpos($key, "_bg"); $position2 = strpos($key, "_clr");
               if ($position == true || $position2 == true || $key==="inc_color" || $key=='inc_score') {
                if( FALSE === $this->proscores_plugin_check_color( $val ) ) {
                    add_settings_error( 'proscores_settings_options_standings', 'proscores_bg_error', 'Insert a valid color for Background '. $key, 'error' );
                    // Get the previous valid value 
                    $valid_fields[$key] = $this->options_standings[$key];
                } else {
                    $valid_fields[$key] = sanitize_text_field($val);  
                }
               } else {
                $valid_fields[$key]=sanitize_text_field($val);//wp_strip_all_tags( stripslashes( $val ) );
               }
            }
        }
        return apply_filters( 'proscores_plugin_validate_options_standings', $valid_fields, $fields);
    }
    public function proscores_plugin_check_color($value) {
        if (preg_match('/^#[a-f0-9]{6}$/i', $value)) {
            return sanitize_hex_color($value);
        }
        return '';
    }
    public function proscores_display_section() { /* Leave blank */ }
    public function proscores_plugin_title_settings_field() { 
        
        $val = ( isset( $this->options['title'] ) ) ? $this->options['title'] : '';
        echo '<input type="text" name="proscores_settings_options[title]" value="' . esc_attr($val) . '" />';
    }
    public function proscores_plugin_colorcb(array $args) {
        $val = isset($this->options[$args['o_name']]) ? $this->options[$args['o_name']] :  $this->defs[$args['o_name']];//def_vars($args['o_name']);
        if (!$val) {
        $val = $args['def_color'];
        }
        echo '<input type="color" onchange=\'codeGen.setvar("'. esc_attr($args['o_name']) .'",this)\' oninput=\'codeGen.setvar("'. esc_attr($args['o_name']) .'",this)\' var="'. esc_attr($args['o_name']) .'" name="proscores_settings_options['. esc_attr($args['o_name']) .']" value="' . esc_attr($val) . '" class="proscores-color-picker" >  ';
    }
    public function proscores_plugin_colorcb_standings(array $args) {
        $val = isset($this->options_standings[$args['o_name']]) ? $this->options_standings[$args['o_name']] :  $this->defs_standings[$args['o_name']];//def_vars($args['o_name']);
        if (!$val) {
        $val = $args['def_color'];
        }
        echo '<input type="color" onchange=\'codeGenST.setvar("'. esc_attr($args['o_name']) .'",this)\' oninput=\'codeGenST.setvar("'. esc_attr($args['o_name']) .'",this)\' var="'. esc_attr($args['o_name']) .'" name="proscores_settings_options_standings['. esc_attr($args['o_name']) .']" value="' . esc_attr($val) . '" class="proscores-color-picker" >  ';
    }
    public function proscores_premium_info() {
        echo "Live match tracker in text format is free to use. However, to access animated live match tracker, you will need to obtain a token to enable access for your domain.";
    }
    public function proscores_your_word_cb() {
        echo '<input type="hidden" name="proscores_settings_options[proscores_your_word]" value="" />';
    }

    public function proscores_c_link_settings_field() {
        $val = isset($this->options['proscores_c_link']) ? $this->options['proscores_c_link'] : 'off';

        if (!$val) {
            $val = 'off';
        }

        $selected_one = array('on' => '', 'off' => '');

        $selected_one[$val] = 'selected="selected"';
        echo '<select name="proscores_settings_options[proscores_c_link]">
                <option value="off" '. esc_html($selected_one['off']) .'>Off</option>
                <option value="on" '. esc_html($selected_one['on']) .'>On</option>
            </select>
        <small>Set it "On" to use your color settings, else you may keep it "Off" for default colors. Need detailed information? Please have a look "Notes" at the bottom of page.</small>';
    }
    public function proscores_select_field(array $args) {
        $mkey=$args["key"];$listen=$args["listen"] ?? '';$attr="";
        $val = ( isset( $this->options[$mkey] ) ) ? $this->options[$mkey] : ($this->defs[$mkey] ?? '');
        $wd='';$selected_one = array();
        $selected_one = array();
        $selected_one[$val] ='selected="selected"';
        foreach ($args["options"] as $pro_key => $pro_val) {
                if($pro_key!='notes') $wd .='<option value="'. esc_html($pro_key) .'" '. esc_html($selected_one[$pro_key] ?? '') .'>'. esc_html($pro_val) .'</option>';
        }
        if($listen=='var') {$attr='onchange=codeGen.setvar("'.$mkey.'",this) var='.$mkey.'';}
        $noteIs='';
        if(isset($args["options"]['notes'])) {
            $noteIs='<br><small>'.$args["options"]['notes'].'</small>';
        }
        echo wp_kses('<select '. esc_html($attr) .' name="proscores_settings_options['.esc_attr($mkey).']">'.$wd.'</select>'. $noteIs,array(
                'br' => array(),
                'small' => array(),
                'select'      => array(
                    'name'  => array(),'onchange'  => array(),'var'  => array(),
                ),
                'option'     => array( 'value' => array(),  'selected' => array(), ),
            )
        );
    }
    public function proscores_text_field(array $args) {
        $mkey=$args["key"];$attr=$args["attr"] ?? '';
        $val = ( isset( $this->options[$mkey] ) ) ? $this->options[$mkey] : '';
        echo wp_kses(($args["bf"] ?? '').'<input type='.esc_attr($args["type"]).' name="proscores_settings_options['.esc_attr($mkey).']" value="'.esc_attr($val).'" '.esc_html($attr).' />' . ($args["af"] ?? ''),
            array(
                 'b'     => array( 'id' => array()),
                'input'      => array(
                    'type'  => array(),'name'  => array(),'value'  => array(),'disabled'  => array(),'id'  => array(),
                ),
                'div'     => array( 'id' => array())
            )
        );
    }
    public function proscores_lang_settings_fieldv2() {
        $proscores_lang_is_ok = array(
            'en' => 'English', 'es' => 'Español', 'de' => 'Deutsch', 'it' => 'Italiano', 'pt' => 'Português' , 'pt-br' => 'Português/Brasil', 'ru' => 'Русский' ,'tr' => 'Türkçe' , 'pl' => 'Polski', 'ro' => 'Română',  'el' => 'Ελληνικά', 'sv' => 'Svenska' , 'sr' => 'Srpski', 'sr-rs' => 'Српски', 'sw' => 'Swahili', 'hi' => 'हिंदी');
        $proscores_locale = substr(get_locale(),0,2);
        $val = isset($this->options['proscores_lang']) ? $this->options['proscores_lang'] : '';
        $selected_one = array();
        if ('' == $val) {
            if ('' != $proscores_lang_is_ok[$proscores_locale]) {
                $val = 'en';
            } else {
                $val = $proscores_locale;
            }
        }
        $selected_one[$val] ='selected="selected"';
        $wd='';
        foreach ($proscores_lang_is_ok as $pro_key => $pro_val) {
                $wd .='<option value="'. esc_html($pro_key) .'" '. esc_html($selected_one[$pro_key] ?? '') .'>'. esc_html($pro_val) .'</option>';
        }

        echo wp_kses('<select name="proscores_settings_options[proscores_lang]">'.$wd.'</select>',  
            array(
                'select'      => array(
                    'name'  => array(),
                ),
                'option'     => array( 'value' => array(),  'selected' => array(), ),
            )

        );
    }
    public function proscores_plugin_code_standings($atts) {
        $atts = shortcode_atts(
            array(
                'path' => '/a/50/league/premier-league/standings',
                'compact' => '0',
                'logos' => '1',
                'legends' => '1',
            ), $atts, 'prostandings'
        );
        $options = get_option('proscores_settings_options_standings');
        $langIs = $this->options['proscores_lang'] ?? "en";
        $proVars = array();
        foreach ($this->defs_standings as $pro_key => $pro_val) {
            if (isset($options[$pro_key]) && $options[$pro_key] !== '' && $options[$pro_key] !== $pro_val) {
                  $proVars[$pro_key]=esc_js($options[$pro_key]);
            }
        }
        $inline_script = 'window.proStandingsStyle = ' . wp_json_encode( $proVars ) . ';';
        //wp_enqueue_script('prowidgetdeep', $this->widgetURI . '/njs/' . $langIs . '/prowidgetdeep.js', array(), $this->jsver, true);
        wp_add_inline_script('prowidgetdeep', $inline_script,'before');
        
        $ancTitle='Standings';
        $path=$atts['path'] ?? '/a/50/league/premier-league/standings';
        if ($path != '') {
            $pathParts = explode('/', trim($path, '/'));
            if (count($pathParts) >= 4) {
                $ancTitle = array_pop($pathParts);
                $ancTitle = str_replace('-', ' ', $ancTitle);
                $ancCont = $ancTitle;
            } else {
                $ancCont = 'standings';
            }
        } else {

        }
        $anc=$this->authorURI . $path;
        $proscores_your_code = '<a href="' . $anc . '" logos="'. esc_attr($atts["logos"] ?? "1") . '"  compact="'. esc_attr($atts["compact"] ?? "0") . '"  legends="'. esc_attr($atts["legends"] ?? "1") . '">' . $ancCont . '</a>';
        $allowed_html = [
            'a' => [
                'href' => [],
                'logos' => [],
                'compact' => [],
                'legends' => [],
            ],
        ];

        return wp_kses($proscores_your_code, $allowed_html);

    }
    public function proscores_plugin_code($atts) {
        $atts = shortcode_atts(
            array(
                'list' => 'today',
                'path' => '/'
            ), $atts, 'proscores'
        );
        $options = get_option('proscores_settings_options');
        $langIs = $options['proscores_lang'] ?? "en";
        $proCs = array(
            'list' => esc_js($atts['list']),
            'lmt' => array(
                'text' => isset($options['lmt_text']) ? ($options['lmt_text']=='true' ? true : false) : true,
                'ani' => isset($options['lmt_ani']) ? ($options['lmt_ani']=='true' ? true : false) : false,
            )
        );

        $proVars = array();
        foreach ($this->defs as $pro_key => $pro_val) {
            if (isset($options[$pro_key]) && $options[$pro_key] !== '' && $options[$pro_key] !== $pro_val) {
                  $proVars[$pro_key]=esc_js($options[$pro_key]);
            }
        }
        if(isset($options['proscores_credit']) && $options['proscores_credit']=='on') {
            $inline_script = 'window.proCs = ' . wp_json_encode( $proCs ) . ';';
            $inline_script .= 'window.proVars = ' . wp_json_encode( $proVars ) . ';';
            
            //wp_enqueue_script('prolivewidget', $this->widgetURI . '/njs/' . $langIs . '/prolivewidget.js', array(), $this->jsver, true);
            wp_add_inline_script('prolivewidget', $inline_script,'before');

            add_filter('script_loader_tag', function($tag, $handle) {
                if ('prolivewidget' !== $handle) {
                    return $tag;
                }
                return str_replace(' src', ' async="async" crossorigin="anonymous" src', $tag);
            }, 10, 2);
            $anc=$this->authorURI . $atts['path'];
            $ancTitle='livescore';
            $ancCont=$this->authorIS;
            $attrs='';
            if($atts['list']=='yesterday') {
                $anc=$this->authorURI . '/yesterday/';$ancTitle='livescore yesterday';
            }
            if($atts['list']=='tomorrow') {
                $anc=$this->authorURI . '/tomorrow/';$ancTitle='tomorrow soccer fixtures';
            }
            if ($atts['path'] != '') {
                $pathParts = explode('/', trim($atts['path'], '/'));
                if (count($pathParts) >= 4) {
                    $ancTitle = array_pop($pathParts);
                    $ancTitle = str_replace('-', ' ', $ancTitle);
                    $ancCont = $ancTitle;
                } else {
                    $ancTitle = 'livescore';
                }
            }
            $proscores_your_code = '<a href="' . $anc . '" title="'. $ancTitle .'">' . $ancCont . '</a>';
        } else {
            $proscores_your_code='
                <iframe
                    src="https://livescores.pro/webmasters.asp'
                        .'?hl='. esc_attr($langIs) . '&host='. $_SERVER['HTTP_HOST'] .'&cs='. base64_encode(wp_json_encode( $proCs )) .'&path='. esc_attr($atts['path'] ?? '')  .'" marginheight="0"
                    marginwidth="0"
                    scrolling="auto"
                    height="8000"
                    width="100"
                    frameborder="0"
                    id="proscoresframe"
                    style="width:100%; height:5000px"
                ></iframe>
            ';
        }

        return wp_kses($proscores_your_code,
            array(
                'a' => array(
                    'href' => array(),
                    'title' => array(),
                ),
                'iframe' => array(
                    'src' => array(),
                    'marginwidth' => array(),
                    'scrolling' => array(),
                    'height' => array(),
                    //'width' => array(),
                    'frameborder' => array(),
                    'id' => array(),
                    //'style' => array(),
                )
            )
        );
    }

}
function pro_load_plugin_textdomain() {
    load_plugin_textdomain('proscores-live-scores', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('init', 'pro_load_plugin_textdomain');

ProScores_Live_Scores::get_instance();
?>