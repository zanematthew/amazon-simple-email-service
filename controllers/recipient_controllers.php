<?php

Class recipient extends zMCustomPostTypeBase{

    private static $instance;
    private $my_cpt;
    private $subscribe_link;
    private $unsubscribe_link;

    /**
     * Our parent construct has the init's for register_post_type
     * register_taxonomy and many other usefullness.
     * @todo automate this?
     */
    public function __construct(){

        parent::__construct();
        self::$instance = $this;

        $this->my_cpt = strtolower( __CLASS__ );

        $this->unsubscribe_link = '<a href="' . site_url() . '/newsletter?unsubscribe="" target="_blank">unsubscribe</a>';
        $this->subscribe_link = '<a href="' . site_url() . '/newsletter" target="_blank">subscribe</a>';

        add_action( 'init', array( &$this, 'init' ) );
        add_action( 'admin_init', array( &$this, 'adminInit' ) );

        add_action( 'wp_ajax_nopriv_optIn', array( &$this, 'optIn' ) );
        add_action( 'wp_ajax_optIn', array( &$this, 'optIn' ) );
    }


    public function adminInit(){
        add_action( 'wp_ajax_addRecipient', array( &$this, 'addRecipient' ) );
    }


    /**
     * Determine if recipients email address already exists in the *_posts table
     *
     * @return true, false
     */
    public function isDuplicateEmail( $email=null ){
        global $wpdb;
        $query = $wpdb->prepare( "SELECT post_title FROM {$wpdb->prefix}posts WHERE post_type LIKE '{$this->my_cpt}' AND post_status LIKE 'publish' AND post_title LIKE '%s';", $email );
        return $wpdb->query( $query ) ? true : false;
    }


    public function init(){
        if ( isset( $_GET['opt_out'] ) ){
            $opt_out = $this->optOut( $_GET['opt_out'] );
            if ( $opt_out ){
                die('unsubscribed');
            }
        }
    }


    public function optOut( $email=null ){
        return wp_delete_post( $this->recipientID( $email ) );
    }


    /**
     * Returns the ID of a post given a recipient email
     */
    static public function recipientID( $user_email ){
        global $wpdb;
        $result = $wpdb->get_results( "SELECT ID FROM {$wpdb->prefix}posts WHERE post_title LIKE '{$user_email}';" );
        return $result[0]->ID;
    }


    /**
     * Adds a Recipient, note all params are via $_POST
     *
     * @subpackage AJAX
     * @param email
     * @param first_name
     * @param last_name
     * @return Prints json encoded message or string
     */
    public function addRecipient(){

        check_admin_referer( 'addRecipient', 'security' );

        if ( $this->isDuplicateEmail( $_POST['email'] ) ){
            $message = "Email already exsists";
        } elseif ( empty( $_POST['email'] ) ) {
            $message = "Email is empty";
        } else {
            $post_id = wp_insert_post(array(
                'post_title' => $_POST['email'],
                'post_date' => date('Y-m-d H:i:s'),
                'post_type' => $this->my_cpt,
                'post_status' => 'publish'
                )
            );

            if ( $post_id ){
                update_post_meta( $post_id, $this->my_cpt . '_first-name', sanitize_text_field( $_POST['first_name'] ) );
                update_post_meta( $post_id, $this->my_cpt . '_last-name', sanitize_text_field( $_POST['last_name']) );

                $message = json_encode( array(
                    'first_name' => $_POST['first_name'],
                    'last_name' => $_POST['last_name'],
                    'email' => $_POST['email']
                    )
                );
            } else {
                print "Something went wrong";
            }
        }

        print $message;
        die();
    }


    /**
     * Builds an array of ALL Recipients from the database
     *
     * @return Array of Recipients (ID|first_name|last_name|email)
     */
    public function recipientList( $list=null ){

        $final_recipients = array();

        if ( empty( $list ) ){
            global $wpdb;

            $recipients = $wpdb->get_results(  "SELECT `ID`, `post_title` FROM {$wpdb->prefix}posts WHERE `post_type` = '{$this->my_cpt}' AND post_status = 'publish';" );
            $tmp = array();
            foreach( $recipients as $recipient ){
                $tmp['ID'] = $recipient->ID;
                $tmp['first_name'] = get_post_meta( $recipient->ID, $this->my_cpt . '_first-name', true );
                $tmp['last_name'] = get_post_meta( $recipient->ID, $this->my_cpt . '_last-name', true );
                $tmp['email'] = $recipient->post_title;
                $tmp['list'] = wp_get_post_terms( $recipient->ID, 'list' );
                $final_recipients[] = $tmp;
            }
        } else {
            $args = array(
                'post_type' => $this->my_cpt,
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

            foreach( $emails->posts as $email ){
                $final_recipients[] = $email->post_title;
            }

            if ( $emails->post_count == 0 ){
                $final_recipients = false;
            }
        }

        return $final_recipients;
    }


    /**
     * Prints a select box of lists
     *
     * @uses zm_base_build_select()
     */
    public function recipientListSelect( $current_list=null ){

        $terms = get_terms( 'list', array( 'hide_empty' => false ) );

        $array_terms = array();

        foreach( $terms as $term ){
            $tmp_terms['id'] = $term->term_id;
            $tmp_terms['name'] = $term->slug;
            $array_terms[] = $tmp_terms;
        }

        foreach( $current_list as $list ){
            $current[] = $list->term_id;
        }

         $args = array(
            'extra_data' => 'data-allows-new-values="true" style="width: 200px;" data-placeholder="Choose a List..."',
            'extra_class' => 'chzn-select',
            'current' =>  $current,
            'multiple' => true,
            'items' => $array_terms,
            'key' => 'list'
        );
        unset( $current );

        print zm_base_build_select( $args );
    }
}