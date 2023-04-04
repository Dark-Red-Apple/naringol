<?php
class Netweb_SkinTypeTest_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {


	  $this->loadLayout();   
	  $this->getLayout()->getBlock("head")->setTitle($this->__("تست پوست نارین گل"));
	        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home"),
                "title" => $this->__("Home"),
                "link"  => Mage::getBaseUrl()
		   ));

      $breadcrumbs->addCrumb("mezaj", array(
                "label" => $this->__("تست پوست"),
                "title" => $this->__("تست پوست")
		   ));

      $this->renderLayout(); 
	  
    }
    public function ajaxAction()
    {
		
        if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->isPost()){

            # Mage::log(print_r(Mage::app()->getRequest()->getParams(), true));


            $caseType = Mage::app()->getRequest()->getParam('caseType');
            $Name = Mage::app()->getRequest()->getParam('recepientName');
            $recepientEmail = Mage::app()->getRequest()->getParam('recepientEmail');
            $recepientName = ( $Name ?  $Name : 'مشتری');
			
			
            $str = file_get_contents('./media/naringol/skintype.json');
            $json = json_decode($str, true);

            $data = $json['data'];
            $res ="";
            foreach($data as $obj){
                if( $caseType == $obj['id'] ) {
                    $res = $obj;
                    break;
                }
            }
			

            if ($recepientEmail or Mage::getSingleton('customer/session')->isLoggedIn()) {
                if ($res){
                    $this->sendMailAction($recepientName, $recepientEmail, $res);
                }
            }
            # Send response Json
            $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
            /*$this->getResponse()->setHeader('Content-type', 'application/json; charset=utf-8');*/
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($res));


            } else {
                //other
            }

        return ;
    }
    public function sendMailAction($recepientName, $recepientEmail, $res)
    {
        if(Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $recepientEmail = $customer->getEmail();
            $recepientName = $customer->getName();
        }

        $emailTemplate  = Mage::getModel('core/email_template')->loadDefault('my_custom_email');

        $info = ['name' => $recepientName, 'data' => $res];

        $processedTemplate = $emailTemplate->getProcessedTemplate($info);

        $mail = new Zend_Mail('UTF-8');
        $mail->setBodyHtml($processedTemplate);
        $mail->setFrom(Mage::getStoreConfig('trans_email/ident_general/email'),"Naringol");
        $mail->addTo($recepientEmail, 'no reply');
        $mail->setSubject('نارین گل - '. $recepientName. ' عزیز'. ' نتیجه تست پوست');
        /*$mail->send();
        $storeId=Mage::app()->getStore()->getId();

        /*$mail = Mage::getModel('core/email_template')
            ->setToName($recepientName)
            ->setToEmail($recepientEmail)
            ->setFromEmail(Mage::getStoreConfig('trans_email/ident_general/email'))
            ->setFromName("Naringol")
            ->setBody($processedTemplate)
            ->setSubject('test')
            ->setType('html');



        $mailer = Mage::getModel('core/email_template_mailer');
        /*$emailInfo = Mage::getModel('core/email_info');
        $emailInfo->addTo((string)$Vendor->getEmail(),(string) $Vendor->getName());
        $mailer->addEmailInfo($emailInfo);*/

        /*$mailer->setSender(Mage::getStoreConfig('trans_email/ident_general/email'),"Naringol");
        $mailer->setStoreId($storeId);
        $mailer->setBody($processedTemplate);
        $mailer->send();*/

        try {
            $mail->send();
        } catch (Exception $error) {
            Mage::log($error->getMessage(), null, 'auto_order_emails.log');

        }

        /*$templateId = 1; // Enter you new template ID
        $senderName = Mage::getStoreConfig('trans_email/ident_support/name');  //Get Sender Name from Store Email Addresses
        $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');  //Get Sender Email Id from Store Email Addresses
        $sender = array('name' => $senderName,
            'email' => $senderEmail);



// Get Store ID
        $storeId = Mage::app()->getStore()->getId();

// Set variables that can be used in email template
        $vars = array('customerName' => $recepientName, 'result' => $res);


// Send Transactional Email
        Mage::getModel('core/email_template')
            ->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $storeId);

        Mage::getSingleton('core/session')->addSuccess($this->__('جواب برای شما ایمیل شده است.'));*/
    }
}
