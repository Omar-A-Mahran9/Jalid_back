// start code for checking all checkboxes
$("#edit-select-all, #add-select-all").change(function () {

    let updatedForm = '#role_form_' + $(this).data('form-type');

    if ($(this).prop('checked')) {
        $(`${updatedForm} input[type="checkbox"]`).prop('checked', true).each((index, value) => $(value).val($(value).data('id')));
    } else {
        $(`${updatedForm} input[type="checkbox"]`).prop('checked', false).each((index, value) => $(value).val(null));
    }

});
// end code for checking all checkboxes


// start code for getting the rank data by ajax request
$('.edit-rank-btn').click(function () {

    let clickedBtn = $(this);
    let rankId = $(this).data('rank-id');

    clickedBtn.attr('disabled', true).attr("data-kt-indicator", "on")

    removeValidationMessages();

    /** turn of all checkboxes of the previous edited rank **/

    $('.edit-checkbox').prop('checked', false);

    $.ajax({
        url: "/dashboard/ranks/" + rankId + "/edit",
        method: "GET",
        success: function (response) {
            console.log(response['name_ar']);
            $("#kt_modal_update_rank").modal('show');

            clickedBtn.attr('disabled', false).removeAttr("data-kt-indicator")

            // set the rank name
            $("#name_ar_inp_edit").val(response['name_ar']);
            $("#name_en_inp_edit").val(response['name_en']);
            $("#completed_orders_count_inp_edit").val(response['completed_orders_count']);
            $("#commission_inp_edit").val(response['commission']);

            // set the route to the update form
            $("#rank_form_update").attr('action', `/dashboard/ranks/${rankId}`);

        },
    });
});
// start code for getting the rank data by ajax request

// start code for getting the role data by ajax request
$('.edit-role-btn').click(function () {

    let clickedBtn = $(this);
    let roleId = $(this).data('role-id');

    clickedBtn.attr('disabled', true).attr("data-kt-indicator", "on")

    removeValidationMessages();

    /** turn of all checkboxes of the previous edited role **/

    $('.edit-checkbox').prop('checked', false);

    $.ajax({
        url: "/dashboard/settings/roles/" + roleId,
        method: "GET",
        success: function (response) {
            console.log(response['name_ar']);
            $("#kt_modal_update_role").modal('show');

            clickedBtn.attr('disabled', false).removeAttr("data-kt-indicator")

            // set the role name
            $("#name_ar_inp_edit").val(response['name_ar']);
            $("#name_en_inp_edit").val(response['name_en']);

            // set the route to the update form
            $("#role_form_update").attr('action', `/dashboard/settings/roles/${roleId}`);
            response['role_abilities'].forEach(function (element) {

                let checkBox = $(`#edit_${element['name']}`);

                checkBox.prop('checked', true);
                checkBox.val(element['id']);

            });
        },
    });
});


// start code for customizing the checkbox value to its ability id
$(':checkbox').change(function () {
    if ($(this).prop('checked'))
        $(this).val($(this).data('id'));
    else
        $(this).val(null);
});
// end   code for customizing the checkbox value to its ability id
