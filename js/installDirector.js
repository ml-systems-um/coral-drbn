$(document).ready(function(){
    let noVersionsFound = (typeof versionsToLoad == 'undefined');
    if(noVersionsFound){
        let htmlError = "<p>List of versions to load not found in installer process. Please contact the developers.</p>";
        $('body#pageBody').html(htmlError);
        return false;
    }
    for(i=0;i<versionsToLoad.versions.length;i++){
        let version = versionsToLoad.versions[i];
        upgradeToVersion(version);
        break;
    }
});

function upgradeToVersion(version){
    $.ajax({
        type: "POST",
        url: "classes/install/Director.php",
        data: {'version': version},
        dataType: "html",
        success: function(data){
            console.log(data);
            $('#pageBody').html(data);
        },
    }).fail(function(jqXHR){
        console.log(jqXHR);
    });
    
}
