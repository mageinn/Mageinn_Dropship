<?php
namespace Mageinn\Dropship\Controller\Adminhtml\Batches;

/**
 * Class View
 * @package Mageinn\Dropship\Controller\Adminhtml\BatchRows
 */
class Download extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $_resultRawFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $_layoutFactory;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $_dir;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $fileSystem;
    /**
     * @var \Magento\Framework\Registry $registry
     */
    protected $registry;

    /**
     * @var \Mageinn\Dropship\Model\Batch
     */
    protected $batchModel;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Filesystem\DirectoryList $dir
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Filesystem\Io\File $fileSystemIo
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Filesystem\Io\File $fileSystemIo,
        \Magento\Framework\Registry $registry,
        \Mageinn\Dropship\Model\Batch $batchModel
    ) {
        $this->_resultRawFactory = $resultRawFactory;
        $this->_layoutFactory = $layoutFactory;
        $this->_fileFactory = $fileFactory;
        $this->_dir = $dir;
        $this->_logger = $logger;
        $this->fileSystem = $fileSystemIo;
        $this->registry = $registry;
        $this->batchModel = $batchModel;

        parent::__construct($context);
    }

    /**
     * Action for when you refresh the batch rows grid
     *
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Raw|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $item = $this->_initItem($this->registry);
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!$item) {
            return $resultRedirect->setPath(
                'sales/batches/view' . $this->registry->registry('current_batch_type'),
                ['_current' => true]
            );
        }

        if ($item->getFilePath()) {
            $filePath = $item->getFilePath();
            $fileInfo = $this->fileSystem->getPathInfo($filePath);
            $fileName = $fileInfo['basename'];

            $searchString = $this->_dir->getRoot() . DIRECTORY_SEPARATOR;
            $projectPath = str_replace($searchString, '', $filePath);

            try {
                $this->_fileFactory->create(
                    $fileName,
                    [
                        'type' => 'filename',
                        'value' => $projectPath
                    ]
                );
                $resultRaw = $this->_resultRawFactory->create();

                return $resultRaw;
            } catch (\Exception $e) {
                $this->_logger->error($e);

                return $resultRedirect->setPath(
                    'sales/batches/view' . $this->registry->registry('current_batch_type'),
                    ['_current' => true]
                );
            }
        } else {
            $this->messageManager->addWarningMessage(__('Batch does not have an export file.'));

            return $resultRedirect->setPath(
                'sales/batches/view' . $this->registry->registry('current_batch_type'),
                ['_current' => true]
            );
        }
    }

    /**
     * @param \Magento\Framework\Registry $registry
     * @return mixed
     */
    private function _initItem($registry)
    {
        $model = $registry->registry('mageinn_batch');
        if (!$model) {
            $id = (int)$this->getRequest()->getParam('id', false);
            $model = $this->batchModel;

            if ($id) {
                $model->load($id);
            }

            $registry->register('mageinn_batch', $model);
            if ($model->getType() == \Mageinn\Dropship\Model\Source\BatchType::MAGEINN_DROPSHIP_BATCH_TYPE_IMPORT) {
                $registry->register('current_batch_type', \Mageinn\Dropship\Model\Batch::BATCH_TYPE_VIEW_IMPORT);
            } else {
                $registry->register('current_batch_type', \Mageinn\Dropship\Model\Batch::BATCH_TYPE_VIEW_EXPORT);
            }
        }

        return $model;
    }
}
