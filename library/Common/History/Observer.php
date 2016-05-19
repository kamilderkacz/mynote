<?php
/*
 * @author Adam Nielski
 * @copyright Web-Soft.com.pl
 * @version 1.0
 * @package common
 * @subpackage history
 */
class Common_History_Observer implements Common_Db_Row_Observer_Interface {

    protected $_oObserve = null;

    public function event( $vAction, Common_Db_Row_Observe_Interface $oObserve ) {
        $this->_oObserve = $oObserve;
        switch ($vAction) {
            case '_insert' :
                    $this->_event( Common_History_Table_History::OPERATION_INSERT );
                break;
            case '_update' :
                    $this->_event( Common_History_Table_History::OPERATION_UPDATE );
                break;
            case '_delete' :
                    $this->_event( Common_History_Table_History::OPERATION_DELETE );
                break;
        }
    }

    protected function _event( $iOperation ) {
        $oHistoryTab = new Common_History_Table_History();
        $oHistoryRow = $oHistoryTab->fetchNew();
        $oHistoryRow->history_operation_id = $iOperation;
        $oHistoryRow->history_change_from = '';//@todo w przyszłości dorobić - na razie nie ma sensu wczytywać obiekt przed i po
        $oHistoryRow->history_change_to = serialize(array_intersect_key($this->_oObserve->getData(), $this->_oObserve->getModifiedFields()));
        $oHistoryRow->history_change = serialize($this->_oObserve->getModifiedFields());
        $oHistoryRow->history_class = get_class($this->_oObserve);
        $oHistoryRow->history_data = time();
        $oHistoryRow->save();
    }
}
