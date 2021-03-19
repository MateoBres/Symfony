export class StorageSettings {

    /* PROPERTIES */
    data = {};
    encrypt = true;

    // aggiungere qui le diverse sezioni di settings o single property
    KEYS = [
        'pageSettings'
    ];

    constructor() {
        this.load();
    }

    save() {
        for (const key in this.data) {
            localStorage.setItem(key, this.encrypt ? btoa(JSON.stringify(this.data[key])) : JSON.stringify(this.data[key]));
        }

    }

    load() {
        for (const key in localStorage) {
            const item = localStorage.getItem(key);
            if (this.KEYS.includes(key) && item) {
                this.data[key] = JSON.parse(this.encrypt ? atob(localStorage.getItem(key)) : localStorage.getItem(key));
            }
        }
    }

    set(key, value) {
        this.data[key] = value;
        this.save();
    }

    get(key) {
        this.load();
        return this.data[key];
    }

    // *** pageSettings methods

    get pageSettings() {
        return typeof this.data.pageSettings != 'undefined' ? this.data.pageSettings : {};
    }

    getPageSettings(path) {
        return typeof this.data.pageSettings != 'undefined' && typeof this.data.pageSettings[path] != 'undefined' ? this.data.pageSettings[path] : {};
    }

    setPageSetting(path, property, value) {
        if (typeof this.data.pageSettings == 'undefined') {
            this.data.pageSettings = {};
        }
        if (typeof this.data.pageSettings[path] == 'undefined') {
            this.data.pageSettings[path] = {};
        }
        this.data.pageSettings[path][property] = value;
        this.save();
    }

    setPageElementPropertyInCategory(path, category, id, value) {

        if (typeof this.data.pageSettings == 'undefined') {
            this.data.pageSettings = {};
        }
        if (typeof this.data.pageSettings[path] == 'undefined') {
            this.data.pageSettings[path] = {};
        }

        if (typeof this.data.pageSettings[path][category] == 'undefined') {
            this.data.pageSettings[path][category] = {};
        }

        this.data.pageSettings[path][category][id] = value;
        this.save();
    }

}