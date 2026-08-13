/**
 * INITIALIZATION
 * document.addEventListener('DOMContentLoaded', () => {
 * 	 classes: {
 * 		 message: {
 * 			 container: 'example',
 * 			 close: 'example',
 * 			 title: 'example',
 * 		   text: 'example',
 * 	  	}
 *  	},
 *   const modal = new ModalWindow({
 *     isOpen: (modal) => {
 *       console.log('opened');
 *     },
 *     isClose: (modal) => {
 *       console.log('closed');
 *     },
 *   });
 * });
 *
 * USING WITH HTML DATA ATTRIBUTES
 * Add data-modal-path attribute to some button element:
 * <button class="open" data-modal-path="anyName">Open Modal</button>
 *
 * Add data-modal-target attribute to modal__container element:
 * <div class="modal-window__container" data-modal-target="anyName">
 *
 * FULL EXAMPLE:
 * Open button:
 *  <button class="open"
 *         data-modal-path="example"
 *         data-modal-animation="fadeInUp"
 *         data-modal-speed-in="300"
 *         data-modal-speed-out="300">
 *  Some Button Text
 * </button>
 *
 * HTML structure for modal window
 * <div class="modal-window">
 *   <div class="modal-window__container" data-modal-target="example">
 *     <button class="modal-window__close">Close</button>
 *     <div class="modal-window__content">
 *       Lorem ipsum, dolor sit amet consectetur adipisicing elit. Quis, asperiores?
 *     </div>
 *   </div>
 * </div>
 *
 * if you need one button to work both for opening and closing the window, add the attribute
 * data-modal-one-button="true"
 *
 * Animation
 * data-modal-path="anyName"
 * data-modal-animation="fadeInUp"
 * data-modal-speed-in="300"
 * data-modal-speed-out="300"
 *
 *
 * USING FROM JS
 *
 * Create HTML element for modal window
 * <div class="modal-window"><div>
 *
 * The modal window is invoked using methods:
 *
 * showMessage(options = {});
 * options = {
 *   text: 'example'
 *   title: 'example',
 *   animation: 'anyType',
 *   speed: 0.3s,
 *   speedOut: 0.3.s,
 *   classes: {
 *     container: 'example',
 *     close: 'example',
 *     title: 'example',
 *     text: 'example',
 *   }
 * }
 *
 * FADE IN EFFECTS
 * fadeInUp
 * fadeInDown
 * fadeInLeft
 * fadeInRight,
 *
 *
 * EVENTS
 *
 * When using HTML attributes
 * To utilize events, you should add the event name as
 * an attribute to the button that opens the modal window.
 * For example:
 * data-modal-event="eventName"
 *
 * When using JS
 * You should add to the options event: 'eventName'.
 *
 * NB
 * When generating events, 'Open' will be appended to
 * the event name for opening events and 'Close' for closing events.
 * For instance, if you name the event 'eventName',
 * the generated events will be 'eventNameOpen'
 * when opening the modal window and 'eventNameClose' when closing it.
 *
 * When doing so, you need to attach the listener
 * to the element instance of the class, for example:
 *
 * const modalWindow = new ModalWindow(options);
 * modalWindow.modalEl.addEventListener('eventNameForMessageOpen', () => console.log('opened'));
 * modalWindow.modalEl.addEventListener('eventNameForMessaageClose', () => console.log('closed'));
 *
 * modalWindow.showMessage({text: 'example', event: 'eventNameForMessage'});
 *
 */

