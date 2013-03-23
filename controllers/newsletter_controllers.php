<?php

Class Newsletter extends zMCustomPostTypeBase{

    private static $instance;
    private $my_cpt;
    private $key;
    private $secret;
    private $subscribe_link;
    private $unsubscribe_link;
    private $opt_hash;
    private $views_dir;

    /**
     * Used for opt out
     */
    private $hash; // mdh5( user_login + user_id )
    public $share_description;

    public function __construct(){
        parent::__construct();

        self::$instance = $this;

        $this->share_description = "Get weekly or Monthly newsletters regarding what BMX Events are going on in your Town.";
        $this->my_cpt = strtolower( __CLASS__ );

        add_action( 'init', array( &$this, 'init' ) );
        add_action( 'admin_menu', array( &$this, 'adminMenu' ) );
        add_action( 'admin_init', array( &$this, 'adminInit') );

        $this->key = get_option('bmx_re_key');
        $this->secret = get_option('bmx_re_secret');
        $this->subscribe_link = '<a href="' . site_url() . '/newsletter" target="_blank">subscribe</a>';
        $this->unsubscribe_link = '<a href="' . site_url() . '/newsletter?unsubscribe="" target="_blank">unsubscribe</a>';
        $this->views_dir = plugin_dir_path( dirname( __FILE__ ) ) . 'views/';
    }


    public function adminInit(){
        add_action( 'wp_ajax_sendEmail', array( &$this, 'sendEmail' ) );
        add_action( 'wp_ajax_previewEmail', array( &$this, 'previewEmail' ) );
        add_action( 'wp_ajax_deployEmails', array( &$this, 'deployEmails' ) );

        $fields = array(
            'bmx_re_key',
            'bmx_re_secret',
            'bmx_re_source',
            'bmx_re_test_email',
            'bmx_re_emails_template_id',
            'bmx_re_emails_footer'
            );

        foreach( $fields as $field ) {
            register_setting('wpmc_plugin_options', $field );
        }
    }


    public function init(){
        if ( isset( $_GET['opt_out'] ) ){
            $user_obj = get_user_by_email( $_GET['opt_out'] );
            $result = delete_user_meta( $user_obj->ID, 'opt_in', $_GET['oo'] );
            if ( ! $result ){
                die('unsubscribed');
            }
        }
    }

    public function adminMenu(){

        $parent = 'edit.php?post_type='.$this->my_cpt;

        $sub_menu_pages = array(
            array(
                'parent_slug' => $parent,
                'page_title' => 'Recipients &amp; List',
                'menu_title' => 'Recipients &amp; List',
                'capability' => 'manage_options',
                'menu_slug' => 'recipients',
                'function' => 'recipientsView'
                ),
            array(
                'parent_slug' => $parent,
                'page_title' => 'Deploy',
                'menu_title' => 'Deploy',
                'capability' => 'manage_options',
                'menu_slug' => 'recipients-deploy',
                'function' => 'deployView'
                ),
            array(
                'parent_slug' => $parent,
                'page_title' => 'Settings',
                'menu_title' => 'Settings',
                'capability' => 'manage_options',
                'menu_slug' => 'newsletter-settings',
                'function' => 'settingsView'
                )
            );

        foreach( $sub_menu_pages as $sub_menu ){
            add_submenu_page(
                $sub_menu['parent_slug'],
                $sub_menu['page_title'],
                $sub_menu['menu_title'],
                $sub_menu['capability'],
                $sub_menu['menu_slug'],
                array( &$this, $sub_menu['function'] )
            );
        }
    }


    public function settingsView(){
        wp_enqueue_style('newsletter_admin-style');
        $this->loadTemplate( 'settings.php', $this->views_dir );
    }


    public function recipientsView(){
        wp_enqueue_script('zm-chosen-script');
        wp_enqueue_style('zm-chosen-style');
        wp_enqueue_style('newsletterrecipient_admin-style');
        $this->loadTemplate( 'recipients.php', $this->views_dir );
    }


    public function deployView(){
        wp_enqueue_script( 'deploy-admin', plugin_dir_url( dirname( __FILE__ ) ) . 'assets/deploy_admin.js', array('jquery') );
        $this->loadTemplate( 'deploy.php', $this->views_dir );
    }


    /**
     * Each user gets a custom email based on their settings, default is to
     * build the email based on the venues in the users state as specified
     * in their profile.
     *
     * @param $user_email used to get the location for content
     * @param $template The post to derive the tempalte from
     *
     * @todo Consider region based or something? like send to everyone in MD, everyone in CA, etc.
     * @todo Remove ALL markup and use a templating system. Markup should come from the wysiwyg
     *
     * @return Array consisting of the email subject, plain_text and body
     * @note This is designed to be used via an ajax request?
     */
    static public function getEventsTemplate( $user_email=null, $template_id=null ){

        if ( ! isset( $_POST['template_id'] ) ){
            $template_id = get_option( 'bmx_re_emails_template_id' );
        } else {
            $template_id = $_POST['template_id'];
        }

        $template = get_post( $template_id );

        $subject = $template->post_title;
        $body = $template->post_content;
        $plain_text = $template->post_excerpt;

        if ( is_null( $user_email ) ){
            $user_email = 'zanematthew@gmail.com';
        }

        $user_obj = get_user_by( 'email', $user_email );

        $email = array();

        if ( ! strpos( $body, '{') ){
            $email['subject'] = $subject;
            $email['plain_text'] = $plain_text;
            $email['body'] = $body . Newsletter::defaultFooterTpl();
        } else {

            /**
             * @todo create a $user object
             * $location = $user->defaultLocation();
             * print $location['city']; // Columbia
             */
            $user_location = get_user_meta( $user_obj->ID, 'user_state', true );

            if ( ! $user_location ){
                return 'Location based emails will not work! <strong>This email has no location set.</strong>';
            }

            $replacements = array(
                '{date}' => date( 'M/d/Y'),
                '{email}' => $user_email,
                '{first_name}' => get_user_meta( $user_obj->ID, 'first_name', true ),
                '{last_name}' => get_user_meta( $user_obj->ID, 'last_name', true ),
                '{state}' => get_user_meta( $user_obj->ID, 'user_state', true ),
            );

            // $body = str_replace( '{state}', $user_location, $body );
            $body = str_replace( array_keys( $replacements ), $replacements, $body );

            $venues_obj = New Venues;
            $venues = $venues_obj->getVenueByState( $user_location );
            $venues_count = count( $venues );


            $events_obj = New Events;

            // yeah, i know
            (string)$final_events = null;
            $i = 0;

            $events_loop = get_string_between( $body, "{events}", "{/events}");

            if ( ! empty( $events_loop ) ){

                (string)$tmp = null;
                $boo = null;

                foreach( $venues as $venue ){

                    $events = $venues_obj->getSchedule( $venue->ID, $past=false );

                    // $replacements['{venue}'] = $venue->post_title;
                    if ( ! empty( $events ) ){

                        $tmp .= '<h2 style="margin: 5px 0 10px 0;padding:0;font-size:14px;font-weight:bold;font-family:Helvetica,Arial,sans-serif">'.$venue->post_title.'</h2>';

                        foreach( $events->posts as $event ){
                            $date = date( 'D F j', strtotime( $events_obj->getDate( $event->ID ) ) );
                            $site_url = site_url();
                            $replacements['{date_title}'] ="<h3 style=' margin: 0 0 10px 0; padding: 0; font-size: 12px; font-weight: bold; font-family: Helvetica,Arial,sans-serif;'><span style='font-weight: bold;'>".$date."</span> <a href='{$site_url}/events/{$event->post_name}' style='color: #1987B1; text-decoration: none;'>".$event->post_title."</a></h3>";
                            $tmp .= str_replace( array_keys( $replacements ), $replacements, $events_loop );
                        }
                        $tmp .= '<hr style="border-top:1px solid #ddd;margin:10px 0">';
                    }
                    $i++;
                }
                $body = str_replace( $events_loop, $tmp, $body );
            }

            $body = str_replace( '{events}', '', $body );
            $body = str_replace( '{/events}', '', $body );

            $email['subject'] = $subject;
            $email['plain_text'] = $plain_text;

            $opt_hash = get_user_meta( $user_obj->ID, 'opt_in', true );
            $footer = str_replace( 'unsubscribe=', 'opt_out='.$user_email.'&oo='.$opt_hash, Newsletter::defaultFooterTpl() );

            $email['body'] = $body . $footer;
        }

        return $email;
    }

    /**
     * Sends a single email using AWS SES SDK
     *
     * SES Documentation: http://docs.amazonwebservices.com/AWSSDKforPHP/latest/#m=AmazonSES/send_email
     *
     * @subpackage AJAX
     * @param $recipient
     * @param $is_ajax
     * @return returns or prints response
     */
    public function sendEmail( $recipient=null, $is_ajax=true ){

        if ( ! empty( $_POST['user_email'] ) ){
            $recipient = $_POST['user_email'];
        }

        require_once( plugin_dir_path( dirname( __FILE__ ) ) .'/vendor/aws-php-sdk-1.5.11/sdk.class.php');

        $ses = new AmazonSES( array('key'=>$this->key,'secret'=>$this->secret ) );

// This is a custom template! it should be a plugin, since it is specific to Events & Venues!
$email = $this->getEventsTemplate( $recipient );

        $subject = $email['subject'];
        $body = $email['body'];
        $plain_text = $email['plain_text'];

        $destination = array(
            'ToAddresses' => array(
                $recipient // String max 50
                )
            );

        $message = array(
            'Subject' => array(
                'Data' => $subject,
                'Charset' => 'UTF-8'
                ),
            'Body' => array(
                'Text' => array(
                    'Data' => $plain_text,
                    'Charset' => 'UTF-8'
                    ),
                'Html' => array(
                    'Data' => $body,
                    'Charset' => 'UTF-8'
                    )
            )
        );
        $response = $ses->send_email( get_option('bmx_re_source'), $destination, $message );

        if ( $is_ajax ) {
            if ( $response->isOK() )
                print $response->isOK();
            else
                print $response->body->Error->Code;
            die();
        } else {
            return $response->isOK();
        }
    }

    public function deployEmails(){

        $message = null;

        if ( empty( $_POST['list'] ) ){
            $message = "List is empty";
        } else {
            $emails = $this->getSubscriberList( $_POST['list'] );
            $template_id = get_option('bmx_re_emails_template_id');
            $i = 1;

            foreach( $emails as $email ){
                $message .= "Start of deployment: {$i}.\n";
                $message .= "Getting template: {$template_id}.\n";
                $message .= "Sending email: {$email}.\n";
                $message .= "Status: " . $this->sendEmail( $email, false ) . "\n";
                $message .= "\n";
                $i++;
            }

            $post_id = $_POST['template_id'];

            if ( $i > 1 ){
                update_post_meta( $post_id, 'email_sent_time', date('Y-m-d H:i:s') );
            }
        }

        print $message;
        die();
    }

    public function previewEmail( $user_email=null ){

        if ( empty( $_POST['user_email'] ) ){
            print 'Need a test user_email';
            die();
        }

        $user_email = $_POST['user_email'];

        $email = $this->getEventsTemplate( $user_email );

        // Yes, funky way of error checking
        if ( is_string( $email ) ){
            print $email;
            die();
        } else {
            print '<style type="text/css">.temp{font-family: arial;font-size: 12px;}</style>';
            print "<div class='temp'>";
            print "<strong>Recipient</strong> {$user_email}<br />";
            print "<strong>Subject</strong> {$email['subject']}<br />";
            print "<strong>Plain Text</strong> {$email['plain_text']}<br />";
            print "<strong style='float: left;margin-bottom: 20px;'>Body</strong>{$email['body']}";
            print "</div>";
            die();
        }
    }


    public function templateDropDown(){
        global $wpdb;

        $cpt = self::$instance->my_cpt;

        $query = "SELECT `ID`, `post_title`
        FROM {$wpdb->prefix}posts
        WHERE `post_type` LIKE '{$cpt}'
        AND `post_status` LIKE 'publish';";

        $results = $wpdb->get_results( $query );
        $html = null;
        $setting_name = 'bmx_re_emails_template_id';
        $current = get_option( $setting_name );

        foreach( $results as $result ){
            $html .= '<option value="'.$result->ID.'" '.selected( $result->ID, $current, false ).'>'.$result->post_title.'</option>';
        }
        print '<select id="bmx_re_email_template_select" name="'.$setting_name.'">'.$html.'</select>';
    }

    static public function getSubscriberCount(){
        global $wpdb;
        return $wpdb->query( "SELECT * FROM {$wpdb->prefix}usermeta WHERE `meta_key` LIKE 'opt_in';" );
    }


    /**
     * @return An array of emails from the newsletterrecipient post_type
     */
    public function getSubscriberList( $list=null ){
        $args = array(
            'post_type' => 'newsletterrecipient',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'tax_query' => array(
                array(
                    'taxonomy' => 'list',
                    'field' => 'id',
                    'terms' => $list
                    )
                )
            );

        $emails = New WP_Query( $args );
        $final_emails = array();
        foreach( $emails->posts as $email ){
            $final_emails[] = $email->post_title;
        }

        if ( $emails->post_count == 0 ){
            return false;
        } else {
            return $final_emails;
        }
    }


    static public function defaultFooterText(){
        return get_option('bmx_re_emails_footer');
    }


    static public function defaultFooterTpl(){
        $content = self::$instance->defaultFooterText();

        $subscribe_link = self::$instance->subscribe_link;
        $unsubscribe_link = self::$instance->unsubscribe_link;

        $tags = array(
            '{site_name}' => get_bloginfo('name'),
            '{unsubscribe_link}' => $unsubscribe_link,
            '{subscribe_link}' => $subscribe_link
            );

        $content = str_replace( array_keys( $tags ), $tags, $content );

        return '<table width="616" border="0" cellpadding="0" cellspacing="0" bgcolor="#e5e5e5" style="background-color:#e5e5e5"><tr><td><p style="text-align: left;color: #454545; font-family: sans-serif,arial;font-size: 11px; line-height: 15px; padding: 10px">'.$content.'</td></tr></table>';
    }


    public function lastSentTime( $post_id=null ){

        $sent = get_post_meta( $post_id, 'email_sent_time', true );

        if ( $sent )
            return date( 'M, j @ h:H', strtotime( $sent ) );
        else
            return "Newsletter has not been sent.";
    }
}