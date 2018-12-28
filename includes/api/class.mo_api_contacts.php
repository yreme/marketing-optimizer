<?php

class mo_api_contacts extends mo_api
{

    public function __construct($id = false)
    {
        parent::__construct();
        if (! $id) {
            $this->set_uri(self::APIURI . 'contacts?options={"page_size":"all"}');
        } else {
            $this->set_uri(self::APIURI . 'contacts/' . $id);
        }
    }
}