<?php
class mo_gravity_forms
{

    public function __construct(){
        if (is_admin()) {
            add_action('admin_head', array(
                $this,
                'mo_gf_javascript'
            ));
        }
        
        add_action('wp_ajax_mo_gf_form_field_mapping', array(
            $this,
            'mo_gf_form_field_mapping'
        ));
        add_action('wp_ajax_mo_mf_get_form_field', array(
            $this,
            'mo_mf_get_form_field'
        ));
        add_action('gform_after_submission', array(
            $this,
            'mo_post_to_marketing_optimizer'
        ), 10, 2);
        
        add_action("gform_post_paging", array(
            $this,
            "mo_paged_post_to_marketing_optimizer"
        ), 10, 3);
    }

    public static function mo_get_gf_dropdown(){
        $forms = GFFormsModel::get_forms(true);
        $gf_dropdown = '<select name="mo_gf_form" id="mo_gf_form">';
        $gf_dropdown .= '<option value="0" >Select a Form</option>';
        foreach ($forms as $form) {
            $gf_dropdown .= '<option value="' . $form->id . '" >' . $form->title . '</option>';
        }
        $gf_dropdown .= '</select>';
        $gf_dropdown .= '<sapn id="mo_loader" style="display:none; height: 28px; margin-left: 10px; padding-top: 14px; vertical-align: middle;"><img src="' . plugins_url() . '/' . mo_plugin::MO_DIRECTORY . '/images/ajax-loader.gif" /></span>';
        
        return $gf_dropdown;
    }

    public function mo_gf_form_field_mapping(){
        if (isset($_POST['form_id']) && $_POST['form_id'] > 0) {
            $form_id = $_POST['form_id'];
            $form_fields_arr = $this->mo_get_form_fields_by_id($form_id);
            echo $this->mo_get_form_field_mapping_view($form_id, $form_fields_arr);
            die();
        }
    }

    public function mo_get_form_fields_by_id($form_id) {
        if (isset($form_id) && $form_id > 0) {
            $form_meta_arr = GFFormsModel::get_form_meta($form_id);
            $form_fields_arr = array();
            foreach ($form_meta_arr['fields'] as $field) {
                if ($field['type'] != 'section' && $field['type'] != 'page' && $field['type'] != 'captcha' && $field['type'] != 'post_title' && $field['type'] != 'post_excerpt' && $field['type'] != 'post_content' && $field['type'] != 'post_tags' && $field['type'] != 'post_category' && $field['type'] != 'post_image' && $field['type'] != 'post_custom_field' && $field['type'] != 'html')
                    if (is_array($field['inputs'])) {
                        foreach ($field['inputs'] as $v) {
                            if ($field['label'] == 'Name') {
                                $form_fields_arr[(string) $v['id']] = $v['label'] . ' ' . $field['label'];
                            } else {
                                $form_fields_arr[(string) $v['id']] = $v['label'];
                            }
                        }
                    } else {
                        $form_fields_arr[(string) $field['id']] = $field['label'];
                    }
            }
            return $form_fields_arr;
        }
    }

    public function mo_get_mf_forms($form_id, $mappingId) {
        $mo_api_form = new mo_api_forms();
        $response = $mo_api_form->execute()->get_response();
        $decodec_result = json_decode($response, true);
        $mf_dropdown = '<select name="f_id" id="mo_mf_form">';
        $mf_dropdown .= '<option value="0" >Select a Form</option>';
        foreach ($decodec_result['data'] as $form_data) {
            if ($form_data['name'] != "") {
                $selected = ($mappingId === $form_data['id']) ? "selected" : "";
                $mf_dropdown .= '<option value="' . $form_data['id'] . '"' . $selected . ' >' . $form_data['name'] . '</option>';
            }
        }
        $mf_dropdown .= '</select>';
        $mf_dropdown .= '<sapn id="mo_loader1" style="display:none; height: 28px; margin-left: 10px; padding-top: 14px; vertical-align: middle;"><img src="' . plugins_url() . '/' . mo_plugin::MO_DIRECTORY . '/images/ajax-loader.gif" /></span>';
        return $mf_dropdown;
    }

    
    public static function mo_get_mf_default_forms($mappingId, $token = NULL){
        $mo_form_api = new mo_api_forms();
        $response = $mo_form_api->execute($token)->get_response();
//        var_dump($response);
//        die;
        $decodec_result = json_decode($response, true);
        $mf_dropdown = '<select name="mo_form_default_id">';
        $mf_dropdown .= '<option value="0" >Select a Form</option>';
        if (isset($decodec_result['data']) && is_array($decodec_result['data'])) {
            foreach ($decodec_result['data'] as $form_data) {
                if ($form_data['name'] != "") {
                    $selected = ($mappingId === $form_data['id']) ? "selected" : "";
                    $mf_dropdown .= '<option value="' . $form_data['id'] . '"' . $selected . ' >' . $form_data['name'] . '</option>';
                }
            }
        }
        $mf_dropdown .= '</select>';
        $mf_dropdown .= '<sapn id="mo_loader1" style="display:none; height: 28px; margin-left: 10px; padding-top: 14px; vertical-align: middle;"><img src="' . plugins_url() . '/' . mo_plugin::MO_DIRECTORY . '/images/ajax-loader.gif" /></span>';
        return $mf_dropdown;
    }
    
