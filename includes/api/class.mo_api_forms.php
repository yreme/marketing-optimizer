<?php

class mo_api_forms extends mo_api
{

    public function __construct($id = false)
    {
        parent::__construct();
        if (! $id) {
            $this->set_uri(self::APIURI . 'forms?options={"page_size":"all"}');
        } else {
            $this->set_uri(self::APIURI . 'forms/' . $id);
        }
    }
}