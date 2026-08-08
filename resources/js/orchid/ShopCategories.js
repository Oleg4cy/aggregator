import SelectRelations from "./SelectRelations"

export default class extends window.Controller {
  connect() {
    const options = {
      element: this.element,
      rows: Number(this.data.get('rows')),
      create: {
        category: {
          default: true,
          dataID: 'select-category',
          disable: 'subCategories',
          data: [],
        },
        subCategories: {
          dataID: 'select-subcategories',
          multiple: true,
          data: [],
        },
      }
    }

    const getData = async () => {
      const response = await fetch('/api/data/allCategories');
      const data = await response.json();
      data.forEach(category => {
        options.create.category.data.push(category);
        options.create.subCategories.data.push({ relation: category.id, items: category.sub_categories });
      });
    };

    (async () => {
      await getData();
      const worker = new SelectRelations(options);
      this.initSorting();
    })();
  }

  initSorting() {
    const container = this.element.querySelector('[data-container]');
    const sortMode = this.element.querySelector('[data-sort-mode]');
    const rowOrder = new WeakMap();
    let nextOrder = 0;
    const storageKey = 'orchid.shopCategories.sortMode';
    const validModes = ['created_at', 'alphabetical'];
    const savedMode = localStorage.getItem(storageKey);
    let currentMode = validModes.includes(savedMode) ? savedMode : 'created_at';

    const registerRows = () => {
      container.querySelectorAll('[data-row]').forEach(row => {
        if (!rowOrder.has(row)) {
          rowOrder.set(row, nextOrder++);
        }
      });
    };

    const sortRows = () => {
      registerRows();
      const rows = Array.from(container.querySelectorAll('[data-row]'));

      rows.sort((first, second) => {
        if (currentMode === 'created_at') {
          return rowOrder.get(first) - rowOrder.get(second);
        }

        const firstSelect = first.querySelector('[data-id="select-category"]');
        const secondSelect = second.querySelector('[data-id="select-category"]');
        const firstValue = firstSelect?.value || '';
        const secondValue = secondSelect?.value || '';

        if (!firstValue && !secondValue) return rowOrder.get(first) - rowOrder.get(second);
        if (!firstValue) return 1;
        if (!secondValue) return -1;

        const comparison = (firstSelect.selectedOptions[0]?.textContent || '').localeCompare(
          secondSelect.selectedOptions[0]?.textContent || '',
          'ru',
          { sensitivity: 'base', numeric: true }
        );

        return comparison || rowOrder.get(first) - rowOrder.get(second);
      });

      rows.forEach(row => container.append(row));
    };

    sortMode.value = currentMode;
    sortMode.addEventListener('change', event => {
      if (!validModes.includes(event.target.value)) {
        currentMode = 'created_at';
        sortMode.value = currentMode;
      } else {
        currentMode = event.target.value;
      }
      localStorage.setItem(storageKey, currentMode);
      sortRows();
    });

    this.element.addEventListener('change', event => {
      if (currentMode === 'alphabetical' && event.target.matches('[data-id="select-category"]')) {
        sortRows();
      }
    });

    sortRows();
  }
}
