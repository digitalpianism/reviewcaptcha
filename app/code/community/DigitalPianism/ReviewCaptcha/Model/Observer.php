<?php

class DigitalPianism_ReviewCaptcha_Model_Observer
{
    public function checkReviewCaptcha(Varien_Event_Observer $observer)
    {
        // Captcha check
        $formId = "review_captcha";
        $captchaModel = Mage::helper("captcha")->getCaptcha($formId);
        if ($captchaModel->isRequired())
        {
            $controller = $observer->getControllerAction();
            $word = $this->_getCaptchaString($controller->getRequest(), $formId);
            if (!$captchaModel->isCorrect($word))
            {
                Mage::getSingleton('core/session')->addError(Mage::helper('captcha')->__('Incorrect CAPTCHA.'));
                $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                if ($redirectUrl = Mage::getSingleton('review/session')->getRedirectUrl(true)) {
                    $controller->getResponse()->setRedirect($redirectUrl);
                    return;
                }
                $referrerUrl = Mage::helper('core/http')->getHttpReferer() ? Mage::helper('core/http')->getHttpReferer()  : Mage::getUrl();
                $controller->getResponse()->setRedirect($referrerUrl);
            }
        }
        return $this;
    }

    /**
     * @param $request
     * @param $formId
     * @return mixed
     */
    protected function _getCaptchaString($request, $formId)
    {
        $captchaParams = $request->getPost(Mage_Captcha_Helper_Data::INPUT_NAME_FIELD_VALUE);
        return $captchaParams[$formId];
    }
}