<?php

class SectionController extends Zend_Controller_Action
{
    public function init() {
        
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->msg = $this->_flashMessenger->getMessages(); 
        
        $auth = Zend_Auth::getInstance();
        if( $auth->hasIdentity() ) {
            $this->view->storage = $auth->getIdentity(); // są to paramsy funkcji getResultRowObject() w AuthController
        }
        
    }
    
    // Funkcja używana do zmiany liczby wyników na stronie. Ajax tylko odpala
    // tę funkcję, która nadaje zmienną sesyjną
    public function setIPPAction() { 
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        // Validation of the request's Int param
        $filterOptions = array (
            'options' => array(
                'min_range' => 10,
                'max_range' => 50
            )
        );
        $iPP = filter_var($_REQUEST['iPP'], FILTER_VALIDATE_INT, $filterOptions);
        // If validation success
        if($iPP != FALSE) {
            $_SESSION['sectionController']['itemsPerPage']  = $iPP;
            return TRUE;
        } else { // or if fails...
            return FALSE;
        }
        
        
    }
  
    public function indexAction()
    {
        $pageTitleSession = new Zend_Session_Namespace('pageTitle'); 
        $pageTitleSession->pageTitle = 'Moje sekcje';
        $_SESSION['navbar']['przegladaj'] = 1;
        //Sekcje
        $section_map = new Application_Model_SectionMapper();
        $authorID = $_SESSION['Zend_Auth']['storage'][0]->user_id;
        $aSections = $section_map->fetchAll('section_removed=0 AND section_author_id='.$authorID, 'section_order ASC');
        $this->view->sections = $aSections; // przekazujemy rowset 
        
        // Paginacja
        $itemsPerPage = (isset($_SESSION['sectionController']['itemsPerPage']))? $_SESSION['sectionController']['itemsPerPage'] : 5 ;
        $paginator = Zend_Paginator::factory($aSections);
        $paginator->setItemCountPerPage($itemsPerPage)
                  ->setCurrentPageNumber($this->_getParam('page', 1))
                  ->setDefaultPageRange(8);
        $this->view->paginator = $paginator;
        
        $this->view->totalItemCount = $paginator->getTotalItemCount();
        $this->view->itemCountPerPage = $paginator->getItemCountPerPage();
        
        // Wyszukiwarka sekcji
        try {
            $request = $this->getRequest();
            $oForm = new My_MyForm_Section_SectionSearchForm();
            if ($request->isPost()) {
                if($oForm->isValid($request->getPost())) {
                    $fullname = $request->getParam('fullname');
                    
                    //TODO (daj to w funkcje w modelu): 

                    $tSection = $section_map->getDbTable();
                    $select = $tSection->select()
                                    ->from(array('s'=>'section'))
                                    ->where('s.section_fullname = ?', $fullname);
                    $row = $tSection->fetchRow($select);
                    
                    if(!$row) {
                        $this->_flashMessenger->addMessage('warning');
                        $this->_flashMessenger->addMessage('Podana przez Ciebie sekcja nie istnieje...');
                        $this->_helper->redirector->gotoRoute(array(), 'note_section_index');
                    }
                    else {
                        $this->_helper->redirector->gotoRoute(array('section_id' => $row['section_id']), 'note_note_index');
                    }
                }
            }
        } catch (Exception $ex) {
            $this->_flashMessenger->addMessage('danger');
            $this->_flashMessenger->addMessage('Błąd wyszukiwania. ' . $ex->getMessage());
        }
        
        $this->view->form = $oForm;
    }
    
    public function addAction() {
        $pageTitleSession = new Zend_Session_Namespace('pageTitle'); 
        $pageTitleSession->pageTitle = 'Dodawanie sekcji';
        $_SESSION['navbar']['przegladaj'] = 1;
        try {
            $request = $this->getRequest();
            $oForm = new My_MyForm_Section_SectionForm();
            $oForm->setDecorators(array(array('ViewScript', array('viewScript' => 'section/form.phtml'))));
            //Setting default color of section
            $oForm->getElement('color')->setValue('primary');
            
            if ($this->getRequest()->isPost()) {
                if ($oForm->isValid($request->getPost())) {
                    $section = new Application_Model_Section($oForm->getValues());
                    
                    $section->setAuthorId($_SESSION['Zend_Auth']['storage'][0]->user_id);
                    $section->setRemoved(0);
                    
                     
                    // get all sections count to 
                    $tSection = new Application_Model_DbTable_Section();
                    $count = $tSection->getAllSectionsCount();
                    // Add 1 to number of elements; set it as an order number
                    $section->setOrder($count + 1);
                    
                    $mapper = new Application_Model_SectionMapper();
                    $mapper->save($section);
                   
                    // redirect...
                    $this->_flashMessenger->addMessage('success');
                    $this->_flashMessenger->addMessage('Sekcja dodana!');
                    return $this->_helper->redirector->gotoRoute(array(), 'note_section_index');
                }
            }
        } catch (Exception $e) {
            $this->_flashMessenger->addMessage('danger');
            $this->_flashMessenger->addMessage('Wystąpił błąd podczas dodawania sekcji. ' . $e->getMessage());
        }
        $this->view->form = $oForm;
    }
    
