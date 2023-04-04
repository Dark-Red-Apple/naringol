<?php
class Netweb_Mezaj_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {


	  $this->loadLayout();   
	  $this->getLayout()->getBlock("head")->setTitle($this->__("تست مزاج شناسی نارین گل"));
	        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home"),
                "title" => $this->__("Home"),
                "link"  => Mage::getBaseUrl()
		   ));

      $breadcrumbs->addCrumb("mezaj", array(
                "label" => $this->__("تست مزاج شناسی "),
                "title" => $this->__("تست مزاج شناسی ")
		   ));

      $this->renderLayout(); 
	  
    }
}