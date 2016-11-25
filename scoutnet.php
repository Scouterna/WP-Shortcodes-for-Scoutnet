<?php
/*
Plugin Name: Scoutnet API
Plugin URI: https://www.scouterna.se
Description: Ett plugin till Scouterna
Version: 1.0
Author: Emil Öhman, Joel Martinsson, Sven Jungmar, Magnus Hasselquist
Author URI: http://etjanster.scout.se
*/

function get_scoutnet_api_url()	{
	
	return $scoutnet_api_url = "s1.test.custard.no";
		
}

/*
Class for optionpage for Scoutnet API
*/
class ScoutnetApiSettingsPage	{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()	{
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()	{
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin', 
            'Scoutnet API', 
            'manage_options', 
            'scoutnet-api-admin',
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()	{
        // Set class property
        $this->options = get_option( 'scoutnet_option_name' );
		
        ?>
        <div class="wrap">
            <h1>Scoutnet API</h1>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'scoutnet_option_group' );
                do_settings_sections( 'scoutnet-api-admin' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()	{        
        register_setting(
            'scoutnet_option_group', // Option group
            'scoutnet_option_name', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'Koppla Wordpress till Scoutnet', // Title
            array( $this, 'print_section_info' ), // Callback
            'scoutnet-api-admin' // Page
        );  

        add_settings_field(
            'kar_id', // Kar ID
            'Kår ID', // Title 
            array( $this, 'kar_id_callback' ), // Callback
            'scoutnet-api-admin', // Page
            'setting_section_id' // Section           
        );      

        add_settings_field(
            'api_nyckel_kar', 
            'API-nyckel <br /><i>View group information</i>', 
            array( $this, 'api_nyckel_kar_callback' ), 
            'scoutnet-api-admin', 
            'setting_section_id'
        );
		
		 add_settings_field(
            'api_nyckel_vantelista', 
            'API-nyckel <br /><i>Register a group member on a waitinglist</i>', 
            array( $this, 'api_nyckel_vantelista_callback' ), 
            'scoutnet-api-admin', 
            'setting_section_id'
        );
		
		add_settings_field(
            'api_nyckel_kar_full', 
            'API-nyckel <br /><i>Get a detailed list of all members </i>', 
            array( $this, 'api_nyckel_kar_full_callback' ), 
            'scoutnet-api-admin', 
            'setting_section_id'
        );

		add_settings_field(
            'api_nyckel_epostlista', 
            'API-nyckel <br /><i>Get a list of members, based on mailing lists</i>', 
            array( $this, 'api_nyckel_epostlista_callback' ), 
            'scoutnet-api-admin', 
            'setting_section_id'
        );
		
		/*
		Section register member
		*/
		
		add_settings_section(
            'setting_section_register_member_id', // ID
            'Inställning för intresseanmälningsformulär', // Title
            array( $this, 'print_section_register_member_info' ), // Callback
            'scoutnet-api-admin' // Page
        );
		
		add_settings_field(
            'register_member_success_message', 
            'Bekräftelse meddelande <br /><i>Text som används vid lyckad användning av formuläret</i>', 
            array( $this, 'register_member_success_message_callback' ), 
            'scoutnet-api-admin', 
            'setting_section_register_member_id'
        );
		
		add_settings_field(
            'register_member_from_email', 
            'Avsändaradress <br /><i>E-postadress som anges som avsändare</i>', 
            array( $this, 'register_member_from_email_callback' ), 
            'scoutnet-api-admin', 
            'setting_section_register_member_id'
        );
		
		add_settings_field(
            'register_member_from_name', 
            'Avsändare <br /><i>Namn som anges som avsändare, tex kårens namn</i>', 
            array( $this, 'register_member_from_name_callback' ), 
            'scoutnet-api-admin', 
            'setting_section_register_member_id'
        );
		
		add_settings_field(
            'register_member_medlemsreg_email', 
            'E-postadress till medlemsregistrerare <br /><i>E-postadress som kopia på datan skickas till</i>', 
            array( $this, 'register_member_medlemsreg_email_callback' ), 
            'scoutnet-api-admin', 
            'setting_section_register_member_id'
        );
		
		add_settings_field(
            'register_member_medlemsreg_avdelning', 
            'Egen fråga <br /><i>T.ex vilken avdelning som är av intresse</i>', 
            array( $this, 'register_member_medlemsreg_avdelning_callback' ), 
            'scoutnet-api-admin', 
            'setting_section_register_member_id'
        );
		
		add_settings_field(
            'register_member_medlemsreg_avdelning_standard', 
            'Exempelsvar <br /><i>T.ex avdelningen Bävrarna</i>', 
            array( $this, 'register_member_medlemsreg_avdelning_standard_callback' ), 
            'scoutnet-api-admin', 
            'setting_section_register_member_id'
        );
		
		
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )	{
        $new_input = array();
        if( isset( $input['kar_id'] ) )	{
            $new_input['kar_id'] = absint( $input['kar_id'] );
		}
		
        if( isset( $input['api_nyckel_kar'] ) )	{
            $new_input['api_nyckel_kar'] = sanitize_text_field( $input['api_nyckel_kar'] );
		}
		
		if( isset( $input['api_nyckel_vantelista'] ) )	{
            $new_input['api_nyckel_vantelista'] = sanitize_text_field( $input['api_nyckel_vantelista'] );
		}
		
		if( isset( $input['api_nyckel_kar_full'] ) )	{
            $new_input['api_nyckel_kar_full'] = sanitize_text_field( $input['api_nyckel_kar_full'] );
		}
		
		if( isset( $input['api_nyckel_epostlista'] ) )	{
            $new_input['api_nyckel_epostlista'] = sanitize_text_field( $input['api_nyckel_epostlista'] );
		}
		
		
		/*
		Register member section
		*/
		if( isset( $input['register_member_success_message'] ) )	{
            //$new_input['register_member_success_message'] = sanitize_text_field( $input['register_member_success_message'] );
			$new_input['register_member_success_message'] = $input['register_member_success_message'];
		}
		
		if( isset( $input['register_member_from_email'] ) )	{
            $new_input['register_member_from_email'] = sanitize_text_field( $input['register_member_from_email'] );
		}
		
		if( isset( $input['register_member_from_name'] ) )	{
            $new_input['register_member_from_name'] = sanitize_text_field( $input['register_member_from_name'] );
		}
		
		if( isset( $input['register_member_medlemsreg_email'] ) )	{
            $new_input['register_member_medlemsreg_email'] = sanitize_text_field( $input['register_member_medlemsreg_email'] );
		}		
		
		if( isset( $input['register_member_medlemsreg_avdelning'] ) )	{
            $new_input['register_member_medlemsreg_avdelning'] = $input['register_member_medlemsreg_avdelning'];
		}
		
		if( isset( $input['register_member_medlemsreg_avdelning_standard'] ) )	{
            $new_input['register_member_medlemsreg_avdelning_standard'] = $input['register_member_medlemsreg_avdelning_standard'];
		}
		
		
		
        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()	{
        print 'Här kan du koppla Wordpress till Scoutnet.
		<br/>Du behöver ha rätt behörighet i Scoutnet för att se sidan där uppgifterna står. Alternativt få uppgifterna från en som har.
		<br/>
		<br/>Du hittar uppgifterna i Scoutnet under "Din kår" > Webbkoppling.
		<br/>Är inte webbkopplingen påslagen i Scoutnet måste du göra detta genom att trycka på knappen längst upp till höger.
		<br/>Kår ID hittar du genom att expandera ett av fälten.
		<br/>
		<br/>Du behöver skriva in flera API-nycklar och inställningar i rutorna nedan för att allt ska fungera fullt ut.
		<br/>Se till att du skriver rätt nyckel i rätt ruta! Se även till att det inte är några blanktecken!
		<br/>
		<br/>Brädgårdstecknen blir gröna när anslutningen fungerar.';
    }
	
	 /** 
     * Print the Section text
     */
    public function print_section_register_member_info()	{
        print 'Här kan du göra inställning för formuläret för intresseanmälningar
		<br/>Om du vill formatera texten använder du html
		<br>För att enkelt ändra från vanlig text/listor till html kan du t.ex använda http://www.unit-conversion.info/texttools/text-to-html		
		';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function kar_id_callback()	{
        printf(
            '<input type="text" id="kar_id" name="scoutnet_option_name[kar_id]" value="%s" />',
            isset( $this->options['kar_id'] ) ? esc_attr( $this->options['kar_id']) : ''
        );
		
		$karnamn = scoutnet_get_kar_namn();
		if (!empty($karnamn))	{
			echo '<br/>Du har anslutit Wordpress till ' . $karnamn;
		}
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function api_nyckel_kar_callback()	{
        printf(
            '<input type="text" size="50" id="api_nyckel_kar" name="scoutnet_option_name[api_nyckel_kar]" value="%s" />',
            isset( $this->options['api_nyckel_kar'] ) ? esc_attr( $this->options['api_nyckel_kar']) : ''
        );
		
		$color = "#00FF00";
		$sgroup = scoutnet_get_group_info();
		if (empty($sgroup))	{
			$color = "#FF0000";
		}
				
		echo "<span style=\"color: $color\">#</span>";
    }
	
	 /** 
     * Get the settings option array and print one of its values
     */
    public function api_nyckel_vantelista_callback()	{
        printf(
            '<input type="text" size="50" id="api_nyckel_vantelista" name="scoutnet_option_name[api_nyckel_vantelista]" value="%s" />',
            isset( $this->options['api_nyckel_vantelista'] ) ? esc_attr( $this->options['api_nyckel_vantelista']) : ''
        );
		
		$color = "#00FF00";
		$register_member = scoutnet_register_member();
		if (empty($register_member))	{
			$color = "#FF0000";
		}
				
		echo "<span style=\"color: $color\">#</span>";
    }
	
	/** 
     * Get the settings option array and print one of its values
     */
    public function api_nyckel_kar_full_callback()	{
        printf(
            '<input type="text" size="50" id="api_nyckel_kar_full" name="scoutnet_option_name[api_nyckel_kar_full]" value="%s" />',
            isset( $this->options['api_nyckel_kar_full'] ) ? esc_attr( $this->options['api_nyckel_kar_full']) : ''
        );
		
		$color = "#00FF00";
		$memberlist = scoutnet_get_member_list();
		if (empty($memberlist))	{
			$color = "#FF0000";
		}
				
		echo "<span style=\"color: $color\">#</span>";
		
    }
	
	/** 
     * Get the settings option array and print one of its values
     */
    public function api_nyckel_epostlista_callback()	{
        printf(
            '<input type="text" size="50" id="api_nyckel_epostlista" name="scoutnet_option_name[api_nyckel_epostlista]" value="%s" />',
            isset( $this->options['api_nyckel_epostlista'] ) ? esc_attr( $this->options['api_nyckel_epostlista']) : ''
        );
		
		$color = "#00FF00";
		$customlist = scoutnet_get_custom_list();
		if (empty($customlist))	{
			$color = "#FF0000";
		}
				
		echo "<span style=\"color: $color\">#</span>";		
		
    }
	
	/** 
     * Get the settings option array and print one of its values
     */
    public function register_member_success_message_callback()	{
       /* printf(
            '<input type="text" size="50" id="register_member_success_message" name="scoutnet_option_name[register_member_success_message]" value="%s" />',
            isset( $this->options['register_member_success_message'] ) ? esc_attr( $this->options['register_member_success_message']) : ''
        );
		*/
		printf(
            '<textarea rows="5" cols="50" id="register_member_success_message" name="scoutnet_option_name[register_member_success_message]">%s</textarea>',
            isset( $this->options['register_member_success_message'] ) ? esc_attr( $this->options['register_member_success_message']) : '<p><b>Tack för din anmälan av en ny eventuell scout.</b> Om du har frågor om din anmälan, kontakta XXXX på YYY@ZZZ.scout.se eller ring 070-12 34 45.</p>

<p><br/>/ZZZ Scoutkår</p>'
        );
		
		
		$color = "#00FF00";
		$message = scoutnet_get_option_register_member_success_message();
		if (empty($message))	{
			$color = "#FF0000";
		}
				
		echo "<span style=\"color: $color\">#</span>";		
		
    }
	
	/** 
     * Get the settings option array and print one of its values
     */
    public function register_member_from_email_callback()	{
        printf(
            '<input type="text" size="50" id="register_member_from_email" name="scoutnet_option_name[register_member_from_email]" value="%s" />',
            isset( $this->options['register_member_from_email'] ) ? esc_attr( $this->options['register_member_from_email']) : ''
        );
		
		$color = "#00FF00";
		$from_email = scoutnet_get_option_register_member_from_email();
		if (empty($from_email))	{
			$color = "#FF0000";
		}
				
		echo "<span style=\"color: $color\">#</span>";		
		
    }
	
	/** 
     * Get the settings option array and print one of its values
     */
    public function register_member_from_name_callback()	{
        printf(
            '<input type="text" size="50" id="register_member_from_name" name="scoutnet_option_name[register_member_from_name]" value="%s" />',
            isset( $this->options['register_member_from_name'] ) ? esc_attr( $this->options['register_member_from_name']) : ''
        );
		
		$color = "#00FF00";
		$from_name = scoutnet_get_option_register_member_from_name();
		if (empty($from_name))	{
			$color = "#FF0000";
		}
				
		echo "<span style=\"color: $color\">#</span>";		
		
    }
	
	/** 
     * Get the settings option array and print one of its values
     */
    public function register_member_medlemsreg_email_callback()	{
        printf(
            '<input type="text" size="50" id="register_member_medlemsreg_email" name="scoutnet_option_name[register_member_medlemsreg_email]" value="%s" />',
            isset( $this->options['register_member_medlemsreg_email'] ) ? esc_attr( $this->options['register_member_medlemsreg_email']) : ''
        );
		
		$color = "#00FF00";
		$medlemsreg_email = scoutnet_get_option_register_member_medlemsreg_email();
		if (empty($medlemsreg_email))	{
			$color = "#FF0000";
		}
				
		echo "<span style=\"color: $color\">#</span>";		
		
    }
	
	/** 
     * Get the settings option array and print one of its values
     */
    public function register_member_medlemsreg_avdelning_callback()	{
        printf(
            '<textarea rows="10" cols="50" id="register_member_medlemsreg_avdelning" name="scoutnet_option_name[register_member_medlemsreg_avdelning]">%s</textarea>',
            isset( $this->options['register_member_medlemsreg_avdelning'] ) ? esc_attr( $this->options['register_member_medlemsreg_avdelning']) : 'Vilken/Vilka avdelning är av intresse?*    <ul><li>Ej börjat skolan</li><li>Skolår 1: söndag (Bävrarna)</li><li>Skolår 2-3: måndag (Fåglarna)</li><li>Skolår 2-3: tisdag (Vilddjuren)</li><li>Skolår 4-5: måndag (Asarna)</li><li>Skolår 4-5: tisdag (Skogsbrynet)</li><li>Skolår 6-8: onsdag (Stigfinnarna)</li><li>Skolår 9- gymnasiet åk3: söndag (Seniorerna)</li><li>Jag önskar börja som ledare</li></ul>'
        );
		
		$color = "#00FF00";
		$medlemsreg_avdelning = scoutnet_get_option_register_member_medlemsreg_avdelning();
		if (empty($medlemsreg_avdelning))	{
			$color = "#FF0000";
		}
				
		echo "<span style=\"color: $color\">#</span>";		
		
    }
	
	/** 
     * Get the settings option array and print one of its values
     */
    public function register_member_medlemsreg_avdelning_standard_callback()	{
        printf(
            '<input type="text" size="50" id="register_member_medlemsreg_avdelning_standard" name="scoutnet_option_name[register_member_medlemsreg_avdelning_standard]" value="%s" />',
            isset( $this->options['register_member_medlemsreg_avdelning_standard'] ) ? esc_attr( $this->options['register_member_medlemsreg_avdelning_standard']) : 'Bävrarna'
        );
		
		$color = "#00FF00";
		$medlemsreg_avdelning_standard = scoutnet_get_option_register_member_medlemsreg_avdelning_standard();
		if (empty($medlemsreg_avdelning_standard))	{
			$color = "#FF0000";
		}
				
		echo "<span style=\"color: $color\">#</span>";		
		
    }
	
	
	
}

//if( is_admin() )	{
    $scoutnet_api_settings_page = new ScoutnetApiSettingsPage();
//}
	


// Register Custom Scoutnet Post Type
function scoutnet_post_type_function()	{

	$labels = array(
		'name'                  => 'Scoutnetkopplingar',
		'singular_name'         => 'Scoutnetkoppling',
		'menu_name'             => 'Scoutnet synk',
		'name_admin_bar'        => 'Scoutnet synk',
		'archives'              => 'Kopplingsarkiv',
		'parent_item_colon'     => 'Överordnad koppling:',
		'all_items'             => 'Alla kopplingar',
		'add_new_item'          => 'Lägg till ny koppling',
		'add_new'               => 'Lägg till',
		'new_item'              => 'Ny koppling',
		'edit_item'             => 'Redigera koppling',
		'update_item'           => 'Uppdatera koppling',
		'view_item'             => 'Visa koppling',
		'search_items'          => 'Sök koppling',
		'not_found'             => 'Hittas ej',
		'not_found_in_trash'    => 'Hittas ej i papperskorgen',
		'featured_image'        => 'Utvald bild',
		'set_featured_image'    => 'Välj utvald bild',
		'remove_featured_image' => 'Radera utvald bild',
		'use_featured_image'    => 'Använd som utvald bild',
		'insert_into_item'      => 'Klistra in i denna koppling',
		'uploaded_to_this_item' => 'Uppladdad till denna koppling',
		'items_list'            => 'Lista av kopplingarna',
		'items_list_navigation' => 'Överblick av kopplingarna',
		'filter_items_list'     => 'Filtrera kopplingarna',
	);
	
	$args = array(
		'label'                 => 'Scoutnetkoppling',
		'description'           => 'Scoutnet API-kopplingar',
		'labels'                => $labels,
		'supports'              => array( 'title', ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'f106',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,		
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'rewrite'               => false,
		'capability_type'     => 'page',
	);
	register_post_type( 'scoutnet_post_type', $args );
}
add_action( 'init', 'scoutnet_post_type_function', 0 );
	
	/*
	Handling the shortcodes
	*/
	function scoutnet_shortcode_init()	{
		 
		function scoutnet_shortcode($atts)	{
		   
		   
		   
		   $atts = array_change_key_case((array)$atts, CASE_LOWER);
		   //Behövs enligt https://developer.wordpress.org/plugins/shortcodes/shortcodes-with-parameters/
		   //men görs också av shortcode_atts() enligt https://codex.wordpress.org/Shortcode_API
		   
		   //Override deafult values
		   $a = shortcode_atts(
			array(
				'alias' => '0',
				'lista' => false,
				'antal' => false,
				'fornamn' => true,
				'efternamn' => false,
				'epost' => false,
				'medlems_nr' => false,
				'antal_medlemmar' => false,
				'antal_vantelista' => false,
				'alder_scouter' => false,
				'inloggad' => false,
				'fodelsedag' => false,
				'form_vantelista' => false,
				'ledare' => false,
				'styrelsen' => false,
			), $atts, 'zkaout_shortcode' );
		  
			$list_id;
			
			//echo "ALIAS = " . $a['alias'];
			$the_return_string = "";
			
			
			if ($a['inloggad'] && !is_user_logged_in())	{
				
				return $the_return_string;
				
			}
			
			if ('0'!=$a['alias'])	{	//If personal code
				$alias = $a['alias'];
				$list_id = scoutnet_get_list_id($alias);
				
				//echo $list_id;
				//echo "Nu finns alias";
				
				$karid = scoutnet_get_option_kar_id();
				$apinyckel = scoutnet_get_option_api_nyckel_epostlista();
				$apiurl = get_scoutnet_api_url();

				$variable = file_get_contents("https://$karid:$apinyckel@$apiurl/api/group/customlists?list_id=$list_id");
				$decoded = json_decode($variable, true);
				$members = $decoded['data'];
				//echo "<pre>";
								
				$medlemmar = array();
				
				
				if (true==$a['antal'])	{					
					$strl = count($members);
					$the_return_string .= $strl;			
				}
				
				if (true==$a['lista'])	{				
					
					foreach ($members as $i => $medlem) {
						
						if ($a['fornamn'])	{
							
							$the_return_string .= $medlem['first_name']['value'] . " ";							
							
						}
						if ($a['efternamn'])	{
							
							$the_return_string .= $medlem['last_name']['value'] . " ";
						}
						
						if ($a['epost'])	{
							
							$the_return_string .= $medlem['email']['value'] . " ";
						}
						
						if ($a['medlems_nr'])	{
							$the_return_string .= $medlem['member_no']['value'] . " ";							
						}
						$the_return_string .= "<br>";						
					}					
					
				}
			}
			elseif  ($a['antal_medlemmar'])	{
				
				return scoutnet_antal_kar('antal_medlemmar');				
			}
			
			elseif  ($a['antal_vantelista'])	{
				
				return scoutnet_antal_kar('antal_vantelista');				
			}
			
			elseif ($a['form_vantelista'])	{
				
				return scoutnet_vantelista($a['ledare']);				
			}
			
			elseif  ($a['alder_scouter'])	{
				
				return scoutnet_alder_scouter();
				
			}
			
			elseif ($a['fodelsedag'])	{
				
				return scoutnet_fodelsedag();
			}
			
			elseif ($a['styrelsen'])	{
				
				return scoutnet_styrelsen();
			}
			
			else	{
				$the_return_string .= "Alias saknas eller så finns ej den inbyggda funktionen";
			}
		
			
			return $the_return_string;
		   
		}
		//Register the shortcode function	
		add_shortcode('scoutnet', 'scoutnet_shortcode');
	}
	add_action('init', 'scoutnet_shortcode_init');
	
	/*
	Make shortcode work within widget
	*/
	add_filter( 'widget_text', 'shortcode_unautop' );
	add_filter( 'widget_text', 'do_shortcode' );
	
	
	/*
	Function to get the value of the kår-id from the option page
	*/	
	function scoutnet_get_option_kar_id()	{
		
		$scoutnet_options = get_option('scoutnet_option_name');
		return $scoutnet_options['kar_id'];
		
	}
	
	/*
	Function to get the value of the api-nyckel Kår from the option page
	*/
	function scoutnet_get_option_api_nyckel_kar()	{
		
		$scoutnet_options = get_option('scoutnet_option_name');
		return $scoutnet_options['api_nyckel_kar'];
	}
	
	/*
	Function to get the value of the api-nyckel Väntelista from the option page
	*/
	function scoutnet_get_option_api_nyckel_vantelista()	{
		
		$scoutnet_options = get_option('scoutnet_option_name');
		return $scoutnet_options['api_nyckel_vantelista'];
	}
	
	/*
	Function to get the value of the api-nyckel Kår deltajerad from the option page
	*/
	function scoutnet_get_option_api_nyckel_kar_full()	{
		
		$scoutnet_options = get_option('scoutnet_option_name');
		return $scoutnet_options['api_nyckel_kar_full'];
	}
	
	/*
	Function to get the value of the api-nyckel E-postlista from the option page
	*/
	function scoutnet_get_option_api_nyckel_epostlista()	{
		
		$scoutnet_options = get_option('scoutnet_option_name');
		return $scoutnet_options['api_nyckel_epostlista'];
	}
	
	/*
	Function to get the value of the Register member success message from the option page
	*/
	function scoutnet_get_option_register_member_success_message()	{
		
		$scoutnet_options = get_option('scoutnet_option_name');
		return $scoutnet_options['register_member_success_message'];
	}
	
	/*
	Function to get the value of the Register member from email from the option page
	*/
	function scoutnet_get_option_register_member_from_email()	{
		
		$scoutnet_options = get_option('scoutnet_option_name');
		return $scoutnet_options['register_member_from_email'];
	}
	
	/*
	Function to get the value of the Register member from name from the option page
	*/
	function scoutnet_get_option_register_member_from_name()	{
		
		$scoutnet_options = get_option('scoutnet_option_name');
		return $scoutnet_options['register_member_from_name'];
	}
	
	/*
	Function to get the value of the Register member medlemsreg email from the option page
	*/
	function scoutnet_get_option_register_member_medlemsreg_email()	{
		
		$scoutnet_options = get_option('scoutnet_option_name');
		return $scoutnet_options['register_member_medlemsreg_email'];
	}
	
	/*
	Function to get the value of the Register member medlemsreg avdelning values from the option page
	*/
	function scoutnet_get_option_register_member_medlemsreg_avdelning()	{
		
		$scoutnet_options = get_option('scoutnet_option_name');
		return $scoutnet_options['register_member_medlemsreg_avdelning'];
	}
	
	/*
	Function to get the value of the Register member medlemsreg avdelning standard value values from the option page
	*/
	function scoutnet_get_option_register_member_medlemsreg_avdelning_standard()	{
		
		$scoutnet_options = get_option('scoutnet_option_name');
		return $scoutnet_options['register_member_medlemsreg_avdelning_standard'];
	}
	
	/*
	Returns the list_id for a given alias
	If error return 0
	*/
	function scoutnet_get_list_id($alias)	{
		
		global $wpdb;
		//$wpdb->show_errors();
		
		$query = "SELECT post_id FROM " . $wpdb->postmeta . " WHERE meta_key='scoutnet_alias' AND meta_value='" . $alias . "'";
		$post_id_match = $wpdb->get_var($query);
		if (is_null($post_id_match))	{
			$post_id_match = 0;
		}
		
		$list_id = get_post_meta($post_id_match, 'list_id', true);
		
		//$wpdb->print_error();
		return $list_id;
	}
	
	
	function scoutnet_fodelsedag()	{
		
		$karid = scoutnet_get_option_kar_id();
		$apinyckel = scoutnet_get_option_api_nyckel_kar_full();
		$apiurl = get_scoutnet_api_url();
		
		$variable = file_get_contents("https://$karid:$apinyckel@$apiurl/api/group/memberlist");
		$decoded = json_decode($variable, true);
		$members = $decoded['data'];
		
		$the_return_string = "";
		
		$dagens_datum = date("m-d");
		
		
		foreach ($members as $i => $medlem) {
			
			$fodelse_datum = $medlem['date_of_birth']['value'];
			
			$fodelse_datum_array = explode('-', $fodelse_datum);
				
			$fodelse_manad = $fodelse_datum_array[1];
			$fodelse_dag = $fodelse_datum_array[2];
				
			$fodelse_datum = $fodelse_manad . '-' . $fodelse_dag;			
			
			if ($dagens_datum==$fodelse_datum)	{	//En person fyller år i dag
			
				$the_return_string .= $medlem['first_name']['value'];
				$the_return_string .= ' (' . $medlem['unit']['value'] . ')';
				$the_return_string .= "<br>";
			}						
		}	
		
		return $the_return_string;		
	}
	//TODO ta bort kodupprepningen med funktionsanrop i stället.
	//TODO gör klart
	function scoutnet_styrelsen()	{
		
		$karid = scoutnet_get_option_kar_id();
		$apinyckel = scoutnet_get_option_api_nyckel_kar_full();
		$apiurl = get_scoutnet_api_url();
		
		$variable = file_get_contents("https://$karid:$apinyckel@$apiurl/api/group/memberlist");
		$decoded = json_decode($variable, true);
		$members = $decoded['data'];
		
		$the_return_string = "";
		
		$ko = '<h2>Kårordförande</h2>';
		$ledamoter = '<h2>Övriga ledamöter</h2>';
		
		foreach ($members as $i => $medlem) {
			
			$roller = $medlem['group_role']['raw_value'];
			
			$roller_array = explode(',', $roller);
			
			$medlems_roller = '';
			
			
			
			foreach ($roller_array as $k => $roll)	{
				
				//Kordupprepning om man enkelt vill ändra vilken uppdelning man vill ha
				switch ($roll)	{
					
					case 6:	//Kårordförande
						$ko .= $medlem['first_name']['value'] . ' ';
						$ko .= $medlem['last_name']['value'] . '</br>';
						$ko .= $medlem['email']['value'] .' ';
						$ko .= $medlem['contact_mobile_phone']['value'];
						break;
					
					case 7:	//Vice kårordförande
						
							$ledamoter .= '</br>' . $medlem['first_name']['value'] .' ';
							$ledamoter .= $medlem['last_name']['value'] .' ';
							$ledamoter .= $medlem['email']['value'] .' ';
							$ledamoter .= $medlem['contact_mobile_phone']['value'] . '</br>';						
					
						
						$ledamoter .= ', <em>Vice Kårordförande</em>';
						break;
					
					case 8:	//Kårkassör
						
							$ledamoter .= '</br>' . $medlem['first_name']['value'] .' ';
							$ledamoter .= $medlem['last_name']['value'] .' ';
							$ledamoter .= $medlem['email']['value'] .' ';
							$ledamoter .= $medlem['contact_mobile_phone']['value'] . '</br>';						
						
						
						$ledamoter .= ', <em>Kårkassör </em>';
						break;
					
					case 10: //Kårsekreterare
						
							$ledamoter .= '</br>' . $medlem['first_name']['value'] .' ';
							$ledamoter .= $medlem['last_name']['value'] .' ';
							$ledamoter .= $medlem['email']['value'] .' ';
							$ledamoter .= $medlem['contact_mobile_phone']['value'] . '</br>';						
						
						$ledamoter .= ', <em>Kårsekreterare</em>';
						break;
					
					case 15: //Styrelseledamot
						
							$ledamoter .= '</br>' . $medlem['first_name']['value'] .' ';
							$ledamoter .= $medlem['last_name']['value'] .' ';
							$ledamoter .= $medlem['email']['value'] .' ';
							$ledamoter .= $medlem['contact_mobile_phone']['value'] . '</br>';						
						
						$ledamoter .= ', <em>Styrelseledamot</em>';
						break;
					
					case 16: //Styrelsesuppleant
						
							$ledamoter .= '</br>' . $medlem['first_name']['value'] .' ';
							$ledamoter .= $medlem['last_name']['value'] .' ';
							$ledamoter .= $medlem['email']['value'] .' ';
							$ledamoter .= $medlem['contact_mobile_phone']['value'] . '</br>';						
						
						$ledamoter .= ', <em>Styrelsesuppleant</em>';
						break;
					
					case 22: //2:e vice kårordförande
						
							$ledamoter .= '</br>' . $medlem['first_name']['value'] .' ';
							$ledamoter .= $medlem['last_name']['value'] .' ';
							$ledamoter .= $medlem['email']['value'] .' ';
							$ledamoter .= $medlem['contact_mobile_phone']['value'] . '</br>';						
						
						$ledamoter .= ', <em>2:e vice kårordförande</em>';
						break;
					
					case 23: //Vice kårkassör
						
							$ledamoter .= '</br>' . $medlem['first_name']['value'] .' ';
							$ledamoter .= $medlem['last_name']['value'] .' ';
							$ledamoter .= $medlem['email']['value'] .' ';
							$ledamoter .= $medlem['contact_mobile_phone']['value'] . '</br>';						
						
						$ledamoter .= ', <em>Vice kårkassör</em>';
						break;
					
					default:
					
						break;
					
					
					
					
				}
				
			/*	if ($roll == 'Kårordförande')	{
					
					$ko .= $medlem['first_name']['value'] . ' ';
					$ko .= $medlem['last_name']['value'] . '</br>';
					$ko .= $medlem['email']['value'] .' ';
					$ko .= $medlem['contact_mobile_phone']['value'];
				}*/
				/*elseif ($roll == 'Vice kårordförande' ||
						$roll == '2:e vice kårordförande' ||
						$roll == 'Kårkassör' ||
						$roll == 'Vice kårkassör' ||
						$roll == 'Kårsekreterare' ||
						$roll == 'Styrelseledamot' ||
						$roll == 'Styrelsesuppleant')	{
					
					if ($first_lap==='1')	{
						$ledamoter .= '</br>' . $medlem['first_name']['value'] .' ';
						$ledamoter .= $medlem['last_name']['value'] .' ';
						$ledamoter .= $medlem['email']['value'] .' ';
						$ledamoter .= $medlem['contact_mobile_phone']['value'] . '</br>';
						
					}
					$ledamoter .= ', <em>' . $roll . '</em>';					
					
				}*/
				/*else	{
					$first_lap = '0';
					//$ledamoter .= ', <em>' . $roll . '</em>';
						
				}*/
				
				
				//echo " " . $roll;
				
			}
			
							
		}	
		$the_return_string .= $ko;
		$the_return_string .= $ledamoter;
		return $the_return_string;			
		
	}
	
	function scoutnet_antal_kar($arg)	{
		
		$titel;
		$variabel;
		if ('antal_medlemmar'==$arg)	{
			
				$titel = 'Antal medlemmar';
				$variabel = 'membercount';
		}
		else	{	//vantelista
			
				$titel = 'Antal i väntelista';
				$variabel = 'waitingcount';
		}
		
		$karid = scoutnet_get_option_kar_id();
		$apinyckel = scoutnet_get_option_api_nyckel_kar();
		$apiurl = get_scoutnet_api_url();
		
		$variable = file_get_contents("https://$karid:$apinyckel@$apiurl/api/organisation/group");
		$decoded = json_decode($variable, true);
		$members = $decoded['Group'][$variabel];
		$the_return_string =  $titel . ":<br/><p style='font-size:2em;'>". $members ." st</p>";
		return $the_return_string;
		
	}
	
	function scoutnet_alder_scouter()	{
		
		$karid = scoutnet_get_option_kar_id();
		$apinyckel = scoutnet_get_option_api_nyckel_kar();
		$apiurl = get_scoutnet_api_url();
		
		$variable = file_get_contents("https://$karid:$apinyckel@$apiurl/api/organisation/group");
		$decoded = json_decode($variable, true);
		$members = $decoded['Group']['stats']['active']['breakdown'];

		$kontotantal = array(0,0,0);
		$medlemmar = array();
		
		$the_return_string;

		foreach ($members as $key => $peralder)	{

			$konantal = array(0,0,0);
			$alder = $key;

			foreach ($peralder as $kon => $antal)	{
				$konantal[$kon]+=$antal;
			}
			$medlemmar[$alder] = $konantal;
		}

		$under18 = 0;
		foreach ($medlemmar as $alder => $konsarray)	{
			
			if ($alder > 18)	{
				break;
			}
			
			$under18 += array_sum($konsarray);
		}
		
		$the_return_string .= "$under18 &auml;r 18 eller yngre<br >";
		$the_return_string .= "<div class=\"container\" style=\"width: ".$under18."px\">\n";
		
		foreach ($medlemmar as $alder => $konsarray)	{
			if ($alder > 18)	{
				break;
			}
			$bredd = array_sum($konsarray);
			$bredd *= 3;
			$fodd = date('Y')-$alder;
			$the_return_string .= "<div class=\"graf\" style=\"width: ".$under18."px;\"><p class=\"alder$alder\" style=\"width: ".$bredd."px; background:#123654\">$fodd</p><p class=\"right\">".array_sum($konsarray).":$konsarray[1]/$konsarray[2]/$konsarray[0]</p></div>\n";

		}
		
		$the_return_string .= "</div>\nTotalt:Killar/Tjejer/Annat";
		
		return $the_return_string;
	}
	
	
	function scoutnet_get_kar_namn()	{
		$decoded = scoutnet_get_group_info();
		return $decoded['Group']['name'];
	}
	function scoutnet_get_group_info()	{
		// lite statistik och info /api/organisation/group		
		
		$karid = scoutnet_get_option_kar_id();
		$apinyckel = scoutnet_get_option_api_nyckel_kar();
		$apiurl = get_scoutnet_api_url();		
		
		
		$result = @file_get_contents("https://$karid:$apinyckel@$apiurl/api/organisation/group");
		
		if($result !== FALSE)	{
			return json_decode($result, true);
		}
	}
	function scoutnet_get_member_list() {
		// detaljerad medlemslista /api/group/memberlist
				
		$karid = scoutnet_get_option_kar_id();
		$apinyckel = scoutnet_get_option_api_nyckel_kar_full();
		$apiurl = get_scoutnet_api_url();		
		
		$result = @file_get_contents("https://$karid:$apinyckel@$apiurl/api/group/memberlist");
		
		if($result !== FALSE)	{
			return json_decode($result, true);
		}
	}
	function scoutnet_get_custom_list() {
		// E-postlista /api/group/customlists		
		
		$karid = scoutnet_get_option_kar_id();
		$apinyckel = scoutnet_get_option_api_nyckel_epostlista();
		$apiurl = get_scoutnet_api_url();
		
		$result = @file_get_contents("https://$karid:$apinyckel@$apiurl/api/group/customlists");
		
		if($result !== FALSE)	{
			return json_decode($result, true);
		}
	}
	function scoutnet_register_member()	{
		// Väntelista /api/organisation/register/member
		
		$karid = scoutnet_get_option_kar_id();
		$apinyckel = scoutnet_get_option_api_nyckel_vantelista();
		$apiurl = get_scoutnet_api_url();
		
		$result = @file_get_contents("https://$karid:$apinyckel@$apiurl/api/organisation/register/member");
		
		if($result !== FALSE)	{
			return json_decode($result, true);
		}	
	}
	
	/*
	Function for a waitinglistform
	@Param $arg is true if the form is intended for leaders to registrate already existing members
	*/
	function scoutnet_vantelista($arg)	{
		
		$ledare = false;
		$the_return_string = "";
		
		if (true == $arg)	{
			$ledare = true;
		}
		//$the_return_string .= "Ledare " . $ledare . "123";
		
		$the_return_string .= "<a id='formular'></a>"; //<!--  För att tillåta att länkar till formuläret på sidan -->
		
		
		// --- CURL och PHPMailer måste vara installerade. 

		//Ange lokal sökväg till PHPMailer nedan (för Joomla   /libraries/phpmailer/) 

		$phpmailerpath = plugin_dir_path( __FILE__ ) . '/class-phpmailer.php';


		if (file_exists($phpmailerpath)) {

				require_once($phpmailerpath);
				

		} else {

				$the_return_string .= "<p>Sökvägen till PHPMailer verkar felaktig, eller PHPMailer är inte installerat.</p>";
				return $the_return_string;

		}

		// och nu testar vi för CURL

		if (!function_exists("curl_init")){ // is cURL installed?

			$the_return_string .= "<p>Servern stöder inte formuläret (CURL inte installerat eller fel sökväg till CURL)</p>";
			return $the_return_string;

		}



		// --- URL för Scoutnet API Endpoint aktiveras och hämtas i Scoutnet, kräver hög (Ko) behörighet

		$karid = scoutnet_get_option_kar_id();
		$apinyckel = scoutnet_get_option_api_nyckel_vantelista();
		$apiurl = get_scoutnet_api_url();
		
		$url = "https://$karid:$apinyckel@$apiurl/api/organisation/register/member";





		// --- Sätt och nollställ variabler. Anpassa dessa för din kår och ditt system

		$query_string = ""; //nollställ query-string

		$success_msg = scoutnet_get_option_register_member_success_message();

		$success_ga = "<script type='text/javascript'>ga('send', 'pageview', {  'page': '/intresseanmalan/tack',  'title': 'Tack för din intresseanmälan'});</script>";

		$from_email = scoutnet_get_option_register_member_from_email();

		$from_name = scoutnet_get_option_register_member_from_name();

		$medlemsreg_email = scoutnet_get_option_register_member_medlemsreg_email();

		$success_subject = "Bekräftelse anmälan för "; // namn läggs till automatiskt.
		
		$avdelning_lista = scoutnet_get_option_register_member_medlemsreg_avdelning();

		$avdelning_lista_standard = scoutnet_get_option_register_member_medlemsreg_avdelning_standard();

		//Ändra inte nedan om du inte vet helt säkert vad du gör

		// --- Ladda in hela formuläret i en PHP-variabel

		$formular = "

		<form id='scoutnet-form' method='post' action='#formular'>

		<fieldset class='intresseform'>

		<legend>Förhoppningsvis blivande scouten</legend>

		<ul>

			<li>

				<label for='profile[first_name]'>Förnamn:*</label>

				<input type='text' size='30' required name='profile[first_name]' id='profile[first_name]' value='".$_POST['profile']['first_name']."' placeholder='Förnamn'>

			</li>

			<li>

				<label for='profile[last_name]'>Efternamn:*</label>

				<input type='text' size='30' required name='profile[last_name]' value='".$_POST['profile']['last_name']."' placeholder='Efternamn'>

			</li>

			<li>		

				<label for='profile[ssno]'>Personnummer:*</label>

				<input type='text' pattern='[0-9]{12}' size='12' required name='profile[ssno]' id='profile[ssno]' value='".$_POST['profile']['ssno']."' placeholder='200812310123'>

			</li>

			<li>

				<label for='address_list[addresses][address_1][address_line1]'>Gatuadress:*</label>

				<input type='text' size='30' required name='address_list[addresses][address_1][address_line1]' id='address_list[addresses][address_1][address_line1]' value='".$_POST['address_list']['addresses']['address_1']['address_line1']."' placeholder='Ringvägen 14'>

			</li>

			<li>	

				<label for='address_list[addresses][address_1][address_line1]'>Postnummer:*</label>

				<input type='text' pattern='[0-9]{5}' size='5' required name='address_list[addresses][address_1][zip_code]' id=name='address_list[addresses][address_1][zip_code]' value='".$_POST['address_list']['addresses']['address_1']['zip_code']."' placeholder='12345'>

			</li>

			<li>		

				<label for='address_list[addresses][address_1][zip_name]'>Postadress:*</label>

				<input type='text' size='30' required name='address_list[addresses][address_1][zip_name]' id='address_list[addresses][address_1][zip_name]' value='".$_POST['address_list']['addresses']['address_1']['zip_name']."'  placeholder='Hässelby'>

			</li>

		</ul>

		</fieldset>

		</br>

		<fieldset class='intresseform'>

			<legend>Mamma / Målsman #1</legend>

		<ul>

			<li>	

				<label for='contact_list[contacts][contact_14][details]'>Namn:*</label>

				<input type='text' size='30' required name='contact_list[contacts][contact_14][details]' id='contact_list[contacts][contact_14][details]' value='".$_POST['contact_list']['contacts']['contact_14']['details']."' placeholder='Förnamn Efternamn'>

			</li>

			<li>

				<label for='contact_list[contacts][contact_33][details]'>E-post:*</label>

				<input type='email' size='30' required name='contact_list[contacts][contact_33][details]' id='contact_list[contacts][contact_33][details]' value='".$_POST['contact_list']['contacts']['contact_33']['details']."' placeholder='e-postadress'>

			</li>

			<li>

				<label for='contact_list[contacts][contact_38][details]'>Mobiltelefon:</label>

				<input type='text' pattern='[0-9]{6,10}' size='10' name='contact_list[contacts][contact_38][details]' id='contact_list[contacts][contact_38][details]' value='".$_POST['contact_list']['contacts']['contact_38']['details']."' placeholder='0701234567'>

			</li>

			<li>

				<label for='contact_list[contacts][contact_43][details]'>Hemtelefon:</label>

				<input type='text' pattern='[0-9]{6,10}' size='10' name='contact_list[contacts][contact_43][details]' id='contact_list[contacts][contact_43][details]' value='".$_POST['contact_list']['contacts']['contact_43']['details']."' placeholder='08123456'><br/>

			</li>

		</ul>

		</fieldset>

		</br>

		<fieldset class='intresseform'>

		<legend>Pappa / Målsman #2</legend>

		<ul>

			</li>

			<li>

				<label for='contact_list[contacts][contact_16][details]'>Namn:</label>

				<input type='text' size='30' name='contact_list[contacts][contact_16][details]' id='contact_list[contacts][contact_16][details]' value='".$_POST['contact_list']['contacts']['contact_16']['details']."' placeholder='Förnamn Efternamn'><br/>

			</li>

			<li>

				<label for='contact_list[contacts][contact_34][details]'>E-post:</label>

				<input type='email' size='30' name='contact_list[contacts][contact_34][details]' id='contact_list[contacts][contact_34][details]' value='".$_POST['contact_list']['contacts']['contact_34']['details']."' placeholder='e-postadress'>

			</li>

			<li>

				<label for='contact_list[contacts][contact_39][details]'>Mobiltelefon:</label>

				<input type='text' pattern='[0-9]{6,10}' size='10' name='contact_list[contacts][contact_39][details]' id='contact_list[contacts][contact_39][details]' value='".$_POST['contact_list']['contacts']['contact_39']['details']."' placeholder='0701234567'>

			</li>

			<li>

				<label for='contact_list[contacts][contact_44][details]'>Hemtelefon:</label>

				<input type='text' pattern='[0-9]{6,10}' size='10' name='contact_list[contacts][contact_44][details]' id='contact_list[contacts][contact_44][details]' value='".$_POST['contact_list']['contacts']['contact_44']['details']."' placeholder='08123456'>

			</li>

		</ul>	

		</fieldset>

		</br>

		<fieldset class='intresseform'>

		<legend>Övrigt</legend>

		<ul>
			
			<li>
				<label for='avdelning'> ". $avdelning_lista ."</label>
				
				<input type='text' size='30' required name='avdelning' id='avdelning' value=''  placeholder='". $avdelning_lista_standard."'>	
				
				
			</li>

			<br/>
			<li>

				<label for='ledarintresse'>Förälder ställer gärna upp som ledare:*</label>
				<br/>
				<input type='radio' name='ledarintresse' id='ledarintresse' value='1' required>Ja. Ledares barn har förtur i kön!</input><br/><input type='radio' name='ledarintresse' value=''>Nej</input>

			</li>
			<br/>
			<li>

				<label for='hjalpatill'>Annat föräldrar kan hjälpa till med:</label>
				<br/>
				<input type='checkbox' name='hjalpatill_2' id='hjalpatill' value='Hjälpa på hajker'>Kan hjälpa till på hajker och läger med t.ex. matlagning</input><br/>

				<input type='checkbox' name='hjalpatill_4' id='hjalpatill' value='Hjälpa som hantverkare'>Kan hjälpa till med enklare hantverkssysslor</input><br/>

				<input type='checkbox' name='hjalpatill_8' id='hjalpatill' value='Ordna rabatter'>Kan ordna rabatter i för scouting relevanta butiker</input><br/>

				<input type='checkbox' name='hjalpatill_16' id='hjalpatill' value='Hjälpa på annat sätt'>Kan hjälpa till på annat sätt (använd textfält nedan)</input><br/>

			</li>

			<li>

				<label for='profile[note]'>Övrigt</label>

				<textarea cols='40' rows='4' name='profile[note]' placeholder='Något övrigt som bör läggas in i medlemsregistret?'>".$_POST['profile']['note']."</textarea>

			</li>
			

		</ul>	

		</fieldset>

		</br>

		<fieldset class='intresseform'>

		<legend>Skicka in</legend>

		<p>Genom att skicka in denna intresseanmälan hamnar uppgifterna i medlemsregistrets väntelista. Personen blir dock medlem först efter att personen börjat samt efter godkännande av medlemsregistreraren</p>

		<input type='submit' value='Skicka!'>

		</fieldset>

		</form>";



		// --- Om någon postat formuläret körs nedanstående kod för att ta emot och skicka vidare till Scoutnet

		if ($_POST)

		{ 

			$new_post_array = $_POST; //läs in postad data

			// print_r($_POST);

			// vi lägger till en massa fält som behövs och lägger värden till dessa

			$new_post_array['membership']['status']=1;

			$new_post_array['address_list']['addresses']['address_1']['address_type'] = 0;	//0=Hemadress

			$new_post_array['address_list']['addresses']['address_1']['country_code'] = 752;
			
			
			$new_post_array['profile']['newsletter'] = 1;	//Vill ha nyhetsbrev, 0=Nej, 1=Ja
			
			$new_post_array['profile']['product_subscription_8'] = 1;	//Vill ha medlemstidning
			

			$new_post_array['contact_list']['contacts']['contact_14']['contact_type_id'] = 14;

			$new_post_array['contact_list']['contacts']['contact_33']['contact_type_id'] = 33;

			$new_post_array['contact_list']['contacts']['contact_38']['contact_type_id'] = 38;



			//vi behandlar formulärets värden och moddar lite innan vi skickar till Scoutnet

			$new_post_array['profile']['email'] = $new_post_array['contact_list']['contacts']['contact_33']['details']; //sätt primär epost från förälder1
				
			
			$im_value = 0; //reset IM field value

			if (! empty($new_post_array['ledarintresse'])) {

				$new_post_array['profile']['note'] .= "<br/>Ledarintresse<br/>";

						$im_value = $im_value + 1;

			}

			if (! empty($new_post_array['hjalpatill_2'])) {

				$new_post_array['profile']['note'] .= "<br/>".$new_post_array['hjalpatill_2']."<br/>";

						$im_value = $im_value + 2;

			}

			if (! empty($new_post_array['hjalpatill_4'])) {

				$new_post_array['profile']['note'] .= "<br/>".$new_post_array['hjalpatill_4']."<br/>";

						$im_value = $im_value + 4;

			}

			if (! empty($new_post_array['hjalpatill_8'])) {

				$new_post_array['profile']['note'] .= "<br/>".$new_post_array['hjalpatill_8']."<br/>";

						$im_value = $im_value + 8;

			}

			if (! empty($new_post_array['hjalpatill_16'])) {

				$new_post_array['profile']['note'] .= "<br/>".$new_post_array['hjalpatill_16']."<br/>";

						$im_value = $im_value + 16;

			}



			if ($im_value > 0) {

				$new_post_array['contact_list']['contacts']['contact_7']['contact_type_id']=7;

				$new_post_array['contact_list']['contacts']['contact_7']['details'] = $im_value; // Sätt IM-fältet till en binärkod som motsvarar kryssrutorna i formuläret

			}

			if (! empty($new_post_array['contact_list']['contacts']['contact_16']['details'])) { $new_post_array['contact_list']['contacts']['contact_16']['contact_type_id']=16; }

			if (! empty($new_post_array['contact_list']['contacts']['contact_34']['details'])) { $new_post_array['contact_list']['contacts']['contact_34']['contact_type_id']=34; }

			if (! empty($new_post_array['contact_list']['contacts']['contact_39']['details'])) { $new_post_array['contact_list']['contacts']['contact_39']['contact_type_id']=39; } 

			if (! empty($new_post_array['contact_list']['contacts']['contact_44']['details'])) { $new_post_array['contact_list']['contacts']['contact_44']['contact_type_id']=44; }

			if (! empty($new_post_array['contact_list']['contacts']['contact_43']['details'])) { $new_post_array['contact_list']['contacts']['contact_43']['contact_type_id']=43; }



			$ssno = $new_post_array['profile']['ssno']; //läs ut personnumret för att fylla i date_of_birth automatiskt

			$new_post_array['profile']['date_of_birth'] = substr($ssno, 0, 4)."-".substr($ssno, 4, 2)."-".substr($ssno, 6, 2); //FIXA SÅ DET SER UT SOM DATUM MED BINDESTRÄCK I

			if ( (substr($ssno, 10, 1)==0) OR (substr($ssno, 10, 1)==2) OR (substr($ssno, 10, 1)==4) OR (substr($ssno, 10, 1)==6) OR (substr($ssno, 10, 1)==8) ) { $new_post_array['profile']['sex'] = 2; } //Kvinna

			if ( (substr($ssno, 10, 1)==1) OR (substr($ssno, 10, 1)==3) OR (substr($ssno, 10, 1)==5) OR (substr($ssno, 10, 1)==7) OR (substr($ssno, 10, 1)==9) ) { $new_post_array['profile']['sex'] = 1; } //Man



			// Behandling klar. Nu skickar vi moddad POST till SCOUTNET


			$query_string = http_build_query($new_post_array);

			 //$the_return_string .= $url ."?". $query_string; //debug

			$curl = curl_init();

			curl_setopt($curl, CURLOPT_HEADER, FALSE);

			curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);

			curl_setopt($curl, CURLOPT_URL, $url ."?". $query_string); 

			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); //cmj fix för scoutnet ssl

			curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, FALSE); //cmj fix2 för scoutnet ssl på dev-miljön

			$json_response = curl_exec($curl);

				

			$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);



			if ( $status != 201 ) { // Något gick fel

				if (curl_error($curl)<>"") { // det var ett uppkopplingsfel mot Scoutnet

					$the_return_string .= "<div class='cmj_error'><p>Fel Uppstod!. Uppkopplingen mot Scoutnet misslyckades:<br/>";

					print curl_error($curl);

					$the_return_string .= "</p></div>";

				}  



				$response_array=json_decode($json_response, true); //läs in svaret i en array.
		//Dölj vid release
				// print_r($response_array); // DEBUG



				for ($i = 0; $i<=10; $i++) { // leta först upp om felet är att profilen redan finns i Scoutnet (exempelvis att man står i kö till en annan scoutkår

					if(strpos($response_array['profile'][$i]['msg'], "Personnumret är redan registrerat på medlem") !== FALSE)  {   

						$the_return_string .= "<div class='cmj_success'><p>".$success_msg."</p></div>";

						$the_return_string .= $success_ga;

						$fake_success = 1;		

						//Maila medlemsreg samt den som fyllde i formuläret och visa en snygg bekräftelse.

						$mail = new PHPMailer;

						$mail->CharSet = 'UTF-8';

						$mail->From = $from_email;

						$mail->FromName = $from_name;

						$mail->isHTML(true);	

						$mail->Subject = $success_subject . $new_post_array['profile']['first_name'] ." ".$new_post_array['profile']['last_name'];		

						

						if (true == $ledare)	{
		//Om ledare///	
							$mail->Body = $new_post_array['profile']['first_name'] . " " .$new_post_array['profile']['last_name'] . " har börjat hos <b>" .$new_post_array['avdelning'] . "</b> och är nu registrerad i Scoutnet";
							$mail->addAddress($medlemsreg_email);						//Maila medlemsreg
						}
		//////////////				
		//Om ej ledare	
						else	{
							$mail->Body = $new_post_array['profile']['first_name'] . " " .$new_post_array['profile']['last_name'] . " vill börja på avdelning: <b>" .$new_post_array['avdelning']. "</b>". $success_msg ;
							$mail->addAddress($new_post_array['profile']['email']); 	//Maila medlem
							$mail->addCC($medlemsreg_email);							//Maila medlemsreg också
						}
		//////////////
						// Nu skickar vi iväg mailet

						if ($mail->send()) {

		//Om ej ledare///	
							if (false == $ledare)	{
								$the_return_string .= "<p>Bekräftelse skickad till ".$new_post_array['profile']['email']."</p>";
							}
		/////////////////
							
							$the_return_string .= "<p>Bekräftelse skickad till medlemsregistreraren</p>";

						} else {

							$the_return_string .= 'Kunde inte skicka bekräftelsemail. FEL: '. $mail->ErrorInfo;

						}



						// vi behöver också maila alla ifyllda uppgifter till medlemsreg, eftersom det kan vara bättre än de som redan står i Scoutnet

						$mail = new PHPMailer;

						$mail->CharSet = 'UTF-8';

						$mail->From = $from_email;

						$mail->FromName = $from_name;

						$mail->isHTML(true);	

				

							$medlemsreg_body_svar = $response_array['profile'][$i]['msg']; //svar
							$medlemsreg_body_medlemsnummer = preg_replace('/[^0-9]/', '', $medlemsreg_body_svar);
							/////Personuppgifter
							$medlemsreg_body_Namn = "<p><b>Personuppgifter</b></p> <p>Namn: " .$new_post_array['profile']['first_name'] ." ".$new_post_array['profile']['last_name']."</p>";
							$medlemsreg_body_Ssno = "<p>Personnummer: " .$new_post_array['profile']['ssno']. "</p>";
							$medlemsreg_body_Adress = "<p>Adress: " .$new_post_array['address_list']['addresses']['address_1']['address_line1'].", ".$new_post_array['address_list']['addresses']['address_1']['zip_code']." ". $new_post_array['address_list']['addresses']['address_1']['zip_name']. "</p>";
							
							$medlemsreg_body_Member = $medlemsreg_body_Namn . $medlemsreg_body_Ssno . $medlemsreg_body_Adress;
							
							/////Målsman#1
							$medlemsreg_body_Malsman_1_Namn = "<p><b>Målsman #1</b></p> <p>Namn: ". $new_post_array['contact_list']['contacts']['contact_14']['details']."</p>";
							$medlemsreg_body_Malsman_1_Epost = "<p>E-post: ". $new_post_array['contact_list']['contacts']['contact_33']['details']."</p>";
							$medlemsreg_body_Malsman_1_Mobil = "<p>Mobil: ". $new_post_array['contact_list']['contacts']['contact_38']['details']."</p>";
							$medlemsreg_body_Malsman_1_Hemtele = "<p>Hemtele: ". $new_post_array['contact_list']['contacts']['contact_43']['details']."</p>";
							
							$medlemsreg_body_Malsman_1 = $medlemsreg_body_Malsman_1_Namn . $medlemsreg_body_Malsman_1_Epost . $medlemsreg_body_Malsman_1_Mobil . $medlemsreg_body_Malsman_1_Hemtele;
							
							
							/////Målsman#2
							$medlemsreg_body_Malsman_2_Namn = "<p><b>Målsman #2</b></p> <p>Namn: ". $new_post_array['contact_list']['contacts']['contact_16']['details']."</p>";
							$medlemsreg_body_Malsman_2_Epost = "<p>E-post: ". $new_post_array['contact_list']['contacts']['contact_34']['details']."</p>";
							$medlemsreg_body_Malsman_2_Mobil = "<p>Mobil: ". $new_post_array['contact_list']['contacts']['contact_39']['details']."</p>";
							$medlemsreg_body_Malsman_2_Hemtele = "<p>Hemtele: ". $new_post_array['contact_list']['contacts']['contact_43']['details']."</p>";
							
							$medlemsreg_body_Malsman_2 = $medlemsreg_body_Malsman_2_Namn . $medlemsreg_body_Malsman_2_Epost . $medlemsreg_body_Malsman_2_Mobil . $medlemsreg_body_Malsman_2_Hemtele;
							
							/////Övrigt
							$medlemsreg_body_Ovrigt_Avdelning = "<p><b>Övrigt</b></p> <p>Avdelning: " .$new_post_array['avdelning']."</p>";
							$medlemsreg_body_Ovrigt_Annat = $new_post_array['profile']['note'];
							
							$medlemsreg_body_Ovrigt = $medlemsreg_body_Ovrigt_Avdelning . $medlemsreg_body_Ovrigt_Annat;
							
							
							
							$medlemsreg_body_alla_uppgifter = $medlemsreg_body_Member . $medlemsreg_body_Malsman_1 . $medlemsreg_body_Malsman_2 . $medlemsreg_body_Ovrigt;
						

						$mail->Subject = "Detaljer anmälan för " .$new_post_array['profile']['first_name'] ." ".$new_post_array['profile']['last_name']. ", ". $medlemsreg_body_medlemsnummer;
						
						$medlemsreg_body = "<p><font size='7'>". $medlemsreg_body_medlemsnummer ."</font></p><p><b>Anmälan för person som redan har profil i Scoutnet.</b> Denna profil måste importeras manuellt!</p><p>". $response_array['profile'][$i]['msg'] ."</p>" . $medlemsreg_body_alla_uppgifter . "<p>". print_r($new_post_array, true) . "<p></p>";

						$mail->Body = $medlemsreg_body;

						$mail->addAddress($medlemsreg_email);

						// Nu skickar vi iväg mailet

						if ($mail->send()) {

							$the_return_string .= "<p>Personen är redan registrerad i Scoutnet. Uppgifter skickade till medlemsregistreraren för manuell hantering.</p>";

						} else {

							$the_return_string .= 'Kunde inte skicka bekräftelsemail. FEL: '. $mail->ErrorInfo;

						}

					}

				}



				if ($fake_success =='') { //Scoutnet returnerade ett felmeddelande

					$the_return_string .= "<div class='cmj_error'>";

					$the_return_string .= "<p>Oops, något gick fel. Kontrollera uppgifterna och försök igen!</p>";

					$the_return_string .= "<p>".$status . " : " . $json_response ."</p>";

					for ($i = 0; $i<=10; $i++) {

						if (isset($response_array['profile'][$i]['key'])) {$the_return_string .= $response_array['profile'][$i]['key'] ." : " . $response_array['profile'][$i]['msg'] ."<br/>";}

						if (isset($response_array['address_list'][$i]['key'])) {$the_return_string .= $response_array['address_list'][$i]['key'] ." : " . $response_array['address_list'][$i]['msg'] ."<br/>";}

					}

					// TODO: Fixa ovanstående bättre med en ball loop..

					// TODO: Visa vilket fält som krånglade!! 

					$the_return_string .= "<br>";

					$the_return_string .= "</div>";

					$the_return_string .= $formular; // skriv ut formuläret igen,

				}





			} else { // Anmälan postades OK

				$the_return_string .= "<div class='cmj_success'>";      

				$the_return_string .= "<p>".$success_msg."</p>";

				// $the_return_string .= "<p>".$status . " : " . $json_response ."</p>";

				$the_return_string .= "</div>";	

				$the_return_string .= $success_ga;	

				//Maila medlemsreg samt den som fyllde i formuläret och visa en snygg bekräftelse.

				$mail = new PHPMailer;

				$mail->CharSet = 'UTF-8';

				$mail->From = $from_email;

				$mail->FromName = $from_name;

				$mail->isHTML(true);

				$mail->Subject = $success_subject . $new_post_array['profile']['first_name'] ." ".$new_post_array['profile']['last_name'];		

		//Om ej ledare
				if (false == $ledare)	{
					$mail->Body = $new_post_array['profile']['first_name'] . " " .$new_post_array['profile']['last_name'] . " vill börja på avdelning: <b>" .$new_post_array['avdelning']. "</b>". $success_msg ;
					$mail->addAddress($new_post_array['profile']['email']);
					$mail->addCC($medlemsreg_email);
				}
		//Om ledare///
				else	{
					$mail->Body = $new_post_array['profile']['first_name'] . " " .$new_post_array['profile']['last_name'] . " har börjat hos <b>" .$new_post_array['avdelning'] . "</b> och är nu registrerad i Scoutnet";
					$mail->addAddress($medlemsreg_email);
				}
		//////////////

				// Nu skickar vi iväg mailet

				if ($mail->send()) {

		//Om ej ledare
					if (false == $ledare)	{
						$the_return_string .= "<p>Bekräftelse skickad till ".$new_post_array['profile']['email']."</p>";
					}
		//////////////		
				
					$the_return_string .= "<p>Bekräftelse skickad till medlemsregistreraren</p>";

				} else {

					$the_return_string .='Kunde inte skicka bekräftelsemail. FEL: '. $mail->ErrorInfo;

				}

			}

			

			curl_close($curl);  
			
				

		} else {// det är inte en POST (första gången någon kommer till sidan, vi visar formuläret

			return $formular;

		}	
		return $the_return_string;
		
		
	}
	

?>