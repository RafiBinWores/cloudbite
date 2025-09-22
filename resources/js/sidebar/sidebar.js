const menuToggle = document.getElementById('menuToggle');
  const menuClose = document.getElementById('menuClose');
  const mobileMenu = document.getElementById('mobileMenu');
  const overlay = document.getElementById('menuOverlay');

  menuToggle.addEventListener('click', () => {
    mobileMenu.classList.remove('-translate-x-full');
    overlay.classList.remove('hidden');
  });

  menuClose.addEventListener('click', () => {
    mobileMenu.classList.add('-translate-x-full');
    overlay.classList.add('hidden');
  });

  overlay.addEventListener('click', () => {
    mobileMenu.classList.add('-translate-x-full');
    overlay.classList.add('hidden');
  });


//   swiper js
const heroSwiper = new Swiper('.myHeroSwiper', {
    loop: true,
    speed: 700,
    autoplay: {
      delay: 4500,
      disableOnInteraction: false,
    },
    effect: 'slide', // try 'fade' if you want
    grabCursor: true,
    slidesPerView: 1,
    centeredSlides: true,
    navigation: {
      nextEl: '.myHeroSwiper .swiper-button-next',
      prevEl: '.myHeroSwiper .swiper-button-prev',
    },
    pagination: {
      el: '.myHeroSwiper .swiper-pagination',
      clickable: true,
    },
    breakpoints: {
      0: {
        // tighter spacing on small screens is handled by Tailwind classes
      },
      1024: {
        // nothing special; layout is responsive via Tailwind grid
      }
    }
  });