    public function mo_get_mf_form_fields($form_id, $fieldName, $fieldId){
        $mo_api_form = new mo_api_forms($form_id);
        $response = $mo_api_form->execute()->get_response();
        $decodec_result2 = json_decode($response, true);
        $mf_dropdown = '<select class="marketing_mapped_field" name="gfffm[' . $fieldName . ']" >';
        $mf_dropdown .= '<option value="0" >Choose Field</option>';
        foreach ($decodec_result2['data']['fields'] as $form_data) {
            if ($form_data['field']['name'] != "") {
                $selected1 = ($fieldId == $form_data['field']['id']) ? "selected" : "";
                
                $mf_dropdown .= '<option value="' . $form_data['field']['id'] . '"' . $selected1 . ' >' . $form_data['field']['name'] . '</option>';
            }
        }
        $mf_dropdown .= '</select>';
        return $mf_dropdown;
    }
    public function mo_mf_get_form_field(){
        if (isset($_POST['form_id']) && $_POST['form_id'] > 0) {
            $mo_api_form = new mo_api_forms($_POST['form_id']);
            $response = $mo_api_form->execute()->get_response();
            $decodec_result2 = json_decode($response, true);
            $mf_dropdown .= '<option value="0" >Choose Field</option>';
            foreach ($decodec_result2['data']['fields'] as $form_data) {
                if ($form_data['field']['name'] != "") {
                    $mf_dropdown .= '<option value="' . $form_data['field']['id'] . ' ">' . $form_data['field']['name'] . '</option>';
                }
            }
            echo $mf_dropdown;
            die();
        }
    }
    
