function fetchStudents(department) {
    fetch(`fetch_students.php?department=${department}`)
        .then(res => res.json())
        .then(data => displayStudents(data, department));
}

function displayStudents(data, department) {
    let container = document.getElementById("students");
    container.innerHTML = `<h2>${department} Students</h2>`;

    if (data.length === 0) {
        container.innerHTML += "<p>No students found in this department.</p>";
        return;
    }

    data.forEach(student => {
        let card = document.createElement("div");
        card.classList.add("student-card");
        card.innerHTML = `
            <img src="${student.photo_url}" alt="Profile">
            <h3>${student.name}</h3>
            <h4>${student.roll_no}</h4>
            <p>${student.department}</p>
            <p>CGPA: ${student.cgpa}</p>
        `;
        container.appendChild(card);
    });
}
