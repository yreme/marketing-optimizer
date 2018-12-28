<?php

class mo_api_users extends mo_api
{

    public function __construct($id = false)
    {
        parent::__construct();
        if (! $id) {
            $this->set_uri(self::APIURI . 'users?options={"page_size":"all"}');
        } else {
            $this->set_uri(self::APIURI . 'users/' . $id);
        }
    }
}