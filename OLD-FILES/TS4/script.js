document.addEventListener("DOMContentLoaded", function () {
    const restaurantList = document.querySelectorAll(".restaurant-card");
    const detailSection = document.getElementById("restaurant-detail");
    const mainMenuBtn = document.getElementById("main-menu-btn");

    // Updated Restaurant Data
    const restaurants = {
        "gabbel": {
            name: "Gabbel Löffel",
            image: "img/dr1.jpg",
            address: "235 Glendale Ave, Meridien City",
            phone: "490 49 4000",
            website: "GANDG.RES",
            category: "Swiss",
            hours: "10AM - 10PM",
            price: "$$$",
            description: `Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus imperdiet, nulla et dictum interdum, nisi lorem egestas odio, vitae scelerisque enim ligula venenatis dolor. Maecenas nisl est, ultrices nec congue eget, auctor vitae massa. Fusce luctus vestibulum augue ut aliquet. Mauris ante ligula, facilisis sed ornare eu, lobortis in odio. Praesent convallis urna a lacus interdum ut hendrerit risus congue. Nunc sagittis dictum nisi, sed ullamcorper ipsum dignissim ac. In at libero sed nunc venenatis imperdiet sed ornare turpis.

            Donec vitae dui eget tellus gravida venenatis. Integer fringilla congue eros non fermentum. Sed dapibus pulvinar nibh tempor porta. Cras ac leo purus. Mauris quis diam velit.`
        },
        "gary-gari": {
            name: "Gary Gari",
            image: "img/dr2.jpg",
            address: "76 No. Highland Ave, Meridien City",
            phone: "490 24 6709",
            website: "GANDG.RES",
            category: "Japanese",
            hours: "5PM - 10PM",
            price: "$$",
            description: `Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus imperdiet, nulla et dictum interdum, nisi lorem egestas odio, vitae scelerisque enim ligula venenatis dolor. Maecenas nisl est, ultrices nec congue eget, auctor vitae massa. Fusce luctus vestibulum augue ut aliquet. Mauris ante ligula, facilisis sed ornare eu, lobortis in odio. Praesent convallis urna a lacus interdum ut hendrerit risus congue. Nunc sagittis dictum nisi, sed ullamcorper ipsum dignissim ac. In at libero sed nunc venenatis imperdiet sed ornare turpis.

            Donec vitae dui eget tellus gravida venenatis. Integer fringilla congue eros non fermentum. Sed dapibus pulvinar nibh tempor porta. Cras ac leo purus. Mauris quis diam velit.`
        },
        "il-piatto": {
            name: "Il Piatto",
            image: "img/dr3.jpg",
            address: "1213 Shoutout Way, Meridien City",
            phone: "490 42 1204",
            website: "ILPIATTO RES",
            category: "Italian",
            hours: "11AM - 11PM",
            price: "$$",
            description: `Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus imperdiet, nulla et dictum interdum, nisi lorem egestas odio, vitae scelerisque enim ligula venenatis dolor. Maecenas nisl est, ultrices nec congue eget, auctor vitae massa. Fusce luctus vestibulum augue ut aliquet. Mauris ante ligula, facilisis sed ornare eu, lobortis in odio. Praesent convallis urna a lacus interdum ut hendrerit risus congue. Nunc sagittis dictum nisi, sed ullamcorper ipsum dignissim ac. In at libero sed nunc venenatis imperdiet sed ornare turpis.

            Donec vitae dui eget tellus gravida venenatis. Integer fringilla congue eros non fermentum. Sed dapibus pulvinar nibh tempor porta. Cras ac leo purus. Mauris quis diam velit.`
        },
        "pierre-platters": {
            name: "Pierre Platters",
            image: "img/dr4.jpg",
            address: "68 8th Ave, Meridien City",
            phone: "490 69 3080",
            website: "PIERREPRES",
            category: "Fusion",
            hours: "11AM - 11PM",
            price: "$$$",
            description: `Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus imperdiet, nulla et dictum interdum, nisi lorem egestas odio, vitae scelerisque enim ligula venenatis dolor. Maecenas nisl est, ultrices nec congue eget, auctor vitae massa. Fusce luctus vestibulum augue ut aliquet. Mauris ante ligula, facilisis sed ornare eu, lobortis in odio. Praesent convallis urna a lacus interdum ut hendrerit risus congue. Nunc sagittis dictum nisi, sed ullamcorper ipsum dignissim ac. In at libero sed nunc venenatis imperdiet sed ornare turpis.

            Donec vitae dui eget tellus gravida venenatis. Integer fringilla congue eros non fermentum. Sed dapibus pulvinar nibh tempor porta. Cras ac leo purus. Mauris quis diam velit.`
        }
    };

    restaurantList.forEach(card => {
        card.addEventListener("click", function () {
            const restaurantId = this.getAttribute("data-id");
            const data = restaurants[restaurantId];

            if (data) {
                document.getElementById("detail-title").innerText = data.name;
                document.getElementById("detail-image").src = data.image;
                document.getElementById("detail-address").innerText = data.address;
                document.getElementById("detail-phone").innerText = data.phone;
                document.getElementById("detail-website").innerText = data.website;
                document.getElementById("detail-category").innerText = data.category;
                document.getElementById("detail-hours").innerText = data.hours;
                document.getElementById("detail-price").innerText = data.price;
                document.getElementById("detail-description").innerText = data.description;
                document.getElementById("detail-strip-title").innerText = data.name;

                // Show the details section and hide restaurant list
                document.getElementById("restaurant-list").style.display = "none";
                detailSection.style.display = "flex";
            }
        });
    });

    // Main menu button
    mainMenuBtn.addEventListener("click", function () {
        document.getElementById("restaurant-list").style.display = "block";
        detailSection.style.display = "none";
    });
});