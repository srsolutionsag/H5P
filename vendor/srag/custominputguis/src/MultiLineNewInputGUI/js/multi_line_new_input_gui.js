il.MultiLineNewInputGUI = {
    /**
     * @param {jQuery} el
     */
    add: function (el) {
        var cloned_el = this.clone_template.clone();

        this.init(cloned_el);

        el.after(cloned_el);

        this.update(el.parent());
    },

    /**
     *
     */
    addFirstLine: function () {
        this.add_first_line.hide();

        var cloned_el = this.clone_template.clone();

        this.init(cloned_el);

        this.add_first_line.parent().parent().children().eq(1).append(cloned_el);

        this.update(this.add_first_line.parent().parent().children().eq(1));
    },

    /**
     * @type {jQuery|null}
     */
    add_first_line: null,

    /**
     * @type {Array}
     */
    cached_options: [],

    /**
     * @param {jQuery} el
     * @param {string} type
     * @param {Object} options
     */
    cacheOptions(el, type, options) {
        this.cached_options.push({
            type: type,
            options: options
        });

        el.attr("data-cached_options_id", (this.cached_options.length - 1));
    },

    /**
     * @type {jQuery|null}
     */
    clone_template: null,

    /**
     * @param {jQuery} el
     */
    down: function (el) {
        el.insertAfter(el.next());

        this.update(el.parent());
    },

    /**
     * @param {jQuery} el
     * @param {boolean} add_first_line
     */
    init: function (el, add_first_line) {
        $("span[data-action]", el).each(function (i, action_el) {
            action_el = $(action_el);

            action_el.off();

            action_el.on("click", this[action_el.data("action")].bind(this, el))
        }.bind(this));

        if (!add_first_line) {
            $(".input-group.date:not([data-cached_options_id])", el).each(function (i2, el2) {
                el2 = $(el2);

                if (el2.data("DateTimePicker")) {
                    this.cacheOptions(el2, "datetimepicker", el2.datetimepicker("options"));
                }
            }.bind(this));

            $("select[data-multiselectsearchnewinputgui]:not([data-cached_options_id])", el).each(function (i2, el2) {
                el2 = $(el2);

                const options = JSON.parse(atob(el2.data("multiselectsearchnewinputgui")));

                this.cacheOptions(el2, "select2", options);
            }.bind(this));

            if (!this.clone_template) {
                this.clone_template = el.clone();

                $("[name]", this.clone_template).each(function (i2, el2) {
                    if (el2.type === "checkbox") {
                        el2.checked = false;
                    } else {
                        el2.value = "";
                    }
                });

                $(".alert", this.clone_template).remove();

                this.clone_template.show();

                $("select[data-multiselectsearchnewinputgui]", this.clone_template).each(function (i2, el2) {
                    el2 = $(el2);

                    el2.html("");
                }.bind(this));

                if (el.parent().parent().data("remove_first_line")) {
                    this.remove(el);
                }
            }
        } else {
            this.add_first_line = el;
        }
    },

    /**
     * @param {jQuery} el
     */
    remove: function (el) {
        var parent = el.parent();

        if (!parent.parent().data("required") || parent.children().length > 1) {
            el.remove();

            this.update(parent);
        }
    },

    /**
     * @param {jQuery} el
     */
    up: function (el) {
        el.insertBefore(el.prev());

        this.update(el.parent());
    },

    /**
     * @param {jQuery} el
     */
    update: function (el) {
        $("span[data-action=up]", el).show();
        $("> div:first-of-type span[data-action=up]", el).hide();

        $("span[data-action=down]", el).show();
        $("> div:last-of-type span[data-action=down]", el).hide();

        for (const key of ["aria-controls", "aria-labelledby", "href", "id", "name"]) {
            el.children().each(function (i, el) {
                $("[" + key + "]", el).each(function (i2, el2) {
                    for (const [char_open, char_close] of [["[", "]["], ["__", "__"]]) {
                        el2.attributes[key].value = el2.attributes[key].value.replace(new RegExp(char_open.replace(/./g, "\\$&") + "[0-9]+" + char_close.replace(/./g, "\\$&")), char_open + i + char_close);
                    }
                }.bind(this));
            }.bind(this));
        }

        if (el.parent().data("required")) {
            if (el.children().length < 2) {
                $("span[data-action=remove]", el).hide();
            } else {
                $("span[data-action=remove]", el).show();
            }
        } else {
            $("span[data-action=remove]", el).show();

            if (el.children().length === 0) {
                this.add_first_line.show();
            }
        }

        $("[data-cached_options_id]", el).each(function (i2, el2) {
            el2 = $(el2);

            const options = this.cached_options[el2.attr("data-cached_options_id")];
            if (!options) {
                return;
            }
            switch (options.type) {
                case "datetimepicker":
                    if (el2.data("DateTimePicker")) {
                        el2.datetimepicker("destroy");
                    }

                    el2.prop("id", "");

                    el2.datetimepicker(options.options);
                    break;

                case "select2":
                    if (el2.data("select2")) {
                        el2.select2("destroy");
                    }

                    el2.next(".select2").remove();

                    el2.removeAttr("class");
                    el2.removeAttr("data-select2-id");
                    el2.removeAttr("aria-hidden");
                    el2.removeAttr("tabindex");

                    el2.select2(options.options);
                    break;

                default:
                    break;
            }
        }.bind(this));
    }
};
