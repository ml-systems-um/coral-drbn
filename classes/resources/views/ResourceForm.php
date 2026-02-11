<?php 
namespace resources\views;
class ResourceForm {
    protected $resource;
    public $acquisitionTypes;
    
    public function __construct($resource){ 
        $this->resource = $resource;
    }
    public function display() { ?>

        <div id='div_resourceSubmitForm'>
		    <form id='resourcePromptForm' class="large">
                <input type='hidden' id='organizationID' value='<?php echo $this->resource->orgID; ?>' />
                <input type='hidden' id='editResourceID' value='<?php echo $this->resource->resourceID; ?>' />
                <h2><?php echo $this->resource->formTitle; ?></h2>
                <p class='required'><?php echo _("* required fields");?></p>
		
                <h3><?php echo _("Product");?></h3>
		        <div class="flex flex-auto">
                    <div class="form-grid">
                        <label for='titleText'><?php echo _("Name:");?> 
                            <span class='required'>*</span>
                        </label>
                        <input required type='text' id='titleText' class='changeInput' value="<?php echo $this->resource->titleText; ?>" aria-describedby="span_error_titleText" />
                        <span id='span_error_titleText' class='error'></span>
                    
                        <label for='descriptionText'><?php echo _("Description:");?></label>
                        <textarea rows='3' id='descriptionText'><?php echo $this->resource->descriptionText; ?></textarea>
                    
                        <label for='providerText'><?php echo _("Provider:");?></label>
                        <input type='text' id='providerText' class='changeInput' value='<?php echo $this->resource->providerText; ?>' aria-describedby="span_error_providerText" />
                        <span id='span_error_providerText' class='error'></span>
                        
                        <label for='resourceURL'><?php echo _("URL:");?></label>
                        <input type='url' id='resourceURL' class='changeInput' value='<?php echo $this->resource->resourceURL; ?>' aria-describedby="span_error_resourceURL" />
                        <span id='span_error_resourceURL' class='error'></span>
                        
                        <label for='resourceAltURL'><?php echo _("Alt URL:");?></label>
                        <input type='url' id='resourceAltURL' class='changeInput' value='<?php echo $this->resource->resourceAltURL; ?>' aria-describedby="span_error_resourceAltURL" />
                        <span id='span_error_resourceAltURL' class='error'></span>
                        
                        <fieldset class="subgrid">	
                            <legend class="fw-normal"><?php echo _("Format");?> <span class='required'>*</span></legend>
                            <div class="form-group">
                                <p id='span_error_resourceFormatID' class='error'></p>
                            
                                <ul class="unstyled">
                                <?php
                                    foreach ($this->resourceFormatArray as $resourceFormat){
                                        $checked = '';
                                        //determine default
                                        if ($resourceID){
                                            if ($resourceFormat['resourceFormatID'] == $this->resource->resourceFormatID) $checked = 'checked';
                                        //otherwise default to electronic
                                        }else{
                                            if (strtoupper($resourceFormat['shortName']) == 'ELECTRONIC') $checked = 'checked';
                                        }

                                        echo "<li class='checkbox'><input required type='radio' name='resourceFormatID' id='resourceFormatID-" . $resourceFormat['resourceFormatID'] . "' value='" . $resourceFormat['resourceFormatID'] . "' " . $checked . " aria-describedby='span_error_resourceFormatID' />";
                                        echo "<label for='resourceFormatID-" . $resourceFormat['resourceFormatID'] . "'>" . $resourceFormat['shortName'] . "</label></li>";
                                    }

                                    ?>
                                    </ul>
                                </div>
                        </fieldset>
                        <fieldset class="subgrid">
                            <legend class="fw-normal"><?php echo _("Acquisition Type");?> <span class='required'>*</span></legend>
                            <div class="form-group">
                                <ul class="unstyled">
                                    <?php
                                    $systemDefaultValueName = "PAID";
                                    foreach ($this->acquisitionTypes as $acquisitionType){
                                        $acquisitionTypeName = $acquisitionType['shortName'];
                                        $acquisitionID = $acquisitionType['acquisitionTypeID'];
                                        $existingResource = ($this->resource->resourceID) ?? FALSE;
                                        $currentlySelectedAcqType = ($this->resource->resourceAcquisition->resourceAcquisitionID) ?? FALSE;
                                        $currentTypeMatch = ($acquisitionID == $currentlySelectedAcqType);
                                        $defaultMatched = (strtoupper($acquisitionTypeName) == $systemDefaultValueName);
                                        $newResourceCheck = (!$existingResource && $defaultMatched);
                                        $existingResourceCheck = ($existingResource && $currentTypeMatch);
                                        $checked = ($newResourceCheck || $existingResourceCheck) ? 'checked' : '';
                                        echo "<li class='checkbox'><input required type='radio' name='acquisitionTypeID' id='acquisitionTypeID-{$acquisitionID}' value='{$acquisitionID}' {$checked} />";
                                        echo "<label for='acquisitionTypeID-{$acquisitionID}'>{$acquisitionTypeName}</label></li>\n";
                                    }

                                    ?>
                                </ul>
                            </div>
                        </fieldset>
	                </div>
                    <div class="form-grid">
                        <fieldset class="subgrid">
                            <legend class="fw-normal"><?php echo _("Resource Type");?> <span class='required'>*</span></legend>
                            <div class="form-group">
                                <p id='span_error_resourceTypeID' class='error'></p>
                                <ul class="unstyled">
                                    <?php
                                    
                                    foreach ($resourceTypeArray as $resourceType){

                                        $checked='';
                                        //determine default checked
                                        if ($resourceID){
                                            if (strtoupper($resourceType['resourceTypeID']) == $this->resource->resourceTypeID) $checked = 'checked';
                                        }

                                        echo "<li class='checkbox'><input required type='radio' name='resourceTypeID' id='resourceTypeID-" . $resourceType['resourceTypeID'] . "' value='" . $resourceType['resourceTypeID'] . "' " . $checked . " aria-describedby='span_error_resourceTypeID' />";
                                        echo "<label for='resourceTypeID-" . $resourceType['resourceTypeID'] . "'>" . $resourceType['shortName'] . "</label></li>\n";

                                    }

                                    ?>
                                </ul>
                            </div>
                        </fieldset>
                        <label for="noteText"><?php echo _("Notes");?></label>
                        <textarea rows='5' id='noteText' name='noteText'><?php echo $resourceNote->noteText; ?></textarea>
                    </div>
		        </div>
                <p class="actions">
                    <input type='submit' value='<?php echo _("Submit to Workflow");?>' id='progress' class='submitResource submit-button primary'>
                    <input type='button' value='<?php echo _("Save to Queue");?>' id='save' class='submitResource secondary'>
                    <input type='button' value='<?php echo _("Cancel");?>' onclick="myCloseDialog('#NewResourceForm')"  class='secondary'>
                </p>
		    </form>
		</div>
		<script type="text/javascript" src="js/forms/resourceNewForm.js?random=<?php echo rand(); ?>"></script>
    <?php }
}

?>
