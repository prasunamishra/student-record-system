document.addEventListener("DOMContentLoaded", function () {
    const input = document.getElementById("live-search");
    const resultBox = document.getElementById("result");

    if (!input) return;

    input.addEventListener("keyup", function () {
        let query = input.value;

        if (query.length < 2) {
            resultBox.innerHTML = "";
            return;
        }

        fetch("../ajax/student_search.php?query=" + query)
            .then(response => response.json())
            .then(data => {
                resultBox.innerHTML = "";
                data.forEach(name => {
                    resultBox.innerHTML += "<div>" + name + "</div>";
                });
            });
    });
});
