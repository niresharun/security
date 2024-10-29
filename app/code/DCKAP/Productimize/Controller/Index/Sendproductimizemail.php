<?php
namespace DCKAP\Productimize\Controller\Index;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;

class SendProductimizeMail extends Action
{
    /**
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Escaper $escaper
    ) {
        parent::__construct($context);
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->_escaper = $escaper;
    }

    /**
     * Post user question
     *
     * @return void
     * @throws \Exception
     */
    public function execute()
    {
        $mailDetails = $this->getRequest()->getPostValue();
        $templateId = 'share_template';
        $fromEmail = $mailDetails['sender_email'];
        $fromName = $mailDetails['sender_name'];
        $toEmail = $mailDetails['receiver_email'];
        try {
            $storeId = $this->storeManager->getStore()->getId();

            $this->inlineTranslation->suspend();
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

            $senderEmail = $this->scopeConfig->getValue('trans_email/ident_support/email', $storeScope);
            $senderName  = $this->scopeConfig->getValue('trans_email/ident_support/name', $storeScope);
            $from = ['email' => $senderEmail, 'name' => $senderName];

            $message = '';
            $message = ($fromName) ? $fromName : '';
            $message .= ($fromEmail) ? ( '(' . $fromEmail . ')' . ' wants to share this design. ' ) : '';

            $templateVars = [
                'welcome_message' => $message,
                'receiver_message' => $mailDetails['receiver_message'],
                'emaillink' => $mailDetails['emaillink'],
                'productlink' => $mailDetails['productlink'],
                'fromEmail' => $fromEmail,
                'fromName' => $fromName
            ];

            $templateOptions = [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $storeId
            ];
            $transport = $this->_transportBuilder->setTemplateIdentifier($templateId, $storeScope)
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFrom($from)
                ->addTo($toEmail)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addError(__('We can\'t process your request right now. Sorry, that\'s all we know.'.$e->getMessage())
            );
            return $e->getMessage();
        }
        //return $this;   
    }
}
