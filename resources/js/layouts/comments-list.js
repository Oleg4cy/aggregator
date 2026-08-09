import Chooser from "../plugins/chooser";

const filter = {
	filters: [],
	listUrl: "/api/data/review-sources",
	chooserID: "",
	desktopID: "comments_filter_",
	mobileID: "mobile_comments_filter_",
	options: {
		current: 1,
		data: [
			{
				value: "Сначала новые",
				// attr: {
				//   'some_attr': 'some_value',
				//   'some_attr': 'some_value',
				//   'some_attr': 'some_value',
				// },
				//
				// id: 'some_unique_id',
				// group: 'some_name',
				// onClick(item) { console.log('asdf'); }
			},
			{ value: "Сначала старые" },
			{ value: "Сначала положительные" },
			{ value: "Сначала негативные" },
		],
		classList: {
			label: `select-menu__label select-menu__label--comments`,
			wrapper: `select-menu__wrapper select-menu__wrapper--comments`,
			current: `select-menu__current select-menu__wrapper--comments`,
			list: `select-menu__list select-menu__list--comments`,
			item: `select-menu__item select-menu__item--comments`,
			icon: `select-menu__icon select-menu__icon--comments`,
		},
	},

	async init() {
		this.chooserID = window.innerWidth >= 900
			? this.desktopID
			: this.mobileID;
		try {
			const services = await this.getServices();
			services.forEach((service) => {
				const options = {
					el: `${this.chooserID}${service.id}`,
					...this.options,
				};
				const filter = new Chooser(options);
				this.filters.push(filter);
			});
		} catch (error) {
			console.log(error);
		}
	},

	async getServices() {
		try {
			const response = await fetch(this.listUrl);
			if (response.ok) {
				const services = await response.json();
				return services;
			}
		} catch (error) {
			console.log(error);
		}
	},
}.init();
