<?php
/*
Plugin Name: Senzoo Donation Notification Widget
Plugin URI: http://wordpress.org/extend/plugins/senzoo-donation-notification-widget/
Description: 
Senzoo Widget makes it simple to add donation form with notification widget which has PayPal, Amazon FPS and Google Checkout on your WordPress blog
Author: Nobu Funaki at Senzoo.net
Version: 1.0.0
Requires at least: 2.7
Author URI: http://senzoo.net/
License: GPLv2
*/
/*  Copyright 2010  Senzoo Donation Notification Widget (email : nobu at senzoo dot net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
if (!class_exists('Senzoo_Widget')) {
    define('SENZOO_WIDGET_PLUGIN_NAME', basename(__FILE__));
    
    $senzoo_widget_options = get_option(SENZOO_WIDGET_PLUGIN_NAME);

    class Senzoo_Widget
    {
        function Senzoo_Widget()
        {
            add_action('admin_menu', array(&$this, 'add_senzoo_widget_options_page'));
            add_action('admin_notices', array(&$this, 'add_senzoo_widget_admin_notice'));
            add_filter('plugin_action_links', array(&$this, 'add_senzoo_widget_plugin_actions'), 10, 2);
        }

        function add_senzoo_widget_admin_notice()
        {
            global $senzoo_widget_options;
            if (substr($_SERVER['PHP_SELF'], -11) === 'plugins.php' && !isset($senzoo_widget_options['code']) && function_exists('admin_url')) {
                echo '<div class="error"><p><strong>',
                    sprintf('Senzoo Widget is disabled. Please go to the <a href="%s">plugin admin page</a> to enable it.', admin_url('options-general.php?page=senzoo_widget.php')),
                    '</strong></p></div>';
            }
        }
        function add_senzoo_widget_plugin_actions($links, $file)
        {
            if ($file === 'senzoo-widget/senzoo_widget.php' && function_exists('admin_url')) {
                $settings_link = '<a href="' . admin_url( 'options-general.php?page=senzoo_widget.php' ) . '">' . __('Settings') . '</a>';
                array_unshift($links, $settings_link);
            }
            return $links;
        }
        function add_senzoo_widget_options_page()
        {
	        if (function_exists('add_options_page')) {
		        add_options_page('Senzoo Donation Notification Widget', 'Senzoo Widget', 'manage_options', SENZOO_WIDGET_PLUGIN_NAME, array(&$this, 'config_page'));
            }
        }
        function config_page()
        {
            global $senzoo_widget_options;

            if (isset($_POST['code']) && !empty($_POST['code'])) {
                $senzoo_widget_options['code'] = stripslashes($_POST['code']);
                update_option(SENZOO_WIDGET_PLUGIN_NAME, array('code' => $senzoo_widget_options['code']));
                echo '<div id="message" class="updated fade"><p>Your settings have been saved.</p></div>';
            }
            ?>
            <div class="wrap">
                <h2>Set Up Your Senzoo Widget</h2>
                <p>Senzoo Widget makes it simple to add donation form with notification widget which has PayPal, Amazon FPS and Google Checkout on your WordPress blog.</p>
                <form action="" method="post">
                <ol>
                    <li>To get started, <a href="http://manage.senzoo.net/signup">create your Senzoo account</a>. It's FREE.</li>
                    <li>Once you have created your account, go to <a href="http://manage.senzoo.net/signin">sign in</a> and copy code from &quot;Embed Code&quot;.</li>
                    <li>Paste the code:<br /><textarea name="code" cols="70" rows="11"><?php echo isset($senzoo_widget_options['code']) ? htmlspecialchars($senzoo_widget_options['code'], ENT_QUOTES, 'UTF-8') : ''; ?></textarea></li>
                </ol>
                <p><input type="submit" value="Save" /></p>
                </form>
                <h3>After Saved</h3>
                <p>You will see Senzoo Widget on your blog&#039;s any pages as often as you set its frequency on <a href="http://manage.senzoo.net/dashboard">dashboard</a>. For debugging purpose, you may want to set the frequency like this:<pre>$.senzoo({frequency:0});</pre></p>
                <h3>Contact Us</h3>
                <p>Any questions? We are welcome to hear your feedback. Shoot us an email to <a href="mailto:helpmeout@senzoo.net">helpmeout@senzoo.net</a> or follow <a href="http://twitter.com/SenzooNuts">@SenzooNuts</a> on twitter.</p>
            </div>
            <?php
        }
        function embed_code()
        {
            //  doesn't work in wp_footer function
            wp_enqueue_script('senzoo', 'http://static.senzoo.net/jquery.senzoo.min.js', array('jquery'), false, true);

            add_action('wp_footer', array(&$this, 'show_code'));	
        }
        function show_code()
        {
            global $senzoo_widget_options;
            if (isset($senzoo_widget_options['code'])) {
                //echo $senzoo_widget_options['code'];
                //  remove some lines

                $senzoo_widget_options['code'] = str_replace('<script src="http://static.senzoo.net/jquery.senzoo.min.js"></script>',
                                                            '', $senzoo_widget_options['code']);
                $senzoo_widget_options['code'] = preg_replace('@<script src="http://www.google.com/jsapi"></script>.*?<script>.*?google\.load\("jquery", "1\.3\.2"\);.*?</script>@s',
                                                            '', $senzoo_widget_options['code']);
                $senzoo_widget_options['code'] = str_replace('$', 'jQuery', $senzoo_widget_options['code']);
                echo $senzoo_widget_options['code'];
            }
        }
    }
    $senzoo = new Senzoo_Widget;
    $senzoo->embed_code();
}
