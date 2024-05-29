
var swiper = new Swiper(".home", {
  centeredSlides: true,
  autoplay: {
    delay: 2500,
    disableOnInteraction: false,
  },
  pagination: {
    el: ".swiper-pagination",
    clickable: true,
  },

});
// scroll up//


let scroll = document.querySelector(".scroll");
window.addEventListener("scroll", () => {
  if (document.documentElement.scrollTop > 50 || document.body.scrollTop > 50) {
    scroll.style.display = "block";
  } else {
    scroll.style.display = "none";
  }
  scroll.addEventListener("click", () => {
    window.scrollTo({
      top: "0",
      behavior: "smooth",
    })
  })
})
// preloader//
// Select the preloader element
const preloader = document.querySelector('.preloader');

// Wait for the window to finish loading before hiding the preloader
window.addEventListener('load', function() {
  // Use a setTimeout function to simulate a delay before hiding the preloader
  setTimeout(function() {
    // Hide the preloader by adding a class to it
    preloader.classList.add('hide');
    // Remove the preloader element from the DOM after it's hidden
    preloader.addEventListener('transitionend', function() {
      preloader.remove();
    }, {once: true});
  }, 500); // Adjust the delay time as needed
});



// navbar color//
window.addEventListener("scroll",()=>{
  if(document.documentElement.scrollTop>30||document.body.scrollTop>30){
    console.log("ll")
    document.querySelector("header").style.background= 'linear-gradient(rgba(0,0,0,0.8),rgba(0,0,0,0.8))';
    document.querySelector("header").style.backdropFilter="blur(10px)";
  }else{
    document.querySelector("header").style.background="";
    document.querySelector("header").style.backdropFilter="blur(0px)";


  }
})



