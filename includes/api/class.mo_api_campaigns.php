<?php

class mo_api_campaigns extends mo_api
{

    public function __construct($id = false)
    {
        parent::__construct();
        if (! $id) {
            $this->set_uri(self::APIURI . 'campaigns?options={"page_size":"all"}');
        } else {
            $this->set_uri(self::APIURI . 'campaigns/' . $id);
        }
    }
}