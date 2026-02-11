<?php 
namespace resources\models;
use common\{Model};
class Resource extends Model  {
    //returns resource object by ebscoKbId
    public function getResourceByEbscoKbID($ebscoKbId){
        $this->setQueryCallType('assoc');
        $this->queryArray = [
            "SELECT *",
            "From Resource",
            "WHERE ebscoKbID = $ebscoKbID",
            "LIMIT 0,1",
        ];
        return $this->callModelQuery();
    }

    //returns resource objects by title
    public function getResourcesByTitle($title){
        $this->setQueryCallType('assoc');
        $upperCaseTitle = strtoupper($title);
        $normalizedTitle = str_replace("'", "''", $upperCaseTitle);
        $this->queryArray = [
            "SELECT *",
            "FROM Resource",
            "WHERE UPPER(titleText) = '{$normalizedTitle}'",
            "ORDER BY 1",
        ];

        return $this->callModelQuery();
    }

    public function getResourceAcquisitions(){
        $this->setQueryCallType('assoc');
        $this->queryArray = [
            "SELECT *",
            "FROM ResourceAcquisition",
            "WHERE resourceID = '{$this->resourceID}'",
            "ORDER BY subscriptionStartDate DESC, subscriptionEndDate DESC",
        ];

        return $this->callModelQuery();
    }

    public function getInitialNote($initialNoteID){
        $this->setQueryCallType('assoc');
        $this->queryArray = [
            "SELECT *",
            "FROM ResourceNote",
            "WHERE entityID = '{$this->resourceID}'",
            "AND noteTypeID = '{$initialNoteID}'",
            "ORDER BY noteTypeID DESC",
            "LIMIT 0,1",
        ];

        return $this->callModelQuery();
    }

}        
?>
