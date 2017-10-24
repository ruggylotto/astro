<?php

namespace app\entities;


class astro_form_err_obj {
    public $fname = "";
    public $lname = "";
    public $DOB = "";
    public $skill = "";
    public $stat = "";
    
    public function get_stat() {
        $stat = "OK";
        if ($this->fname != ""){$stat = "bad";}
        if ($this->lname != ""){$stat = "bad";}
        if ($this->DOB != ""){$stat = "bad";}
        if ($this->skill != ""){$stat = "bad";}
        $this->stat = $stat;
        return $stat;
    }
}
