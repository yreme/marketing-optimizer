<?php

class mo_api_groups extends mo_api
{

    public function __construct($id = false)
    {
        parent::__construct();
        if (! $id) {
            $this->set_uri(self::APIURI . 'groups?options={"page_size":"all"}');
        } else {
            $this->set_uri(self::APIURI . 'groups/' . $id);
        }
    }
}