import FilterBase from './FilterBase';

export default class extends FilterBase {
  selectors = {
    buttons: {
      apply: '.search--filter .search__btn--selection',
      clear: '.search--filter .search__btn--clear',
      wrapper: '.search--filter .search__action',
    },
    inputs: {
      equipmentTypes: 'input[name="filter-equipment-type"]',
      brands: 'data-filter-equipment-type',
    },
    count: {
      apply: {
        el: '.search--filter .search__apply-count',
        title: '.search--filter .search__apply-title',
      },
      clear: {
        data: 'data-filter-clean',
        el: '.search--filter .search__clear-count',
        title: '.search--filter .search__clear-title',
      },
    },
    brands: {
      list: 'data-brand-target',
      close: '[data-brand-close]',
      open: '[data-brand-path]',
    },
  };

  classes = {
    open: 'open',
    hidden: 'hidden',
    disabled: 'disabled',
    activePartial: 'checkbox-square__input--partial',
  };


  count = {
    apply: {
      el: null,
      title: null,
    },
    clear: {
      el: null,
      title: null,
    },
  };

  buttons = {
    wrapper: null,
    apply: null,
    clear: null,
  };

  inputs = {};

  isBrandListOpen = false;

  constructor(fields) {
    super(fields);
    this.count.apply.el = document.querySelector(this.selectors.count.apply.el);
    this.count.apply.title = document.querySelector(this.selectors.count.apply.title);
    this.count.clear.el = document.querySelector(this.selectors.count.clear.el);
    this.count.clear.title = document.querySelector(this.selectors.count.clear.title);
    this.checkboxCrumble = document.querySelectorAll(this.selectors.brands.close);
    this.brandButtons = document.querySelectorAll(this.selectors.brands.open);

    this.initInputs();
    this.initActionButtons();
    this.initCheckboxCrumble();
    this.initBrandButtons();
  }

  init() { }

  setURLparams(urlParams) {
    Object.values(this.inputs).forEach(equipmentType => {
      if (equipmentType.activeBrands <= 0) return;
      Object.values(equipmentType.brands).forEach(brand => {
        brand.el.checked && urlParams.append(this.fields.brands, brand.id);
      });
    });
  }

  initInputs() {
    document.querySelectorAll(this.selectors.inputs.equipmentTypes).forEach(input => {
      this.inputs[input.value] = {
        'brands': {},
        'el': input,
        'id': +input.value,
        'activeBrands': 0,
      };
      input.addEventListener('click', this.toggleEquipmentTypes.bind(this));
      document.querySelectorAll(`[${this.selectors.inputs.brands}="${input.value}"]`).forEach(brand => {
        this.inputs[input.value].brands[brand.value] = {
          'el': brand,
          'id': +brand.value,
        };
        if (brand.checked) {
          this.inputs[input.value].activeBrands++;
          this.inputs[input.value].el.classList.add(this.classes.activePartial);
        };
        brand.addEventListener('click', this.toggleBrands.bind(this));
      });
    });
  }

  toggleEquipmentTypes(event) {
    const equipmentType = this.inputs[event.target.value];
    if (equipmentType.el.classList.contains(this.classes.activePartial)) {
      equipmentType.el.classList.remove(this.classes.activePartial);
      equipmentType.el.checked = false;
    }
    Object.values(equipmentType.brands).forEach(brand => {
      if (equipmentType.el.checked) {
        brand.el.checked = true;
        equipmentType.activeBrands = Object.keys(equipmentType.brands).length;
      } else {
        equipmentType.activeBrands = 0;
        brand.el.checked = false;
      }
    });

    this.setApplyCount();
    this.filterApply();
  }

  toggleBrands(event) {
    const equipmentType = event.target.dataset.filterEquipmentType;
    if (event.target.checked) {
      this.inputs[equipmentType].el.classList.add(this.classes.activePartial);
      ++this.inputs[equipmentType].activeBrands;
    } else {
      --this.inputs[equipmentType].activeBrands;
      if (this.inputs[equipmentType].activeBrands) {
        this.inputs[equipmentType].el.checked = false;
        this.inputs[equipmentType].el.classList.add(this.classes.activePartial);
      } else {
        this.inputs[equipmentType].el.classList.remove(this.classes.activePartial);
      }
    }

    this.setApplyCount();
    this.setClearCount(equipmentType);
    this.filterApply();
  }

