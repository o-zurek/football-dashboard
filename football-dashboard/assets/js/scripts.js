function openTab(evt, tabName) {
    let i, tabcontent, tablinks;

    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].classList.remove("active");
    }

    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.classList.add("active");
}

document.addEventListener("DOMContentLoaded", () => {
    document.querySelector(".tablinks").click();
});

function filterMatches() {
    let ligue = document.getElementById("filterLigue").value.toLowerCase();
    let club = document.getElementById("filterClub").value.toLowerCase();
    let date = document.getElementById("filterDate").value;

    document.querySelectorAll("#matchTable tr.data").forEach(row => {
        let show = true;

        if (ligue && !row.dataset.ligue.includes(ligue)) show = false;
        if (club && !row.dataset.club.includes(club)) show = false;
        if (date && !row.dataset.date.includes(date)) show = false;

        row.style.display = show ? "" : "none";
    });
}
