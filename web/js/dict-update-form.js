$(function () {
    $(".dictionary-table").on("click", ".delete-button", function () {
        var input = $("#removeFields");
        var data = input.val();
        data = JSON.parse(data);
        var word = $(this).parents("tr").children("td:first").text();
        if (data.words === undefined) {
            data.words = [];
        }
        data.words.push(word);
        input.val(JSON.stringify(data));
        console.log(input.val());
    });
});