  initActionButtons() {
    this.buttons.apply = document.querySelector(this.selectors.buttons.apply);
    this.buttons.clear = document.querySelector(this.selectors.buttons.clear);
    this.buttons.wrapper = document.querySelector(this.selectors.buttons.wrapper);

    this.buttons.wrapper.addEventListener('click', e => {
      switch (e.target) {
        case this.buttons.apply:
          return this.apply();
        case this.buttons.clear:
          return this.clear();
      }
    });
  }

  initCheckboxCrumble() {
    this.checkboxCrumble.forEach((element) => {
      element.addEventListener("click", (e) => {
        this.isBrandListOpen = false;
        const target = document.querySelector(`[${this.selectors.brands.list}="${e.target.dataset.brandClose}"]`);
        target.classList.remove(this.classes.open);
        this.setClearCount(e.target.dataset.brandClose);
        this.buttons.clear.removeAttribute(this.selectors.count.clear.data);
        this.buttons.clear.textContent = 'Сбросить всё';
        if (!this.getActiveBrands()) {
          this.buttons.clear.classList.add(this.classes.disabled);
        } else {
          this.buttons.clear.classList.remove(this.classes.disabled);
        }
      });
    });
  }

  initBrandButtons() {
    this.brandButtons.forEach((element) => {
      element.addEventListener("click", (e) => {
        this.isBrandListOpen = true;
        const equipmentType = e.target.dataset.brandPath;
        const target = document.querySelector(`[${this.selectors.brands.list}="${equipmentType}"]`);
        this.buttons.clear.setAttribute(this.selectors.count.clear.data, equipmentType);
        target.classList.add(this.classes.open);
        this.setClearCount(equipmentType);
      });
    });
  }

  setClearCount(equipmentType) {
    if (equipmentType && this.inputs[equipmentType].activeBrands && this.isBrandListOpen) {
      this.buttons.clear.textContent = 'Сбросить: ' + this.inputs[equipmentType].activeBrands;
    } else if (equipmentType && this.isBrandListOpen) {
      this.buttons.clear.textContent = 'Сбросить всё';
      this.buttons.clear.classList.add(this.classes.disabled);
    }
  }

  getActiveBrands() {
    return Object.values(this.inputs).reduce((acc, equipmentType) => {
      return acc + equipmentType.activeBrands;
    }, 0);
  }

  setApplyCount() {
    const count = this.getActiveBrands();
    if (count) {
      this.count.apply.title.innerHTML = 'Выбрано:&nbsp;';
      this.count.apply.el.innerHTML = count;
      this.buttons.clear.classList.remove(this.classes.disabled);
    } else {
      this.count.apply.title.innerHTML = 'Не выбрано';
      this.count.apply.el.innerHTML = '';
      this.buttons.clear.classList.add(this.classes.disabled);
    }
  }

  apply() {
    console.log('apply');
  }

  clear() {
    if (this.buttons.clear.hasAttribute(this.selectors.count.clear.data)) {
      this.clearBrands(this.inputs[this.buttons.clear.dataset.filterClean]);
    } else {
      this.clearFull();
    }

    this.filterApply();
  }

  clearFull() {
    Object.values(this.inputs).forEach(equipmentType => {
      this.clearBrands(equipmentType);
    });
  }

  clearBrands(equipmentType) {
    equipmentType.el.checked = false;
    equipmentType.el.classList.remove(this.classes.activePartial);
    equipmentType.activeBrands = 0;
    Object.values(equipmentType.brands).forEach(brand => {
      brand.el.checked = false;
    });

    this.setApplyCount();
    this.setClearCount(equipmentType.id);
  }

  reset() {
    for (let input in this.inputs) {
      this.inputs[input].el.classList.remove(this.classes.activePartial);
      this.inputs[input].el.checked = false;
      if (this.inputs[input].activeBrands < 1) continue;
      for (let sub in this.inputs[input].brands) {
        this.inputs[input].brands[sub].el.checked = false;
      }
    }
  }
}