    public function editAction() { 
        $pageTitleSession = new Zend_Session_Namespace('pageTitle'); 
        $pageTitleSession->pageTitle = 'Edycja sekcji';
        $_SESSION['navbar']['przegladaj'] = 1;
        try {
            $request = $this->getRequest();
            $section_id = $request->getParam('section_id');
            $oForm = new My_MyForm_Section_SectionForm();
            $oForm->setDecorators(array(array('ViewScript', array('viewScript' => 'section/form.phtml'))));

            $oSection = new Application_Model_Section();
            $oSectionMap = new Application_Model_SectionMapper();
            // Pobieramy obiekt sekcji (razem z id)
            $section = $oSectionMap->fetchOne($section_id, $oSection);
            
            if ($this->getRequest()->isPost()) {
                
                if ($oForm->isValid($request->getPost())) {
                    $data = $oForm->getValues(); // pobranie danych z forma
                    $section->setAuthorId($_SESSION['Zend_Auth']['storage'][0]->user_id);
                    $section->setFullname($data['fullname']);
                    $section->setColor($data['color']);
                    $section->setVisibility($data['visibility']);
                    $oSectionMap->save($section);
                    $this->_flashMessenger->addMessage('success'); 
                    $this->_flashMessenger->addMessage('Sekcja zmieniona.');
                    return $this->_helper->redirector->gotoRoute(array(), 'note_section_index');
                }
            }
            else {
                // musze tak to zrobić... bo konwersja chronionych paraamsów zwraca * w nazwach ;(
                $section2 = array( 
                    'fullname' => $section->getFullname(),
                    'color' => $section->getColor(),
                    'visibility' => $section->getVisibility()
                );
                $oForm->populate($section2);
            }
        } catch (Exception $e) {
            $this->_flashMessenger->addMessage('danger');
            $this->_flashMessenger->addMessage('Wystąpił błąd przy edycji sekcji. ' . $e->getMessage());
        }
        $this->view->form = $oForm;
    }
    
    public function deleteAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        try {
            $request = $this->getRequest();
            $section_id = $request->getParam('section_id');
            
            $section = new Application_Model_Section();
            $oSectionMapper = new Application_Model_SectionMapper();
            $oSection = $oSectionMapper->fetchOne($section_id, $section);
            $oSection->setId($section_id)
                     ->setRemoved(1);
            $oSectionMapper->save($oSection);
                $oNoteMap = new Application_Model_NoteMapper();
                $oNoteMap->deleteAllSectionNotes($section_id);
            
            $this->_flashMessenger->addMessage('success');
            $this->_flashMessenger->addMessage('Sekcja została usunięta.');
            
            $this->_helper->redirector->gotoRoute(array(), 'note_section_index');
            
        } catch (Exception $e) {
            $this->_flashMessenger->addMessage('danger');
            $this->_flashMessenger->addMessage('Wystąpił błąd podczas usuwania sekcji. ' . $e->getMessage());
        }
    }
    // Na cvo przeznaczysz zarobione pieniądze
    
    public function changevisibilityAction(){ 
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        // Parametry ajax
        $sectionId = $this->getRequest()->getParam('section_id');
        $changeTo = $this->getRequest()->getParam('change_to');
        if($changeTo=='public') $visibility = true; else $visibility = false;

        try {
            $section = new Application_Model_Section();
            $sectionMapper = new Application_Model_SectionMapper();
            $oSection = $sectionMapper->fetchOne($sectionId,$section);
            $oSection->setVisibility($visibility);
            
            if($sectionMapper->save($oSection)) {
                // to co wyświetlimy przez echo json_encode będzie w zmiennej "response" w fukkcji w ajaxie
                echo json_encode(array('success' => true, 'msg' => 'Zmieniono widoczność sesji!'));
            }
            else {
                echo json_encode(array('success' => false, 'msg' => 'Wystąpił nieznany błąd podczas zmiany prywatności sekcji.'));
            }

        } catch (Exception $er) {
            echo json_encode(array('success' => false, 'msg' => $er->getMessage()));
        }
    }
    
    public function changesectionsorderAction()
    { 
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        try
        {
            // get ajax function parameters
            $page = $this->getRequest()->getParam('page');
            $data = $this->getRequest()->getParam('data');

            // Change a strange string from jq-ui sortable to normal array ;)
            $aData = explode('&section[]=', $data); // wyraz "section" został ustalony w atr. "id" tagu <li>
            $aData[0] = substr($aData[0], 10);

            // for each section's id's (array values), set order (array index)
            // order numbers are modified depending of page:

            $i = 1 + (10 * ( $page - 1 ));
            foreach($aData as $item) {
                $sectionId = $item;
                // update section
                $section = new Application_Model_Section();

                $tSection = new Application_Model_DbTable_Section();
                $tSection->updateOrder($sectionId, $i);

                $i++;
            }

            echo json_encode([
                'success' => TRUE,
            ]);
            
        } catch (Exception $ex) {
            echo json_encode([
                'success' => FALSE,
            ]);
        }
        


    }

}

