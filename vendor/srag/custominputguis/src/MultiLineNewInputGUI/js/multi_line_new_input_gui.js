il.MultiLineNewInputGUI = {
    /**
     * @param {jQuery} el
     */
    add: function (el) {
        var cloned_el = el.clone();

        $("[name]", el).each(function (i2, el2) {
            el2.value = "";
        });

        $(".alert", el).remove();

        this.init(cloned_el);

        el.before(cloned_el);

        this.update(el.parent());
    },

    /**
     * @param {jQuery} el
     */
    down: function (el) {
        el.insertAfter(el.next());

        this.update(el.parent());
    },

    /**
     * @param {jQuery} el
     */
    init: function (el) {
        $("span[data-action]", el).each(function (i, action_el) {
            action_el = $(action_el);

            action_el.off();

            action_el.on("click", this[action_el.data("action")].bind(this, el))
        }.bind(this));
    },

    /**
     * @param {jQuery} el
     */
    remove: function (el) {
        var parent = el.parent();

        if (parent.children().length > 1) {
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
        for (const key of ["aria-controls", "aria-labelledby", "href", "id", "name"]) {
            el.children().each(function (i, el) {
                $("[" + key + "]", el).each(function (i2, el2) {
                    for (const [char_open, char_close] of [["[", "]["], ["__", "__"]]) {
                        el2.attributes[key].value = el2.attributes[key].value.replace(new RegExp(char_open.replace(/./g, "\\$&") + "[0-9]+" + char_close.replace(/./g, "\\$&")), char_open + i + char_close);
                    }
                }.bind(this));
            }.bind(this));
        }
    }
};
