document.addEventListener("DOMContentLoaded", () => {
    const input = document.getElementById("student-search");
    const resultBox = document.getElementById("search-result");
    if (!input) return;
    input.addEventListener("keyup", () => {
        const q = input.value.trim();
        if (q.length < 2) {
            resultBox.innerHTML = "";
            return;
        }
        fetch("../ajax/student_search.php?q=" + encodeURIComponent(q))
            .then(res => res.json())
            .then(data => {
                resultBox.innerHTML = "";
                if (data.length === 0) {
                    resultBox.innerHTML = "<div>No results found</div>";
                    return;
                }
                data.forEach(s => {
                    resultBox.innerHTML += `
                        <div>
                            <strong>${s.name}</strong><br>
                            Roll: ${s.roll_number} |
                            Course: ${s.course_name ?? '—'}
                        </div>
                    `;
                });
            });
    });
});
