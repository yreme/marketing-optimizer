<?php

class mo_api_fields extends mo_api
{

    public function __construct($id = false)
    {
        parent::__construct();
        if (! $id) {
            $this->set_uri(self::APIURI . 'fields?options={"page_size":"all"}');
        } else {
            $this->set_uri(self::APIURI . 'fields/' . $id);
        }
    }
}