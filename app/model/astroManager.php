<?php

namespace App\Model;

use app\entities\DBwrap;
use app\entities\astro_row_obj;
use Nette\Utils\Validators;
use app\entities\astro_form_err_obj;

class astroManager {
    private $DBwrap;
    
    public function __construct() {
        $this->DBwrap = new DBwrap;
    }
    
    public function get_astros(){
        $result = array();
        $this->DBwrap->sendSQL("SELECT * FROM astro_tab;", array());
        while ($row = $this->DBwrap->fetch()){
            $result[] = new astro_row_obj($row);
        }
        return $result;
    }
    
    public function add_row($payload) {
        $day = (int)$payload->DOB[0];
        $mon = (int)$payload->DOB[1];
        $year = (int)$payload->DOB[2];
        $date = $this->conv_date($day, $mon, $year);
        $para_arr = array($payload->fname, $payload->lname, $date, $payload->skill);
        $this->DBwrap->sendSQL("insert into astro_tab (f_name, l_name, DOB, skill) values(?, ?, ?, ?);", $para_arr);
    }
    public function mod_row($payload) {
        $day = (int)$payload->DOB[0];
        $mon = (int)$payload->DOB[1];
        $year = (int)$payload->DOB[2];
        $date = $this->conv_date($day, $mon, $year);
        $para_arr = array($payload->fname, $payload->lname, $date, $payload->skill, $payload->row_id);
        $this->DBwrap->sendSQL("update astro_tab set f_name = ?, l_name = ?, DOB = ?, skill = ? "
                            . "where id = ?;", $para_arr);
    }
    public function del_row($row) {
        $this->DBwrap->sendSQL("delete from astro_tab where id = ?;", array($row));
    }
    public function validate_form($payload) {
        $errs = new astro_form_err_obj;
        if ($payload->fname == ""){$errs->fname = "Chybí jméno";}
        if (strlen($payload->fname) > 20){$errs->fname = "Max 20 znaků";}
        if ($payload->lname == ""){$errs->lname = "Chybí příjmení";}
        if (strlen($payload->lname) > 20){$errs->lname = "Max 20 znaků";}
        for ($c = 0; $c < 3; $c++){
            if ($payload->DOB[$c] == ""){$errs->DOB = "Chybí datum"; break;}
            if (!Validators::isNumericInt($payload->DOB[$c])){$errs->DOB = "Špatné datum"; break;}
            if ((int)$payload->DOB[$c] < 1){$errs->DOB = "Špatné datum"; break;}
        }
        if ((int)$payload->DOB[1] > 12){$errs->DOB = "Špatné datum";}
        if ($errs->DOB == ""){
            $day = (int)$payload->DOB[0];
            $mon = (int)$payload->DOB[1];
            $year = (int)$payload->DOB[2];
            if ($year < 0){$errs->DOB = "Letopočet musí být v rozsahu 0-9999";}
            if ($year > 9999){$errs->DOB = "Letopočet musí být v rozsahu 0-9999";}
            $days_per_mon = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
            if ($mon != 2){
                if ($day > $days_per_mon[$mon - 1]){$errs->DOB = "Špatné datum";}
            }
            if ($mon == 2){
                if ($year % 4 == 0 && $day > 29){$errs->DOB = "Špatné datum";}
                if ($year % 4 != 0 && $day > 28){$errs->DOB = "Špatné datum";}
            }
        }
        if ($payload->skill == ""){$errs->skill = "Chybí schopnost";}
        if (strlen($payload->fname) > 45){$errs->fname = "Max 45 znaků";}
        if ($errs->get_stat() == "bad"){return $errs;}
        $date = $this->conv_date($day, $mon, $year);
        if ($payload->task == "Přidat"){
        $para_arr = array($payload->fname, $payload->lname, $date);
        $this->DBwrap->sendSQL("select count(*) as count from astro_tab t "
                . "where t.f_name = ? and t.l_name = ? "
                . "and t.DOB = ?;", $para_arr);
        if ($this->DBwrap->fetch()["count"] > 0){$errs->stat = "doop";}
        return $errs;}
        if ($payload->task == "Změnit"){
        $para_arr = array($payload->fname, $payload->lname, $date, $payload->skill);
        $this->DBwrap->sendSQL("select count(*) as count from astro_tab t "
                . "where t.f_name = ? and t.l_name = ? "
                . "and t.DOB = ? and t.skill = ?;", $para_arr);
        if ($this->DBwrap->fetch()["count"] > 0){$errs->stat = "doop";}
        return $errs;}
    }
    
    public function conv_date($day, $mon, $year) {
        $day = (string)$day;
        if (strlen($day) == 1){$day = "0" . $day;}
        $mon = (string)$mon;
        if (strlen($mon) == 1){$mon = "0" . $mon;}
        $year = (string)$year;
        if (strlen($year) < 4){$year = str_pad($year, 4, "0", STR_PAD_LEFT);}
        return $year . "-" . $mon . "-" . $day;
    }
}
