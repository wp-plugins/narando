<?php


include_once('Narando_LifeCycle.php');

class Narando_Plugin extends Narando_LifeCycle {

    /**
     * See: http://plugin.michael-simpson.com/?page_id=31
     * @return array of option meta data.
     */
    public function getOptionMetaData() {
        //  http://plugin.michael-simpson.com/?page_id=31
        return array(
            //'_version' => array('Installed Version'), // Leave this one commented-out. Uncomment to test upgrades.
			
			'NRAutoplay' => array(__('Autoplay', 'narando-plugin'), 'true', 'false'),
			'NRPosition' => array(__('Position des Players', 'narando-plugin'), 'Before Post', 'After Post'),
			'NRPlayerMobile' => array(__(' Player für Mobile-Endgeräte anzeigen lassen (reagiert nur bei Mobilen-Endgeräten)', 'narando-plugin'), 'true', 'false'),
			'NRColorControls' => array(__(' Farbe für die Controls (#e74c3c)', 'narando-plugin')),
			'NRColorBackground' => array(__(' Farbe für die Hintergrund (#ffffff)', 'narando-plugin')),
			'NRColorText' => array(__(' Farbe für die Text (#666666)', 'narando-plugin')),
			'NRColorFrame' => array(__(' Farbe für die Text (#cbcbcb)', 'narando-plugin'))
        );
    }

//    protected function getOptionValueI18nString($optionValue) {
//        $i18nValue = parent::getOptionValueI18nString($optionValue);
//        return $i18nValue;
//    }

    protected function initOptions() {
        $options = $this->getOptionMetaData();
        if (!empty($options)) {
            foreach ($options as $key => $arr) {
                if (is_array($arr) && count($arr > 1)) {
                    $this->addOption($key, $arr[1]);
                }
            }
        }
    }

    public function getPluginDisplayName() {
        return 'narando';
    }

    protected function getMainPluginFileName() {
        return 'narando.php';
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Called by install() to create any database tables if needed.
     * Best Practice:
     * (1) Prefix all table names with $wpdb->prefix
     * (2) make table names lower case only
     * @return void
     */
    protected function installDatabaseTables() {
        //        global $wpdb;
        //        $tableName = $this->prefixTableName('mytable');
        //        $wpdb->query("CREATE TABLE IF NOT EXISTS `$tableName` (
        //            `id` INTEGER NOT NULL");
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Drop plugin-created tables on uninstall.
     * @return void
     */
    protected function unInstallDatabaseTables() {
        //        global $wpdb;
        //        $tableName = $this->prefixTableName('mytable');
        //        $wpdb->query("DROP TABLE IF EXISTS `$tableName`");
    }


    /**
     * Perform actions when upgrading from version X to version Y
     * See: http://plugin.michael-simpson.com/?page_id=35
     * @return void
     */
    public function upgrade() {
    }

    public function addActionsAndFilters() {

		

        // Add options administration page
        // http://plugin.michael-simpson.com/?page_id=47
        add_action('admin_menu', array(&$this, 'addSettingsSubMenuPage'));
		
		//add Narando Script
		add_action('wp_footer', array(&$this, 'registerNarandoScript'));
		add_filter( 'the_content', array(&$this, 'narandoPlayerContainer'));

		
    }

	public function registerNarandoScript() {
		wp_enqueue_script( 'narando-player', esc_url_raw( 'http://narando.com/assets/narando.player.js' ));		
	}
	
	public function narandoPlayerContainer($content='' ) {
		global $wp_query;
		
		if ( is_single() ) {
			$permalink = get_permalink($wp_query->post->ID); //get post link
			
			$autoplay = "";
			if ("true" == $this->getOption("NRAutoplay")) {
				$autoplay = "autoplay";
			}
				
			$data_fg_color = $this->getOption("NRColorControls");
			$data_bg_color = $this->getOption("NRColorBackground");
			$data_txt_color = $this->getOption("NRColorText");
			$data_fr_color = $this->getOption("NRColorFrame");
			
			if ("Before Post" == $this->getOption("NRPosition")) {
				$content = sprintf('<div class="narando-player" data-canonical="%s" data-floating="mobile" data-fg-color="%s" data-bg-color="%s" data-txt-color="%s" data-fr-color="%s" %s></div>%s', $permalink, $data_fg_color, $data_bg_color, $data_txt_color, $data_fr_color ,$autoplay, $content);
			} else {
				$content = sprintf('%s<div class="narando-player" data-canonical="%s" data-floating="mobile" data-fg-color="%s" data-bg-color="%s" data-txt-color="%s" data-fr-color="%s" %s></div>', $content, $permalink, $data_fg_color, $data_bg_color, $data_txt_color, $data_fr_color, $autoplay);
			}
		}

		return $content;
	}

}
