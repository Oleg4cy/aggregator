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
    const swiperEl = document.querySelector(this.swiperEl);

    if (!swiperEl) return;

    this.swiper = new Swiper(swiperEl, this.params);
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
    if (app.modal?.modalEl) {
      app.modal.modalEl.addEventListener('photosCarouselClose', () => this.destroy());
    }

    document.addEventListener("click", (event) => {
      const button = event.target.closest(this.previewEls);

      if (!button) return;

      const swiperEl = document.querySelector(this.swiperEl);

      if (!swiperEl) return;

      this.destroy();
      this.swiper = new Swiper(swiperEl, this.params);

      const index = Number(button.dataset.carouselPreview);

      if (Number.isInteger(index)) {
        this.swiper.slideToLoop(index, 0, false);
      }
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
  breakpoint: 900,

  init() {
    document.addEventListener("click", (event) => {
      const enter = event.target.closest(
        '[data-modal-path="carousel_photos"][data-carousel-preview]'
      );

      if (enter) {
        this.enterFullscreen();
        return;
      }

      if (event.target.closest("#exit_fullscreen_photos")) {
        this.exitFullscreen();
      }
    });
  },

  enterFullscreen() {
    const modal = document.querySelector(
      '[data-modal-target="carousel_photos"]'
    );

    if (!modal) return;
    if (window.innerWidth >= this.breakpoint) return;

    if (modal.requestFullscreen) {
      modal.requestFullscreen();
    } else if (modal.webkitRequestFullscreen) {
      modal.webkitRequestFullscreen();
    } else if (modal.msRequestFullscreen) {
      modal.msRequestFullscreen();
    }
  },

  exitFullscreen() {
    if (document.fullscreenElement && document.exitFullscreen) {
      const result = document.exitFullscreen();

      if (result?.catch) {
        result.catch(() => {});
      }

      return;
    }

    if (
      document.webkitFullscreenElement
      && document.webkitExitFullscreen
    ) {
      document.webkitExitFullscreen();
      return;
    }

    if (
      document.msFullscreenElement
      && document.msExitFullscreen
    ) {
      document.msExitFullscreen();
    }
  }
}.init();
