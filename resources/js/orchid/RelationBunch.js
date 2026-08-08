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
    Object.keys(this.current).forEach(type => {
      if (this.defaultType === type) {
        this.temp = this.data[type];
        this.setOptions(type);
      }
      if (!this.start) return;
      if (!this.disableMap[type]) return;
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

  setOptions(type) {
    this.temp && this.temp.forEach(item => this.createOptionEl(item.id, item.name, type));
    this.temp = null;
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
    if (!this.start) return;
    let check;
    if (this.multiples.includes(type)) {
      check = this.current[type]?.includes(value);
      check && (this.current[type].push(value));
    } else {
      check = this.current[type] === value;
      check && (this.current[type] = value);
    }

    if (check) return true;
    return false;
  };

  checkDisabled(type) {
    if (!this.el[type].hasAttribute('disabled')) return;
    if (!this.temp || this.temp.length < 1) return;
    this.el[type].removeAttribute('disabled');
  };
}
