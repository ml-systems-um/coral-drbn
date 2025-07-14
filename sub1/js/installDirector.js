$(document).ready(function(){
    if(typeof versionsToLoad !== undefined){

        for(i=0;i<versionsToLoad.versions.length;i++){
            let version = versionsToLoad.versions[i];
            upgradeToVersion(version);
            break;
        }
    }
});

function upgradeToVersion(version){
    $.ajax({
        type: "POST",
        url: "classes/install/Director.php",
        data: {'version': version},
        dataType: "html",
        success: function(data){
            $('#pageBody').html(data);
        },
    }).fail(function(jqXHR){
        console.log(jqXHR);
    });
}
