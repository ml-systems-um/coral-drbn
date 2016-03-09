/*
**************************************************************************************************************************
** CORAL Organizations Module v. 1.0
**
** Copyright (c) 2010 University of Notre Dame
**
** This file is part of CORAL.
**
** CORAL is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
**
** CORAL is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License along with CORAL.  If not, see <http://www.gnu.org/licenses/>.
**
**************************************************************************************************************************
*/

 $(function(){

	  	 
 });
 





function updateYTDOverride(titleStatsYTDIDValue, overrideColumn){

  
  if (validateForm(overrideColumn + "_" + titleStatsYTDIDValue) === true) {

	  $.ajax({
		 type:       "POST",
		 url:        "ajax_processing.php?action=updateYTDOverride",
		 cache:      false,
		 data:       { overrideCount: $("#" + overrideColumn + "_" + titleStatsYTDIDValue).val(), overrideColumn: overrideColumn, titleStatsYTDID: titleStatsYTDIDValue },
		 success:    function(html) {
		 	$('#span_' + titleStatsYTDIDValue + '_response').html(html);
		 	

			 // close the span in 3 secs
			 setTimeout("emptyResponse('" + titleStatsYTDIDValue + "');",3000); 		 	
		 }


	 });
	 
   }

}



//validates fields
function validateForm (columnName){
	myReturn=0;
	if (!validateRequired(columnName, 'Count is required.')) myReturn="1";
	if (myReturn == "1"){
		return false;
	}else{
		return true;
	}
}




 function emptyResponse(spanname){
 	$('#span_' + spanname + '_response').html("");
 }
 