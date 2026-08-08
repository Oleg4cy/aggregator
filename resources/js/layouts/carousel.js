import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";
import Swiper from "swiper";
import { Navigation, Pagination } from "swiper/modules";

const preview = {
  swiperEl: ".swiper--carousel-preview",
  swiper: null,
  params: {
    modules: [Navigation],
    navigation: {
      nextEl: ".carousel-preview-next",
      prevEl: ".carousel-preview-prev",
    },
    freeMode: true,
    spaceBetween: 10,
    slidesPerView: 2,
    breakpoints: {
      500: {
        slidesPerView: 3,
      },
      900: {
        slidesPerView: 5,
      },
      1000: {
        slidesPerView: 4,
      },
      1400: {
        slidesPerView: 5,
      },
      1700: {
        slidesPerView: 6,
      },
      1800: {
        slidesPerView: 7,
      },
    },
  },

  init() {
    this.swiper = new Swiper(this.swiperEl, this.params);
  }
}.init();


const photos = {
  swiperEl: '.swiper--carousel-photos',
  previewEls: '[data-carousel-preview]',
  swiper: null,
  params: {
    modules: [Navigation, Pagination],
    loop: true,
    slidesPerView: 1,
    pagination: {
      el: '.carousel-photos-pagination',
      clickable: true,
    },
    navigation: {
      nextEl: '.carousel-photos-next',
      prevEl: '.carousel-photos-prev',
    },
  },

  init() {
    app.modal.modalEl.addEventListener('photosCarouselClose', () => this.destroy());
    document.querySelectorAll(this.previewEls).forEach(button => {
      button.addEventListener('click', (e) => {
        this.swiper = new Swiper(this.swiperEl, this.params);
        const index = +e.target.dataset.carouselPreview;
        this.swiper.slideToLoop(index, 0, false);
      });
    });
  },

  destroy: function () {
    if (this.swiper) {
      this.swiper.destroy(true, true);
      this.swiper = null;
    }
  },
}.init();

const fullscreenController = {
  modal: document.getElementById('carousel-photos'),
  enters: document.querySelectorAll('[data-modal-path="carousel_photos"]'),
  exit: document.getElementById('exit_fullscreen_photos'),
  breakpoint: 900,

  init() {
    this.enters.forEach(button => button.addEventListener('click', this.enterFullscreen.bind(this)));
    this.exitFullscreen = this.exitFullscreen.bind(this);
  },

  enterFullscreen() {
    if (window.innerWidth < this.breakpoint) {
      if (this.modal.requestFullscreen) {
        this.modal.requestFullscreen();
      } else if (this.modal.webkitRequestFullscreen) {
        this.modal.webkitRequestFullscreen();
      } else if (this.modal.msRequestFullscreen) {
        this.modal.msRequestFullscreen();
      }
      this.exit.addEventListener('click', this.exitFullscreen);
    }
  },

  exitFullscreen() {
    if (document.exitFullscreen) {
      document.exitFullscreen();
    } else if (document.webkitexitFullscreen) {
      document.webkitexitFullscreen();
    } else if (document.msexitFullscreen) {
      document.msexitFullscreen();
    }
    this.exit.removeEventListener('click', this.exitFullscreen);
  }
}.init();
