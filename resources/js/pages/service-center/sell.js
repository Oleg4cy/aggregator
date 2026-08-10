const sell = {
  els: null,
  classes: {
    el: '.categories-list--sell > .categories-list__item',
    open: 'open',
  },
  attributes: {
    target: 'data-target',
    breadcrumbs: 'data-target-breadcrumbs',
  },

  init() {
    this.els = document.querySelectorAll(`${this.classes.el}`);
    this.els.forEach(el => {
      if (!el.parentElement.classList.contains(this.classes.open)) {
        el.parentElement.classList.add(this.classes.open);
      }
      el.addEventListener('click', e => {
        const targetList = document.querySelector(`[${this.attributes.target}="${el.parentElement.dataset.path}"]`);
        const breadcrumbs = document.querySelector(`[${this.attributes.breadcrumbs}="${el.parentElement.dataset.path}"]`);
        targetList.classList.add(this.classes.open);
        breadcrumbs.parentElement.classList.add(this.classes.open);
        el.parentElement.classList.remove(this.classes.open);
        const close = () => {
          targetList.classList.remove(this.classes.open);
          breadcrumbs.parentElement.classList.remove(this.classes.open);
          el.parentElement.classList.add(this.classes.open);
          breadcrumbs.removeEventListener("click", close);
        };
        breadcrumbs.addEventListener("click", close);
      });
    });
  }
}.init();
