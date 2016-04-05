$(function () {
    var submit = $("#saveButton");
    submit.prop("disabled", true);
    $(".dictionary-table").on("click", ".delete-button", function () {
        submit.prop("disabled", false);
        $(this).parents("tr").hide();
        var input = $("#removeFields");
        var data = input.val();
        data = JSON.parse(data);
        var word = $(this).parents("tr").children("td:first").text();
        if (data === undefined) {
            data = [];
        }
        data.push(word);
        input.val(JSON.stringify(data));
    });
});
