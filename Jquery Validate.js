/**
 * Get plugin and document
 * http://jqueryvalidation.org/documentation/
 * @type {{formAddUserInAdminPage: string, saveAddUserInAdminPageButton: string, getUserName: string, checkExistsUser: string, uploadAvatarId: string, tmpAvaImage: string, clickAddUserButton: string}}
 * @private
 */

var _ids = {
    formAddUserInAdminPage:'#add_user_admin_form',
    saveAddUserInAdminPageButton: "#addUserSubmitButton",
    getUserName: "#userNameAddUserForm",
    checkExistsUser: "#checkExistsUser",
    uploadAvatarId : "#uploadAvatarId",
    tmpAvaImage: "#tmpAvaImage",
    clickAddUserButton: "#clickAddUserButton"
};

var editForm = {
    clickUserName: ".clickUserName",
    firstName:'input[name=firstNameEdit]',
    lastName: 'input[name=lastNameEdit]',
    username: 'input[name=usernameEdit]',
    email: 'input[name=emailEdit]',
    phone: 'input[name=phoneEdit]',
    female: 'input[name=genderEdit][data-gender=0]',
    male: 'input[name=genderEdit][data-gender=1]',
    clickUploadButton: "#clickUploadAvaEditForm",
    uploadAvatarId : "#uploadAvatarEdit",
    tmpAvaImage: "#tmpAvaImageEdit"
};

var addForm = {
    firstName:'input[name=firstName]',
    lastName: 'input[name=lastName]',
    username: 'input[name=username]',
    password: 'input[name=password]',
    re_password: 'input[name=re_password]',
    email: 'input[name=email]',
    phone: 'input[name=phone]',
    clickUploadButton: "#clickUploadAvaAddUserForm",
    uploadAvatarId : "#uploadAvatar",
    tmpAvaImage: "#tmpAvaImage"
};

var infoFormEvent = {
    init: function () {
        this.validationFormAddUserInAdminPage();
        this.saveAddUserInAdminPageButtonClick();
        this.clickEditUserName();
    },
    validationFormAddUserInAdminPage: function () {
        $(_ids.formAddUserInAdminPage).validate({
            rules: {
                firstName: {required: true},
                lastName: {required: true},
                username: {required: true},
                email: {required: true,email: true},
                password: { required: true },
                re_password: { required: true, equalTo: '#firstPassword' },
                phone: { required: true, number: true},
                existsUser:  {equalTo: '#valueExistsUser'},
                uploadAvatar: {required: true}
            },
            messages: {
                firstName: {required: "First name is required."},
                lastName: {required: "Last name is required."},
                username: {required: "First name is required."},
                password: {required: "Password is required."},
                re_password: {required: "Re-password is required."},
                phone : { required: "Phone is required.", number: "Not number"},
                email: {required: "Email is required."},
                existsUser:  {equalTo: "Username of Email is exists."},
                uploadAvatar: {required: "Avatar is required."}
            }
        });
    },
    saveAddUserInAdminPageButtonClick: function () {
        // Username input is changed (add user form)
        $(_ids.getUserName).change(function(){
            var username = $(addForm.username).val();
            var email = $(addForm.email).val();
            infoFormEvent.checkExistsUser(username, email);
        });
        // Email input is changed (add user form)
        $(addForm.email).change(function(){
            var username = $(addForm.username).val();
            var email = $(addForm.email).val();
            infoFormEvent.checkExistsUser(username, email);
        });
        // Click upload Avatar button (add user form)
        $(addForm.clickUploadButton).click(function(){
            $(addForm.uploadAvatarId).trigger("click");
        });
        // Click upload Avatar button (Edit user form)
        $(editForm.clickUploadButton).click(function(){
            $(editForm.uploadAvatarId).trigger("click");
        });
        // Get uploaded Avatar (add user form)
        $(addForm.uploadAvatarId).change(function(e){
            var srcAva = URL.createObjectURL(e.target.files[0]);
            $(addForm.tmpAvaImage).attr("src",srcAva);
        });
        // Click add User button (admin page)
        $(_ids.clickAddUserButton).click(function(){
            infoFormEvent.resetAddUserForm();
        });
        // Click Submit Button (add user form)
        $(_ids.saveAddUserInAdminPageButton).on("click", function () {
            if ($(_ids.formAddUserInAdminPage).valid()) {
                $(_ids.formAddUserInAdminPage).submit();
            }
        });
    },
    checkExistsUser: function (username, email) {
        $.ajax({
            url: getBaseUrl() +'/admin/default/existsUser',
            type: 'post',
            data:{
                'username':username,
                'email':email
            },
            cache: false,
            success: function(data) {
                var checkExists = (data === "true");
                infoFormEvent.setExistsUser(checkExists);
                return data;
            }
        });
    },
    setExistsUser: function (check) {
        $(_ids.checkExistsUser).val(check);
        $(_ids.formAddUserInAdminPage).valid();
    },
    clickEditUserName: function(){
        // Click username link
        $(editForm.clickUserName).click(function(){
            // Get User Id
            var userId = $(this).attr('data-user-id');
            // Load AJAX User Data
            infoFormEvent.loadAjaxByUserId(userId);
        });
    },
    loadAjaxByUserId: function(uId) {
        $.ajax({
            type: "post",
            url: getBaseUrl() +'/admin/default/getUser',
            data: { 'id' : uId },
            cache: false,
            success: function(data){
                var dataObj = $.parseJSON(data);
                infoFormEvent.pushUserToEditForm(dataObj);
            }
        });
    },
    pushUserToEditForm: function(data) {
        try {
            $(editForm.firstName).val(data.firstName);
            $(editForm.lastName).val(data.lastName);
            $(editForm.username).val(data.username);
            $(editForm.email).val(data.email);
            $(editForm.phone).val(data.phone);

            if (data.gender === "1") {
                $(editForm.male).attr("checked","checked");
                $(editForm.female).removeAttr("checked");
            } else {
                $(editForm.male).removeAttr("checked");
                $(editForm.female).attr("checked","checked");
            }

            var avaSrc = (data.avatarPath && data.avatarPath != "") ? '/upload/avatars/'+data.avatarPath : "/images/defaultUser.png";
            $(editForm.tmpAvaImage).attr("src", avaSrc);
        } catch (err) {
            console.log(err);
        }
    },
    resetAddUserForm: function(){
        $(addForm.firstName).val("");
        $(addForm.lastName).val("");
        $(addForm.username).val("");
        $(addForm.password).val("");
        $(addForm.re_password).val("");
        $(addForm.email).val("");
        $(addForm.phone).val("");
        $(addForm.tmpAvaImage).attr("src", "/images/defaultUser.png");
        var validator = $(_ids.formAddUserInAdminPage).validate();
        validator.resetForm();
    }
};

$(document).ready(function () {
    "use strict";
    infoFormEvent.init();
});