export default class ModalWindow {
  constructor(options) {
    this.focusElements = [
      "a[href]",
      "input",
      "button",
      "select",
      "textarea",
      "[tabindex]",
    ];

    this.modalElEvents = {
      open: 'modalWindowOpen',
      close: 'modalWindowClose',
    }

    this.attributes = {
      path: 'data-modal-path',
      target: 'data-modal-target'
    };

    this.classes = {
      modal: 'modal-window',
      message: 'modal-window__message',
      container: 'modal-window__container',
      animation: 'modal-window__animate',
      close: 'modal-window__close',
      openPostfix: '--open',
      disableScroll: 'modal-disable-scroll',
      fixBlock: 'modal-fix-block',
    };

    this.ids = {
      modal: 'modal-window',
      tmp: 'modal-window-tmp',
    };

    let defaultOptions = {
      isOpen: () => { },
      isClose: () => { },
    };

    this.options = Object.assign(defaultOptions, options);
    this.modalEl = document.getElementById(this.classes.modal);
    this.alert = document.querySelector(`.${this.classes.message}`);
    this.fixBlocks = document.querySelectorAll(`.${this.classes.fixBlock}`);

    this.speed = false;
    this.animation = false;
    this.isOpen = false;
    this.isTmpOpen = false;
    this.isTmpCreated = false;
    this.modalElContainer = false;
    this.previosActiveElement = false;
    this.generateEvent = false;
    this.events();
  }

  events() {
    if (!this.modalEl) return;
    document.addEventListener("click", function (e) {
      if (this.isTmpOpen) return;
      if (e.target.classList.contains(this.classes.close)) return this.close(e);
      const clickedElement = e.target.closest(`[${this.attributes.path}]`);
      if (!clickedElement) return this.checkForClose(e);
      this.generateEvent = clickedElement.dataset.modalEvent ?? false;
      if (clickedElement.dataset.modalOneButton && this.isOpen) return this.close(e);
      let target = clickedElement.dataset.modalPath;
      let animation = clickedElement.dataset.modalAnimation;
      let speed = clickedElement.dataset.modalSpeedIn;
      let speedOut = clickedElement.dataset.modalSpeedOut;
      this.setAnimadionOptions(animation, speed, speedOut);
      this.modalElContainer = document.querySelector(`[${this.attributes.target}="${target}"]`);
      if (
        this.modalElContainer
        && this.modalEl
        && !this.modalEl.contains(this.modalElContainer)
      ) {
        this.modalEl.append(this.modalElContainer);
      }
      if (this.modalElContainer) this.open(e);
    }.bind(this));

    window.addEventListener("keydown", function (e) {
      if (e.keyCode == 27 && this.isOpen) return this.close(e);
      if (e.keyCode == 9) return this.focusCatch(e);
    }.bind(this));
  }

  setAnimadionOptions(animation, speed, speedOut) {
    this.animation = animation ?? "fade";
    this.speed = speed ? parseInt(speed) : 300;
    this.speedOut = speedOut ? parseInt(speedOut) : 100;
  }

  showMessage(options = {}) {
    this.setAnimadionOptions(options.animation, options.speed, options.speedOut);
    const tmp = this.getTmpElement();
    tmp.innerHTML = this.getMessagetmplate(options);
    tmp.classList.add(this.classes.container);
    this.options.classes.message.container && tmp.classList.add(this.options.classes.message.container);
    options.classes?.container && tmp.classList.add(options.classes?.container);
    this.modalEl.append(tmp);
    this.modalElContainer = tmp;
    this.generateEvent = options.generateEvent ?? false;
    this.isTmpOpen = true;
    this.isTmpCreated = true;
    this.open();
  }

  getMessagetmplate(options) {
    return (`
      <button class="${this.createClassString(this.classes.close, this.options.classes.message.close, options.classes?.close)}"></button>
      ${options.title ? `<h2 class="${this.createClassString(this.options.classes.message.title, options.classes?.title)}">${options.title ?? ''}</h2>` : ''}
      ${options.text ? `<p class="${this.createClassString(this.options.classes.message.text, options.classes?.text)}">${options.text ?? ''}</p>` : ''}
    `);
  }

  getTmpElement() {
    const tmp = document.createElement('div');
    tmp.setAttribute('id', this.ids.tmp);
    return tmp;
  }

  removeTmpElement() {
    const tmp = document.getElementById(this.ids.tmp);
    tmp.parentElement.removeChild(tmp);
    this.isTmpCreated = false;
  }

  createClassString(baseClass, ...additionalClasses) {
    return [baseClass, ...additionalClasses].filter(Boolean).join(' ').trim();
  }

