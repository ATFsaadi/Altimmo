document.addEventListener("DOMContentLoaded", function () {
    // Vérification si AOS est défini
    if (typeof AOS !== "undefined") {
        AOS.init({
            duration: 1200,  // Animation légèrement plus fluide
            once: true,
            easing: 'ease-in-out',
            delay: 200  // Délai pour un effet progressif sur plusieurs éléments
        });
    } else {
        console.error("AOS n'est pas chargé !");
    }

    // Animation fluide de la navbar au scroll
    const navbar = document.querySelector(".navbar");
    let lastScrollTop = 0;

    function handleNavbarScroll() {
        let scrollTop = window.scrollY;

        if (scrollTop > 50) {
            navbar.classList.add("scrolled");
        } else {
            navbar.classList.remove("scrolled");
        }

        lastScrollTop = scrollTop;
    }

    // Ajout d'une animation plus fluide avec requestAnimationFrame
    window.addEventListener("scroll", function () {
        requestAnimationFrame(handleNavbarScroll);
    });
});


document.addEventListener("DOMContentLoaded", function () {
    const advancedSearchToggle = document.querySelector("[data-bs-target='#advancedSearch']");
    const searchButtonContainer = document.createElement("div");
    searchButtonContainer.classList.add("search-btn-container");

    const searchButton = document.querySelector(".search-bar .btn-primary").parentElement;
    searchButtonContainer.appendChild(searchButton);

    // Placer le bouton centré sous la barre de recherche par défaut
    document.querySelector(".search-bar form").appendChild(searchButtonContainer);

    advancedSearchToggle.addEventListener("click", function () {
        setTimeout(() => {
            const advancedSearch = document.querySelector("#advancedSearch");

            if (advancedSearch.classList.contains("show")) {
                // Déplacer le bouton centré sous la recherche avancée
                advancedSearch.appendChild(searchButtonContainer);
            } else {
                // Remettre le bouton centré sous la barre de recherche
                document.querySelector(".search-bar form").appendChild(searchButtonContainer);
            }
        }, 350); // Ajout d'un délai pour laisser l'animation se terminer
    });
});
document.querySelector("form").addEventListener("submit", function (event) {
    let password = document.getElementById("password").value;
    let confirmPassword = document.getElementById("confirm-password").value;

    if (password !== confirmPassword) {
        event.preventDefault(); // Empêche l'envoi du formulaire
        alert("Les mots de passe ne correspondent pas !");
    }
});







