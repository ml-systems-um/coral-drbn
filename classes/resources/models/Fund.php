<?php 
namespace resources\models;
use common\{Model};
class Fund extends Model  {
	public function allAsArray() {
        $this->setQueryCallType('assoc');
        $this->queryArray = [
            "SELECT *",
            "FROM Fund",
            "ORDER BY shortName",
        ];
        return $this->callModelQuery();
	}

	//returns number of children for this particular contact role
	public function getNumberOfChildren(){
        $this->setQueryCallType('assoc');
        $this->queryArray = [
            "SELECT COUNT(*) AS childCount",
            "FROM ResourcePayment",
            "WHERE fundID = '{$this->fundID}'"
        ];
        return $this->callModelQuery();
	}

    public function getFundIDFromFundCode($fundCode) {
        $this->setQueryCallType('assoc');
        $this->queryArray = [
            "SELECT fundID",
            "FROM Fund",
            "WHERE fundCode = '{$fundCode}';",
        ];
        return $this->callModelQuery();
    }

	//returns array of archived objects
    public function getUnArchivedFunds(){
        $this->setQueryCallType('assoc');
        $this->queryArray = [
            "SELECT *",
            "FROM Fund",
            "WHERE archived IS NULL OR archive = 0",
            "ORDER BY shortName",
        ];
        return $this->callModelQuery();
    }

    //get all unarchived ones and include the archived if it was selected previously
    public function getUnArchivedFundsForCostHistory($fundID){
        $this->setQueryCallType('assoc');
        $this->queryArray = [
            "SELECT *",
            "FROM Fund",
            "WHERE fundID = {$fundID}",
            "OR archived IS NULL",
            "OR archived = 0",
            "ORDER BY shortName",
        ];
        return $this->callModelQuery();
    }
}
?>
