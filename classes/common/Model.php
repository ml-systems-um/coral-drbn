<?php 
    namespace common;
    use common\{DatabaseObject};
    class Model extends DatabaseObject {
        protected $queryCallType = 'assoc';
        protected $queryArray = [];
        private function setQueryCallType($queryType){
            $this->queryCallType = $queryType;
        }

        public function callModelQuery(){
            $queryString = implode(" ", $this->queryArray);
            $result = $this->db->processQuery($queryString, $this->queryCallType);
            return $result;
        }


    }

?>