    public function mo_get_form_field_mapping_view($form_id, $form_field_mappings){
        $formFieldMappingArr = $this->mo_get_form_field_mapping($form_id);
        $gf_persist = $formFieldMappingArr["gf_persist"] == 'true'?'true':'false';
        $table = '<table style="margin-bottom:15px; width:100%;" >';
        $table .= '<tr ><td style="width:20%; text-align:left;">Select a Marketing Optimizer Form</td><td style="width:30%; padding: 15px 0; text-align:left;">' . $this->mo_get_mf_forms($form_id, $formFieldMappingArr['f_id']) . '</td></tr>';
        $table .= '<tr valign="top">
                            <td >Submit Multi-page forms after each page:</td>
                            <td ><div class="toggle-gf-multipage toggle-modern" style="padding-left:3px;"></div> 
                                 <input type="hidden" name="gf_persist" value="' . ($formFieldMappingArr['gf_persist'] == 'true' ? 'true' : '') . '" /></td>
                            </td>
                    </tr>';
        $table .= '</table>';
        $table .= '<script>
                        jQuery(document).ready(function(){
                            jQuery(\'.toggle-gf-multipage\').toggles({on:' . $gf_persist  . '});
                            jQuery(\'.toggle-gf-multipage\').on(\'toggle\',function(e,active){
                                    if(active){
                                            jQuery(\'[name="gf_persist"]\').val("true");
                                    }else{
                                            jQuery(\'[name="gf_persist"]\').val("");
                                    }
                            });
                        });
		</script>';
        
        $table .= '<table>';
        $table .= '<tr>';
        $table .= '<th style="width:8.8%;text-align:left;">Gravity Forms Field</th>';
        $table .= '<th style="width:7.8%;text-align:left;">To</th>';
        $table .= '<th style="width:25%;text-align:left;">Marketing Optimizer Field</th>';
        $table .= '</tr>';
        $table .= '<tbody>';
        
        foreach ($form_field_mappings as $k => $v) {
            if ($formFieldMappingArr) {
                $form_field_mapping_id = $formFieldMappingArr[$k];
                $table .= '<tr><td>' . $v . '</td><td><img src="' . plugins_url() . '/' . mo_plugin::MO_DIRECTORY . '/images/move.png" /></td><td>' . $this->mo_get_mf_form_fields($formFieldMappingArr['f_id'], $k, $form_field_mapping_id) . '</td></tr>';
            } else {
                $table .= '<tr><td width:25%;text-align:left;">' . $v . '</td><td width:10%;text-align:left;"><img src="' . plugins_url() . '/' . mo_plugin::MO_DIRECTORY . '/images/move.png" /></td><td width:25%;text-align:left;"><select class="marketing_mapped_field" name="gfffm[' . $k . ']" ><option value="">choose form</option></select></td></tr>';
            }
        }
        $table .= '</tbody>';
        $table .= '</table>';
        return $table;
    }

    public static function mo_save_form_field_mapping($form_id, $fieldMappingArr) {
        if ($form_id && count($fieldMappingArr) > 0) {
            $form_id = trim($form_id);
            $formFieldsArr = array();
            foreach ($fieldMappingArr['gfffm'] as $k => $v) {
                $formFieldsArr[$k] = trim($v);
            }
            $formFieldsArr['f_id'] = trim($fieldMappingArr['f_id']);
            if (!isset($fieldMappingArr['gf_persist']) || $fieldMappingArr['gf_persist'] == '') {
                $formFieldsArr['gf_persist'] = 'false';
            } else {
                $formFieldsArr['gf_persist'] = 'true';
            }
            update_option('mo_form_field_mapping_' . $form_id, serialize($formFieldsArr));
        }
    }

    public static function mo_get_form_field_mapping($form_id){
        if ($form_id) {
            return unserialize(get_option('mo_form_field_mapping_' . $form_id));
        } else {
            return false;
        }
    }

    function mo_post_to_marketing_optimizer($entry, $form){
        $post_url = REMOTE_FORM_POST_URL;//'https://api.staging.marketingoptimizer.com/remote/form_post.php';
        $form_id = $entry['form_id'];
        $formFieldMappingArr = mo_gravity_forms::mo_get_form_field_mapping($form_id);
        $v_id = isset($_COOKIE['ap_cookie_1p_' . get_option('mo_account_id')]) ? $_COOKIE['ap_cookie_1p_' . get_option('mo_account_id')] : 0;
        $body = array();
        $body['org_id'] = get_option('mo_account_id');
        $body['action'] = 'feedback_post_add';
        $body['v_id'] = $v_id;
        if ($formFieldMappingArr) {
            $formFieldMappingArr = array_flip($formFieldMappingArr);
            foreach ($formFieldMappingArr as $k => $v) {
                if ($v == 'f_id') {
                    $body[$v] = $k;
                } else {
                    $body['ap_field_' . $k] = $entry[$v];
                }
            }
            $request = new WP_Http();
            $response = $request->post($post_url, array(
                'body' => $body,
                'timeout' => 10
            ));
        }
    }

    function mo_paged_post_to_marketing_optimizer($form, $coming_from_page, $current_page) {
        $post_url = REMOTE_FORM_POST_URL;//'https://api.staging.marketingoptimizer.com/remote/form_post.php';
        $form_id = $form['id'];
        $v_id = isset($_COOKIE['ap_cookie_1p_' . get_option('mo_account_id')]) ? $_COOKIE['ap_cookie_1p_' . get_option('mo_account_id')] : 0;
        $formFieldMappingArr = mo_gravity_forms::mo_get_form_field_mapping($form_id);
        if ($formFieldMappingArr['gf_persist'] == 'true') {
            $body = array();
            $body['org_id'] = get_option('mo_account_id');
            $body['action'] = 'feedback_post_add';
            $body['v_id'] = $v_id;
            $entry = array();
            $needle = 'input_';
            foreach ($_POST as $k => $v) {
                if (strpos($k, $needle) !== false) {
                    $k = substr($k, strlen($needle));
                    $k = str_replace('_', '.', $k);
                    $entry[$k] = $v;
                }
            }
            if ($formFieldMappingArr) {
                $formFieldMappingArr = array_flip($formFieldMappingArr);
                foreach ($formFieldMappingArr as $k => $v) {
                    if ($v == 'f_id') {
                        $body[$v] = $k;
                    } else {
                        $body['ap_field_' . $k] = $entry[$v];
                    }
                }
                $request = new WP_Http();
                $response = $request->post($post_url, array(
                    'body' => $body,
                    'timeout' => 10
                ));
            }
        }
    }

    function mo_gf_javascript()
    {
        echo '<script>
			jQuery(document).ready(function($) {
                            
				$(\'#mo_gf_form\').change(function(){
                                        $("#form_field_mapping_table").empty().html("");
					var data = {
                                                    action: \'mo_gf_form_field_mapping\',
                                                    form_id: $(\'#mo_gf_form\').val()
					};
					if(data.form_id > 0){
						$(\'#mo_loader\').show();
						$.post(\'' . admin_url('admin-ajax.php') . '\', data, function(response) {
							$("#form_field_mapping_table").empty();
							$("#form_field_mapping_table").html(response);
							$(\'#mo_loader\').hide();
							//alert(\'Got this from the server: \' + response);
						});
					}
				});
			
				$(\'#mo_mf_form\').live(\'change\',function(){
					var mf_form_id = $(this).val();
					var data1 = {
                                                    action: \'mo_mf_get_form_field\',
                                                    form_id: mf_form_id
					};
					if(data1.form_id > 0){
						$(\'#mo_loader1\').show();
						$.post(\'' . admin_url('admin-ajax.php') . '\', data1, function(response) {
                                                    $(".marketing_mapped_field").html(response);
                                                    $(\'#mo_loader1\').hide();
						});
					}
				});
			});
		</script>';
    }
}
$mo_gravity_forms = new mo_gravity_forms();