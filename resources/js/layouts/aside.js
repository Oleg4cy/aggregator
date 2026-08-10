const equipmentTypeUI = {
  classes: { active: 'active', },
  equipmentTypeList: document.querySelector(".search--filter"),
  toggleEquipmentTypeListBtn: document.getElementById("toggle-equipment-type"),
  applyEquipmentTypes: document.querySelector('.search--filter .search__btn--selection'),
  // actionButtons: document.querySelectorAll('.search--filter  .search__btn'),

  init() {
    this.toggleEquipmentTypeListBtn.addEventListener("click", () => {
      this.toggleActive(this.equipmentTypeList);
      this.toggleActive(this.toggleEquipmentTypeListBtn);
    });
    this.applyEquipmentTypes.addEventListener("click", () => {
      this.equipmentTypeList.classList.remove(this.classes.active);
      this.toggleEquipmentTypeListBtn.classList.remove(this.classes.active);
      this.closeBrandsList();
    });
  },

  toggleActive: function (element) {
    element.classList.toggle(this.classes.active);
  },

  closeBrandsList() {
    const list = document.querySelector('.equipment-types-list--filter .equipment-types-list__item .equipment-types-list__brands.open');
    list && list.classList.remove('open');
  }
}.init();
