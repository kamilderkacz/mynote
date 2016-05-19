<?php

class NoteController extends Zend_Controller_Action {

    public function init() {
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
//        $this->initView();
        $this->view->msg = $this->_flashMessenger->getMessages(); // Pobranie wiadomości flashowej
        
        $auth = Zend_Auth::getInstance();
        if( $auth->hasIdentity() ) {
            $this->view->storage = $auth->getIdentity();
        }
        
    }
    
    // Pokazywanie wszystkich
    public function indexAction() {
        try {
            $pageTitleSession = new Zend_Session_Namespace('pageTitle'); 
            $pageTitleSession->pageTitle = 'Moje notatki';
            $_SESSION['navbar']['przegladaj'] = 1;
            $params = $this->getRequest()->getParams();
            $section_id = $params['section_id'];
            $note_map = new Application_Model_NoteMapper();
            $section_map = new Application_Model_SectionMapper();
            $section = new Application_Model_Section();
            $oSection = $section_map->fetchOne($section_id, $section);
            $aNotes = $note_map->fetchAll(array('note_section_id='.$section_id, 'note_removed=0'), 'note_creation_datetime DESC');
            $this->view->section = $oSection; // obiekt sekcji

            $this->view->notes = $aNotes; // tablica notatek sekcji w postaci obiektów 
                    // Zastosowanie paginacji
            $itemsPerPage = $itemsPerPage = (isset($_SESSION['SectionController']['itemsPerPage']))? $_SESSION['SectionController']['itemsPerPage'] : 10 ;
            $paginator = Zend_Paginator::factory($aNotes);
            $paginator->setItemCountPerPage($itemsPerPage)
                      ->setCurrentPageNumber($this->_getParam('page', 1))
                      ->setDefaultPageRange(8);
            $this->view->paginator = $paginator;
        } catch(Exception $e) {
            $this->_flashMessenger->addMessage('danger');
            $this->_flashMessenger->addMessage('Wystąpił błąd podczas wyświetlania notatek. ' . $e->getMessage());
        }
    }
    // Pokazywanie jednej
    public function showAction() {
        $pageTitleSession = new Zend_Session_Namespace('pageTitle'); 
        $pageTitleSession->pageTitle = 'Pokaż notatkę';
        $_SESSION['navbar']['przegladaj'] = 1;
        
        $params = $this->getRequest()->getParams();
        $note_id = $params['note_id'];
        
        $note_map = new Application_Model_NoteMapper();
        $note = new Application_Model_Note();
        $oNote = $note_map->fetchOne($note_id, $note);
        //separacja
        $section = new Application_Model_Section();
        $section_map = new Application_Model_SectionMapper();
        $oSection = $section_map->fetchOne($oNote->getSectionId(), $section);
        
        $this->view->note = $oNote;
        $this->view->section = $oSection;
    }
    // Dodawanie
    public function addAction() {
        $pageTitleSession = new Zend_Session_Namespace('pageTitle'); 
        $pageTitleSession->pageTitle = 'Dodawanie notatki';
        $_SESSION['navbar']['przegladaj'] = 1;
        
        try {
            $request = $this->getRequest();
            $oForm = new My_MyForm_Note_NoteForm();
            $oForm->setDecorators(array(array('ViewScript', array('viewScript' => 'note/form.phtml'))));

            if ($this->getRequest()->isPost()) {
                
                if ($oForm->isValid($request->getPost())) {
                    $note = new Application_Model_Note($oForm->getValues());
                    $note->setSectionId($request->getParam('section_id'))
                         ->setRemoved(0)
                         ->setAuthorId($_SESSION['Zend_Auth']['storage'][0]->user_id);
                    $mapper = new Application_Model_NoteMapper();
                    $mapper->save($note); // zapis do db
                    $this->_flashMessenger->addMessage('success');
                    $this->_flashMessenger->addMessage('Notatka dodana.');
                    return $this->_helper->redirector->gotoRoute(array('section_id' => $note->getSectionId()), 'note_note_index');
                }
            }
        } catch (Exception $e) {
            $this->_flashMessenger->addMessage('danger');
            $this->_flashMessenger->addMessage('Wystąpił błąd podczas dodawania notatki. ' . $e->getMessage());
        }
        $this->view->form = $oForm;
        $sectionMap = new Application_Model_SectionMapper();
        $section = new Application_Model_Section();
        $oSection = $sectionMap->fetchOne($request->getParam('section_id'), $section);
        $this->view->section_name = $oSection->getFullname();
    }
    // Edycja
    public function editAction() { 
        $pageTitleSession = new Zend_Session_Namespace('pageTitle'); 
        $pageTitleSession->pageTitle = 'Edycja notatki';
        $_SESSION['navbar']['przegladaj'] = 1;
        
        try {
            $request = $this->getRequest();
            $note_id = $request->getParam('note_id');
            $oForm = new My_MyForm_Note_NoteForm();
            $oForm->setDecorators(array(array('ViewScript', array('viewScript' => 'note/form.phtml'))));

            $oNote = new Application_Model_Note();
            $oNoteMap = new Application_Model_NoteMapper();
            $note = $oNoteMap->fetchOne($note_id, $oNote);
            
            if ($this->getRequest()->isPost()) {
                
                if ($oForm->isValid($request->getPost())) {
                    $data = $oForm->getValues(); // pobranie danych z forma
                    $note->setTitle($data['title']);
                    $note->setContent($data['content']);
                    $oNoteMap->save($note);
                    
                    $this->_flashMessenger->addMessage('success');
                    $this->_flashMessenger->addMessage('Notatka zedytowana');
                    
                    return $this->_helper->redirector->gotoRoute(array('section_id' => $oNote->getSectionId()), 'note_note_index');
                }
            }
            else {
                // musze tak to zrobić... bo konwersja chronionych paramsów zwraca * w nazwach ;(
                $note2 = array( 
                    'title' => $note->getTitle(),
                    'content' => $note->getContent() 
                );
                $oForm->populate($note2);
            }
        } catch (Exception $e) {
            $this->_flashMessenger->addMessage('danger');
            $this->_flashMessenger->addMessage('Wystąpił błąd podczas edycji notatki. ' . $e->getMessage());
        }
        $this->view->form = $oForm;
    }
    // Usuwanie jednej
    public function deleteAction() {
        //Zapobiega wywołaniu widoku
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        try {
            $request = $this->getRequest();
            $note_id = $request->getParam('note_id');
            
            $note = new Application_Model_Note();
            $oNoteMapper = new Application_Model_NoteMapper();
            $oNote = $oNoteMapper->fetchOne($note_id, $note);
            $oNote->setId($note_id)
                  ->setRemoved(1);
//            $oNoteMapper->delete($note_id); 
            $oNoteMapper->save($oNote);

            $this->_flashMessenger->addMessage('success');
            $this->_flashMessenger->addMessage('Notatka została usunięta.');
            
            $this->_helper->redirector->gotoRoute(array('section_id' => $oNote->getSectionId()), 'note_note_index');
            
        } catch (Exception $e) {
            $this->_flashMessenger->addMessage('danger');
            $this->_flashMessenger->addMessage('Wystąpił błąd podczas usuwania notatki. ' . $e->getMessage());
        }
    }

}
