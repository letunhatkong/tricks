/**
 * Created by DELL on 5/13/2016.
 */

// Get uploaded Avatar
$(_ids.uploadAvatarId).change(function(e){
    var srcAva = URL.createObjectURL(e.target.files[0]);
    $(_ids.tmpAvaImage).attr("src",srcAva);
});