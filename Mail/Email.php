<?php

declare(strict_types=1);

namespace SelectCo\Sage200Api\Mail;

use Magento\Framework\App\Area;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use SelectCo\Core\Helper\StoreHelper;
use SelectCo\Core\Mail\Sender;
use SelectCo\Sage200Api\Helper\Data;

class Email
{
    /**
     * @var Data
     */
    private $helper;
    /**
     * @var StoreHelper
     */
    private $storeHelper;
    /**
     * @var Sender
     */
    private $sender;

    public function __construct(Data $data, StoreHelper $storeHelper, Sender $sender)
    {
        $this->helper = $data;
        $this->storeHelper = $storeHelper;
        $this->sender = $sender;
    }

    /**
     * @param string $templateId
     * @param array|null $templateVars
     * @return void
     * @throws LocalizedException
     * @throws MailException
     */
    public function send(string $templateId, ?array $templateVars = [])
    {
        $emailTo = $this->helper->getEmailTo();

        if ($this->helper->isEmailSelect() === false) {
            $emailSender = $this->storeHelper->getEmailSenderByName($this->helper->getEmailSender());
        } else {
            $emailSender = $this->helper->getCustomEmailSender();
        }

        if ($this->helper->getCopyMethod() === '') {
            $emailArray = explode(',', str_replace(' ', '', $emailTo));
            foreach ($emailArray as $email) {
                $this->sender->send($email, $emailSender, $templateId, Area::AREA_ADMINHTML, null, $templateVars);
            }
        } else {
            $this->sender->send($emailTo, $emailSender, $templateId, Area::AREA_ADMINHTML, null, $templateVars);
        }
    }
}
