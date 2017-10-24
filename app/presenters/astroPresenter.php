<?php


namespace App\Presenters;

use App\Model\astroManager;
use Nette\Application\UI;
use Nette\Application\Responses;
use Nette\Application\IResponse;


class astroPresenter extends UI\Presenter{
    private $astroModel;
    
   
    public function renderDefault($param) {
        $this->astroModel = new astroManager;
        $this->template->astro_tab = $this->astroModel->get_astros();
    }
    public function handleSend($payload) {
        $this->astroModel = new astroManager;
        $payload = json_decode($payload);
        $payload->fname = trim($payload->fname, " ");
        $payload->lname = trim($payload->lname, " ");
        $errs = $this->astroModel->validate_form($payload);
        $resp = ["fname" => $errs->fname, "lname" => $errs->lname, "DOB" => $errs->DOB, "skill" => $errs->skill, "stat" => $errs->stat];
        if ($resp["stat"] == "OK"){
            if ($payload->task == "Přidat"){ $this->astroModel->add_row($payload);}
            if ($payload->task == "Změnit"){ $this->astroModel->mod_row($payload);}
        }
        $this->sendResponse(new \Nette\Application\Responses\JsonResponse($resp));
    }
    public function handleDel($row) {
        $this->astroModel = new astroManager;
        $this->astroModel->del_row($row);
    }
}
