/**
 *
 *  ТАБЫ
 * Чтобы использовать надо задать HTML
 * элементам data аттрибуты.
 *
 * Для кнопки      - data-tab-path="example"
 * Для секции      - data-tab-target="example"
 * Для группировки - data-tab-group="example-group"
 *
 * Группировка табов обязательна!
 *
 * При нажатии на кнопку соответствующей секции добавляется
 * клас open (для него есть стили в /resources/styles/layouts/tabs.scss)
 * при этом у соответствующей группы табов этот класс удаляется
 *
 */

const SHOW_CLASS = "open";
const ACTIVE_CLASS = "active";

document.addEventListener("click", (event) => {
  const path = event.target.closest("[data-tab-path]");

  if (!path) return;

  const group = path.dataset.tabGroup;
  const targetPath = path.dataset.tabPath;

  if (!group || !targetPath) return;

  document.querySelectorAll("[data-tab-path]").forEach((item) => {
    if (item.dataset.tabGroup === group) {
      item.classList.remove(ACTIVE_CLASS);
      item.setAttribute("aria-selected", "false");
    }
  });

  document.querySelectorAll("[data-tab-target]").forEach((target) => {
    if (target.dataset.tabGroup === group) {
      target.classList.remove(SHOW_CLASS);
    }
  });

  path.classList.add(ACTIVE_CLASS);
  path.setAttribute("aria-selected", "true");

  document.querySelectorAll("[data-tab-target]").forEach((target) => {
    if (
      target.dataset.tabGroup === group &&
      target.dataset.tabTarget === targetPath
    ) {
      target.classList.add(SHOW_CLASS);
    }
  });
});