  checkForClose(e) {
    if (this.isCloseButtonClicked(e)) {
      this.close(e);
    } else if (this.isClickOutsideModal(e)) {
      this.close(e);
    }
  }

  isCloseButtonClicked(e) {
    return e.target.closest(`.${this.modalElCloseClass}`);
  }

  isClickOutsideModal(e) {
    return !e.target.classList.contains(this.classes.container) &&
      !e.target.closest(`.${this.classes.container}`) &&
      this.isOpen;
  }

  open(e = null) {
    if (this.generateEvent) {
      const openEvent = new CustomEvent(this.generateEvent + 'Open', { detail: { instance: this } });
      this.modalEl.dispatchEvent(openEvent);
    }
    this.previosActiveElement = document.activeElement;
    this.modalEl.style.setProperty("--transition-time", `${this.speed / 1000}s`);
    this.modalEl.classList.add(`${this.classes.modal}${this.classes.openPostfix}`);
    this.disableScroll();

    this.modalElContainer.classList.add(`${this.classes.modal}${this.classes.openPostfix}`);
    this.modalElContainer.classList.add(`${this.classes.modal}__${this.animation}`);

    this.isOpen = true;
    this.focusTrap();

    setTimeout(() => {
      this.options.isOpen(this, e);
      this.modalElContainer.classList.add(`${this.classes.animation}`);
      this.isTmpOpen = false;
    }, this.speed);
  }

  close(e = null) {
    if (!this.modalElContainer) return;
    this.modalElContainer.classList.remove(`${this.classes.animation}`);
    setTimeout(() => {
      this.modalEl.classList.remove(`${this.classes.modal}${this.classes.openPostfix}`);
      this.modalElContainer.classList.remove(`${this.classes.modal}__${this.animation}`);
      this.modalElContainer.classList.remove(`${this.classes.modal}${this.classes.openPostfix}`);
      this.enableScroll();
      this.isOpen = false;
      this.focusTrap();
      this.options.isClose(this, e);
      this.isTmpCreated && this.removeTmpElement();
      if (this.generateEvent) {
        const closeEvent = new CustomEvent(this.generateEvent + 'Close', { detail: { instance: this } });
        this.modalEl.dispatchEvent(closeEvent);
        this.generateEvent = false;
      }
    }, this.speedOut);
  }

  focusCatch(e) {
    if (!this.isOpen) return;
    const focusable = this.modalElContainer.querySelectorAll(this.focusElements);
    const focusArray = Array.prototype.slice.call(focusable);
    const focusedIndex = focusArray.indexOf(document.activeElement);

    if (e.shiftKey && focusedIndex === 0) {
      focusArray[focusArray.length - 1].focus();
      e.preventDefault();
    }
    if (!e.shiftKey && focusedIndex === (focusArray.length - 1)) {
      focusArray[0].focus();
      e.preventDefault();
    }
  }

  focusTrap() {
    const focusable = this.modalElContainer.querySelectorAll(this.focusElements);
    if (this.isOpen && focusable.length > 0) {
      setTimeout(() => focusable[0].focus(), 100);
    } else {
      this.previosActiveElement.focus();
    }
  }

  disableScroll() {
    let pagePosition = window.scrollY;
    this.lockPadding();
    document.body.classList.add(`${this.classes.disableScroll}`);
    document.body.dataset.modalPosition = pagePosition;
    document.body.style.top = -pagePosition + "px";
  }

  enableScroll() {
    let pagePosition = parseInt(document.body.dataset.modalPosition, 10);
    this.unlockPadding();
    document.body.style.top = "auto";
    document.body.classList.remove(`${this.classes.disableScroll}`);
    window.scrollTo({
      top: pagePosition,
      behavior: "instant",
    });
  }

  lockPadding() {
    let paddingOffset = window.innerWidth - document.body.offsetWidth + "px";
    this.fixBlocks.forEach((el) => el.style.paddingRight = paddingOffset);
    document.body.style.paddingRight = paddingOffset;
  }

  unlockPadding() {
    this.fixBlocks.forEach((el) => el.style.paddingRight = "0px");
    document.body.style.paddingRight = "0px";
  }
}
