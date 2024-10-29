<?php
namespace Wendover\MyCatalog\Block;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Wendover\MyCatalog\Helper\Pdf as Helper;
class Pdf extends \Magento\Framework\View\Element\Template
{
	public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        private readonly \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        private readonly Helper $helper,
        private readonly RequestInterface $request,

        )
	{
		parent::__construct($context);
	}

    public function pdf()
    {
        $date  = $this->request->getParam('date');
        $pdf  = $this->request->getParam('pdf');
        $filePath = $this->helper->getMediaUrl().'custom_catalog/logos/pdf/'.$date.'/';
        return $filePath.'/'.$pdf;
    }
}
