<?php


namespace app\entities;


class astro_row_obj {
    public $id;
    public $f_name;
    public $l_name;
    public $DOB;
    public $skill;
    public $date_stamped;
    
    public function __construct($paras) {
        $this->id = $paras["id"];
        $this->f_name = $paras["f_name"];
        $this->l_name = $paras["l_name"];
        $DOB_array = explode("-", $paras["DOB"]);
        $this->DOB = $DOB_array[2] . "." . $DOB_array[1] . "." . $DOB_array[0];
        $this->skill = $paras["skill"];
    }
}
