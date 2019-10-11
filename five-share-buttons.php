<?php
/*
Plugin Name: Five Share Buttons
Plugin URI: http://websmi.by/five/
Description: Плагин добавляет пять кнопок для распространения вашего контента в социальных сетях: vk.com, twitter.com, facebook.com, my.mail.ru (отключено), odnoklassniki.ru
Author: Nikolay Saskovets
Version: 0.0.4
Author URI: http://shurph.mp/
License: GPL v2
Derivate:  Плагин создан на основе плагина Vkontakte Share Button от Eugene Padlov (email: fox.sawyer@gmail.com ; site:http://www.jackyfox.com/)
*/

/*  Copyright 2010  Eugene Padlov  (email : fox.sawyer@gmail.com)
 *  Copyright 2011  Nikolay Saskovets (email : shurph@gmail.com)

    This plugin is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This plugin is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this plugin; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class FiveShareButton
{
	public $plugin_url;
	public $plugin_domain = 'five-share-button';
	function __construct()
	{
		$this->plugin_url = trailingslashit(WP_PLUGIN_URL.'/'.dirname(plugin_basename(__FILE__)));
		// Определяем версию (нужно будет удалить: те, у кого WP < 3 -- ССЗБ)
		global $wp_version;
		if (version_compare($wp_version,"2.8","<"))
                {
                    $exit_msg = __('Five share buttons plugin requires Wordpress 2.8 or newer. <a href="http://codex.wordpress.org/Upgrading_WordPress">Please update!</a>', $this->plugin_domain);
                    exit ($exit_msg);
		}
		add_action('wp_print_scripts', array(&$this, 'add_head'));
		add_filter('the_content', array(&$this, 'place_button'));
		add_shortcode('five-share-button', array(&$this, 'the_buttons'));
	}
        /**
         * add_head()
         * Функция добавляет js-скрипты и CSS в блок head страницы
         */
        function add_head()
        {
            if (!is_admin())
            {
                echo '<link rel="stylesheet" href="'.$this->plugin_url.'five-share-buttons.css" type="text/css" />';
                echo $this->odnoklassnikiCSSURL;
                wp_enqueue_script( 'five_vk_share_button_api_script', $this->vkAPIURL );
                wp_enqueue_script( 'five_od_share_button_api_script', $this->odnoklassnikiAPIURL );
            }
	}
        /**
         * the_buttons()
         * Функция возвращает html-код кнопок
         *
         * @return string
         */
	function the_buttons() {
            $button_code =
                '<table><tbody><tr>'
                . '<td>' . $this->vkButton . '</td>' 
                . '<td>' . $this->twitterButton . '</td>' 
                . '<td>' . $this->odnoklassnikiButtom . '</td>' 
                . '<td>' . $this->facebookButton . '</td>' 
                . '</tr></tbody></table>';
		return $button_code;
	}
	/**
         * the_buttons_flex()
         * Функция возвращает html-код кнопок
	 * return buttons in a flex layout
         *
         * @return string
         */
	function the_buttons_flex() {
            $button_code =
                '<div class="five_buttons_flex">'
                . '<div class="flex_button">' . $this->vkButton . '</div>'
                . '<div class="flex_button">' . $this->twitterButton . '</div>'
                . '<div class="flex_button">' . $this->odnoklassnikiButtom . '</div>'
                . '<div class="flex_button">' . $this->facebookButton . '</div>'
                . '</div>';
		return $button_code;
	}
        /**
         * place_button()
         * Функция размещает код кнопок в посте
         * 
         * @param string $content содержимое поста
         * @return string
         */
        function place_button($content) 
        {
            $buttons = '<div class="five-buttons">' . $this->the_buttons() . '</div>';
            if ( is_single() || is_page() ) 
            {
                // Вставить кнопки после поста
                return $content . $buttons;
            }
            else
            {
                return $content;
            }
        }
        /*
         * Переменные с содержимым кнопок
         * @todo сделать в виде массива или вынести в конфиг WP
         */
        /*
         *
         * TWITTER . COM
         *
         */
        protected $twitterButton = '
            <a href="https://twitter.com/share" class="twitter-share-button" data-lang="ru" rel="nofollow">Твитнуть</a>
            <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
';
        /*
         *
         * ODNOKLASSNIKI . RU
         *
         */
        protected $odnoklassnikiCSSURL = '<link href="http://stg.odnoklassniki.ru/share/odkl_share.css" rel="stylesheet">';
//          <script>ODKL.init(); </script>';
        protected $odnoklassnikiAPIURL = 'http://stg.odnoklassniki.ru/share/odkl_share.js';
        protected $odnoklassnikiButtom = '<script>ODKL.init(); </script><a class="odkl-klass-stat" href="" onclick="ODKL.Share(this);return false;" ><span>0</span></a>';
        /*
         *
         * MY . MAIL . RU
         *
         */
        protected $myMailButton = '';
        /*
         *
         * VK . COM
         *
         */
        protected $vkAPIURL = 'http://vk.com/js/api/share.js?11';
        protected $vkButton = '<script type="text/javascript">document.write(VK.Share.button(false,{type: "button", text: "Интересно!"}));</script>';
        /*
         *
         * FACEBOOK . COM
         *
         */
        protected $facebookButton = '<div id="fb-root"></div><script>(function(d, s, id) { var js, fjs = d.getElementsByTagName(s)[0]; if (d.getElementById(id)) return; js = d.createElement(s); js.id = id;  js.src = "//connect.facebook.net/ru_RU/all.js#xfbml=1"; fjs.parentNode.insertBefore(js, fjs); } (document, "script", "facebook-jssdk"));</script>          <div class="fb-like" data-send="false" data-layout="button_count" data-show-faces="true" data-action="recommend"></div>';

} // class FiveShareButton 

$FiveShareButton = new FiveShareButton();
