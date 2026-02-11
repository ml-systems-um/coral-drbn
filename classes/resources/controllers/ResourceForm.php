<?php 
namespace resources\controllers;
use common\{NamedArguments};
use resources\controllers\{Resource, ResourceNote};
use resources\views as form;
class ResourceForm {
    public function __construct($resourceID = NULL){
        $resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));
        $existingResource = ($resourceID);
        // get resource acquisition for this resource 
        // at this point, there are none (resource not saved yet)
        // or only one (resource saved as draft)
        if($existingResource){
            $resourceAcquisitions = $resource->getResourceAcquisitions();
            $acquisitionValue = $resourceAcquisitions[0];
        }
        $resource->resourceAcquisition = ($existingResource) ? $acquisitionValue : FALSE;
		//Get the Initial Note for the Resource
        $resource->resourceNote = ($existingResource) ? $resource->getInitialNote() : new ResourceNote();

		$formDisplay = new form\ResourceForm($resource);
		//get all acquisition types for output in drop down
		$acquisitionTypeObj = new AcquisitionType();
		$formDisplay->acquisitionTypes = $acquisitionTypeObj->sortedArray();

		$formDisplay->display();
/*
		//get all resource formats for output in drop down
		$resourceFormatArray = array();
		$resourceFormatObj = new ResourceFormat();
		$resourceFormatArray = $resourceFormatObj->sortedArray();

		//get all resource types for output in drop down
		$resourceTypeArray = array();
		$resourceTypeObj = new ResourceType();
		$resourceTypeArray = $resourceTypeObj->allAsArray();


		//get all currency for output in drop down
		$currencyArray = array();
		$currencyObj = new Currency();
		$currencyArray = $currencyObj->allAsArray();

		//get all Order Types for output in drop down
		$orderTypeArray = array();
		$orderTypeObj = new OrderType();
		$orderTypeArray = $orderTypeObj->allAsArray();

		//get all Cost Details for output in drop down
		$costDetailsArray = array();
		$costDetailsObj = new CostDetails();
		$costDetailsArray = $costDetailsObj->allAsArray();


		$orgArray = $resource->getOrganizationArray();
		if (count($orgArray)>0){
			foreach ($orgArray as $org){
				$providerText = $org['organization'];
				$orgID = $org['organizationID'];
			}
		}else{
			$providerText = $resource->providerText;
			$orgID = '';
		}
            */
    }
}        
?>

