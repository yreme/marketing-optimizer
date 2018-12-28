<?php
class mo_settings{
    
    // general settings
    public $mo_lp_permalink_prefix;

    public $mo_lp_variation_pages;

    public $mo_lp_cache_compatible;

    public $mo_lp_track_admin;

    public $mo_lp_variation_percentage;
    
    public $mo_sp_permalink_prefix;

    public $mo_sp_variation_pages;

    public $mo_sp_track_admin;

    public $mo_sp_variation_percentage;

    public $mo_sp_show_time = 15;
    // call to action page setting
    public $mo_ct_permalink_prefix;
    // marketing optimizer integration settings
    public $mo_marketing_optimizer;

    public $mo_account_id = '';
    
    public $mo_user_id;

    public $mo_username;

    public $mo_password;
    
    public $mo_access_token;
    
    public $mo_refresh_token;
    
    public $mo_user_display_name;
    
    public $mo_account_display_name;

    public $mo_phone_tracking;

    public $mo_phone_publish_cls;

    public $mo_phone_tracking_default_number;

    public $mo_phone_tracking_thank_you_url;

    public $mo_form_default_id;
    
    public $mo_phone_ctc;

    public function __construct(){
        foreach (get_object_vars($this) as $property => $value) {
            $function_name = 'set_' . $property;
            if (property_exists($this, $property)) {
                $this->{$function_name}(get_option($property));
            }
        }
    }

    public function get_mo_lp_permalink_prefix(){
        return $this->mo_lp_permalink_prefix;
    }

    public function set_mo_lp_permalink_prefix($value){
        $this->mo_lp_permalink_prefix = $value;
    }

    public function get_mo_lp_variation_pages(){
        return $this->mo_lp_variation_pages;
    }

    public function set_mo_lp_variation_pages($value){
        $this->mo_lp_variation_pages = $value;
    }

    public function get_mo_lp_cache_compatible(){
        return $this->mo_lp_cache_compatible;
    }

    public function set_mo_lp_cache_compatible($value){
        $this->mo_lp_cache_compatible = $value;
    }

    public function get_mo_lp_track_admin(){
        return $this->mo_lp_track_admin;
    }

    public function set_mo_lp_track_admin($value){
        $this->mo_lp_track_admin = $value;
    }

    public function get_mo_lp_variation_percentage(){
        return $this->mo_lp_variation_percentage;
    }

    public function set_mo_lp_variation_percentage($value){
        $this->mo_lp_variation_percentage = $value;
    }

    public function get_mo_sp_permalink_prefix(){
        return $this->mo_sp_permalink_prefix;
    }

    public function set_mo_sp_permalink_prefix($value){
        $this->mo_sp_permalink_prefix = $value;
    }
    
    public function get_mo_ct_permalink_prefix() {
        return $this->mo_ct_permalink_prefix;
    }

    public function set_mo_ct_permalink_prefix($value) {
        $this->mo_ct_permalink_prefix = $value;
    }

    public function get_mo_sp_variation_pages(){
        return $this->mo_sp_variation_pages;
    }

    public function set_mo_sp_variation_pages($value){
        $this->mo_sp_variation_pages = $value;
    }

    public function get_mo_sp_show_time(){
        return $this->mo_sp_show_time;
    }

    public function set_mo_sp_show_time($value){
        $this->mo_sp_show_time = $value;
    }

    public function get_mo_sp_track_admin(){
        return $this->mo_sp_track_admin;
    }

    public function set_mo_sp_track_admin($value){
        $this->mo_sp_track_admin = $value;
    }

    public function get_mo_sp_variation_percentage(){
        return $this->mo_sp_variation_percentage;
    }

    public function set_mo_sp_variation_percentage($value){
        $this->mo_sp_variation_percentage = $value;
    }

    public function get_mo_marketing_optimizer(){
        return $this->mo_marketing_optimizer;
    }

    public function set_mo_marketing_optimizer($value){
        $this->mo_marketing_optimizer = $value;
    }

    public function get_mo_account_id(){
        return (int) $this->mo_account_id;
    }

    public function set_mo_account_id($value){
        $this->mo_account_id = (int) $value;
    }
    
    public function get_mo_account_display_name(){
        return $this->mo_account_display_name;
    }

    public function set_mo_account_display_name($value){
        $this->mo_account_display_name =  $value;
    }
    
    public function get_mo_user_id(){
        return $this->mo_user_id;
    }

    public function set_mo_user_id($value){
        $this->mo_user_id = $value;
    }
    
    public function get_mo_user_display_name(){
        return $this->mo_user_display_name;
    }

    public function set_mo_user_display_name($value){
        $this->mo_user_display_name =  $value;
    }
    
    public function get_mo_username(){
        return $this->mo_username;
    }

    public function set_mo_username($value){
        $this->mo_username = $value;
    }

    public function get_mo_password(){
        return $this->mo_password;
    }

    public function set_mo_password($value){
        $this->mo_password = $value;
    }
    
    public function get_mo_access_token() {
        return $this->mo_access_token;
    }
    
    public function set_mo_access_token($value) {
        $this->mo_access_token = $value;
    }
    
    public function get_mo_refresh_token() {
        return $this->mo_refresh_token;
    }
    
    public function set_mo_refresh_token($value) {
        return $this->mo_refresh_token = $value;
    }

    public function get_mo_phone_tracking(){
        return $this->mo_phone_tracking;
    }

    public function set_mo_phone_tracking($value){
        $this->mo_phone_tracking = $value;
    }

    public function get_mo_phone_publish_cls(){
        return $this->mo_phone_publish_cls;
    }

    public function set_mo_phone_publish_cls($value){
        $this->mo_phone_publish_cls = $value;
    }

    public function get_mo_phone_tracking_default_number(){
        return $this->mo_phone_tracking_default_number;
    }

    public function set_mo_phone_tracking_default_number($value){
        $this->mo_phone_tracking_default_number = $value;
    }

    public function get_mo_phone_tracking_thank_you_url(){
        return $this->mo_phone_tracking_thank_you_url;
    }

    public function set_mo_phone_tracking_thank_you_url($value){
        $this->mo_phone_tracking_thank_you_url = $value;
    }

    public function get_mo_form_default_id(){
        return $this->mo_form_default_id;
    }

    public function set_mo_form_default_id($value){
        $this->mo_form_default_id = $value;
    }
    
    public function get_mo_phone_ctc() {
        return $this->mo_phone_ctc;
    }

    public function set_mo_phone_ctc($value) {
        $this->mo_phone_ctc = $value;
    }

    public function save(){
        foreach (get_object_vars($this) as $property => $value) {
            if (property_exists($this, $property)) {
                $function_name = 'get_' . $property;
                update_option($property, $this->{$function_name}());
            }
        }
    }

    public function mo_get_tests_from_api_get(){
        $mo_api_tests = new mo_api_tests();
        $response = $mo_api_tests->set_cookie_name('mo_api_live')
            ->execute()
            ->get_response();
        $decoded_response = json_decode($response, true);
        $response_data = $decoded_response['data'];
        print_r( $response_data );
    }
}