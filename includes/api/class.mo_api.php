<?php
class mo_api
{
	const TOKEN_STORAGE = 'wordpress'; // 'wordpress', 'cookie'
    const APIURI = API_URL;

    private $cookie_name = 'mo_api_live';

    private $error = false;

    private $is_new_session = false;

    private $request;

    private $request_type = 'GET';

    private $response;

    private $uri = self::APIURI;
    
    private $id_token;
    
	
	
	public function getToken() {
		if (self::TOKEN_STORAGE == 'cookie'){			
			if(isset($_COOKIE[$this->get_cookie_name()])) {
				return  $_COOKIE[$this->get_cookie_name()];
			}			
		} elseif (self::TOKEN_STORAGE == 'wordpress'){	
			return get_option($this->get_cookie_name());
		}
		return '';
	}
	
	public function setToken ($token) {
		
		if (self::TOKEN_STORAGE == 'cookie'){		
			setcookie($this->get_cookie_name(), $token);
		} elseif (self::TOKEN_STORAGE == 'wordpress'){		
			add_option($this->get_cookie_name(), $token);
			update_option($this->get_cookie_name(), $token);	
		}
	}
	
    public function __construct()
    {
        return $this;
    }

    public function get_cookie_name()
    {
        return $this->cookie_name;
    }

    public function set_cookie_name($cookie_name)
    {
        $this->cookie_name = $cookie_name;
        return $this;
    }

    public function get_error()
    {
        return $this->error;
    }

    public function set_error($error)
    {
        $this->error = $error;
        return $this;
    }

    public function get_is_new_session()
    {
        return $this->is_new_session;
    }

    public function set_is_new_session($is_new_session)
    {
        $this->is_new_session = $is_new_session;
        return $this;
    }

    public function get_request()
    {
        return $this->request;
    }

    public function set_request($request)
    {
        $this->request = $request;
        return $this;
    }

    public function get_request_type()
    {
        return $this->request_type;
    }

    public function set_request_type($request_type)
    {
        $this->request_type = $request_type;
        return $this;
    }

    public function get_response()
    {
        return $this->response;
    }

    public function set_response($response)
    {
        $this->response = $response;
        return $this;
    }

    public function get_uri()
    {
        return $this->uri;
    }

    public function set_uri($uri)
    {
        $this->uri = $uri;
        return $this;
    }
    
    public function execute()
    {
        $mo_settings_obj = new mo_settings();
        $token = $this->getToken();
        if(empty($token)) {
            $token = $mo_settings_obj->get_mo_access_token();
        } 		
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->get_uri());
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer ". $token));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        switch ($this->get_request_type()) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->get_request());
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->get_request_type());
                break;
            default:
                break;
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
		
        curl_close($ch);
        if ($error) {
            $this->set_error($error);
        }
        if($httpcode === 401 && $mo_settings_obj->get_mo_refresh_token()) {
            $this->refreshToken($mo_settings_obj->get_mo_refresh_token());
        }
        $this->set_response($response);
        return $this;
    }
    
    public function refreshToken($refresh_token) {
        $url = self::APIURI.'auth/refresh';
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => "{\n\t\"refresh_token\" : \"$refresh_token\"\n}",
          CURLOPT_HEADER => false,
        ));

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);
        $mo_settings_obj = new mo_settings();
        if($httpcode === 200) {
            $decodec_result = json_decode($response, true);
            if ( $decodec_result['success'] == 'true' && is_array($decodec_result['data']) && !empty($decodec_result['data'])) {
                $this->setToken($decodec_result['data']['access_token']);
                $mo_settings_obj->set_mo_access_token($decodec_result['data']['access_token']);
                $mo_settings_obj->save();
                $this->execute();
            }
        } else {
            $this->setToken(null);
            $mo_settings_obj->set_mo_access_token(null);
            $mo_settings_obj->set_mo_refresh_token(null);
            $mo_settings_obj->set_mo_account_display_name(null);
            $mo_settings_obj->set_mo_user_display_name(null);
            $mo_settings_obj->set_mo_account_id(null);
            $mo_settings_obj->set_mo_user_id(null);
            $mo_settings_obj->set_mo_marketing_optimizer ( 'false' );
            $mo_settings_obj->save();
            
            
        }
    }
}
