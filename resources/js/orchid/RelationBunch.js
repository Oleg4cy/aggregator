export default class RelationBunch {
  start = true;
  container = null;
  element = null;
  options = null;
  defaultType = null;
  temp = null;
  removeButton = null;
  data = {};
  el = {};
  current = {};
  disableMap = {};
  multiples = [];

  constructor(element, container, options, start) {
    this.start = start;
    this.container = container;
    this.element = element;
    this.options = options;
    this.createNewBunch();
    this.createNewOptions();
    if (this.options.rows) {
      this.removeButton = this.element.querySelector('[data-remove]') || null;
      this.removeButton.addEventListener('click', this.removeBunch.bind(this));
    }
  }

  removeBunch() {
    this.container.removeChild(this.element);
  };

  createNewOptions() {
    if (this.start && Object.values(this.el).every(select => select.hasAttribute('data-server-hydrated'))) {
      return;
    }

    Object.keys(this.current).forEach(type => {
      if (this.start && this.el[type].hasAttribute('data-server-hydrated')) return;
      if (this.defaultType === type) {
        this.temp = this.data[type];
        this.setOptions(type);
      }
      if (!this.start) return;
      if (!this.disableMap[type]) return;
      if (this.el[this.disableMap[type]].hasAttribute('data-server-hydrated')) return;
      this.setRenderArray(this.disableMap[type], this.current[type]);
      this.checkDisabled(this.disableMap[type]);
      this.setOptions(this.disableMap[type]);
    });
  };

  createNewBunch() {
    Object.keys(this.options.create).forEach(type => {
      this.el[type] = this.element.querySelector(`[data-id="${this.options.create[type].dataID}"]`);
      this.data[type] = this.options.create[type].data;
      if (this.options.create[type].disable) {
        this.disableMap[type] = this.options.create[type].disable;
        this.el[type].addEventListener('change', this.setCurrent.bind(this, type));
      }
      if (this.options.create[type].multiple) {
        this.multiples.push(type);
      }
      if (this.options.create[type].default) {
        this.defaultType = type;
      }


      if (!this.el[type].dataset.current) {
        this.current[type] = null;
        return;
      }

      if (this.options.create[type].multiple) {
        this.current[type] = this.el[type].dataset.current.split(',').map(Number);
      } else {
        this.current[type] = +this.el[type].dataset.current;
      }
    });
  };

  setCurrent(type, event) {
    const value = +event.target.value;
    if (this.multiples.includes(type)) {
      return this.setCurrentMultipe(type, value);
    }
    this.current[type] = value;
    this.setRenderArray(this.disableMap[type], value);
    this.resetOptions(type);
    this.checkDisabled(this.disableMap[type]);
    this.setOptions(this.disableMap[type]);
  };

  setCurrentMultipe(type, value) {
    this.current[type].push(value);
  };

  setRenderArray(type, id) {
    const data = this.data[type] || [];
    this.temp = data.find(item => item.relation === id)?.items || [];
  };

  compareItemNames(first, second) {
    const firstName = first?.name == null ? '' : String(first.name);
    const secondName = second?.name == null ? '' : String(second.name);

    return firstName.localeCompare(secondName, 'ru', {
      sensitivity: 'base',
      numeric: true,
    });
  };

  getSortedItems(type, items) {
    const sort = this.options.create[type]?.sort;
    if (!['alphabetical', 'selected-first-alphabetical'].includes(sort)) {
      return items;
    }

    const sortedItems = [...items];
    if (sort === 'alphabetical') {
      return sortedItems.sort(this.compareItemNames.bind(this));
    }

    const selectedIds = new Set(
      (Array.isArray(this.current[type]) ? this.current[type] : []).map(Number)
    );
    const selectedItems = sortedItems.filter(item => selectedIds.has(Number(item.id)));
    const unselectedItems = sortedItems.filter(item => !selectedIds.has(Number(item.id)));

    selectedItems.sort(this.compareItemNames.bind(this));
    unselectedItems.sort(this.compareItemNames.bind(this));

    return [...selectedItems, ...unselectedItems];
  };

  setOptions(type) {
    if (this.temp) {
      this.getSortedItems(type, this.temp).forEach(item => this.createOptionEl(item.id, item.name, type));
    }
    this.temp = null;
    if (!this.multiples.includes(type) && this.current[type] === null && this.el[type].closest('[data-enhanced-select]')) {
      this.el[type].selectedIndex = -1;
    }
    this.syncEnhancedSelect(type);
  };

  syncEnhancedSelect(type) {
    const select = this.el[type];
    if (!select) return;

    const wrapper = select.closest('[data-enhanced-select]');
    if (!wrapper) return;

    const tomSelect = select.tomselect;
    if (!tomSelect) return;

    tomSelect.clearOptions(() => false);
    tomSelect.sync();
    if (this.multiples.includes(type)) {
      const values = Array.isArray(this.current[type])
        ? this.current[type].map(String)
        : [];
      tomSelect.setValue(values, true);
    } else if (this.current[type] !== null) {
      tomSelect.setValue(String(this.current[type]), true);
    } else {
      tomSelect.setValue([], true);
    }
    if (select.disabled) {
      tomSelect.disable();
    } else {
      tomSelect.enable();
    }
  };

  resetOptions(type) {
    if (this.multiples.includes(this.disableMap[type])) {
      this.el[this.disableMap[type]].innerHTML = '';
      this.current[this.disableMap[type]] = [];
    } else {
      this.current[this.disableMap[type]] = null;
      this.removeOptions(this.disableMap[type]);
    }
    this.el[this.disableMap[type]].setAttribute('disabled', true);
    if (this.disableMap[this.disableMap[type]]) {
      this.resetOptions(this.disableMap[type]);
    }
  };

  removeOptions(type) {
    this.current[type] = null;
    this.el[type].selectedIndex = 0;
    const elChilds = this.el[type].children;
    for (var i = elChilds.length - 1; i > 0; i--) {
      this.el[type].removeChild(elChilds[i]);
    }
  };

  createOptionEl(value, text, type) {
    const option = document.createElement('option');
    option.setAttribute('value', value);
    option.setAttribute('data-type', type);
    option.textContent = text;
    if (this.checkSelected(value, type)) {
      option.setAttribute('selected', 'selected')
    }
    this.el[type].append(option);
  };

  checkSelected(value, type) {
    if (!this.start) return false;

    const numericValue = Number(value);
    if (this.multiples.includes(type)) {
      return Array.isArray(this.current[type])
        && this.current[type].some(currentValue => Number(currentValue) === numericValue);
    }

    return this.current[type] !== null
      && Number(this.current[type]) === numericValue;
  };

  checkDisabled(type) {
    if (!this.el[type].hasAttribute('disabled')) return;
    if (!this.temp || this.temp.length < 1) return;
    this.el[type].removeAttribute('disabled');
  };
}
