const carousel_container = document.querySelector(".image-carousel-container");
const carousel = document.querySelector(".image-carousel");
let images;
let test = { images: [{ name: "image1", text: "text1" }] };

fetch("./config/carousel_images.json")
  .then((data) => data.json())
  .then((data) => {
    for (imageIndex in data.images) {
      appendImage(data.images[imageIndex]);
    }
    appendImage(data.images[0], true);
    appendImage(data.images[1]);
  })
  .finally(() => {
    startCarousel();
    images = document.querySelectorAll(".image-carousel a");
  });

function appendImage(image, hasClass) {
  let href = document.createElement("a");
  href.setAttribute("href", image.url);
  if (hasClass) {
    href.setAttribute("class", "last-image");
  }

  let img = document.createElement("img");
  img.setAttribute("src", "./images/carousel_images/" + image.name);
  href.appendChild(img);
  carousel.appendChild(href);
}

let current = 0;
const imagePlusBuffer = 160;
function startCarousel() {
  setInterval(() => {
    current++;
    carousel.style.transition = "500ms ease-in-out transform";
    carousel.style.transform = `translateX(${-imagePlusBuffer * current}px)`;
  }, 5000);
}

carousel.addEventListener("transitionend", () => {
  try {
    if (images[current].classList.contains("last-image")) {
      carousel.style.transition = "none";
      current = 0;
      carousel.style.transform = `translateX(0px)`;
    }
  } catch (error) {
    carousel.style.transition = "none";
    current = 0;
    carousel.style.transform = `translateX(0px)`;
  }
});
