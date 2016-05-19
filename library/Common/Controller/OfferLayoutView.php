<?php
/**
 * Baza dla kontrolerów korzystających z layout'ów.
 * W celu wykorzystania tego kontrolera należy zaimplementować
 * następujący fragment wyłączający layout:
 *
 * @example Zend_Layout::getMvcInstance()->disableLayout();
 *
 */
class Common_Controller_OfferLayoutView extends Common_Controller_OfferView {
	//=============================================================================
	// ATTRIBUTES
	/**
	 * Obiekt layout'u.
	 *
	 * @var Zend_Layout
	 */
	protected $_oLayout;
	/**
	 * Nazwa layout'u z którego będzie korzystał kontroler.
	 *
	 * @var string
	 */
	protected $_sLayout = 'layout_two';
	protected $_baner = 'Domyślny tekst';

  protected function _getValue( $sField ) {
    $sFieldForm = 'form_' . $sField;
    return ($this->getRequest()->getParam($sFieldForm,$this->getRequest()->getParam($sField,'')));
  }

  protected function _getValueChcked( $sField ) {
    $sFieldForm = 'form_' . $sField;
    if (($this->getRequest()->getParam($sFieldForm,null)!=null)?$this->getRequest()->getParam($sFieldForm):$this->getRequest()->getParam($sField,'')) {
      $aField = explode('_', $sField);
      return $aField[count($aField)-1];
    }
    return null;
  }

	protected function _prepareLayout() {
		$this->_oLayout = Zend_Layout::startMvc();
		$this->_oLayout->setLayout($this->_sLayout);
	}

  public function init() {
		parent::init();
		$this->view->baseUrl = $this->_sBaseUrl;
    $this->_prepareLayout();
    $this->_oLayout->getView()->config = $this->_oConfig;
    $this->_oLayout->setViewScriptPath( APPLICATION_PATH . $this->_oConfig->views . '/layout');
    $this->view->setBasePath( APPLICATION_PATH . $this->_oConfig->views . '/modules/' . strtolower($this->getRequest()->getModuleName()) );
    $this->view->addHelperPath( APPLICATION_PATH . $this->_oConfig->views . '/helpers');
    $this->view->addFilterPath( APPLICATION_PATH . $this->_oConfig->views . '/filters' );
    $this->view->setLfiProtection(false);
    $this->view->bannertext = $this->_baner;

      Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination_control.phtml');
	}

    public function setBanner($txt){
        $this->view->bannertext = $txt;
    }

    public function setCategorieTextPath($categories){
        $txt = 'Kategoria główna';
        $categories = array_reverse($categories);
        foreach($categories as $kat => $v){
            $txt .= '/'.$v['category_name'];
        }
        return $txt;
    }
	// PUBLIC ---------------------------------------------------------------------
}