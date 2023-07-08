(function(e) {
    var t = require("ankama.widget"),
        n = require("lodash");
    t.widget("ankama.ak_masterpage", t.ankama.widget, {
        options: {
            initSelector: "body"
        },
        sCurrentProfile: null,
        iTimeout: null,
        _create: function() {
            var n = this;
            t(e).on("resize", t.proxy(n._onWindowResize, n)), e !== e.top && !n.element.hasClass("ak-iframe-allow") && (e.top.location.href = e.location.href)
        },
        _init: function() {
            var e = this;
            e.sCurrentProfile = t.getCurrentProfile()
        },
        _onWindowResize: function() {
            var n = this;
            e.clearTimeout(n.iTimeout), n.iTimeout = e.setTimeout(function() {
                t.getCurrentProfile() !== n.sCurrentProfile && (t(document).trigger("profilechange", [n.sCurrentProfile, t.getCurrentProfile()]), n.sCurrentProfile = t.getCurrentProfile())
            }, 200)
        }
    }), t(document).bind("ready widgetcreate", function(e) {
        t.ankama.ak_masterpage.prototype.enhanceWithin(e.target)
    })
})(this),
function(e, t) {
    var n = require("ankama.widget"),
        r = require("lodash");
    require("jquery.loadmask"), require("jquery.pjax"), n.widget("ankama.ak_ajaxloader", n.ankama.widget, {
        options: {
            initSelector: ".ak-ajaxloader",
            bindSubmitEvent: !0,
            scrollOffset: 0,
            direct: !1,
            removeEmptyFields: !1,
            handleClick: !0,
            pjax: {
                timeout: 3e4,
                scrollTo: !1
            }
        },
        _create: function() {
            var e = this;
            if (!n.support.pjax) return !1;
            var t = this.element,
                i = this.options.pjax.container || t.akmData("target") || "body",
                s = this.options.pjax.fragment || t.akmData("fragment") || i;
            r.merge(this.options.pjax, {
                container: i,
                fragment: s
            }), t.is("form") ? (t.prop("method").toLowerCase() == "post" && typeof this.options.pjax.push == "undefined" && (this.options.pjax.push = !1), this.option("bindSubmitEvent") === !0 && t.on("submit", function(t) {
                e.processSubmit(t)
            })) : (t.on("direct.ajaxloader", function() {
                n.pjax(r.clone(e.options.pjax))
            }), this.option("handleClick") && t.on("click", t.is("a") ? null : "a", function(t) {
                n.pjax.click(t, r.clone(e.options.pjax))
            }))
        },
        _sLastContainer: null,
        _init: function() {
            var e = this.options.pjax.container,
                t = this.element,
                i = this;
            delete this.options.pjax.data;
            if (this.options.scroll) {
                var s = null;
                this.options.scrollElement && n(this.options.scrollElement).length != 0 ? s = n(this.options.scrollElement) : n(this.options.pjax.container).length != 0 && (s = n(this.options.pjax.container)), s && r.merge(this.options.pjax, {
                    scrollTo: s.offset().top - this.options.scrollOffset
                })
            }
            this._sLastContainer && this._sLastContainer != e && (n(this._sLastContainer).off("pjax:start"), n(this._sLastContainer).off("pjax:end")), e != this._sLastContainer && (n(e).data("ajaxloader") || (n(e).on("pjax:start", function(e) {
                var t = n.contains(i.element[0], e.target);
                (i.options.direct && i.element[0] == e.target || t) && e.stopPropagation(), t || n(this).mask()
            }), n(e).on("pjax:complete", function() {
                n(this).unmask()
            }), n(e).on("pjax:end", function() {
                n(this).unmask(), n(document).trigger("widgetcreate"), n(this).trigger("ajaxloaded")
            })), n(e).data("ajaxloader", !0)), this._sLastContainer = e, t.is("form") && n('[type="submit"]', t).on("click", function() {
                t.data("button", this)
            })
        },
        _destroy: function() {
            this._sLastContainer && !n(this._sLastContainer).contains(this.element) && n(this._sLastContainer).off("pjax:start")
        },
        processSubmit: function(e) {
            var t = this,
                i = this.element,
                s = i.data("button");
            t.options.pjax.data || (t.options.pjax.data = i.serializeArray()), s && s.name && s.value && r.findIndex(t.options.pjax.data, {
                name: s.name
            }) == -1 && t.options.pjax.data.push({
                name: s.name,
                value: s.value
            }), t.options.removeEmptyFields && (t.options.pjax.data = n.grep(t.options.pjax.data, function(e) {
                return !r.isEmpty(e.value)
            })), n.pjax.submit(e, r.clone(t.options.pjax))
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_ajaxloader.prototype.enhanceWithin(e.target)
    })
}(this),
function(e, t) {
    "use strict";
    var n = require("ankama.widget");
    n = require("jquery.jstorage");
    var r = require("lodash");
    n.widget("ankama.ak_history", n.ankama.widget, {
        options: {
            initSelector: ".ak-history",
            templateSelector: ".ak-history-template",
            storageKey: "HISTORIC_ENCYCLO",
            storage: [],
            maxElements: 10
        },
        _jqParent: null,
        _create: function() {
            this._jqParent = this.element, this.options.template || (this.options.template = this.element.find(this.options.templateSelector)[0].outerHTML, this._jqParent = this.element.find(this.options.templateSelector).parent(), this.element.find(this.options.templateSelector).remove()), this.store(), this.refresh()
        },
        refresh: function() {
            var e = n.jStorage.get(this.options.storageKey);
            if (e) {
                e = e.slice(), e.reverse();
                var t = this.options.template,
                    r = [];
                for (var i = 0; i < e.length; i++) {
                    var s = t.replace(/\[-TYPE-\]/g, e[i].type).replace(/\[-TITLE-\]/g, e[i].title).replace(/\[-HREF-\]/g, e[i].href).replace(/\[-IMAGE-\]/g, n("<img>").attr("src", e[i].image)[0].outerHTML).replace(/\[-ASIDE-\]/g, e[i].aside ? e[i].aside : ""),
                        o = n(s).removeClass("hide");
                    o.find("div:empty").remove(), r.push(o)
                }
                this._jqParent.append(r), r = null
            }
        },
        store: function() {
            var e = this.options.storage;
            if (n.isEmptyObject(e)) return !1;
            var t = n.jStorage.get(this.options.storageKey);
            n.isArray(t) || (t = []);
            var r = [];
            for (var i = 0; i < t.length; i++)(t[i].type != e.type || t[i].id != e.id) && r.push(t[i]);
            r.length >= this.options.maxElements && (r = r.splice(r.length - (this.options.maxElements - 1), this.options.maxElements - 1)), r.push(e), n.jStorage.set(this.options.storageKey, r)
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_history.prototype.enhanceWithin(e.target)
    })
}(this),
function(e) {
    var t = require("ankama.widget"),
        n = require("lodash");
    require("ankama.ui.dialog"), require("jquery.pjax"), require("jquery.validate"), require("jquery.ankama"), require("jquery.serialize-object"), t.validator.addMethod("server", function(e, n, r) {
        var i = t(arguments[1].form).data().options.wn,
            s = t(arguments[1].form).data()["ankama-" + i];
        return Object.getPrototypeOf(s).remoteValidation.apply(s, arguments), "pending"
    }), t.validator.addMethod("alphanum", function(e, t) {
        return this.optional(t) || /^[a-zA-Z0-9\-]+$/i.test(e)
    }), t.validator.addMethod("alpha", function(e, t) {
        return this.optional(t) || /^[a-zA-Z\-]+$/i.test(e)
    }), t.validator.addMethod("editor_required", function(e, n, r) {
        if (typeof CKEDITOR != "undefined") {
            var i;
            t.each(CKEDITOR.instances, function(e, t) {
                t.editable().isInline() && t.fire("blur"), t.element.getNameAtt() == n.name && (i = t)
            });
            if (i) return i.getData() != ""
        }
        return !0
    }), t.widget("ankama.ak_simpleform", t.ankama.widget, {
        options: {
            initSelector: ".ak-simpleform-nonused",
            helpPopupBtnCls: "ak-btn-popup",
            server: {
                xhrFields: {
                    withCredentials: !0
                }
            },
            disabledClass: "disabled",
            ajaxSubmit: !0,
            ajaxFragment: null,
            ajaxTarget: null,
            pjaxSettings: {
                timeout: 2e4,
                scrollTo: !1
            },
            validate: {
                rules: {},
                messages: {},
                onkeyup: !0,
                focusCleanup: !1,
                focusInvalid: !0,
                ignoreTitle: !0,
                debug: !1
            }
        },
        jqValidator: null,
        aFieldsOptions: [],
        _oErrorFieldHandlers: {},
        _invalidServersValidations: null,
        _destroy: function() {
            this.jqValidator = null
        },
        _create: function() {
            var e = this,
                r = t(this.element),
                i = r.akmData("target") || e.option("ajaxTarget") || "body",
                s = r.akmData("fragment") || e.option("ajaxFragment") || i;
            e._invalidServersValidations = [], e.option("wn", e.widgetName), n.merge(e.options.pjaxSettings, {
                container: i,
                fragment: s
            }), r.ak_ajaxloader({
                bindSubmitEvent: !1,
                pjax: e.options.pjaxSettings
            }), r.on("reset_form", function(t, n) {
                e._reset(n)
            }), this.element.data("options", this.options), this._superApply(arguments)
        },
        iTimerId: null,
        _init: function() {
            var s = this,
                o = t(this.element),
                u = t.akLang(),
                a = t.akClientLang(),
                f = r[u] || r[a] || r.en;
            this.element.data("validator") && (this.element.removeData("validator"), this.element.unbind("submit")), this._addHelpTooltip(), s.aFieldsOptions = [], this.options.validate.rules = {}, this.options.validate.messages = {}, this.options.validate.groups = {}, n.each(this.element.find(":input"), t.proxy(function(r) {
                var o = t(r),
                    l = o.akOptions();
                if (n.isEmpty(l)) return;
                s.aFieldsOptions[r.name] = l, o.data("alt-fieldname") && (this.options.validate.groups[r.name] = r.name + " " + o.data("alt-fieldname")), o.attr("id") || o.attr("id", n.uniqueId("ak_field_"));
                var c = o.attr("name");
                if ("validation" in l) var h = l.validation.rules;
                h !== e && (this.options.validate.rules[c] = {}, this.options.validate.messages[c] = {}, n.each(h, function(e) {
                    var t;
                    n.has(e, "message") && (n.isEmpty(e.message) || (t = e.message), delete e.message), n.forIn(e, function(e, r) {
                        var o;
                        r in i && (o = i[r][u] || i[r][a]), typeof o == "function" && (o = o.apply(this, n.isArray(e) ? e : [e])), s.options.validate.rules[c][r] = e, s.options.validate.messages[c][r] = t || o || l.validation.message || f
                    })
                })), "validation" in l && (l.validation.remote === !0 || l.validation.remote === "true") && (s.options.validate.rules[c].server = !0)
            }, this)), t(this.element).validate(t.extend(this.options.validate, {
                submitHandler: t.proxy(this.submit, this),
                invalidHandler: t.proxy(this.formInvalidHandler, this),
                showErrors: t.proxy(this.formShowErrors, this),
                onfocusout: t.proxy(this.elementFocusOut, this),
                onclick: t.proxy(this.elementClick, this),
                onkeyup: t.proxy(this.elementKeyUp, this),
                highlight: t.proxy(this.elementError, this),
                success: t.proxy(this.elementSuccess, this),
                errorPlacement: t.proxy(this.elementErrorPlacement, this)
            })), this.jqValidator = this.element.data("validator"), t(window).delegate(s.element, "resize", function() {
                clearTimeout(s.iTimerId), s.iTimerId = setTimeout(t.proxy(s._addHelpTooltip, s), 250)
            })
        },
        _addHelpTooltip: function() {
            var e = this.element.closest(".ak-modal-wrapper");
            n.each(this.element.find(":input"), t.proxy(function(r) {
                var i = t(r),
                    s = i.akOptions();
                if (!(!n.isEmpty(s) && "tooltip" in s)) return;
                var o = i.nextAll(".ak-btn-popup:visible");
                if (o.length) {
                    if (!i.data("btnpopued") && !i.nextAll(".ak-tooltip").length) {
                        var u = i.ak_tooltip("getOptions");
                        i.nextAll(".ak-btn-popup:visible").ak_tooltip(n.merge(u, {
                            manual: !0,
                            tooltip: {
                                position: {
                                    my: "center left",
                                    at: "center right",
                                    viewport: t(window)
                                },
                                show: {
                                    event: "click",
                                    delay: 0,
                                    effect: !1
                                },
                                hide: {
                                    event: "click unfocus",
                                    delay: 0,
                                    effect: !1
                                }
                            }
                        })), i.data("btnpopued", !0)
                    }
                    i.data("ankama-ak_tooltip") && e.length ? i.ak_tooltip("destroy") : i.ak_tooltip("disable")
                } else i.data("btnpopued") ? i.ak_tooltip("enable") : (i.attr("type") == "text" || i.attr("type") == "password" || i.attr("type") == "email") && i.ak_tooltip("disable")
            }, this))
        },
        submit: function(e, r) {
            var i = this,
                s = t(this.element);
            if (i.beforeSubmit(arguments) === !1) return !1;
            if (s.triggerHandler("beforesubmit") === !1) return !1;
            var o = i.element.find("input[type=submit]");
            i.options.ajaxSubmit && t.support.pjax ? (o.addClass(i.options.disabledClass), i.element.parents(".qtip").length && i.element.parents(".qtip").qtip("disable"), t(i.options.pjaxSettings.container).one("pjax:requested", function() {
                i.element.parents(".qtip").length && i.element.parents(".qtip").qtip("enable"), o.removeClass(i.options.disabledClass), i._onSubmitted.apply(i, arguments)
            }), t(i.options.pjaxSettings.container).one("pjax:end", function() {
                i.element.parents(".qtip").length && setTimeout(function() {
                    i.element.parents(".qtip").qtip("api").reposition(null, !1)
                }, 0)
            }), s.ak_ajaxloader(), s.ak_ajaxloader("option", "pjax", n.merge(s.ak_ajaxloader("option", "pjax"), i.options.pjaxSettings)), s.ak_ajaxloader("processSubmit", r)) : (o.addClass(i.options.disabledClass), e.submit())
        },
        _reset: function(e) {
            var r = this,
                i = t(this.element);
            i.get(0).reset(), i.find(".ak-cascading").each(function(e, n) {
                t(".form-group", this).each(function(e, n) {
                    e > 0 && t(n).remove()
                })
            }), i.find(".ak-select").each(function(e, n) {
                t(this).trigger("chosen:updated")
            }), n.isFunction(e) && e.call(r)
        },
        _onSubmitted: function(e, t, n, r, i) {},
        beforeSubmit: function() {
            return !(this._invalidServersValidations.length > 0)
        },
        getField: function(e) {
            return this.element.find(':input[name="' + e + '"]')
        },
        elementFocusOut: function(e, n) {
            t.validator.defaults.onfocusout.apply(this.jqValidator, arguments)
        },
        elementClick: function(e, n) {
            t.validator.defaults.onclick.apply(this.jqValidator, arguments)
        },
        elementKeyUp: function(e, t) {
            var n = this;
            if (t.keyCode === 9 && this.jqValidator.elementValue(e) === "") return !1
        },
        elementError: function(e, n, r) {
            var i = this,
                s = t(e);
            s.closest(".form-group").removeClass("has-success").addClass("has-error"), i.toogleTooltip(s, !0)
        },
        elementUnError: function(e, n, r) {
            var i = this,
                s = t(e);
            s.closest(".form-group").removeClass("has-error")
        },
        elementSuccess: function(e, n) {
            var r = this,
                i = t(n);
            n.type !== "checkbox" ? e.closest(".form-group").removeClass("has-error").addClass("has-success") : e.closest(".form-group").removeClass("has-error"), r.toogleTooltip(i, !1)
        },
        elementErrorPlacement: function(e, n) {
            t(e).addClass("control-label");
            if (!n.is(":checkbox")) {
                var r = n.next('script[type="application/json"]').first();
                t(e).insertAfter(r.length > 0 ? r : n)
            } else t(e).insertAfter(n.parent())
        },
        toogleTooltip: function(e, t) {
            var r = e.akOptions();
            if (!n.isEmpty(r) && "tooltip" in r) {
                t = !n.isUndefined(t) && n.isBoolean(t) ? t : !0;
                if (!e.data("ankama-ak_tooltip")) return;
                if (!e.data("ankama-ak_tooltip")) return;
                t ? (e.ak_tooltip("enable"), e.ak_tooltip("show")) : (e.ak_tooltip("hide"), e.ak_tooltip("disable"))
            }
        },
        beforeSerialize: function(e, t) {},
        formInvalidHandler: function(e, t) {},
        formShowErrors: function(e, t) {
            this.jqValidator.defaultShowErrors()
        },
        remoteValidation: function(e, r, i) {
            var s = this,
                o = n.clone(s.options.validate.rules[r.name]);
            delete o.server;
            var u = n.clone(s.options.server),
                a = u.contentType === "application/json",
                f = n.merge(u, {
                    url: s.element.attr("action") || s.options.server.url || window.location.href,
                    type: s.element.attr("method") || "POST",
                    beforeSend: function(e, n) {
                        a || e.setRequestHeader("X-Requested-With", "XMLHttpRequest"), t(r).parent(".controls").addClass("working")
                    },
                    complete: function() {
                        t(r).parent(".controls").removeClass("working")
                    },
                    data: {
                        sTestField: t(r).data("alt-fieldname") || t(r).attr("name"),
                        aFormData: s.element.serializeObject()
                    },
                    dataFilter: t.proxy(s._parseResult, s)
                }, i);
            a && n.isObject(f.data) && (f.data = JSON.stringify(f.data));
            var l = s._getAlternativeElement(r),
                r = l.get(0),
                e = l.val();
            return t.validator.methods.remote.call(s.jqValidator, e, r, f)
        },
        _parseResult: function(e) {
            var n = this,
                r = t.parseJSON(e),
                i = r.result.valid === !0,
                s = this.jqValidator.findByName(r.result.field).get(0),
                o = t(s);
            return i && this._invalidServersValidations.indexOf(s.name) > -1 && this._invalidServersValidations.splice(this._invalidServersValidations.indexOf(s.name), 1), o.data("validate-result", r), this.jqValidator.stopRequest(s, i), i ? !0 : (this._invalidServersValidations.indexOf(s.name) == -1 && this._invalidServersValidations.push(s.name), this._oErrorFieldHandlers && typeof this._oErrorFieldHandlers[s.name] != "undefined" && this._oErrorFieldHandlers[s.name].apply(this, [s, r]), this.jqValidator.settings.messages[n._getOriginalElement(s).get(0).name].server = r.result.errors[s.name], this.jqValidator.settings.messages[s.name].server = r.result.errors[s.name], '"' + r.result.errors[s.name].replace(/"/g, "&quot;") + '"')
        },
        _getAlternativeElement: function(e) {
            var n = this,
                r = t(e);
            return r.data("alt-fieldname") ? t(':input[name="' + r.data("alt-fieldname") + '"]') : r
        },
        _getOriginalElement: function(e) {
            var n = this,
                r = t(e);
            return r.data("ori-fieldname") ? t(':input[name="' + r.data("ori-fieldname") + '"]', n.element) : r
        }
    });
    var r = {
            fr: "[FR] Ce champ contient des erreurs",
            en: "This field contains errors",
            de: "[DE] This field contains errors",
            es: "[ES] This field contains errors",
            it: "[IT] This field contains errors",
            pt: "[PT] This field contains errors"
        },
        i = {
            required: {
                fr: "Ce champ est requis",
                en: "This field is required.",
                de: "Pflichtfeld",
                es: "Campo obligatorio.",
                it: "Questo campo Ã¨ obbligatorio.",
                pt: "Este campo Ã© obrigatÃ³rio"
            },
            remote: {
                fr: "[FR] Please fix this field.",
                en: "Please fix this field.",
                de: "[DE] Please fix this field.",
                es: "[ES] Please fix this field.",
                it: "[IT] Please fix this field.",
                pt: "[PT] Please fix this field."
            },
            email: {
                fr: "[FR] Please enter a valid email address.",
                en: "Please enter a valid email address.",
                de: "[DE] Please enter a valid email address.",
                es: "[ES] Please enter a valid email address.",
                it: "[IT] Please enter a valid email address.",
                pt: "[PT] Please enter a valid email address."
            },
            url: {
                fr: "[FR] Please enter a valid URL.",
                en: "Please enter a valid URL.",
                de: "[DE] Please enter a valid URL.",
                es: "[ES] Please enter a valid URL.",
                it: "[IT] Please enter a valid URL.",
                pt: "[PT] Please enter a valid URL."
            },
            date: {
                fr: "[FR] Please enter a valid date.",
                en: "Please enter a valid date.",
                de: "[DE] Please enter a valid date.",
                es: "[ES] Please enter a valid date.",
                it: "[IT] Please enter a valid date.",
                pt: "[PT] Please enter a valid date."
            },
            dateISO: {
                fr: "[FR] Please enter a valid date (ISO).",
                en: "Please enter a valid date (ISO).",
                de: "[DE] Please enter a valid date (ISO).",
                es: "[ES] Please enter a valid date (ISO).",
                it: "[IT] Please enter a valid date (ISO).",
                pt: "[PT] Please enter a valid date (ISO)."
            },
            number: {
                fr: "[FR] Please enter a valid number.",
                en: "Please enter a valid date (ISO).",
                de: "[DE] Please enter a valid date (ISO).",
                es: "[ES] Please enter a valid date (ISO).",
                it: "[IT] Please enter a valid date (ISO).",
                pt: "[PT] Please enter a valid date (ISO)."
            },
            digits: {
                fr: "[FR] Please enter only digits.",
                en: "Please enter only digits.",
                de: "[DE] Please enter only digits.",
                es: "[ES] Please enter only digits.",
                it: "[IT] Please enter only digits.",
                pt: "[PT] Please enter only digits."
            },
            creditcard: {
                fr: "[FR] Please enter a valid credit card number.",
                en: "Please enter a valid credit card number.",
                de: "[DE] Please enter a valid credit card number.",
                es: "[ES] Please enter a valid credit card number.",
                it: "[IT] Please enter a valid credit card number.",
                pt: "[PT] Please enter a valid credit card number."
            },
            equalTo: {
                fr: "[FR] Please enter the same value again.",
                en: "Please enter the same value again.",
                de: "[DE] Please enter the same value again.",
                es: "[ES] Please enter the same value again.",
                it: "[IT] Please enter the same value again.",
                pt: "[PT] Please enter the same value again."
            },
            maxlength: {
                fr: t.validator.format("[FR] Please enter no more than {0} characters."),
                en: t.validator.format("Please enter no more than {0} characters."),
                de: t.validator.format("[DE] Please enter no more than {0} characters."),
                es: t.validator.format("[ES] Please enter no more than {0} characters."),
                it: t.validator.format("[IT] Please enter no more than {0} characters."),
                pt: t.validator.format("[PT] Please enter no more than {0} characters.")
            },
            minlength: {
                fr: t.validator.format("[FR] Please enter at least {0} characters."),
                en: t.validator.format("Please enter at least {0} characters."),
                de: t.validator.format("[DE] Please enter at least {0} characters."),
                es: t.validator.format("[ES] Please enter at least {0} characters."),
                it: t.validator.format("[IT] Please enter at least {0} characters."),
                pt: t.validator.format("[PT] Please enter at least {0} characters.")
            },
            rangelength: {
                fr: t.validator.format("[FR] Please enter a value between {0} and {1} characters long."),
                en: t.validator.format("Please enter a value between {0} and {1} characters long."),
                de: t.validator.format("[DE] Please enter a value between {0} and {1} characters long."),
                es: t.validator.format("[ES] Please enter a value between {0} and {1} characters long."),
                it: t.validator.format("[IT] Please enter a value between {0} and {1} characters long."),
                pt: t.validator.format("[PT] Please enter a value between {0} and {1} characters long.")
            },
            range: {
                fr: t.validator.format("[FR] Please enter a value between {0} and {1}."),
                en: t.validator.format("Please enter a value between {0} and {1}."),
                de: t.validator.format("[DE] Please enter a value between {0} and {1}."),
                es: t.validator.format("[ES] Please enter a value between {0} and {1}."),
                it: t.validator.format("[IT] Please enter a value between {0} and {1}."),
                pt: t.validator.format("[PT] Please enter a value between {0} and {1}.")
            },
            max: {
                fr: t.validator.format("[FR] Please enter a value less than or equal to {0}."),
                en: t.validator.format("Please enter a value less than or equal to {0}."),
                de: t.validator.format("[DE] Please enter a value less than or equal to {0}."),
                es: t.validator.format("[ES] Please enter a value less than or equal to {0}."),
                it: t.validator.format("[IT] Please enter a value less than or equal to {0}."),
                pt: t.validator.format("[PT] Please enter a value less than or equal to {0}.")
            },
            min: {
                fr: t.validator.format("[FR] Please enter a value greater than or equal to {0}."),
                en: t.validator.format("Please enter a value greater than or equal to {0}."),
                de: t.validator.format("[DE] Please enter a value greater than or equal to {0}."),
                es: t.validator.format("[ES] Please enter a value greater than or equal to {0}."),
                it: t.validator.format("[IT] Please enter a value greater than or equal to {0}."),
                pt: t.validator.format("[PT] Please enter a value greater than or equal to {0}.")
            }
        },
        r = {
            fr: "[FR] Ce champ contient des erreurs",
            en: "This field contains errors",
            de: "[DE] This field contains errors",
            es: "[ES] This field contains errors",
            it: "[IT] This field contains errors",
            pt: "[PT] This field contains errors"
        },
        i = {
            required: {
                fr: "Ce champ est requis",
                en: "This field is required.",
                de: "Pflichtfeld",
                es: "Campo obligatorio.",
                it: "Questo campo Ã¨ obbligatorio.",
                pt: "Este campo Ã© obrigatÃ³rio"
            },
            remote: {
                fr: "[FR] Please fix this field.",
                en: "Please fix this field.",
                de: "[DE] Please fix this field.",
                es: "[ES] Please fix this field.",
                it: "[IT] Please fix this field.",
                pt: "[PT] Please fix this field."
            },
            email: {
                fr: "[FR] Please enter a valid email address.",
                en: "Please enter a valid email address.",
                de: "[DE] Please enter a valid email address.",
                es: "[ES] Please enter a valid email address.",
                it: "[IT] Please enter a valid email address.",
                pt: "[PT] Please enter a valid email address."
            },
            url: {
                fr: "[FR] Please enter a valid URL.",
                en: "Please enter a valid URL.",
                de: "[DE] Please enter a valid URL.",
                es: "[ES] Please enter a valid URL.",
                it: "[IT] Please enter a valid URL.",
                pt: "[PT] Please enter a valid URL."
            },
            date: {
                fr: "[FR] Please enter a valid date.",
                en: "Please enter a valid date.",
                de: "[DE] Please enter a valid date.",
                es: "[ES] Please enter a valid date.",
                it: "[IT] Please enter a valid date.",
                pt: "[PT] Please enter a valid date."
            },
            dateISO: {
                fr: "[FR] Please enter a valid date (ISO).",
                en: "Please enter a valid date (ISO).",
                de: "[DE] Please enter a valid date (ISO).",
                es: "[ES] Please enter a valid date (ISO).",
                it: "[IT] Please enter a valid date (ISO).",
                pt: "[PT] Please enter a valid date (ISO)."
            },
            number: {
                fr: "[FR] Please enter a valid number.",
                en: "Please enter a valid date (ISO).",
                de: "[DE] Please enter a valid date (ISO).",
                es: "[ES] Please enter a valid date (ISO).",
                it: "[IT] Please enter a valid date (ISO).",
                pt: "[PT] Please enter a valid date (ISO)."
            },
            digits: {
                fr: "[FR] Please enter only digits.",
                en: "Please enter only digits.",
                de: "[DE] Please enter only digits.",
                es: "[ES] Please enter only digits.",
                it: "[IT] Please enter only digits.",
                pt: "[PT] Please enter only digits."
            },
            creditcard: {
                fr: "[FR] Please enter a valid credit card number.",
                en: "Please enter a valid credit card number.",
                de: "[DE] Please enter a valid credit card number.",
                es: "[ES] Please enter a valid credit card number.",
                it: "[IT] Please enter a valid credit card number.",
                pt: "[PT] Please enter a valid credit card number."
            },
            equalTo: {
                fr: "[FR] Please enter the same value again.",
                en: "Please enter the same value again.",
                de: "[DE] Please enter the same value again.",
                es: "[ES] Please enter the same value again.",
                it: "[IT] Please enter the same value again.",
                pt: "[PT] Please enter the same value again."
            },
            maxlength: {
                fr: t.validator.format("[FR] Please enter no more than {0} characters."),
                en: t.validator.format("Please enter no more than {0} characters."),
                de: t.validator.format("[DE] Please enter no more than {0} characters."),
                es: t.validator.format("[ES] Please enter no more than {0} characters."),
                it: t.validator.format("[IT] Please enter no more than {0} characters."),
                pt: t.validator.format("[PT] Please enter no more than {0} characters.")
            },
            minlength: {
                fr: t.validator.format("[FR] Please enter at least {0} characters."),
                en: t.validator.format("Please enter at least {0} characters."),
                de: t.validator.format("[DE] Please enter at least {0} characters."),
                es: t.validator.format("[ES] Please enter at least {0} characters."),
                it: t.validator.format("[IT] Please enter at least {0} characters."),
                pt: t.validator.format("[PT] Please enter at least {0} characters.")
            },
            rangelength: {
                fr: t.validator.format("[FR] Please enter a value between {0} and {1} characters long."),
                en: t.validator.format("Please enter a value between {0} and {1} characters long."),
                de: t.validator.format("[DE] Please enter a value between {0} and {1} characters long."),
                es: t.validator.format("[ES] Please enter a value between {0} and {1} characters long."),
                it: t.validator.format("[IT] Please enter a value between {0} and {1} characters long."),
                pt: t.validator.format("[PT] Please enter a value between {0} and {1} characters long.")
            },
            range: {
                fr: t.validator.format("[FR] Please enter a value between {0} and {1}."),
                en: t.validator.format("Please enter a value between {0} and {1}."),
                de: t.validator.format("[DE] Please enter a value between {0} and {1}."),
                es: t.validator.format("[ES] Please enter a value between {0} and {1}."),
                it: t.validator.format("[IT] Please enter a value between {0} and {1}."),
                pt: t.validator.format("[PT] Please enter a value between {0} and {1}.")
            },
            max: {
                fr: t.validator.format("[FR] Please enter a value less than or equal to {0}."),
                en: t.validator.format("Please enter a value less than or equal to {0}."),
                de: t.validator.format("[DE] Please enter a value less than or equal to {0}."),
                es: t.validator.format("[ES] Please enter a value less than or equal to {0}."),
                it: t.validator.format("[IT] Please enter a value less than or equal to {0}."),
                pt: t.validator.format("[PT] Please enter a value less than or equal to {0}.")
            },
            min: {
                fr: t.validator.format("[FR] Please enter a value greater than or equal to {0}."),
                en: t.validator.format("Please enter a value greater than or equal to {0}."),
                de: t.validator.format("[DE] Please enter a value greater than or equal to {0}."),
                es: t.validator.format("[ES] Please enter a value greater than or equal to {0}."),
                it: t.validator.format("[IT] Please enter a value greater than or equal to {0}."),
                pt: t.validator.format("[PT] Please enter a value greater than or equal to {0}.")
            }
        };
    t(document).bind("ready widgetcreate", function(e) {
        t.ankama.ak_simpleform.prototype.enhanceWithin(e.target)
    })
}(),
function(e) {
    var t = require("ankama.widget"),
        n = require("lodash");
    require("jquery.steps"), t.widget("ankama.ak_stepform", t.ankama.ak_simpleform, {
        options: {
            initSelector: ".ak-stepform",
            headerTag: "h4",
            bodyTag: "fieldset",
            transitionEffect: "fade",
            forceMoveForward: !0,
            enablePagination: !1,
            startIndex: 0
        },
        _create: function() {
            this._superApply(arguments);
            var e = n.merge(this.options, {
                onStepChanging: t.proxy(this.stepChanging, this),
                onStepChanged: t.proxy(this.stepChanged, this),
                onFinishing: t.proxy(this.stepsFinishing, this),
                onFinished: t.proxy(this.stepsFinished, this)
            });
            this.element.steps(e), t(this.element).trigger("widgetcreate")
        },
        stepChanging: function(e, t, n) {
            return t > n ? !0 : (t < n && (this.element.find(".body:eq(" + n + ") label.error").remove(), this.element.find(".body:eq(" + n + ") .error").removeClass("error")), this.element.valid())
        },
        stepChanged: function(e, t, n) {},
        stepsFinishing: function(e, t) {
            return this.element.valid()
        },
        stepsFinished: function(e, t) {
            this.submit()
        },
        elementSuccess: function(e, t) {
            this._superApply(arguments), this.stepValid()
        },
        elementError: function(e, t, n) {
            this._superApply(arguments), this.stepValid()
        },
        elementClick: function(e, t) {
            this._superApply(arguments), this.stepValid()
        },
        stepValid: function() {},
        getStepElements: function() {
            return this.jqValidator.elements()
        }
    }), t(document).bind("ready widgetcreate", function(e) {
        t.ankama.ak_stepform.prototype.enhanceWithin(e.target)
    })
}(),
function(e, t) {
    "use strict";
    var n = require("ankama.widget");
    require("jquery.ui.autocomplete"), require("jquery.pjax"), n.widget("ankama.ak_autocomplete", n.ankama.widget, {
        options: {
            initSelector: ".ak-autocomplete",
            triggerSelector: ".ak-expander",
            url: null,
            minLength: 1,
            delay: 500,
            shownoresults: !0,
            noresults: "",
            source: null,
            select: "",
            submitOnSelect: !0,
            expand: null
        },
        initValue: "",
        _searchText: "",
        _bTriggered: !1,
        _destroy: function() {
            this.element.autocomplete("destroy"), this._super()
        },
        _create: function() {
            this.initValue = this.element.val();
            var r = this,
                i = n(this.element).data("page"),
                s = !1;
            r.element.parent().find(r.options.triggerSelector).length && r.element.parent().find(r.options.triggerSelector).click(function(e) {
                r.element.trigger("expand")
            }), i && this._on({
                keypress: function(e) {
                    if (e.which === 13 && n(r.element).val() !== "") return r.element.autocomplete("disable"), s = !0, !1
                }
            }), this._on({
                expand: this._onExpand,
                focus: this._onFocus,
                blur: this._onBlur
            }), this.element.parent().find("input[type=submit]").length && this.element.parent().find("input[type=submit]").on("click", function(e) {
                r.initValue === r.element.val() && r.element.val("")
            }), !this.options.url && this.element.parents("form").length && !this.options.source && (this.options.url = this.element.parents("form").attr("action")), this.element.autocomplete({
                minLength: this.options.minLength,
                delay: this.options.delay,
                source: this.options.url ? this.options.url : this.options.source && this.options.source.length ? this.options.source : "",
                appendTo: this.element.parent(),
                position: {
                    my: "right top",
                    at: "right bottom",
                    collision: "none"
                },
                autoFocus: !0,
                search: function(e, t) {
                    r._bTriggered ? (r._bTriggered = !1, r._searchText = "") : r._searchText = r.element.val().trim();
                    if (s) return !1
                },
                open: function(e, t) {
                    n(document).trigger("widgetcreate")
                },
                select: function(i, s) {
                    var o = r.options.select;
                    return r.options.submitOnSelect && s.item.nolink === !0 && r.element.parents("form").length && r.options.shownoresults ? (r.element.parents("form").submit(), !1) : (o === "location" && s.item.link !== t ? e.location.href = s.item.link : n.isFunction(o) ? o.apply(i, s) : n.isPlainObject(o) && "pjax" in o && o.pjax !== "" && s.item.link !== t && n.pjax({
                        url: s.item.link,
                        container: o.pjax
                    }), r.element.val(s.item.value), i.preventDefault(), !1)
                },
                response: function(e, t) {
                    !t.content.length && r.options.shownoresults && t.content.push({
                        value: r._searchText,
                        nolink: !0,
                        label: r.options.noresults ? r.options.noresults : "No Results"
                    })
                }
            }), this.element.data("ui-autocomplete")._renderItem = function(e, t) {
                var i = n(t.nolink ? "<span>" : '<a href="' + t.link + '">');
                t.data && "role" in t.data && i.attr("data-role", t.data.role).attr("data-" + t.data.role.split("_")[1], t.data[t.data.role.split("_")[1]]);
                var s = r.options.titlemax && t.label.length > r.options.titlemax ? t.label.substr(0, r.options.titlemax) + "..." : t.label,
                    o = r._searchText.split(" ").join("|"),
                    u = new RegExp("(" + o + ")", "gi");
                return s = s.replace(u, "<span>$1</span>"), i.append(("src" in t ? "<img src='" + t.src + "' />, " : "") + "<div>" + "<span class='ak-title'>" + s + "</span>" + ("sublabel" in t ? "<span class='ak-subtitle'>" + t.sublabel + "</span>" : "") + "</div>"), n("<li role='presentation' class='ui-menu-item " + ("class" in t ? t["class"] : "") + "'>").data("item.autocomplete", t).append(i).appendTo(n(e).addClass("search_global_results"))
            }, this.element.data("ui-autocomplete")._renderMenu = function(e, t) {
                var r = this,
                    i = "";
                n.each(t, function(t, n) {
                    "category" in n && n.category != i && (e.append("<li class='ui-autocomplete-category'>" + n.category + "</li>"), i = n.category), r._renderItemData(e, n)
                })
            }
        },
        _onExpand: function(e) {
            var t = this;
            t.element.hasClass("triggered") ? (t.element.removeClass("triggered"), t._bTriggered = !1, t.element.autocomplete("close")) : (t.element.addClass("triggered"), t._bTriggered = !0, t.element.autocomplete("search", ""), t.element[0].focus())
        },
        _onFocus: function(e) {
            this.initValue === n(this.element).val() && n(this.element).val("")
        },
        _onBlur: function(e) {
            n(this.element).val() === "" && n(this.element).val(this.initValue)
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_autocomplete.prototype.enhanceWithin(e.target)
    })
}(window),
function(e, t) {
    var n = require("ankama.widget"),
        n = require("jquery.ankama"),
        r = require("lodash");
    n.widget("ankama.ak_searchable_list", n.ankama.widget, {
        options: {
            valueNames: [],
            initSelector: ".ak-searchable-list",
            searchFieldCls: "ak-searchable-list-search"
        },
        aItems: [],
        jqSearchField: t,
        _destroy: function() {
            this.element.removeClass("searchable"), this.jqSearchField.val(""), this._search(), this.jqSearchField.bind("keyup"), this.jqSearchField.hide()
        },
        _create: function() {
            var e = this;
            e.element.addClass("searchable"), e.jqSearchField = n("." + this.options.searchFieldCls), e.jqSearchField.show(), e.jqSearchField.bind("keyup", n.proxy(this._search, this))
        },
        _init: function() {
            var e = this;
            this.aItems = [], r.each(this.element.find("li"), function(t) {
                var r = {
                    elm: t,
                    values: []
                };
                if (!e.options.valueNames.length && t.childNodes.length == 1 && t.childNodes[0].nodeType == 3) r.values.push(t.childNodes[0].innerHTML);
                else
                    for (var i = 0; i < e.options.valueNames.length; i++) {
                        var s = e.options.valueNames[i],
                            o = n(t).find("." + s);
                        o.get(0) && r.values.push(o.get(0).innerHTML)
                    }
                e.aItems.push(r)
            })
        },
        _search: function() {
            var e = this,
                t = e.jqSearchField.val();
            r.each(e.aItems, function(e) {
                var r = n.ankama.ak_searchable_list.filter(e.values, t);
                r.length ? e.elm.style.display == "none" && (e.elm.style.display = "block") : e.elm.style.display = "none"
            })
        }
    }), n.extend(n.ankama.ak_searchable_list, {
        escapeRegex: function(e) {
            return e.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, "\\$&")
        },
        filter: function(e, t) {
            var r = new RegExp(n.ankama.ak_searchable_list.escapeRegex(t), "i");
            return n.grep(e, function(e) {
                return r.test(e.label || e.value || e)
            })
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_searchable_list.prototype.enhanceWithin(e.target)
    })
}(this),
function(e, t) {
    "use strict";
    var n = require("ankama.widget"),
        r = require("lodash");
    n.widget("ankama.ak_hidden_no_flash", n.ankama.widget, {
        options: {
            initSelector: ".ak-hidden-no-flash",
            display: "block"
        },
        _create: function() {
            var t = this;
            n.akClientSupportFlash() ? n(t.element).css("display", t.options.display) : n(t.element).css("display", "none"), console.log(t.options.display)
        }
    }), n(e.document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_hidden_no_flash.prototype.
        enhanceWithin(e.target)
    })
}(this),
function(e) {
    var t = require("lodash"),
        n = require("ankama.widget");
    n.widget("ankama.ak_spoiler", n.ankama.widget, {
        options: {
            initSelector: ".ak-spoiler",
            toggleTarget: null
        },
        _create: function() {
            var e = this,
                t = n(".ak-spoiler-content");
            e._manageClasses(t), e.element.on("click", function(r) {
                e.element.is("a") && r.preventDefault(), t.toggle({
                    duration: 0,
                    complete: function(t) {
                        e._manageClasses(n(this))
                    }
                })
            })
        },
        _manageClasses: function(e) {
            var t = this;
            e.is(":hidden") ? t.element.removeClass("spoiler_off").addClass("spoiler_on") : t.element.removeClass("spoiler_on").addClass("spoiler_off")
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_spoiler.prototype.enhanceWithin(e.target, !0)
    })
}(this),
function(e, t) {
    var n = require("ankama.widget"),
        n = require("jquery.ankama"),
        r = require("lodash");
    n.widget("ankama.ak_button_login", n.ankama.widget, {
        options: {
            initSelector: ".ak-button-login"
        },
        _create: function() {
            this._on(this.element, {
                click: function(e) {
                    e.preventDefault(), n("div.qtip:visible").qtip("hide"), e != t && e.target != t && n(e.target).attr("data-from") && n('form[name="connectform"] input[name="from"]').val(n(e.target).attr("data-from")), n.openLoginModal(e)
                }
            })
        }
    }), n.widget("ankama.ak_qtip_close", n.ankama.widget, {
        options: {
            initSelector: ".ak-qtip-close"
        },
        _create: function() {
            this._on(this.element, {
                click: function(e) {
                    e.preventDefault(), e != t && e.target != t && n(e.target).closest(".qtip").qtip("hide")
                }
            })
        }
    }), n.widget("ankama.ak_button_backtotop", n.ankama.widget, {
        options: {
            initSelector: ".ak-backtotop"
        },
        _destroy: function() {},
        _create: function() {
            var t = n(this.element);
            jqWindow = n(e), iWindowHeight = jqWindow.outerHeight(!0), iDocumentHeight = n(document).height(), iButtonBottom = parseInt(t.css("bottom"), 10), iFooterHeight = n("footer").outerHeight(!0), bFixed = !0;
            var r = function() {
                jqWindow.scrollTop() > 500 && t.is(":hidden") && t.show(), jqWindow.scrollTop() < 500 && t.is(":visible") && t.hide(), t.is(":visible") && jqWindow.scrollTop() + iWindowHeight >= n(document).height() - iFooterHeight ? bFixed && (t.css({
                    position: "absolute",
                    top: n(document).height() - iFooterHeight - t.height() - iButtonBottom
                }), bFixed = !1) : bFixed === !1 && (t.css({
                    position: "fixed",
                    top: "",
                    bottom: iButtonBottom
                }), bFixed = !0)
            };
            jqWindow.scroll(r), r(), this._on(this.element, {
                click: function(e) {
                    e.preventDefault(), n("html, body").animate({
                        scrollTop: 0
                    }, 250)
                }
            })
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_button_login.prototype.enhanceWithin(e.target), n.ankama.ak_button_backtotop.prototype.enhanceWithin(e.target), n.ankama.ak_qtip_close.prototype.enhanceWithin(e.target)
    })
}(this),
function(e, t) {
    var n = require("ankama.widget"),
        r = require("lodash");
    n.widget("ankama.ak_tree", n.ankama.widget, {
        options: {
            initSelector: ".ak-tree",
            bSlideToggle: !1,
            sClickableElement: ".ak-haschild",
            sClassOpen: "ak-open",
            sClassClosed: "ak-closed",
            bState: !1,
            sLocalStorageVar: "closedTree"
        },
        _sId: null,
        _create: function() {
            var e = this,
                t = e.options.bState == 1;
            if (t) {
                var r = localStorage.getItem(e.options.sLocalStorageVar);
                r && (r = JSON.parse(r), r && Array.isArray(r) && r.forEach(function(t) {
                    var n = e.element.find("[data-id=" + t + "]");
                    n.length && n.toggleClass(e.options.sClassClosed + " " + e.options.sClassOpen)
                }))
            }
            e.options.bSlideToggle && e.element.on("click", e.options.sClickableElement, function(r) {
                if (n(r.currentTarget.classList[0]).selector == n(r.target).closest(".ak-haschild")[0].classList[0])
                    if (n(r.target).is("a") && n(r.target).attr("href")) n(r.target).hasClass("ak-ajaxloader") && r.preventDefault();
                    else {
                        r.preventDefault(), n(this).toggleClass(e.options.sClassClosed + " " + e.options.sClassOpen);
                        if (t) {
                            var i = n(this).data("id"),
                                s = JSON.parse(localStorage.getItem(e.options.sLocalStorageVar)) || [];
                            if (!i) return !1;
                            s.includes(i) ? s.splice(s.indexOf(i), 1) : s.push(i), localStorage.setItem(e.options.sLocalStorageVar, JSON.stringify(s))
                        }
                    }
            })
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_tree.prototype.enhanceWithin(e.target)
    })
}(this),
function(e, t) {
    var n = require("lodash"),
        r = require("ankama.widget");
    require("jquery.loadmask"), r.widget("ankama.ak_tabs", r.ankama.widget, {
        options: {
            initSelector: ".ak-tabs-container"
        },
        _create: function() {
            var e = this;
            this._super(), this.element.on("click", ".ak-tabs li a", function(t) {
                t.preventDefault(), e.setActive(r(this).parent("li"))
            })
        },
        setActive: function(e) {
            var t = this;
            if (e.hasClass("active")) return;
            e.siblings(".active:first").removeClass("active"), e.addClass("active");
            var n = e.find("a:first");
            n.attr("href")[0] == "#" && t.showTab(r(n.attr("href"), r(t.element)))
        },
        showTab: function(e) {
            r(".ak-tab", r(this.element)).addClass("hide"), r(e).removeClass("hide")
        },
        removeTab: function(e) {
            var t = r(".ak-tabs", this.element).find("a[href=#" + e + "]");
            this.setActive(t.closest("li").siblings(":first")), t.remove(), r("#" + e, this.element).remove()
        },
        addTab: function(e, t, n, i) {
            var s = e + (r(".ak-tabs", this.element).find("li").length + 1);
            r(".ak-tabs", this.element).append('<li class="' + s + " " + i + '"><a href="#' + s + '">' + t + "</a></li>"), r(".ak-tabs-body", this.element).append('<div id="' + s + '" class="ak-tab hide">' + n + "</div>"), this.element.trigger("widgetcreate")
        }
    }), r(document).bind("ready widgetcreate", function(e) {
        r.ankama.ak_tabs.prototype.enhanceWithin(e.target)
    })
}(this),
function(e) {
    var t = require("ankama.widget"),
        n = require("lodash");
    require("jquery.ankama"), t.widget("ankama.ak_panel", t.ankama.widget, {
        COLLAPSIBLE_CLASS: "ak-collapsible",
        options: {
            initSelector: ".ak-panel"
        },
        _destroy: function() {},
        _create: function() {
            var e = this;
            e.element.hasClass(e.COLLAPSIBLE_CLASS) && e._addCollapseEvents()
        },
        _addCollapseEvents: function() {
            var e = this;
            e.element.delegate(".ak-panel-title", "click", function(t) {
                e.element.toggleClass("ak-collapsed")
            })
        }
    }), t(document).bind("ready widgetcreate", function(e) {
        t.ankama.ak_panel.prototype.enhanceWithin(e.target)
    })
}(this),
function(e) {
    "use strict";
    define("utils/pagination", ["require", "exports", "module"], function(e, t, n) {
        var r = e("jquery"),
            i = e("lodash"),
            s = e("eventemitter2").EventEmitter2,
            o = function(e, t, n) {
                return this.emit(e, t, n), !0
            },
            u = function(e) {
                e = e || {}, this.options = i.defaults(e, this.options), o = i.bind(o, this), this._create()
            };
        u.prototype = Object.create(s.prototype), i.extend(u.prototype, {
            _iTotalPages: null,
            element: null,
            options: {
                initSelector: ":akmData(role='pagination')",
                circular: !0,
                currentPage: 0,
                itemelement: "li"
            },
            destroy: function() {
                this.removeAllListeners("beforeclick"), this.removeAllListeners("nextpage"), this.removeAllListeners("previouspage"), this.element && this.element.length && this.element.off("click")
            },
            _set: function(e, t) {
                e === "currentPage" && this._desactiveCurrentIndex(), this.options[e] = t, e === "currentPage" && this._activeCurrentIndex()
            },
            _create: function() {
                var e = this;
                if (!this.options.element) throw "['element' option is required]";
                this.element = r(this.options.element);
                if (!this.element.length) throw "['element' is not a valid selector or dom ellement]";
                if (!this.options.itemelement) throw "['itemelement' option is required]";
                i.bindAll(e), this.element.on("click", e.options.itemelement, this._onClickElement), this._iTotalPages = this.element.find(this.options.itemelement).length, this._activeCurrentIndex()
            },
            truc: function() {
                this.emit("toto")
            },
            _onClickElement: function(e) {
                if (!o("beforeclick")) return !1;
                var t = this.options.itemelement,
                    n = this.element.find(e.currentTarget).index();
                if (t === n) return;
                this._desactiveCurrentIndex(), this._set("currentPage", n), this._activeCurrentIndex(), o("click", e, {
                    clickedindex: n
                })
            },
            gotoIndex: function(e) {
                if (e > this._iTotalPages - 1 || e < 0) return;
                this._set("currentPage", e)
            },
            next: function(e) {
                var t = this.options.circular,
                    n = this.options.currentPage;
                if (n === this._iTotalPages - 1 && !t) return;
                n++, n >= this._iTotalPages && (n = 0), o("nextpage", e, n), this._set("currentPage", n)
            },
            previous: function(e) {
                var t = this.options.circular,
                    n = this.options.currentPage;
                if (n === 0 && !t) return;
                n--, n < 0 && (n = this._iTotalPages - 1), o("previouspage", e, n), this._set("currentPage", n)
            },
            _desactiveCurrentIndex: function() {
                var e = this.element.find(this.options.itemelement + ":eq(" + this.options.currentPage + ")");
                e.removeClass("active")
            },
            _activeCurrentIndex: function() {
                var e = this.element.find(this.options.itemelement + ":eq(" + this.options.currentPage + ")");
                e.addClass("active")
            }
        }), n.exports = u
    })
}(),
function(e, t) {
    "use strict";
    var n = require("ankama.widget"),
        r = require("lodash"),
        i = require("q"),
        s = require("jquery.hammer");
    n.widget("ankama.ak_carouseltouch", n.ankama.widget, {
        options: {
            initSelector: ".ak-carouseltouch",
            showArrows: !0,
            paginationcontrol: "",
            startindex: 0,
            viewwidth: 0,
            width: 0,
            height: 0,
            autoroll: !1,
            itemwidth: 0,
            spacebetween: 6,
            circular: !0,
            responsive: !1,
            snap: !0,
            animationclick: "slide",
            animationduration: 300,
            select: null,
            loaded: null,
            ready: null,
            tap: null
        },
        iCurrentIndex: 0,
        _bCanMove: !0,
        _bFirstInitDone: !1,
        _jqItemsContainer: null,
        _autoRollInterval: null,
        oPaginationModule: null,
        _destroy: function() {
            this._jqItemsContainer.off("release dragstart dragend dragleft dragright swipeleft swiperight", this._handleHammer), this._jqItemsContainer = null, this.oPaginationModule && (this.oPaginationModule.off("click"), this.oPaginationModule.destroy(), this.oPaginationModule = null)
        },
        _create: function() {
            var t = this;
            n(e).on("resize", n.proxy(this._onResize, t)), this._iTotalItems = this.element.children().length, this.options.autoroll && (this.options.itemwidth = 1, this.options.circular = !0, t.element.hover(function() {
                t._stopAutoRoll()
            }, function() {
                t._startAutoRoll()
            }), this._startAutoRoll()), this._generateMarkup(), this.element.addClass("loading"), this.aPositions = [0], this.element.find(".carouselcontainer").hammer({
                drag_lock_to_axis: !0,
                stop_browser_behavior: {
                    touchAction: "pan-y"
                }
            }).on("tap release dragstart dragend dragleft dragright swipeleft swiperight", n.proxy(this._handleHammer, this)), this._trigger("ready", null)
        },
        _startAutoRoll: function() {
            var e = this;
            if (!this.options.autoroll) return !1;
            this._stopAutoRoll(), this._autoRollInterval = window.setTimeout(function() {
                e.gotoIndex(++e.iCurrentIndex)
            }, this.options.autoroll === !0 ? 5e3 : this.options.autoroll)
        },
        _stopAutoRoll: function() {
            clearTimeout(this._autoRollInterval)
        },
        _onClickAction: function(e) {
            var t = this,
                i, s, o, u;
            switch (n(e.currentTarget).data("action")) {
                case "prev":
                    i = Math.abs(this._curX), o = +this.option("viewwidth");
                    var a = 0;
                    r.each(t.aPositions, function(e, t) {
                        if (i <= e) return a = e, !1
                    }), r.each(t.aPositions, function(e, t) {
                        u = t;
                        if (a - o <= e) return !1
                    }), u === this.iCurrentIndex && u--, u < 0 && (i != 0 || !this.option("circular") ? u = 0 : u = this._iTotalItems - 1);
                    break;
                case "next":
                    i = Math.abs(this._curX), o = +this.option("viewwidth"), r.each(t.aPositions, function(e, t) {
                        if (i + o < e) return !1;
                        u = t
                    }), u === this.iCurrentIndex && u++, u >= this._iTotalItems && (this.option("circular") ? u = 0 : u = this._iTotalItems - 1)
            }
            t.gotoIndex(u)
        },
        iMaxWidth: 0,
        _iInitTimes: 0,
        aPositions: null,
        _bLoading: !1,
        _init: function() {
            function s(t) {
                e._trigger("loaded", null), e._onLoaded()
            }
            var e = this;
            if (e._bLoading) return;
            e._bLoading = !0, e.element.css("opacity", 1), e._jqItemsContainer = this.element.find(".carouselcontainer"), e._bCanMove = !0, e.bShouldRedraw = !e._bFirstInitDone || e.options.responsive || e.bShouldRedraw, e.options.itemwidth !== t && e.options.itemwidth === 1 ? (e.element.css("width", e.element.offsetParent().outerWidth()), e.element.find("img").length == 1 && e.element.find("img").addClass("img-maxresponsive")) : e.element.width() || e.element.css("width", e.element.parent().outerWidth());
            if (e.options.viewwidth) e.element.width(e.options.viewwidth);
            else if (e.element.width() === 0) {
                if (e._iInitTimes < 30) {
                    setTimeout(function() {
                        e._iInitTimes++, e._init()
                    }, 0);
                    return
                }
                throw new Error("Impossible de dÃ©finir la taille du conteneur")
            }
            e._setOption("viewwidth", e.element.width()), e.iTotalItems = e.element.find(".item").length, e.iMaxWidth = 0, e._maxX = 0;
            var r;
            e.element.find(".item").each(function(t, s) {
                var o, u = n(s),
                    a = u.children().first();
                if (e.options.itemwidth && e.options.itemwidth <= 1) e.options.spacebetween = e.options.itemwidth === 1 ? 0 : e.options.spacebetween, u.css("width", e.options.viewwidth * e.options.itemwidth + "px"), o = u.width() + e.options.spacebetween, e.aPositions[t + 1] = o;
                else if (a.attr("width")) u.css("width", a.attr("width") + "px"), o = a.width() + e.options.spacebetween, e.aPositions[t + 1] = o;
                else if (a[0].tagName == "IMG" || a.find("img").length) {
                    r || (r = []);
                    var f;
                    a.find("img").length ? f = a.find("img") : f = a;
                    var l = 0,
                        c = i.defer();
                    r.push(c.promise), e.aPositions[t + 1] = 0, f.each(function(r, i) {
                        i.complete ? (u.width() < i.width && u.css("width", i.width + "px"), o = u.width() + e.options.spacebetween, e.aPositions[t + 1] = Math.max(e.aPositions[t + 1], o), l++, f.length == l && c.resolve("loaded")) : (n(i).one("error", {
                            jqLi: u,
                            iIndex: t
                        }, function(e) {
                            e.preventDefault(), c.reject(new Error("Error load"))
                        }), n(i).one("load", {
                            jqLi: u,
                            iIndex: t
                        }, function(t) {
                            u.width() < i.width && t.data.jqLi.css("width", this.width + "px"), o = t.data.jqLi.width() + e.options.spacebetween, e.aPositions[t.data.iIndex + 1] = Math.max(e.aPositions[t.data.iIndex + 1], o), l++, f.length == l && c.resolve("loaded")
                        }))
                    })
                } else a.css("width") != "0px" ? e.aPositions[t + 1] = a.width() + e.options.spacebetween : u.css("display", "none")
            }), r ? (i.all(r).then(s), setTimeout(s, 2e3), setTimeout(s, 1e4)) : (e._trigger("loaded", null), e._onLoaded())
        },
        _firstAnimated: !1,
        _bLoaded: !1,
        _onLoaded: function() {
            var e = this;
            e._bLoading = !1;
            if (e.bShouldRedraw) {
                var t = 0;
                r.each(e.aPositions, function(n, r) {
                    t += n, e.aPositions[r] = t
                }), e.iMaxWidth = e.aPositions[e.aPositions.length - 1]
            }
            e._bLoaded = !0, e._setOption("width", e.iMaxWidth), e._maxX = e.option("width") - e.option("viewwidth"), e.options.height && e.element.height(e.options.height ? e.options.height : e.element.outerHeight(!0));
            var i = 0;
            e.element.find(".item").each(function(t, r) {
                e.bShouldRedraw && (n(r).height(e.element.height()), n(r).css("line-height", e.element.height() + "px")), r.style.left = i + "px", i += n(r).width() + e.options.spacebetween
            }), e.options.width < e.options.viewwidth || e.options.itemwidth === 1 && e.iTotalItems <= 1 ? (e.element.find("[data-action]").hide(), e._jqItemsContainer.animate({
                left: 0
            }, e.options.animationduration), e._bCanMove = !1) : (e.element.find("[data-action]").show(), e._bCanMove = !0), !e._firstAnimated && !e.element.hasClass("firstanim") ? (e.element.addClass("firstanim"), e.element.animate({
                opacity: 0
            }, 200, function() {
                e.element.removeClass("loading"), e.element.animate({
                    opacity: 1
                }, 500, function() {
                    e._firstAnimated = !0, e.element.removeClass("firstanim"), e._onFinishInit()
                })
            })) : e._onFinishInit()
        },
        _onFinishInit: function() {
            var e = this;
            e.bShouldRedraw = !1, typeof e.options.startindex != "undefined" && (e.gotoIndex(e.options.startindex, !1), delete e.options.startindex), e._bFirstInitDone = !0, e._trigger("inited")
        },
        resize: function() {
            self._onResize()
        },
        _iResizeTimerId: null,
        _onResize: function() {
            var e = this;
            e.bShouldRedraw = !0, typeof this._iResizeTimerId != "undefined" && clearTimeout(this._iResizeTimerId), this._iResizeTimerId = setTimeout(function() {
                e._init(), e._bCanMove && e.iCurrentIndex !== t && e.gotoIndex(e.iCurrentIndex)
            }, 100)
        },
        _curX: 0,
        _maxX: 0,
        _bSaveX: !0,
        _animateTo: function(e, n) {
            var r = this;
            if (!r._bCanMove) return;
            n = n === t ? !0 : n, this._curX = -this.element.find(".items .item:eq(" + e + ")").css("left").replace("px", ""), Math.abs(this._curX) > this._maxX && (this._curX = -this._maxX), this._bBlockUI = !0, r.options.animationclick === "fade" ? r._jqItemsContainer.fadeTo(n ? r.options.animationduration : 0, 0, function() {
                r._jqItemsContainer.css("left", r._curX + "px"), r._jqItemsContainer.fadeTo(n ? r.options.animationduration : 0, 1, function() {
                    r.iCurrentIndex = e, r._bBlockUI = !1
                })
            }) : r._jqItemsContainer.animate({
                left: this._curX
            }, n ? r.options.animationduration : 0, function() {
                r.iCurrentIndex = e, r._bBlockUI = !1
            })
        },
        gotoIndex: function(e, n) {
            if (this._bBlockUI) return !1;
            e < 0 ? this.option("circular") ? e = this._iTotalItems - 1 : e = 0 : e >= this._iTotalItems && (this.option("circular") ? e = 0 : e = this._iTotalItems - 1);
            if (e !== t) {
                this._startAutoRoll(), this._animateTo(e, n), this.oPaginationModule && this.oPaginationModule.gotoIndex(e);
                var r = this.element.find(".item:eq(" + e + ")");
                this._trigger("select", null, {
                    index: e,
                    item: r
                })
            }
        },
        _getCurrentIndex: function(e) {
            var t = this,
                n = 0;
            return r.each(t.aPositions, function(t, r) {
                if (e < t) return !1;
                n = r
            }), n
        },
        _getLeftForIndex: function(e) {
            return this.aPositions[e]
        },
        _handleHammer: function(t) {
            if (!("gesture" in t)) return;
            var n = this;
            r.contains([s.DIRECTION_UP, s.DIRECTION_DOWN], t.gesture.direction) || t.gesture.preventDefault();
            var i = n.options.itemwidth === 1 ? !0 : !1;
            if (!i) return !1;
            switch (t.type) {
                case "dragright":
                case "dragleft":
                    if (i) return;
                    var o = this._curX + +t.gesture.deltaX,
                        u = this._getCurrentIndex(Math.abs(o));
                    if (t.gesture.direction == s.DIRECTION_RIGHT && o > 0) {
                        this._curX = 0, this._bSaveX = !1, t.gesture.stopPropagation();
                        return
                    }
                    if (t.gesture.direction == s.DIRECTION_LEFT && Math.abs(o) > this._maxX) {
                        this._curX = -this._maxX, this._bSaveX = !1, t.gesture.stopPropagation();
                        return
                    }
                    this.iCurrentIndex = u, this.oPaginationModule && this.oPaginationModule.gotoIndex(u), this._bSaveX = !0, this._jqItemsContainer.css("left", o + "px");
                    break;
                case "swipeleft":
                    if (!i) return !1;
                    this._curX += t.gesture.deltaX;
                    var a = this.iCurrentIndex;
                    a++, this._bSaveX = !0, this.gotoIndex(a), t.gesture.stopDetect();
                    break;
                case "swiperight":
                    if (!i) return !1;
                    this._curX += t.gesture.deltaX;
                    var a = this.iCurrentIndex;
                    a--, this._bSaveX = !0, this.gotoIndex(a), this._bSaveX = !0, t.gesture.stopDetect();
                    break;
                case "release":
                    this._bSaveX && (this._curX += t.gesture.deltaX), this._bSaveX = !0;
                    break;
                case "tap":
                    this._trigger("tap", t), t.gesture.stopDetect();
                    return
            }
            if (r.contains(["swipeleft", "swiperight"], t.type)) return;
            if (this.options.snap && r.contains(["release"], t.type)) {
                t.stopImmediatePropagation(), t.preventDefault();
                var f = Math.abs(n._curX),
                    a = n._getCurrentIndex(f),
                    l = n._getLeftForIndex(a),
                    c = n._getLeftForIndex(a + 1),
                    h = c - l;
                f - l > h / 2 && a++, this.gotoIndex(a)
            }
        },
        _generateMarkup: function() {
            var e = this.element.children();
            r.each(e, function(e, t) {
                n(e).wrap(n('<li class="item">')), n(e).parent().data(n(e).data()), n(e).data({})
            }, this), this.element.find(".item").wrapAll('<ul class="items">'), this.element.find(".items").wrap('<div class="carouselcontainer">'), this.options.showArrows === !0 && (this.element.siblings("[data-action]").length ? this.element.siblings("[data-action]").on("click", n.proxy(this._onClickAction, this)) : (this.element.on("click", "[data-action]", n.proxy(this._onClickAction, this)), this.element.prepend('<button data-action="prev" class="prev arrow arrow-left">'), this.element.prepend('<button data-action="next" class="next arrow arrow-right">')));
            if (this.options.paginationcontrol !== "") {
                var t = "",
                    i;
                for (i = 0; i < this._iTotalItems; i++) t += "<li><span>" + (i + 1) + "</span></li>";
                this.element[this.option("paginationcontrol") == "next" ? "after" : "before"]('<ul class="pagination">'), this.element[this.option("paginationcontrol") == "next" ? "next" : "prev"]().append(n("<ul>").append(t));
                var s = require("utils/pagination"),
                    o = new s({
                        element: this.element[this.option("paginationcontrol") == "next" ? "next" : "prev"](),
                        itemelement: "li",
                        circular: this.options.circular
                    });
                this.oPaginationModule = o, this.oPaginationModule.on("click", n.proxy(function(e, t) {
                    this.gotoIndex(t.clickedindex)
                }, this))
            }
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_carouseltouch.prototype.enhanceWithin(e.target)
    })
}(this),
function(e) {
    var t = require("lodash"),
        n = require("ankama.widget"),
        r = require("jquery.hammer");
    n.widget("ankama.ak_background_carousel", n.ankama.widget, {
        options: {
            initSelector: ".ak-background-carousel"
        },
        _jqBackgroundItems: [],
        _jqCcontentItems: [],
        _jqNavButtons: [],
        _iIndex: 0,
        _bAutoToggle: !0,
        _iAutoToggleTime: 3e3,
        _bToggleOn: !1,
        _iContainerMaxWidth: null,
        _create: function() {
            var i = this,
                s = n("body");
            s.children("header,div,footer").css("z-index", "100");
            var o = n(i.element).children("div.ak-background-root");
            o.css("z-index", "10"), i._jqBackgroundItems = o.children("div.ak-background-item"), i._jqBackgroundItems.css("height", s.height()).css("z-index", "10").hide().first().css("z-index", "12").show(), s.append(o);
            var u = n(i.element).children("div.ak-content-root");
            i._jqContentItems = u.find("div.ak-content-item"), i._jqContentItems.css("cursor", "pointer").hide().first().show(), i._jqContentItems.click(function() {
                var r = n(this).find(".ak-redirect-button").attr("href");
                t.isEmpty(r) || e.location.replace(r)
            }), r(i._jqContentItems).on("swipeleft", function() {
                i._toggleItems(!0), i._bAutoToggle = !1
            }), r(i._jqContentItems).on("swiperight", function() {
                i._toggleItems(!1), i._bAutoToggle = !1
            }), i._jqNavButtons = u.find("a.ak-content-nav-button"), i._jqNavButtons.click(function(e) {
                e.preventDefault(), i._toggleItems(n(this).attr("href") == "#next"), i._bAutoToggle = !1
            }), i._tooglePlay(), i._initResizeHandler(n(i.element).parent("div"))
        },
        _initResizeHandler: function(r) {
            var i = this,
                s = r.css("max-width");
            if (!t.isEmpty(s)) {
                var o = new RegExp("(\\d+)px"),
                    u = o.exec(s, "g");
                s = t.isArray(u) && !t.isEmpty(u) ? +u[1] : null, i._switchBackgroundSize(s), n(e).resize({
                    containerMaxWidth: s
                }, function() {
                    i._switchBackgroundSize(s)
                })
            }
        },
        _switchBackgroundSize: function(t) {
            var r = this,
                i = n(e).width(),
                s = r._jqBackgroundItems.first().hasClass("phone");
            if (i < t) {
                r._toogleNavButtons(!1);
                var o = i / t * 100;
                o < 33 && !s && r._jqBackgroundItems.addClass("phone"), o > 33 && s && r._jqBackgroundItems.removeClass("phone")
            } else r._toogleNavButtons(!0), s && r._jqBackgroundItems.removeClass("phone")
        },
        _toggleItems: function(e) {
            var r = this;
            if (r._bToggleOn) return !1;
            r._bToggleOn = !0, t.isBoolean(e) && (e = e == 1 ? r._iIndex + 1 <= r._jqBackgroundItems.length - 1 ? r._iIndex + 1 : 0 : r._iIndex - 1 >= 0 ? r._iIndex - 1 : r._jqBackgroundItems.length - 1);
            var i = n(r._jqBackgroundItems.get(r._iIndex)),
                s = n(r._jqBackgroundItems.get(e)),
                o = n(r._jqContentItems.get(r._iIndex)),
                u = n(r._jqContentItems.get(e));
            o.fadeOut("slow", function() {
                u.fadeIn("fast", function() {
                    o.css("display", "none"), u.css("display", "block")
                })
            }), s.css("z-index", "11").css("display", "block"), i.fadeOut("slow", function() {
                i.css("z-index", "10"), i.css("display", "none"), s.css("z-index", "12"), r._iIndex = e, r._bToggleOn = !1, r._bAutoToggle == 1 && r._tooglePlay()
            })
        },
        _tooglePlay: function() {
            var t = this;
            e.setTimeout(function() {
                t._bAutoToggle == 1 && t._toggleItems(!0)
            }, t._iAutoToggleTime)
        },
        _toogleNavButtons: function(e) {
            var t = this;
            e ? t._jqNavButtons.show() : t._jqNavButtons.hide()
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_background_carousel.prototype.enhanceWithin(e.target, !0)
    })
}(this),
function(e, t) {
    define("utils/shadowshim", ["require", "exports", "module"], function(t, n, r) {
        function i() {
            var n = t("jquery"),
                r = t("lodash");
            r.each(n('a[rel^="shadowbox"]:not(:data(ssr))', e.document.body), function(e) {
                n(e).data("ssr", !0), n(e).attr("id", ""), n(e).addClass("ak-lightboxtouch");
                var t = n(e).attr("rel").replace(/shadowbox;?/, ""),
                    i = "",
                    s = 800,
                    o = 600;
                r.each(t.split(";"), function(e) {
                    var t = e.split("="),
                        r = t[0],
                        u = t[1];
                    switch (r) {
                        case "width":
                            s = u;
                            break;
                        case "height":
                            o = u;
                            break;
                        case "options":
                            if (u) {
                                u = u.replace(/\'/g, '"');
                                var a = n.parseJSON(u);
                                "flashVars" in a && (i = n.param(a.flashVars))
                            }
                    }
                }), n(e).data("options", {
                    type: "swf",
                    mainClass: "shadowbox",
                    swfPath: e.href,
                    swfFlashvars: i,
                    swfWidth: s,
                    swfHeight: o
                })
            }), e.Shadowbox = {}, e.Shadowbox.open = function(t) {
                var r;
                Shadowbox.open.caller.arguments.length ? r = n(Shadowbox.open.caller.arguments[0].target) : r = n("<div>");
                if (!r.hasClass("ak-lightboxtouch")) {
                    r.addClass("ak-lightboxtouch");
                    var i = "",
                        s = 800,
                        o = 600;
                    "width" in t && (s = t.width), "height" in t && (o = t.height), r.data("options", {
                        type: t.player === "html" ? "inline" : "swf",
                        mainClass: "shadowbox",
                        items: {
                            src: t.content
                        },
                        callbacks: {
                            open: t.options && "onFinish" in t.options ? t.options.onFinish : n.noop
                        }
                    }), r.ak_lightboxtouch(), r.trigger("click")
                }
            }
        }
        r.exports = i
    })
}(window),
function(e, t) {
    "use strict";
    var n = require("ankama.widget"),
        r = require("lodash");
    require("jquery.magnific-popup"), n.widget("ankama.ak_lightboxtouch", n.ankama.widget, {
        options: {
            initSelector: ".ak-lightboxtouch",
            touchSelector: ".ak-carouseltouch",
            thumbsHeight: "105px",
            open: null,
            close: null
        },
        oMasterCarousel: null,
        oThumbsCarousel: null,
        jqContainerCarousel: null,
        sGalleryClassName: "",
        _destroy: function() {
            self.jqContainerCarousel.remove(), self.jqContainerCarousel = null, this.oMasterCarousel && (this.oMasterCarousel = this.oThumbsCarousel = null)
        },
        _onResize: function(e) {
            this.resize()
        },
        resize: function() {
            this.oMasterCarousel && this.oThumbsCarousel && (this.oMasterCarousel.option({
                viewwidth: n(e).width(),
                itemwidth: 1,
                circular: !0,
                responsive: !0,
                snap: !0,
                height: n(e).height() * .8
            }), this.oThumbsCarousel.option({
                viewwidth: n(e).width(),
                circular: !0,
                snap: !1,
                height: this.options.thumbsHeight
            }))
        },
        _create: function() {
            var i = this;
            this.element.attr("id") || this.element.uniqueId();
            if (!this.options.type) throw new Error("A type is needed");
            if (this.options.gallery) this.sGalleryClassName = "lb-" + this.options.gallery, this.element.addClass(this.sGalleryClassName), this.element.off("click").click(function(t) {
                i._initGallery(), t.preventDefault();
                var s = r.findIndex(n.ankama.ak_lightboxtouch.oCachesGalleries[i.sGalleryClassName], function(e) {
                    return t.currentTarget.id === e.id
                });
                i._initCarousels(s), n.magnificPopup.open({
                    closeOnBgClick: !0,
                    items: [{
                        src: i.jqContainerCarousel,
                        type: "inline"
                    }],
                    callbacks: {
                        open: function() {
                            n.ankama.ak_lightboxtouch.bActive = !0, n(e).on("keyup", n.proxy(i._onContextKeyUp, i)), n(e).trigger("resize")
                        },
                        close: function() {
                            n.ankama.ak_lightboxtouch.bActive = !1, n.magnificPopup.instance.container.off("tap"), n(e).off("resize", n.proxy(this._onResize, this)), n(e).off("keyup", n.proxy(i._onContextKeyUp, i)), r.isNull(i.oMasterCarousel) || i.oMasterCarousel.destroy(), r.isNull(i.oThumbsCarousel) || i.oThumbsCarousel.destroy(), i.jqContainerCarousel.remove(), i.jqContainerCarousel = null, i.oMasterCarousel = null, i.oThumbsCarousel = null
                        }
                    },
                    modal: !0
                });
                var o = n.magnificPopup.instance;
                o.container.attr("id", Math.ceil(Math.random() * 1e5)), o.container.on("tap", function(e) {
                    if (e.target.tagName == "BUTTON") return;
                    e.target.tagName == "IMG" && i.oMasterCarousel ? i.oMasterCarousel.gotoIndex(i.oMasterCarousel.iCurrentIndex + 1) : o.close(), i.oMasterCarousel && (i.oMasterCarousel.gotoIndex(i.oMasterCarousel.iCurrentIndex + 1), i.oThumbsCarousel.gotoIndex(i.oThumbsCarousel.iCurrentIndex + 1))
                })
            }), n(e).off("resize", n.proxy(this._onResize, this)), n(e).on("resize", n.proxy(this._onResize, this));
            else {
                i.options.type == "swf" && "swfPath" in i.options && (i.options = r.merge(r.clone(i.options), {
                    type: "inline",
                    midClick: !0,
                    items: {
                        src: '<button title="Close (Esc)" type="button" class="mfp-close">Ã—</button><object data="' + i.options.swfPath + '" ' + 'type="application/x-shockwave-flash" id="sb-content" ' + 'width="' + ("swfFlashvars" in i.options ? i.options.swfWidth : "800") + '" ' + 'height="' + ("swfFlashvars" in i.options ? i.options.swfHeight : "600") + '">' + '<param name="bgcolor" value="">' + '<param name="allowFullscreen" value="true">' + '<param name="flashvars" value="' + ("swfFlashvars" in i.options ? i.options.swfFlashvars : "") + '">' + '<param name="expressInstaller" value="http://staticns.ankama.com/global/swf/expressInstall.swf">' + '<param name="movie" value="' + i.options.swfPath + '">' + '<param name="wmode" value="opaque">' + "</object>"
                    }
                }));
                var s = r.merge({
                    callbacks: {
                        beforeOpen: function() {
                            n(e.document).trigger("widgetcreate")
                        },
                        open: function() {
                            i._trigger("open")
                        },
                        close: function() {
                            r.isNull(i.oMasterCarousel) || i.oMasterCarousel.destroy(), r.isNull(i.oThumbsCarousel) || i.oThumbsCarousel.destroy(), i._trigger("close")
                        }
                    }
                }, r.clone(i.options));
                n(this.element).magnificPopup(s)
            }
        },
        _initGallery: function() {
            n.ankama.ak_lightboxtouch.oCachesGalleries || (n.ankama.ak_lightboxtouch.oCachesGalleries = {});
            if (!n.ankama.ak_lightboxtouch.oCachesGalleries || !(this.sGalleryClassName in n.ankama.ak_lightboxtouch.oCachesGalleries)) n.ankama.ak_lightboxtouch.oCachesGalleries[this.sGalleryClassName] = e.document.getElementsByClassName(this.sGalleryClassName)
        },
        _initCarousels: function(i) {
            var s = this,
                o = n.ankama.ak_lightboxtouch.oCachesGalleries[s.sGalleryClassName],
                u = n('<div class="lb-master ' + s.options.touchSelector.slice(1) + " gallery-" + s.sGalleryClassName + '">'),
                a = n('<div class="lb-thumbs ' + s.options.touchSelector.slice(1) + " gallery-" + s.sGalleryClassName + '">');
            r(o).each(function(e) {
                var t = n(e),
                    r = t.ak_lightboxtouch("option", "type"),
                    i = null;
                switch (r) {
                    case "image":
                        i = n('<img style="max-height: 100%;max-width:100%" src="' + e.href + '" />');
                        break;
                    case "iframe":
                        i = n('<iframe width="90%" height="90%" src="' + e.href + '" frameborder="0" allowfullscreen></iframe>');
                        break;
                    case "inline":
                        t.data("src") ? i = n(t.data("src")).clone().css("display", "block") : i = t.next().clone().css("display", "block")
                }
                t.data("title") || t.data("title", t.attr("title")), i.data(t.data()), u.append(i), a.append('<img src="' + t.data("thumb") + '" />')
            }), s.jqContainerCarousel = n('<div class="lb-container ak-carousel">').uniqueId(), s.jqContainerCarousel.append('<button title="Close (Esc)" type="button" class="mfp-close">Ã—</button>'), s.jqContainerCarousel.append(u), s.jqContainerCarousel.append(n('<div class="mfp-bottom-bar">').append(n('<div class="mfp-title">').text(""), a)), u.ak_carouseltouch({
                viewwidth: n(e).width(),
                itemwidth: 1,
                startindex: i,
                circular: !0,
                responsive: !0,
                animationclick: "fade",
                snap: !0,
                height: n(e).height() * .8,
                select: n.proxy(s._onMasterSelect, s)
            }), a.ak_carouseltouch({
                viewwidth: n(e).width(),
                startindex: i,
                circular: !0,
                snap: !1,
                height: this.options.thumbsHeight
            }), s.oMasterCarousel = u.data("ankama-ak_carouseltouch"), s.oThumbsCarousel = a.data("ankama-ak_carouseltouch"), a.on("tap", ".item", n.proxy(s._onClickThumbItem, s)), n(".item", a).css("cursor", "pointer")
        },
        _jqTitle: null,
        _onMasterSelect: function(e, t) {
            var n = this;
            n._jqTitle = n.jqContainerCarousel.find(".mfp-title"), setTimeout(function() {
                var e = t.item.data("title") || t.item.attr("title");
                e ? (n._jqTitle.text(e), t.item.data("description") && n._jqTitle.append("<small>" + t.item.data("description") + "</small>")) : n._jqTitle.text(""), setTimeout(function() {
                    n.oThumbsCarousel && n.oMasterCarousel && n.oThumbsCarousel.gotoIndex(t.index)
                }, 0)
            }, 0)
        },
        _onClickThumbItem: function(e) {
            var t = n(e.currentTarget).index();
            this.oMasterCarousel.gotoIndex(t);
            var r = this._getElementByIndex(t)
        },
        _getElementByIndex: function(e) {
            var t = n.ankama.ak_lightboxtouch.oCachesGalleries[this.sGalleryClassName],
                i = 0,
                s = r.find(t, function(t) {
                    return i === e
                });
            return s
        },
        _onContextKeyUp: function(e) {
            if (!n.ankama.ak_lightboxtouch.bActive) return;
            (e.keyCode == 37 || e.keyCode == 38) && this.oMasterCarousel.gotoIndex(this.oMasterCarousel.iCurrentIndex - 1), (e.keyCode == 39 || e.keyCode == 40) && this.oMasterCarousel.gotoIndex(this.oMasterCarousel.iCurrentIndex + 1), e.keyCode == 27 && n.magnificPopup.instance.close()
        }
    }), n.ankama.ak_lightboxtouch.bActive = !1, n(e.document).bind("ready widgetcreate", function(e) {
        require("utils/shadowshim")(), n.ankama.ak_lightboxtouch.prototype.enhanceWithin(e.target)
    })
}(this),
function(e, t) {
    "use strict";
    define("utils/scrollsmooth", ["require", "exports", "module"], function(t, n, r) {
        function s() {
            function t() {
                var t = e.location.hash;
                i(t.replace("jt_", "")).length && i.smoothScroll({
                    scrollTarget: t.replace("jt_", ""),
                    speed: 400
                })
            }
            i(document.body).on("pjax:success", function() {
                setTimeout(function() {
                    t()
                }, 400)
            }), i(e).on("popstate", function() {
                t()
            }), i(e).on("hashchange", function(e) {
                return e.preventDefault(), t(), !1
            })
        }
        var i = t("jquery.smoothscroll");
        r.exports = s
    }), require("utils/scrollsmooth")()
}(this),
function(e, t) {
    var n = require("ankama.widget"),
        r = require("lodash");
    n.widget("ankama.ak_poll", n.ankama.widget, {
        options: {
            initSelector: ".ak-poll"
        },
        _create: function() {
            var e = this;
            e.jqElement = jqElement = n(e.element), n(".ak-poll-show-results", jqElement).bind("click", function() {
                e.element.toggleClass("open")
            })
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_poll.prototype.enhanceWithin(e.target)
    })
}(this),
function(e, t) {
    var n = require("ankama.widget"),
        r = require("lodash");
    require("jquery.loadmask"), n.widget("ankama.ak_pollform", n.ankama.ak_simpleform, {
        options: {
            initSelector: ".ak-pollform"
        },
        _create: function() {
            this._superApply(arguments)
        },
        _init: function() {
            this._superApply(arguments)
        },
        submit: function() {
            var e = this,
                t = ':input[name="answer"]',
                i = ':input:checked[name="answer"]';
            n(".ak-poll-error", e.element).addClass("hidden");
            if (e.option("bRequiredLogin") === !0) return n.openLoginModal(), !1;
            var s = {
                    iPollId: n(':input[name="poll"]', e.element).val() || null
                },
                o = n(r.max(n(t, e.element), function(e) {
                    return parseInt(n(e).data().questionorder, 10)
                })).data().questionorder,
                u = n(r.max(n(i, e.element), function(e) {
                    return parseInt(n(e).data().questionorder, 10)
                })).data().questionorder || 0;
            if (u < o) return n(".ak-poll-error", e.element).removeClass("hidden"), !1;
            var a = new Array(u + 1);
            for (var f = 1; f <= u; f++) a[f] = [];
            r.each(n(i, e.element), function(e) {
                var t = n(e);
                a[parseInt(t.data().questionorder, 10)].push(t.val())
            }), s.aAnswer = a, e.option("bRequiredLogin") !== null && (s.bRequiredLogin = e.option("bRequiredLogin") === !0 ? "1" : "0"), oFormattedParams = [];
            for (var f in s) oFormattedParams.push({
                name: f,
                value: f == "aAnswer" ? JSON.stringify(s[f]) : s[f]
            });
            e.options.pjaxSettings.data = oFormattedParams, e._superApply(arguments)
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_pollform.prototype.enhanceWithin(e.target)
    })
}(this),
function(e, t) {
    var n = require("ankama.widget");
    n.widget("ankama.ak_accordion", n.ankama.widget, {
        options: {
            initSelector: ".ak-accordion",
            multiple: !1
        },
        _animate: function(e, t) {
            if (!e.hasClass("anim")) {
                if (t && e.hasClass("out")) return;
                e.addClass("anim"), e[t ? "slideUp" : "slideToggle"]("fast", function() {
                    e.hasClass("in") ? e.removeClass("in").removeClass("anim").addClass("out") : e.removeClass("out").removeClass("anim").addClass("in")
                })
            }
        },
        _create: function() {
            this.options.multiple = n(this.element).data("multiple");
            var e = this;
            n(this.element).find("a.accordion-toggle").click(function() {
                var t = n(this).closest("div.panel-heading").next();
                e.options.multiple || n(e.element).find("div.panel-collapse").each(function(r, i) {
                    n(i).is(t) || e._animate(n(i), !0)
                }), e._animate(t)
            })
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_accordion
            .prototype.enhanceWithin(e.target)
    })
}(this),
function(e, t) {
    "use strict";
    var n = require("ankama.widget"),
        r = require("lodash");
    n.widget("ankama.ak_tooltip_linker", n.ankama.widget, {
        options: {
            initSelector: ".ak-tooltip-linker"
        },
        _create: function() {
            var e = this,
                t = ".ak-pagination.pagination li";
            n("a[data-action]", t).bind("click", function(t) {
                t.preventDefault(), e._changeTabPage(t)
            });
            var i = 0;
            r.each(n(".ak-localepage", e.element), function(e) {
                var t = n(".ak-list-element", n(e)),
                    s = t.length,
                    o = r(n.map(t, function(e) {
                        return n(e).outerHeight(!0)
                    })),
                    u = o.reduce(function(e, t) {
                        return e + t
                    });
                u > i && (i = u)
            }), i && n(".tab-content > div, .ak-tabs-inner-content", e.element).css({
                "min-height": i
            })
        },
        _changeTabPage: function(e) {
            var t = this,
                r = n(e.currentTarget);
            t["_" + r.data().action + "TabPage"].apply(t, arguments)
        },
        _prevTabPage: function(e) {
            var t = this,
                r = n(e.currentTarget),
                i = n(".ak-localepage:visible", t.element),
                s = i.prev(":hidden");
            if (!s.length) return;
            n('a[data-action="next"]', t.element).parent().removeClass("disabled"), i.toggleClass("hide"), s.toggleClass("hide"), s.prev(":hidden").length || r.parent().addClass("disabled")
        },
        _nextTabPage: function(e) {
            var t = this,
                r = n(e.currentTarget),
                i = n(".ak-localepage:visible", t.element),
                s = i.next(":hidden");
            if (!s.length) return;
            n('a[data-action="prev"]', t.element).parent().removeClass("disabled"), i.toggleClass("hide"), s.toggleClass("hide"), s.next(":hidden").length || r.parent().addClass("disabled")
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_tooltip_linker.prototype.enhanceWithin(e.target)
    })
}(this),
function(e) {
    var t = require("lodash"),
        n = require("ankama.widget");
    require("jquery.magnific-popup"), n.widget("ankama.ak_linker", n.ankama.widget, {
        CLASS_CLOSE: "ak-linker-close",
        DISPLAY_TYPE_INNER: "INNER",
        DISPLAY_TYPE_SHADOWBOX: "SHADOWBOX",
        DISPLAY_TYPE_TOOLTIP: "TOOLTIP",
        _bCache: !0,
        _oCache: {},
        options: {
            initSelector: ".ak-linker",
            iShowDelay: 500,
            iHideDelay: 250
        },
        _setProperties: function(e) {
            var t = this;
            t._sId = t.option("linker-id"), t._sDisplayType = t.option("linker-display-type"), t._sPath = t.option("linker-path"), t._aQueryDatas = t.option("linker-query-datas"), t._sPath && (t._sName = t._ucwords((t._sPath + "").split("?")[0].replace(/\//g, " ")).replace(/ /g, ""))
        },
        _create: function() {
            var e = this;
            e._setProperties(e.element);
            if (n.isTouchDevice() || n.inArray(n.getCurrentProfile(), ["desktop", "largedesktop"]) == -1) return;
            e._sDisplayType == e.DISPLAY_TYPE_TOOLTIP ? e._createTooltip() : e._addEvents()
        },
        _init: function() {
            var e = this;
            n(".ak-linker-content").hide(), e._initLoading()
        },
        _createTooltip: function() {
            var t = this;
            n(t.element).ak_tooltip({
                manual: !0,
                forceOnTouch: !0,
                tooltip: {
                    overwrite: !1,
                    id: t.option("linker-id"),
                    content: {
                        button: this.element.parents(".visible-xs").length,
                        text: function(e, n) {
                            return n.tooltip.find(".qtip-close").removeClass("show"), n.tooltip.find(".qtip-close").addClass("hide"), t._getContent(function(e, t) {
                                n.set("content.text", e.html()), setTimeout(function() {
                                    n.reposition(null, !1)
                                }, 0), n.tooltip.trigger("widgetcreate"), n.tooltip.find(".qtip-close").removeClass("hide"), n.tooltip.find(".qtip-close").addClass("show")
                            }), ""
                        }
                    },
                    show: {
                        event: n.isTouchDevice() ? "click" : "mouseenter",
                        effect: !1,
                        delay: t.options.iShowDelay
                    },
                    hide: {
                        event: n.isTouchDevice() ? !1 : "mouseleave",
                        effect: !1,
                        delay: t.options.iHideDelay,
                        fixed: !0,
                        leave: !1
                    },
                    position: {
                        target: t.element,
                        effect: !1,
                        my: "top left",
                        at: "bottom right",
                        viewport: n(e),
                        adjust: {
                            method: "shift"
                        }
                    },
                    fullscreen: n.getCurrentProfile() === "mobile",
                    style: {
                        classes: "ak-linker-tooltip"
                    }
                }
            })
        },
        _addEvents: function() {
            var e = this;
            e.element.on("mouseenter", ".ak-linker-hover", function(t) {
                t.preventDefault();
                if (e._bShowLoading) return;
                var r = n(t.currentTarget);
                e._show(r)
            }), e.element.on("click", ".ak-linker-click", function(t) {
                t.preventDefault(), t.stopPropagation();
                if (e._bShowLoading) return;
                e._show(n(t.currentTarget))
            })
        },
        _show: function(e) {
            var t = this,
                r = n(e.currentTarget);
            t._init(), t._setProperties(r), t._getContent(function(e) {
                switch (t._sDisplayType) {
                    case t.DISPLAY_TYPE_SHADOWBOX:
                        n.magnificPopup.open({
                            items: {
                                src: e,
                                type: "inline"
                            },
                            closeBtnInside: !1
                        });
                        break;
                    case t.DISPLAY_TYPE_INNER:
                        e.appendTo(t.element)
                }
                e.on("click", "." + t.CLASS_CLOSE, function(r) {
                    r.preventDefault(), t._sDisplayType == t.DISPLAY_TYPE_SHADOWBOX ? n.magnificPopup.close() : e.hide()
                })
            })
        },
        _getContent: function(e) {
            var t = this,
                r = t._sId.replace(/ak-/g, "ak-content-"),
                i = n("." + r);
            t._getApiDatas(function(i, s, o) {
                if (i == "success") {
                    var u = s,
                        a = n("<div/>", {
                            id: r,
                            html: u,
                            "class": "ak-linker-content " + r
                        });
                    e.call(t, a)
                }
            })
        },
        _getApiDatas: function(e) {
            var r = this,
                i = r._sId,
                s = r._aQueryDatas;
            r._bCache && !t.isEmpty(this._oCache[i]) ? e.apply(r, ["success", this._oCache[i], "CACHE:" + r._sName + '["' + i + '"]']) : (r._showLoading(), n.ajax({
                url: r.option("linker-path"),
                method: "get",
                data: s,
                success: function(t, n, i) {
                    r._hideLoading(), e.apply(r, [n, t, i])
                },
                error: function(t, n, i) {
                    r._hideLoading(), e.apply(r, [n, i, t])
                }
            }))
        },
        _initJS: function() {
            var e = this;
            if (e._aLoadedJsUrls.length > 0) try {
                var t = require(e.option("linker-path")),
                    n = new t;
                n.init()
            } catch (r) {
                console.error(r.message)
            }
        },
        _initLoading: function() {
            var e = this,
                t = n("div.linkerLoading");
            if (t.length == 0) {
                var t = n("<div/>", {
                    html: "&nbsp;"
                }).css({
                    height: "16px",
                    width: "16px",
                    position: "absolute",
                    "z-index": "2147483646"
                }).hide().addClass("linkerLoading linker-loader").appendTo("body");
                n(document).mousemove(function(n) {
                    if (n.pageX || n.pageY) e._fCoordX = n.pageX, e._fCoordY = n.pageY;
                    else if (n.clientX || n.clientY) e._fCoordX = n.clientX + document.body.scrollLeft + document.documentElement.scrollLeft, e._fCoordY = n.clientY + document.body.scrollTop + document.documentElement.scrollTop;
                    t.css("left", parseInt(e._fCoordX) - 20 + "px").css("top", parseInt(e._fCoordY) - 20 + "px")
                })
            }
        },
        _showLoading: function() {
            this._bShowLoading = !0, n("div.linkerLoading").show()
        },
        _hideLoading: function() {
            this._bShowLoading = !1, n("div.linkerLoading").hide()
        },
        _ucwords: function(e) {
            return (e + "").replace(/^([a-z])|\s+([a-z])/g, function(e) {
                return e.toUpperCase()
            })
        },
        _getCache: function() {
            return t.isEmpty(this._sName) || !localStorage ? null : (t.isEmpty(this._oCache) && (this._oCache = t.isEmpty(localStorage.getItem(this._sName)) ? {} : JSON.parse(localStorage.getItem(this._sName))), this._oCache)
        },
        _saveCache: function() {
            if (t.isEmpty(this._sName) || !localStorage) return null;
            localStorage.setItem(this._sName, JSON.stringify(this._oCache))
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_linker.prototype.enhanceWithin(e.target, !0)
    })
}(this),
function(e, t) {
    var n = require("ankama.widget"),
        r = require("lodash");
    require("ankama.ui.dialog"), n.widget("ankama.ak_modal", n.ankama.widget, {
        options: {
            initSelector: ".ak-modal",
            closable: !0,
            closeDestroy: !1,
            mask: !0,
            autoShow: !1,
            url: !1,
            data: {},
            method: "GET",
            text: !1,
            title: !1,
            target: !1,
            dialog: {},
            interactionSelector: [],
            openmodal: null,
            bridgemodal: null,
            closemodal: null
        },
        toggle: function() {
            this.element.is(":visible") ? this.close() : this.open()
        },
        bridge: function(e) {
            this._trigger("bridgemodal", null, e)
        },
        _destroy: function() {
            this.jqFromTarget && (this.jqFromTarget.removeData("modalId"), delete this.jqFromTarget), this.element.ak_dialog("destroy"), this.element && this.element.remove()
        },
        close: function() {
            var e = this;
            e._trigger("closemodal", e), e.options.closeDestroy ? e.destroy() : e.element.ak_dialog("close")
        },
        set_closemodal: function(e) {
            var t = this;
            t.options.closemodal = e
        },
        open: function() {
            var e = this;
            this.options.url ? (e.element.mask(e.option("text") || t), n.ajax({
                url: this.options.url,
                data: this.options.data,
                type: this.options.method
            }).done(function(t) {
                var r = /<h1\b[^>]*>([\s\S]*?)<\/h1>/gi.exec(t);
                r && r.length > 0 && (document.title = r[1]), e.element.html(n("<div>").append(t).find(".container-content, .main")), e.element.unmask(), e._trigger("openmodal", e), e.element.trigger("widgetcreate"), e.element.ak_dialog("refreshDimensions")
            })) : e._trigger("openmodal", e), this.element.ak_dialog("open"), this.options.url || e.element.trigger("widgetcreate")
        },
        _create: function() {
            this.options.mask ? (this.options.dialog.mask = this.options.mask, this.options.dialog.modal = !0, this.options.dialog.interactionSelector = this.options.interactionSelector) : (this.options.dialog.resizable = !0, this.options.dialog.draggable = !0), this.options.closable || (this.options.dialog.closeOnEscape = !1, this.options.dialog.dialogClass = "no-close"), this.options.title && (this.options.dialog.title = this.options.title), this.options.dialog.autoOpen = this.options.autoShow, this.options.dialog.close = n.proxy(this.close, this), this.element.ak_dialog(this.options.dialog)
        },
        _init: function() {
            this.element.ak_dialog(this.options.dialog)
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_modal.prototype.enhanceWithin(e.target)
    }), n.widget("ankama.ak_modal_trigger", n.ankama.widget, {
        options: {
            initSelector: ".ak-modal-trigger",
            eventTrigger: "click"
        },
        _sModalWidgetName: null,
        _jqModal: null,
        _create: function() {
            var e = this;
            this._sModalWidgetName = r.initial(this.widgetName.split("_")).join("_"), this.element.on(this.options.eventTrigger, function(t) {
                e._add(t)
            })
        },
        _add: function(e) {
            var i = this,
                s = n.extend({
                    mask: !0
                }, this.options);
            (r.isNull(this.options.preventDefault) || this.options.preventDefault != 0) && e.preventDefault();
            var o = n(e.currentTarget);
            o.data().modalId && (s.target = n("#" + o.data().modalId));
            if (s.target) {
                n(s.target)[i._sModalWidgetName]("toggle");
                return
            }
            s.url === t && o.is("a") && (s.url = o.attr("href"));
            var u = n("<div>");
            s.modalid && u.attr("id", s.modalid);
            var a = n.ankama[i._sModalWidgetName].prototype.options.initSelector.substring(1);
            u.addClass(a).appendTo(document.body), u.bind(i._sModalWidgetName + "bridgemodal saveitem", function(e, t) {
                var r = n.Event("bridgetrigger", {
                    parentEvent: e
                });
                i.element.trigger(r, t)
            });
            var f = u[i._sModalWidgetName](s)[i._sModalWidgetName]("toggle");
            o.removeData("modalId").data("modalId", f.attr("id")), f.data("ankama-" + i._sModalWidgetName).jqFromTarget = o, this._jqModal = f
        },
        _setOption: function(e, t) {
            this._super(e, t), this._jqModal && this._jqModal[this._sModalWidgetName]("option", e, t)
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_modal_trigger.prototype.enhanceWithin(e.target)
    })
}(this),
function(e, t) {
    "use strict";
    var n = require("ankama.widget");
    n.widget("ankama.ak_sticky", n.ankama.widget, {
        options: {
            initSelector: ".ak-sticky",
            topOffset: 0,
            stickClass: "sticked",
            bWhenInvisible: !1,
            viewport: "body"
        },
        _iTopOffset: 0,
        _create: function() {
            var r = this,
                i = n(this.element),
                s = n(this.option("viewport")),
                o = this.option("stickClass"),
                u = this.element.scrollParent(),
                a = r.option("bWhenInvisible"),
                f = !1,
                l, c;
            this.calculateTopOffset();
            var h = function() {
                    c = s.position(), c.bottom = c.top + s.outerHeight(!0), a === !0 && (c.top = c.top + i.outerHeight())
                },
                p;
            n(e).resize(function() {
                p && clearTimeout(p), p = setTimeout(function() {
                    p = t, f === !0 && i.css("width", s.width()), h(), r.calculateTopOffset()
                }, 100)
            }), h();
            var d = function() {
                    i.removeClass(o).css("position", "").css("top", "").css("width", ""), r._trigger("unsticked"), f = !1
                },
                v = function() {
                    var h = u.scrollTop();
                    if (h >= c.top - r._iTopOffset && (a === !1 && h <= c.bottom - r._iTopOffset || a === !0 && h + n(e).height() <= n(document).height()) && (u[0] != document || u[0] == document && n(document).outerHeight(!0) - n(e).outerHeight(!0) > r._iTopOffset + n(i).outerHeight(!0) + 10)) {
                        if (!i.hasClass(o)) {
                            i.is("thead") && n("tbody tr:eq(1) td:not(:hidden)", s).each(function(e, t) {
                                n("thead th:not(:hidden):eq(" + e + ")", s).width(n(t).width()), n(t).width(n(t).width())
                            });
                            if (!r._trigger("sticked", null)) return;
                            i.addClass(o).css("position", "fixed").css("top", u[0] == document ? r._iTopOffset : s.offset().top).css("width", s.width()), r.option("bWhenInvisible") && (i.css({
                                top: -i.outerHeight()
                            }), i.animate({
                                top: 0
                            }, {
                                queue: !1,
                                duration: 150,
                                done: function() {}
                            })), f = !0
                        }
                    } else i.hasClass(o) && (l && clearTimeout(l), l = setTimeout(function() {
                        l = t, d()
                    }, 100))
                };
            v(), u.scroll(v)
        },
        calculateTopOffset: function() {
            var e = this,
                t = n(this.option("initSelector")),
                r = t.index(t.filter(n(this.element)));
            e._iTopOffset = this.option("topOffset"), t.filter(":lt(" + r + ")").each(function(t, r) {
                e._iTopOffset += n(r).outerHeight(!0)
            })
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_sticky.prototype.enhanceWithin(e.target)
    })
}(this),
function(e, t) {
    var n = require("ankama.widget"),
        r = require("lodash");
    require("jquery.pjax"), require("jquery.loadmask"), n.widget("ankama.ak_responsivetable", n.ankama.widget, {
        options: {
            initSelector: ".ak-responsivetable",
            brkCls: "ak-responsivetable-breakpoint",
            expandCls: "ak-responsivetable-expand",
            wrapperCls: "ak-responsivetable-wrapper",
            rowDetailCls: "ak-responsivetable-row-detail",
            rowDetailContainerCls: "ak-responsivetable-row-detail-inner",
            rowDetailAjaxContainerCls: "ak-responsivetable-row-ajax-detail-inner",
            rowShowCls: "ak-responsivetable-detail-show",
            debug: !1,
            ajaxDetails: !1,
            multiline: !1
        },
        iResizeTimer: null,
        _destroy: function() {
            var e = this;
            e.element.unwrap(), delete this.jqWrapper, e.element.removeData("old").unbind("resize"), e.restoreAll()
        },
        _create: function() {
            var t = this;
            t.element.wrap('<div class="' + t.options.wrapperCls + '"/>'), t.jqWrapper = this.element.parent(), t.element.delegate("tbody tr:not(." + this.options.rowDetailCls + ")", "click", n.proxy(t._rowExpanded, t));
            var r = n(".device-profile:visible:first").data("deviceprofile");
            r === "mobile" && (t.options.multiline = !0), t.options.multiline === !1 && (t.element.css({
                whiteSpace: "nowrap"
            }), t.jqWrapper.css({
                overflow: "hidden"
            }));
            var i = n(e);
            t.element.data("old", {
                width: i.width(),
                height: i.height()
            }), i.bind("resize", function(e) {
                var n = i.width(),
                    r = i.height(),
                    s = t.element.data("old"),
                    o = s ? t.element.data("old").width : 0;
                if (n === o) return !1;
                t.bExtend = n >= o, t.element.data("old", {
                    width: i.width(),
                    height: i.height()
                }), t._resized()
            })
        },
        _init: function() {
            var e = this;
            e.aCols = [], e.aColHead = [], e.aPriorityCols = [], e.aBreakPoints = [], e.bExtend = !1, e.jqHeadElements = n(this.element).find("thead tr").children(), r.each(e.jqHeadElements, function(t, r) {
                var i = n(t);
                e.aCols.push({
                    index: r,
                    priority: i.data("priority"),
                    width: i.width(),
                    el: i
                }), e.aColHead.push(i.html())
            }), e.aPriorityCols = r.sortBy(e.aCols.reverse(), function(e) {
                return e.priority
            }), e._resized(), e._update()
        },
        _ajaxDetailsRequest: function(e, t) {
            var i = this,
                s = e.attr("ajax-details-url"),
                o = r.isEmpty(e.attr("data-options")) ? {} : JSON.parse(e.attr("data-options"));
            o.url = r.isEmpty(s) ? o.url : s, o.type = r.isEmpty(o.type) ? "GET" : o.type, o.data = r.isEmpty(o.data) ? [] : o.data, r.isEmpty(o.url) && t.call(i, e, !1), n.ajax({
                type: o.type,
                url: o.url,
                data: o.data,
                beforeSend: function(t, n) {
                    e.next().find("td").mask()
                },
                success: function(n, r, s) {
                    e.next().find("td").unmask(), t.call(i, e, n)
                }
            })
        },
        _resized: function() {
            var e = this,
                t = function() {
                    return e.bExtend === !0 ? !0 : e._getColVisibleCount() > 1 && e.jqWrapper.get(0).scrollWidth > e.jqWrapper.outerWidth(!0)
                };
            if (t() === !1) {
                e.element.css("visibility") === "hidden" && e.element.css("visibility", "visible");
                return
            }
            var i = function() {
                if (e.bExtend === !0) {
                    var t = r.filter(e.aBreakPoints, function(t, n) {
                        return t.x <= r.parseInt(e.jqWrapper.outerWidth(!0))
                    });
                    r.size(t) > 0 && (r.each(t, function(t, n) {
                        e.aBreakPoints = r.without(e.aBreakPoints, t), e.restoreColumn(t.col)
                    }), e._update())
                } else if (e._getColVisibleCount() > 1) {
                    var i = function() {
                            var t = !1;
                            while (e.jqWrapper.get(0).scrollWidth > e.jqWrapper.outerWidth(!0)) e.breakColumn(), t = !0;
                            return t && e._update(), e.jqWrapper.get(0).scrollWidth > e.jqWrapper.outerWidth(!0)
                        },
                        s = i();
                    while (s === !0) s = i()
                }
                n(e.element).css({
                    visibility: "visible"
                })
            };
            e.iResizeTimer ? clearTimeout(e.iResizeTimer) : i(), e.iResizeTimer = setTimeout(i, 500)
        },
        breakColumn: function() {
            var e = this,
                t = e.aPriorityCols[e.aBreakPoints.length];
            e.aBreakPoints.push({
                x: t.el.position().left + t.el.outerWidth(),
                col: t
            }), t.el.get(0).style.setProperty("display", "none", "important"), r.each(n(this.element).find("tr td:nth-child(" + (t.index + 1) + ")"), function(e) {
                e.style.setProperty("display", "none", "important")
            })
        },
        restoreColumn: function(e) {
            var t = this,
                i = e.index;
            n(t.jqHeadElements[i]).show(), r.each(n(this.element).find("tr td:nth-child(" + (i + 1) + ")"), function(e) {
                e.style.display = ""
            })
        },
        restoreAll: function() {
            var e = this;
            r.each(e.aBreakPoints, function(t, n) {
                e.aBreakPoints = r.without(e.aBreakPoints, t), e.restoreColumn(t.col)
            }), e._update()
        },
        _rowExpanded: function(e, t) {
            var r = this,
                i = e instanceof n.Event ? n(e.currentTarget) : e;
            if (e instanceof n.Event && typeof e.target != "undefined" && n(e.target).is("a")) return;
            if (i.hasClass(this.options.rowShowCls)) {
                this._rowContracted(e);
                return
            }
            if (!r.options.ajaxDetails && r._allIsVisible()) return;
            i.addClass(this.options.rowShowCls), i.next("tr").hasClass(this.options.rowDetailCls) ? i.next("tr").show() : i.after('<tr class="' + this.options.rowDetailCls + '"><td colspan=' + this._getColVisibleCount() + '><div class="' + this.options.rowDetailContainerCls + '"></div></td></tr>'), this._updateRowDetail(i)
        },
        _rowContracted: function(e) {
            var t = e instanceof n.Event ? n(e.currentTarget) : e;
            t.removeClass(this.options.rowShowCls), t.next("tr." + this.options.rowDetailCls).hide()
        },
        _update: function() {
            var e = this,
                t = e._allIsVisible(),
                i = "tbody tr:not(." + this.options.rowDetailCls + ")";
            t ? (e.element.removeClass(this.options.brkCls), r.each(e.element.find(i), function(t) {
                if (e.options.ajaxDetails) return !1;
                n(t).children("td." + e.options.expandCls).removeClass(e.options.expandCls)
            })) : this.element.hasClass(this.options.brkCls) || e.element.addClass(this.options.brkCls);
            var s = t && !e.options.ajaxDetails ? "hide" : "show";
            r.each(e.element.find("tbody tr." + e.options.rowShowCls), function(t) {
                n(t).next("tr." + e.options.rowDetailCls)[s](), e._updateRowDetail(t)
            });
            if (!t || e.options.ajaxDetails && t) r.each(e.element.find(i).find("td." + e.options.expandCls), function(t) {
                n(t).removeClass(e.options.expandCls)
            }), r.each(e.element.find(i).find("td:visible:last"), function(t) {
                n(t).addClass(e.options.expandCls)
            })
        },
        _updateRowDetail: function(e) {
            var i = this,
                s = n(e),
                o = s.next("tr." + this.options.rowDetailCls),
                u = o.find("td div." + this.options.rowDetailContainerCls);
            u.empty();
            var a = s.children(),
                f = s.children(":hidden"),
                l = 0;
            r.each(this.element.find("thead tr").children(":visible"), function(e) {
                var r = n(e).attr("colspan");
                r !== t && (l += parseInt(r, 10) - 1)
            }), o.children("td").attr("colspan", this._getColVisibleCount() + l), r.each(f, function(e) {
                var t = i.aColHead[a.index(e)];
                u.append((t ? "<div><strong>" + t + "</strong>: " : "") + n(e).html() + "</div>")
            }), i.options.ajaxDetails && !o.hasClass("ajax-details-loaded") ? i._ajaxDetailsRequest(s, function(e, t) {
                o.addClass("ajax-details-loaded"), t && u.parent().append('<div class="' + this.options.rowDetailAjaxContainerCls + '">' + t + "</div>"), n(document).trigger("widgetcreate")
            }) : u.trigger("widgetcreate")
        },
        _getColVisibleCount: function() {
            return this.element.find("thead tr").children(":visible").length
        },
        _allIsVisible: function() {
            var e = this;
            return this._getColVisibleCount() === e.aPriorityCols.length
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_responsivetable.prototype.enhanceWithin(e.target)
    })
}(this),
function(e, t) {
    var n = require("ankama.widget");
    n.widget("ankama.ak_listoptions_actions", n.ankama.widget, {
        options: {
            initSelector: ".ak-listoptions-actions"
        },
        _create: function() {
            var e = this;
            this.element.find(".ak-triggeraction").change(function(t) {
                if (e.element.closest(".ak-ajaxloader").length) {
                    var r = e.element.closest(".ak-ajaxloader").first();
                    r.ak_ajaxloader("option", "direct", !0), r.ak_ajaxloader("option", "pjax.url", n(t.currentTarget).val()), r.trigger("direct.ajaxloader")
                }
            })
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_listoptions_actions.prototype.enhanceWithin(e.target)
    })
}(this),
function(e, t) {
    var n = require("lodash"),
        r = require("ankama.widget");
    r.widget("ankama.ak_lists_paginable", r.ankama.widget, {
        _jqPages: null,
        _jqButonNext: null,
        _jqButonPrevious: null,
        options: {
            initSelector: ".ak-lists-paginable"
        },
        _create: function() {
            var e = this;
            e._jqPages = r(".ak-page", r(this.element)), e._jqButonNext = r(".ak-next", r(this.element)), e._jqButonPrevious = r(".ak-previous", r(this.element)), this.element.on("click", ".ak-next", function(t) {
                t.preventDefault(), e._move("next")
            }), this.element.on("click", ".ak-previous", function(t) {
                t.preventDefault(), e._move("previous")
            })
        },
        _move: function(e) {
            var t = this,
                n = t._jqPages.filter(".active"),
                r = parseInt(n.attr("data-value")),
                i = e == "next" && r + 1 <= t._jqPages.length ? r + 1 : e == "previous" && r - 1 >= 1 ? r - 1 : 1;
            if (r != i) {
                n.removeClass("active").css("display", "none");
                var s = t._jqPages.filter('[data-value="' + i + '"]');
                s.addClass("active").css("display", "block"), t._jqButonPrevious.css("display", i == 1 ? "none" : "block"), t._jqButonNext.css("display", i == t._jqPages.length ? "none" : "block")
            }
        }
    }), r(document).bind("ready widgetcreate", function(e) {
        r.ankama.ak_lists_paginable.prototype.enhanceWithin(e.target)
    })
}(this),
function(e, t) {
    "use strict";
    var n = require("ankama.widget"),
        r = require("lodash");
    require("jquery.qtip"), n.widget("ankama.ak_tooltip", n.ankama.widget, {
        options: {
            initSelector: ".ak-tooltip",
            manual: !1,
            hideOnScroll: !1,
            hideOnResize: !0,
            forceOnTouch: !1,
            tooltip: {
                ajax: null,
                fullscreen: !1,
                events: {},
                style: {
                    classes: "ak-tooltip-content"
                },
                position: {
                    my: "bottom center",
                    at: "top center",
                    adjust: {
                        method: "flipinvert",
                        scroll: !0
                    },
                    effect: !1,
                    viewport: n(e)
                },
                show: {
                    delay: 0,
                    effect: !1,
                    ready: !1
                },
                hide: {
                    delay: 0,
                    effect: !1
                }
            }
        },
        qtipTarget: null,
        _bShowLoading: null,
        getOptions: function() {
            return r.clone(this.options)
        },
        _destroy: function() {
            this.qtipTarget.qtip("destroy"), this.qtipTarget = null
        },
        disable: function() {
            this.qtipTarget.qtip("disable", !0)
        },
        enable: function() {
            this.qtipTarget.qtip("disable", !1), this.qtipTarget.qtip("reposition")
        },
        show: function() {
            this.qtipTarget.qtip("show")
        },
        hide: function() {
            this.qtipTarget.qtip("hide")
        },
        api: function() {
            return this.qtipTarget.qtip("api")
        },
        _init: function() {},
        _create: function() {
            var e = this;
            this.options.tooltip.position.container && (this.options.tooltip.position.container === "sibling" ? this.options.tooltip.position.container = n(this.element).parent() : this.options.tooltip.position.container = this.element.closest(this.options.tooltip.position.container)), this.options.tooltip.fullscreen && (this.options.tooltip.style.classes += " ak-tooltip-fullscreen"), "ajax" in this.options.tooltip && this.options.tooltip.ajax && (this._initLoading(), this.options.tooltip.style.classes += " ak-tooltip-ajax", this.options.tooltip.show.ready = !1, r.merge(this.options.tooltip, {
                content: {
                    text: n.proxy(function(e, t) {
                        var r = this;
                        return r._showLoading(), t.elements.tooltip.addClass("loading"), n.ajax({
                            url: r.options.tooltip.ajax
                        }).done(function(e) {
                            t.elements.content.html(e)
                        }).then(function(e) {
                            r._hideLoading(), t.elements.tooltip.removeClass("loading"), t.reposition(null, !1), t.elements.content.trigger("widgetcreate")
                        }, function(e, n, r) {
                            t.set("content.text", n + ": " + r)
                        })
                    }, this)
                }
            })), n.extend(!0, e.options.tooltip.events, {
                render: function(t, n) {
                    e._render.apply(e, arguments)
                },
                focus: function(t, n) {
                    e._focus.apply(e, arguments)
                },
                toggle: function(t, n) {
                    e._toggle.apply(e, arguments)
                },
                move: function(t, n) {
                    if (!t.originalEvent) return;
                    (e.option("hideOnResize") === !0 && t.originalEvent.type === "resize" || e.option("hideOnScroll") === !0 && t.originalEvent.type === "scroll") && n.hide()
                }
            });
            if (n.isTouchDevice()) {
                if (!e.options.forceOnTouch) {
                    console.warn("No rollover tooltip on touch device", e.options.tooltip.show.event);
                    return
                }
                e.options.tooltip.show.event = "click", e.options.tooltip.hide.event = "click", e.options.tooltip.hide.delay = 0
            }
            e.options.tooltip.show.ready && !e.element.is(":visible") && (e.options.tooltip.show.ready = !1), e.element.attr("title") && e.element.attr("title") != "" ? (e.element.qtip(r.merge(r.clone(e.options.tooltip), {
                content: {
                    text: e.element.attr("title")
                }
            })), e.qtipTarget = e.element) : e.options.manual ? (e.element.qtip(r.merge(r.clone(e.options.tooltip))), e.qtipTarget = e.element) : (e.element.hide(), e.qtipTarget = n(e.element.prevAll(":not(script)")[0]), e.qtipTarget.qtip(r.merge(r.clone(e.options.tooltip), {
                content: {
                    text: e.element.html()
                }
            })))
        },
        _render: function(e, t) {},
        _focus: function(e, t) {},
        _toggle: function(e, t) {
            e && e.type === "tooltipshow" && t.tooltip.trigger("widgetcreate")
        },
        _initLoading: function() {
            var e = this,
                t = n("div.linkerLoading");
            if (t.length == 0) {
                var t = n("<div/>", {
                    html: "&nbsp;"
                }).css({
                    height: "16px",
                    width: "16px",
                    position: "absolute",
                    "z-index": "2147483646"
                }).hide().addClass("linkerLoading linker-loader").appendTo("body");
                n(document).mousemove(function(n) {
                    if (n.pageX || n.pageY) e._fCoordX = n.pageX, e._fCoordY = n.pageY;
                    else if (n.clientX || n.clientY) e._fCoordX = n.clientX + document.body.scrollLeft + document.documentElement.scrollLeft, e._fCoordY = n.clientY + document.body.scrollTop + document.documentElement.scrollTop;
                    t.css("left", parseInt(e._fCoordX) - 20 + "px").css("top", parseInt(e._fCoordY) - 20 + "px")
                })
            }
        },
        _showLoading: function() {
            this._bShowLoading = !0, n("div.linkerLoading").show()
        },
        _hideLoading: function() {
            this._bShowLoading = !1, n("div.linkerLoading").hide()
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_tooltip.prototype.enhanceWithin(e.target)
    })
}(this),
function(e, t) {
    var n = require("ankama.widget"),
        r = require("lodash");
    n.widget("ankama.ak_button_modal", n.ankama.ak_tooltip, {
        options: {
            initSelector: ".ak-button-modal",
            target: t,
            manual: !0,
            hideOnScroll: !0,
            forceOnTouch: !0,
            tooltip: {
                content: {},
                style: {
                    classes: "ak-button-modal-content qtip-shadow",
                    tip: !1,
                    def: !1
                },
                position: {
                    my: "top right",
                    at: "bottom right",
                    viewport: n(e),
                    effect: !1,
                    adjust: {
                        method: "shift none"
                    }
                },
                show: {
                    solo: !0,
                    event: "click",
                    effect: !1,
                    delay: 0,
                    modal: {
                        on: !1,
                        effect: !0,
                        blur: !0,
                        escape: !0
                    }
                },
                hide: {
                    fixed: !0,
                    event: "click",
                    effect: !1,
                    delay: 0
                }
            }
        },
        hide: function() {
            this._superApply(arguments)
        },
        _create: function() {
            var e = this,
                t = this.options.tooltip,
                r = this.option("target") ? n(this.option("target")) : e.element.nextAll(":not(script):first");
            t.content.text = r.clone(), this._superApply(arguments)
        },
        _render: function(e, t) {
            var n = this;
            t.tooltip.data("button", n.element), this._superApply(arguments)
        },
        _focus: function(e, t) {
            this._superApply(arguments)
        },
        _toggle: function(e, t) {
            this._superApply(arguments);
            var r = e.type === "tooltipshow" ? "addClass" : "removeClass";
            n(this.element)[r]("open")
        },
        _init: function() {
            this._superApply(arguments)
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_button_modal.prototype.enhanceWithin(e.target)
    })
}(this),
function(e, t) {
    "use strict";
    var n = require("ankama.widget"),
        r = require("lodash");
    require("jquery.ui.datepicker"), require("jquery.timepicker"), n.widget("ankama.ak_datetimepicker", n.ankama.widget, {
        options: {
            initSelector: ".ak-datetimepicker",
            inlinePicker: !1,
            initDateValue: null,
            picker: {
                dateFormat: "yy-mm-dd",
                timeFormat: "HH:mm:ss"
            },
            aSelectableDates: []
        },
        _create: function() {
            this._super();
            var e = this;
            if (this.option("inlinePicker") === !1 && this.element.get(0).type.indexOf("date") !== -1) return;
            var t = this.options,
                r = this.element.attr("name"),
                i = n("<input />").attr("type", "hidden").attr("name", r).attr("id", "alt-" + r).data("ori-fieldname", r + "-alt");
            this.element.attr("name", r + "-alt").data("alt-fieldname", r);
            var s = this.element.next('script[type="application/json"]').first(),
                o = s;
            o.exists() || (o = this.element), i.insertAfter(o), this.option("inlinePicker") === !1 && s.exists() && s.clone().insertAfter(i), n.extend(!0, t.picker, n.datepicker.regional[n.akLang()] || n.datepicker.regional[""] || {}), t.picker.altField = "#alt-" + r, t.picker.altFormat = "yy-mm-dd";
            var u = n.akClientLang(!0);
            n.extend(!0, t.picker, {
                onSelect: function() {
                    e.element.trigger("blur"), i.trigger("change")
                }
            }), t.timepicker ? (t.picker.altFieldTimeOnly = !1, t.picker.altSeparator = "T", n(this.element).datetimepicker(t.picker)) : (t.picker.beforeShowDay = n.proxy(e.beforeShowDay, e), n(this.element).datepicker(t.picker), this.option("initDateValue") !== null && n(this.element).datepicker("setDate", this.option("initDateValue")))
        },
        beforeShowDay: function(e) {
            var t = this,
                r = e.getMonth(),
                i = e.getDate(),
                s = e.getFullYear();
            for (var o = 0; o < t.options.aSelectableDates.length; o++) {
                var u = i + "/" + (r + 1) + "/" + s;
                if (n.inArray(u, t.options.aSelectableDates) != -1) return [!0, "ak-selectable", ""]
            }
            return [!0]
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_datetimepicker.prototype.enhanceWithin(e.target)
    })
}(window),
function(e, t) {
    "use strict";
    define("utils/responsiveimage", ["require", "exports", "module"], function(n, r, i) {
        var s = n("jquery");
        s(e).load(function() {
            var n = .3,
                r = 200,
                i = function(o) {
                    s(".img-responsive, .img-maxresponsive").each(function(i, u) {
                        var a = s(u);
                        if (!a.attr("data-src")) return;
                        var f = a.parent(),
                            l = f.width(),
                            c = f.height(),
                            h = s(e).scrollTop(),
                            p = h + s(e).height(),
                            d = a.offset().top,
                            v = d + c,
                            m = v >= h - r && d <= p + r;
                        if (!m) {
                            o === "resize" && a.data("redraw", !0);
                            return
                        }
                        if (o === "resize" || a.data("redraw") === !0) {
                            a.data("redraw", !1);
                            var g = a.data("breakpoints");
                            if (!a.data("breakpoints")) {
                                var y = +a.attr("data-max"),
                                    b = [y],
                                    w = 0;
                                for (var E = 1.5; E <= 2; E += .1) b.push(Math.floor(b[w] !== t ? b[w] / E : y)), w++;
                                b.reverse(), a.data("breakpoints", b), g = b
                            }
                            var S = 0;
                            for (var x = 0, T = g.length; x < T; x++) {
                                S = g[x];
                                if (!(l > g[x] * (1 + n))) break
                            }
                            if (S >= a.data("optimalwidth") || !u.src) {
                                a.data("optimalwidth", S);
                                var N = a.attr("data-src").split("."),
                                    C = N[N.length - 1].split("?")[0];
                                N.pop(), /^w[0-9]*(h[0-9]*)?$/.test(N[N.length - 1]) && N.pop();
                                var k = "";
                                /^m[0-9]+$/.test(N[N.length - 1]) && (k = N.pop() + ".");
                                var L = N.join(".") + ".w" + S + "h." + k + C;
                                a.attr("data-src", L)
                            }
                        }
                        a.attr("data-src") && u.src !== a.attr("data-src") && m && (u.src = a.attr("data-src"))
                    })
                },
                o = null;
            i("resize"), s(e).on("scroll", function(e) {
                i("scroll")
            }), s(e).on("resize", function(e) {
                o && clearTimeout(o), o = setTimeout(function() {
                    o = null, i("resize")
                }, 50)
            }), s(document).ajaxSuccess(function(e, t, n) {
                i("resize")
            })
        }), i.exports = {}
    }), require("utils/responsiveimage")
}(window),
function(e, t) {
    var n = require("ankama.widget");
    n.widget("ankama.ak_navexpand", n.ankama.widget, {
        options: {
            initSelector: ".ak-nav-expand"
        },
        _initH: null,
        _create: function() {
            n(e).resize(n.proxy(this._resize, this));
            var t = this.element.find(".ak-nav-expand-more");
            t.click(n.proxy(this._clickMore, this)), this._initH = this.element.find(".ak-nav-expand-links > ").first().outerHeight(), this.element.find(".ak-nav-expand-container").height(this._initH), t.width(this._initH).height(this._initH).css("line-height", this._initH + "px"), this.element.is(".ak-expanded") && this._clickMore()
        },
        _resize: function() {
            this._init()
        },
        _init: function() {
            var e = this.element.find(".ak-nav-expand-container"),
                t = this.element.find(".ak-nav-expand-links"),
                n = this.element.find(".ak-nav-expand-more .ak-nav-expand-icon");
            n.hasClass("ak-picto-close") ? t.height() == this._initH ? (n.hide(), n.removeClass("ak-picto-close"), n.addClass("ak-picto-open"), e.height(this._initH)) : n.show() : t.height() > e.height() ? n.show() : (n.hide(), n.removeClass("ak-picto-close"), n.addClass("ak-picto-open"), e.height(this._initH))
        },
        _clickMore: function() {
            var e = this.element.find(".ak-nav-expand-container"),
                t = this.element.find(".ak-nav-expand-links"),
                n = this.element.find(".ak-nav-expand-more .ak-nav-expand-icon");
            n.hasClass("ak-picto-close") ? (n.removeClass("ak-picto-close"), n.addClass("ak-picto-open"), e.height(this._initH)) : (n.removeClass("ak-picto-open"), n.addClass("ak-picto-close"), e.height(t.height()))
        }
    }), n(e.document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_navexpand.prototype.enhanceWithin(e.target)
    })
}(this),
function(e, t) {
    var n = require("ankama.widget"),
        r = require("lodash");
    require("jquery.sticky"), n.widget("ankama.ak_block_header", n.ankama.widget, {
        options: {
            initSelector: "header",
            stickyClass: "ak-navbar-fixed navbar-fixed-top",
            bDisableSticky: !1
        },
        _create: function() {
            var e = this,
                t = !1;
            e.option("bDisableSticky") === !1 && (e.jqNotificationsHeader = n(".ak-notification-list-header", e.element), e.element.on("sticky-start", n.proxy(e._onSticked, e)), e.element.on("sticky-end", n.proxy(e._onUnsticked, e)), e._startSticky(), e.jqNotificationsHeader = n(".ak-notification-list-header", e.element), e.jqNotificationsHeader.bind("remove", function() {
                n("body").removeClass("notifications-header"), e._startSticky()
            }), n(document).bind("profilechange", function() {
                e._restarSticky()
            })), n(document).click(function(t) {
                var r = n(".ak-button-modal.open", e.element);
                r.length && (n(t.target).closest(".ak-button-modal, .ak-button-modal-content").length || r.each(function(e, t) {
                    n(t).ak_button_modal("hide")
                }))
            })
        },
        _startSticky: function() {
            var e = this;
            e.bStarted ? e.element.sticky("update", {
                topSpacing: -e.element.outerHeight(!0)
            }) : (e.element.sticky({
                responsiveWidth: !0,
                getWidthFrom: "body",
                topSpacing: -e.element.outerHeight(!0)
            }), e.bStarted = !0)
        },
        _stopSticky: function() {
            var e = this;
            e.element.unstick(), e.element.removeClass(e.option("stickyClass")), e.bStarted = !1
        },
        _restarSticky: function() {
            var e = this;
            e._stopSticky(), e.element.data("restarted", !0), n(".ak-notification-list-header", e.element).css("position", ""), n(".ak-notification-list-header", e.element).show(), e._startSticky(), e.element.sticky("update"), e.element.removeData("restarted")
        },
        _onSticked: function(e) {
            var t = this;
            n(".ak-notification-list-header", t.element).hide(), t.element.addClass(t.option("stickyClass")), t.element.data("restarted") ? t.element.css("top", 0) : t.element.animate({
                top: 0
            }, {
                queue: !1,
                duration: 150
            })
        },
        _onUnsticked: function(e) {
            var t = this;
            n(".ak-notification-list-header", t.element).css("position", ""), n(".ak-notification-list-header", t.element).show(), t.element.removeClass(t.option("stickyClass"))
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_block_header.prototype.enhanceWithin(e.target)
    })
}(this),
function(e, t) {
    var n = require("ankama.widget");
    n.widget("ankama.ak_background", n.ankama.widget, {
        options: {
            initSelector: ".ak-carrousel-bg"
        },
        _create: function() {
            n("body").is(".ak-background-type-homepage") && n(".ak-pagetop").length && n(".ak-pagetop").outerHeight() && n(".ak-pagetop").css({
                "margin-top": -n(".ak-pagetop").outerHeight() + "px"
            })
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama
            .ak_background.prototype.enhanceWithin(e.target)
    })
}(this),
function(e, t) {
    var n = require("ankama.widget");
    n.widget("ankama.ak_breadcrumb", n.ankama.widget, {
        options: {
            initSelector: ".breadcrumb"
        },
        _create: function() {
            var e = this,
                t = n(e.element).closest(".row").find(">").last().outerHeight();
            t -= n(e.element).outerHeight(), t -= parseInt(n(e.element).css("margin-bottom")), n(e.element).parent().find(".ak-breadcrumb-spacer").css({
                height: t + "px"
            })
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_breadcrumb.prototype.enhanceWithin(e.target)
    })
}(this),
function(e, t) {
    var n = require("jquery"),
        r = require("lodash");
    n.widget("ankama.ak_navbar_search", n.ankama.widget, {
        options: {
            initSelector: ".ak-navbar-search"
        },
        initValue: "",
        sDataCls: "navbar-search",
        _destroy: function() {
            var e = this;
            e._off(e.document, "scroll click"), e._off(e.window, "resize"), e._off(e.jqElements, "click")
        },
        _create: function() {
            var e = this,
                t = n("input", this.element);
            e.initValue = t.val(), e._on(e.document, {
                scroll: n.proxy(this._onDocumentScroll, this),
                click: n.proxy(this._onDocumentClick, this)
            }), e.element.on("click", function() {
                setTimeout(function() {
                    t[0].focus()
                }, 0)
            })
        },
        _onDocumentClick: function(e) {
            var t = this;
            if (n(e.target).closest(".ak-navbar-search").length) {
                if (n(e.target).closest("input").length) {
                    n(e.target).closest("input").val() == t.initValue && n(e.target).closest("input").val("");
                    return
                }
                n(".ak-navbar-search").toggleClass("open")
            } else n(".ak-navbar-search").is(".open") && n(".ak-navbar-search").removeClass("open")
        },
        _onDocumentScroll: function(e) {
            var t = this
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_navbar_search.prototype.enhanceWithin(e.target)
    })
}(this),
function(e) {
    var t = require("lodash"),
        n = require("ankama.widget");
    n.widget("ankama.ak_filter", n.ankama.widget, {
        FILTERS_SELECTOR: ".ak-filters",
        FILTERS_TITLE: ".ak-title-filters",
        FILTERS_TITLE_SELECTOR: ".ak-filter-selection",
        FILTERS_TITLE_SELECTED: ".ak-filter-selected",
        FILTERS_LIST_SELECTOR: ".ak-list-filters-active",
        CHOICES_BLOCK_SELECTOR: ".ak-list-filters",
        CHOICES_BLOCK_TITLE_SELECTOR: ".ak-filter-selection",
        CHOICES_BLOCK_LIST_SELECTOR: ".ak-list-filters-check",
        CHOICES_BLOCK_LIST_INNER_SELECTOR: ".ak-list-filters-inner",
        SELECTED_HIDDEN_INPUTS: ".ak-selected-hidden-inputs",
        MORE_SELECTOR: ".ak-more-less",
        MORE_BUTTON_SELECTOR: ".ak-more-less-btn",
        MOVE_ORIGIN_SELECTOR: ".ak-filters-move-origin",
        MOVE_TARGET_SELECTOR: ".ak-filters-move-target",
        CACHE_INIT: "INIT",
        CACHE_EXPANDED_BLOCKS: "EB",
        options: {
            initSelector: ".ak-filters"
        },
        _sId: null,
        _oCache: {},
        _jqForm: null,
        _jqChoicesBlocks: null,
        _jqFiltersListItem: null,
        _jqSelectedHiddenInputs: null,
        _bIsMovedToTarget: !1,
        _create: function() {
            var t = this;
            t._sId = t.element.attr("id") || t.element.attr("data-name"), t._jqForm = t.element.find("form"), t._jqChoicesBlocks = t.element.find(t.CHOICES_BLOCK_SELECTOR), t._render(), t.element.find(t.CHOICES_BLOCK_LIST_SELECTOR).on("change", ":input", function(e) {
                n(e.target).is('[type="radio"]') ? (n(e.target).closest("ul").find("li").removeClass("ak-selected"), n(e.target).closest("li").addClass("ak-selected")) : n(e.target).closest("li").toggleClass("ak-selected")
            });
            var r = n(".ak-button-click", t._jqForm);
            r.length == 1 ? t._jqForm.on("click", ".ak-button-click", function(e) {
                e.preventDefault(), t._jqForm.submit()
            }) : t.element.find(t.CHOICES_BLOCK_LIST_SELECTOR).on("change", ":input:not(.no-submit)", function(e) {
                e.preventDefault(), t._jqForm.submit()
            }), t.element.on("click", this.FILTERS_TITLE, function(e) {
                t._toggleAll(!n(e.currentTarget).hasClass("ak-open"))
            }), n(t.FILTERS_TITLE_SELECTOR).on("click", "a.ak-picto-reset-filters", function(e) {
                e.preventDefault(), t._deleteChoices(null)
            }), t._addToggleBlocksEvents();
            var i = n(t.MOVE_TARGET_SELECTOR);
            if (i.length == 1) {
                var s = null;
                n(e).delegate(t.element, "resize", function() {
                    s = clearTimeout(s), s = setTimeout(function() {
                        s = null, !t._bIsMovedToTarget && i.css("display") != "none" ? t._moveToTarget(i) : t._bIsMovedToTarget && i.css("display") == "none" && t._moveToTarget(i)
                    }, 250)
                }), t._moveToTarget(i)
            }
        },
        _render: t.debounce(function() {
            var e = this,
                r = !n.akStorage.getSub(e._sId, e.CACHE_INIT),
                i = e._getCacheExpandedBlocks();
            e.oChoices = {}, e._jqChoicesBlocks.each(function() {
                var t = n(this),
                    s = t.attr("data-name");
                r && t.find(e.CHOICES_BLOCK_LIST_INNER_SELECTOR).css("display") == "block" && (i.push(s), e._setCacheExpandedBlocks(i));
                var o = [];
                t.find(":input:not(.ak-not-in-choices)").each(function() {
                    var t = n(this),
                        r = e._getInputValue(t);
                    r != null && o.push(r.sText)
                }), o.length > 0 && (e.oChoices[t.attr("data-name")] = {
                    sTitle: n.trim(t.find(e.CHOICES_BLOCK_TITLE_SELECTOR).text()),
                    aChoices: o
                });
                if (!r) {
                    var u = t.find(e.CHOICES_BLOCK_LIST_INNER_SELECTOR).hasClass("ak-force-open");
                    u || i.indexOf(s) != -1 || o.length > 0 ? (t.find(e.CHOICES_BLOCK_LIST_INNER_SELECTOR).css("display", "block"), t.find(e.CHOICES_BLOCK_TITLE_SELECTOR).find("a.ak-toggle").removeClass("ak-picto-open").addClass("ak-picto-close")) : (t.find(e.CHOICES_BLOCK_LIST_INNER_SELECTOR).css("display", "none"), t.find(e.CHOICES_BLOCK_TITLE_SELECTOR).find("a.ak-toggle").removeClass("ak-picto-close").addClass("ak-picto-open"))
                }
            });
            var s = n(e.FILTERS_TITLE_SELECTOR),
                o = n(e.FILTERS_TITLE_SELECTED),
                u = n(e.FILTERS_LIST_SELECTOR),
                a = e.element.find(this.FILTERS_TITLE);
            if (!u.hasClass("ak-server-side")) {
                t.isNull(e._jqFiltersListItem) && (e._jqFiltersListItem = u.find("li").first().clone()), o.css("display", "none"), u.css("display", "none"), u.find("li").remove();
                var f = 0;
                t.isEmpty(e.oChoices) || (n.each(e.oChoices, function(t, r) {
                    var i = r.sTitle,
                        s = r.aChoices,
                        o = s.join(", ");
                    o.length > 20 && (o = o.substring(0, 20) + "&hellip;");
                    var a = e._jqFiltersListItem.clone().append(i + (n.akLang() == "fr" ? " " : "") + ": " + o);
                    a.find("a").attr("data-name", t), a.on("click", "a", function(t) {
                        t.preventDefault(), e._deleteChoices(n(t.currentTarget).attr("data-name"))
                    }), u.append(a), f += s.length
                }), a.addClass("ak-open").next().css("display", "block"), o.css("display", "block"), u.css("display", "block")), s.find("span.badge").text("(" + f + ")")
            } else e._jqSelectedHiddenInputs = e.element.find(e.SELECTED_HIDDEN_INPUTS), u.find("li").on("click", "a", function(t) {
                t.preventDefault(), e._deleteChoices(n(t.currentTarget).attr("data-name"))
            });
            n.akStorage.setSub(e._sId, e.CACHE_INIT, !0)
        }, 10),
        _toggleAll: function(e) {
            var t = this;
            if (!t._bIsMovedToTarget) return !1;
            var n = t.element.find(this.FILTERS_TITLE);
            return e ? (n.removeClass("ak-close").addClass("ak-open"), n.next().css("display", "block")) : (n.next().css("display", "none"), n.removeClass("ak-open").addClass("ak-close")), !0
        },
        _addToggleBlocksEvents: function() {
            var e = this;
            e._jqChoicesBlocks.on("click", e.CHOICES_BLOCK_TITLE_SELECTOR, function(t) {
                var r = n(t.currentTarget);
                r.parents(e.CHOICES_BLOCK_SELECTOR).find(e.CHOICES_BLOCK_LIST_INNER_SELECTOR).slideToggle(e._getEffectsDuration(), function() {
                    var t = n(this),
                        i = e._getCacheExpandedBlocks(),
                        s = t.parents(e.CHOICES_BLOCK_SELECTOR).attr("data-name"),
                        o = i.indexOf(s);
                    t.css("display") == "block" ? (i.push(s), r.find("a.ak-toggle").removeClass("ak-picto-open").addClass("ak-picto-close")) : o != -1 && (i.splice(o, 1), r.find("a.ak-toggle").removeClass("ak-picto-close").addClass("ak-picto-open")), e._setCacheExpandedBlocks(i)
                })
            })
        },
        _deleteChoices: function(e) {
            var r = this,
                i = r._jqChoicesBlocks;
            e != null && (i = r._jqChoicesBlocks.filter(function() {
                return n(this).attr("data-name") == e
            })), i.each(function() {
                n(this).find(":input").each(function() {
                    r._inputReset(this)
                })
            });
            if (!t.isNull(r._jqSelectedHiddenInputs)) {
                var s = r._jqSelectedHiddenInputs;
                t.isNull(e) || (s = r._jqSelectedHiddenInputs.find(":input[data-name='" + e + "']")), s.each(function() {
                    r._inputReset(this)
                })
            }
            r._jqForm.submit()
        },
        _getEffectsDuration: function() {
            return 0
        },
        _moveToTarget: function(e) {
            var t = this;
            if (n.getCurrentProfile() === "mobile" || n.getCurrentProfile() === "tablet") {
                t.element.parent().hasClass("ak-filters-move-origin") || t.element.wrap('<div class="ak-filters-move-origin ak-filters-clone"></div>');
                var r = t.element.find(t.FILTERS_TITLE);
                e.append(t.element), n('a[href="#' + t.element.attr("id") + '"]').length == 0 ? n(t.MOVE_ORIGIN_SELECTOR).addClass("ak-filters-clone").append('<a href="#' + t.element.attr("id") + '" class="ak-filters-gotop ak-title">' + r.html() + "</a>") : n(t.MOVE_ORIGIN_SELECTOR).show(), n(".ak-filters-gotop").on("click", function(e) {
                    e.preventDefault(), n("html, body").animate({
                        scrollTop: n("#" + t.element.attr("id")).offset().top - 50
                    }, 250), t._toggleAll(!0)
                }), t._jqChoicesBlocks.each(function(e, r) {
                    var i = n(this),
                        s = t.oChoices[i.attr("data-name")],
                        o = i.find(t.CHOICES_BLOCK_LIST_INNER_SELECTOR).hasClass("ak-force-open");
                    o || s && s.aChoices.length > 0 || t._jqChoicesBlocks.length == 1 && e == 0 ? (i.find(t.CHOICES_BLOCK_LIST_INNER_SELECTOR).css("display", "block"), i.find(t.CHOICES_BLOCK_TITLE_SELECTOR).find("a.ak-toggle").removeClass("ak-picto-open").addClass("ak-picto-close")) : (i.find(t.CHOICES_BLOCK_LIST_INNER_SELECTOR).css("display", "none"), i.find(t.CHOICES_BLOCK_TITLE_SELECTOR).find("a.ak-toggle").removeClass("ak-picto-close").addClass("ak-picto-open"))
                }), t._bIsMovedToTarget = !0, t._toggleAll(t.options.bMobileToggleAll)
            } else t._toggleAll(!0), n(t.MOVE_ORIGIN_SELECTOR).removeClass("ak-filters-clone").html("").append(t.element), t._bIsMovedToTarget = !1, t._render()
        },
        _escapeHtml: function(e) {
            return e.replace(/&/g, "&amp;").replace(/"/g, "&quot;").replace(/'/g, "&#39;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace("?", "-")
        },
        _getInputValue: function(e) {
            var r = this,
                i = e.attr("id"),
                s = t.isUndefined(e.attr("type")) ? e.prop("tagName").toLowerCase() : e.attr("type"),
                o = null;
            switch (s) {
                case "radio":
                    e.prop("checked") && (o = n('label[for="' + i + '"]').text());
                    break;
                case "checkbox":
                    e.prop("checked") && (o = n('label[for="' + i + '"]').text());
                    break;
                case "text":
                    !t.isEmpty(e.val()) && e.val() != e.attr("placeholder") && (o = e.val(), o = r._escapeHtml(o));
                    break;
                case "select":
                    t.isEmpty(e.val()) || (o = e.find('option[value="' + e.val() + '"]').text());
                    break;
                default:
                    return null
            }
            return t.isNull(o) || (o = o.replace(/\n|\r/g, "").trim()), o != null ? {
                sId: i,
                sType: s,
                sText: o
            } : null
        },
        _inputReset: function(e) {
            var r = n(e),
                i = t.isUndefined(r.attr("type")) ? r.prop("tagName").toLowerCase() : r.attr("type"),
                s = null;
            switch (i) {
                case "radio":
                    r.prop("checked") && r.prop("checked", !1);
                    break;
                case "checkbox":
                    r.prop("checked") && r.prop("checked", !1);
                    break;
                case "text":
                    s = r.val(), !t.isEmpty(s) && s != r.attr("placeholder") && r.val(null);
                    break;
                case "select":
                    r.find("option:selected").prop("selected", null);
                    break;
                case "hidden":
                    r.remove()
            }
        },
        _getCacheExpandedBlocks: function() {
            return n.getCurrentProfile() !== "mobile" && n.getCurrentProfile() !== "tablet" && !t.isNull(n.akStorage.getSub(this._sId, this.CACHE_EXPANDED_BLOCKS)) ? n.akStorage.getSub(this._sId, this.CACHE_EXPANDED_BLOCKS) : []
        },
        _setCacheExpandedBlocks: function(e) {
            n.getCurrentProfile() !== "mobile" && n.getCurrentProfile() !== "tablet" && n.akStorage.setSub(this._sId, this.CACHE_EXPANDED_BLOCKS, e)
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_filter.prototype.enhanceWithin(e.target, !0)
    })
}(this),
function(e, t) {
    var n = require("ankama.widget"),
        n = require("jquery.ankama"),
        r = require("lodash");
    n.widget("ankama.ak_dofusrender", n.ankama.widget, {
        options: {
            initSelector: ".ak-dofus-render"
        },
        _isFlashEnabled: function() {
            var t = !1;
            if ("ActiveXObject" in e) try {
                t = !!(new ActiveXObject("ShockwaveFlash.ShockwaveFlash"))
            } catch (n) {
                t = !1
            } else t = !!navigator.mimeTypes["application/x-shockwave-flash"];
            return t
        },
        _renderFlash: function(e) {
            var t = [];
            switch (e.type) {
                case "guild":
                    var i = "DofusGuildes";
                    r.each(e, function(e, n) {
                        n.indexOf("bg") != -1 ? t.push("bcg" + n.substr(2, 1).toUpperCase() + n.substr(3).toLowerCase() + "=" + e) : n.indexOf("fr") != -1 ? t.push("frt" + n.substr(2, 1).toUpperCase() + n.substr(3).toLowerCase() + "=" + e) : n == "mode" && t.push(n + "=" + e)
                    });
                    break;
                case "character":
                case "alliance":
                    var i = e.type == "character" ? "DofusPersos" : "DofusAlliances";
                    t = ["render=direct", "align=TL"], e.render && t.push("focus=" + e.render), e.look && t.push("look=" + e.look)
            }
            return '<!--[if !IE]> --><object type="application/x-shockwave-flash" width="' + e.width + '" height="' + e.height + '" data="' + n.akStatic() + "/dofus/www/game/" + i + '.swf" >' + "<!-- <![endif]-->" + "<!--[if IE]>" + '<object class="fix" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="' + e.width + '" height="' + e.height + '" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10.0.0">' + '  <param name="movie" value="' + n.akStatic() + "/dofus/www/game/" + i + '.swf" />' + '  <param name="allowscriptaccess" value="always" />' + '  <param name="flashvars" value="' + t.join("&") + '" />' + '  <param name="wmode" value="transparent" />' + "<!--><!--dgx-->" + '  <param name="allowscriptaccess" value="always" />' + '  <param name="flashvars" value="' + t.join("&") + '" />' + '  <param name="wmode" value="transparent" />' + "</object>" + "<!-- <![endif]-->"
        },
        _renderImage: function(e) {
            var t = e.type == "alliance" ? "character" : e.type;
            delete e.type, e.look && (e.look = e.look.replace(/#/g, "~"));
            var i = [];
            return r.each(e, function(e, t) {
                i.push(t + "=" + e)
            }), '<img src="' + n.akStatic() + "/dofus/game/" + t + ".png?" + i.join("&") + '" alt="" width="' + e.width + '" height="' + e.height + '" />'
        },
        _create: function() {
            var e = this.element.akOptions();
            this.element.html(this._renderImage(e))
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_dofusrender.prototype.enhanceWithin(e.target)
    })
}(this),
function(e, t) {
    "use strict";
    var n = require("ankama.widget");
    n.widget("ankama.ak_mediacarousel", n.ankama.widget, {
        options: {
            initSelector: ".ak-media-carousel"
        },
        sMain: ".ak-media-carousel-main",
        sSelectedClass: "ak-media-preview-selected",
        _create: function() {
            var e = this;
            n(".ak-carouseltouch .carouselcontainer", e.element).delegate(".item", "tap", n.proxy(e._onClickElement, e)), e._switchImage(n(".ak-carouseltouch .carouselcontainer .item:first", e.element))
        },
        _switchImage: function(e) {
            var t = this;
            n(t.sMain + " img", t.element).attr("src", e.data().media), n("." + t.sSelectedClass).removeClass(t.sSelectedClass), e.addClass(t.sSelectedClass)
        },
        _onClickElement: function(e) {
            var t = this;
            t._switchImage(n(e.currentTarget), t.element)
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_mediacarousel.prototype.enhanceWithin(e.target)
    })
}(window),
function(e) {
    "use strict";
    var t = require("lodash"),
        n = require("ankama.widget");
    n.widget("ankama.ak_numeric_min_max", n.ankama.widget, {
        CLASS_INPUT_MIN: "ak-numeric-min",
        CLASS_INPUT_MAX: "ak-numeric-max",
        STATUS_MIN: "MIN",
        STATUS_MAX: "MAX",
        options: {
            initSelector: ".ak-numeric-min-max"
        },
        _jqInputMin: null,
        _jqInputMax: null,
        _create: function() {
            var e = this;
            e._jqInputMin = n("." + e.CLASS_INPUT_MIN, e.element), e._jqInputMax = n("." + e.CLASS_INPUT_MAX, e.element);
            if (e._jqInputMin.length != 1 || e._jqInputMax.length != 1) return !1;
            e._setDefaultMinMaxValues(), e.element.on("change", ":input", function(t) {
                t.preventDefault(), e._change(n(t.currentTarget))
            })
        },
        _setDefaultMinMaxValues: function() {
            var e = this,
                n = null,
                r = e._getValue(e._jqInputMin);
            t.isNull(r) && (n = e._getMinMaxValues(e._jqInputMin), e._setValue(e._jqInputMin, n.iMinValue));
            var i = e._getValue(e._jqInputMax);
            t.isNull(i) && (n = e._getMinMaxValues(e._jqInputMax), e._setValue(e._jqInputMax, n.iMaxValue))
        },
        _change: function(e) {
            var t = this,
                r = t._getInputType(e),
                i = t._getValue(t._jqInputMin),
                s = t._getValue(t._jqInputMax);
            if (i == null || s == null) {
                if (i == null && s == null) return !0;
                if (r == "select") {
                    var o = e.hasClass(t.CLASS_INPUT_MIN);
                    o && i && !s && (s = n("option", t._jqInputMax).last().val(), t._setValue(t._jqInputMax, s))
                }
            } else if (s < i) {
                var u = e.hasClass(t.CLASS_INPUT_MIN) ? t.STATUS_MIN : e.hasClass(t.CLASS_INPUT_MAX) ? t.STATUS_MAX : null,
                    a = {};
                u == t.STATUS_MIN ? (a = r == "select" ? t._getSelectPreviousNextValues(t._jqInputMax, i) : {
                    iPreviousValue: "NaN",
                    iNextValue: i + 1
                }, t._setValue(t._jqInputMax, a.iNextValue)) : u == t.STATUS_MAX && (a = r == "select" ? t._getSelectPreviousNextValues(t._jqInputMin, s) : {
                    iPreviousValue: s - 1 >= 0 ? s - 1 : 0,
                    iNextValue: "NaN"
                }, t._setValue(t._jqInputMin, a.iPreviousValue))
            }
            return !0
        },
        _getInputType: function(e) {
            return t.isUndefined(e.attr("type")) ? e.prop("tagName").toLowerCase() : e.attr("type")
        },
        _getValue: function(e) {
            var n = this,
                r = n._getInputType(e),
                i = null;
            switch (r) {
                case "text":
                    !t.isEmpty(e.val()) && e.val() != e.attr("placeholder") && (i = e.val(), t.isFinite(i) || (i = null, e.val("")));
                    break;
                case "select":
                    t.isEmpty(e.val()) || (i = e.val())
            }
            return i != null && !t.isNumber(i) && (i = i.replace(/[^0-9]/g, "")), i != null ? +i : null
        },
        _setValue: function(e, t) {
            var n = this,
                r = n._getInputType(e);
            switch (r) {
                case "text":
                    e.val(t);
                    break;
                case "select":
                    e.find('option[value="' + t + '"]').prop("selected", !0)
            }
        },
        _getMinMaxValues: function(e) {
            var r = null,
                i = null;
            return n("option", e).each(function() {
                if (!t.isEmpty(n(this).val())) {
                    var e = +n(this).val();
                    r == null && (r = e), i = e
                }
            }), {
                iMinValue: r,
                iMaxValue: i
            }
        },
        _getSelectPreviousNextValues: function(e, r) {
            var i = null,
                s = null;
            return n("option", e).each(function() {
                if (!t.isEmpty(n(this).val())) {
                    var e = +n(this).val();
                    if (i == null || e < r) i = e;
                    else if (s == null && e > r) return s = e, !1
                }
            }), {
                iPreviousValue: i != null ? i : r,
                iNextValue: s != null ? s : r
            }
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_numeric_min_max.prototype.enhanceWithin(e.target, !0)
    })
}(this),
function(e) {
    var t = require("lodash"),
        n = require("ankama.widget");
    n.widget("ankama.ak_forum_post_panel", n.ankama.widget, {
        options: {
            initSelector: ".ak-forum-post-panel"
        },
        _create: function() {
            var e = this;
            n(e.element).on("click", ".ak-comment-submit", function(t) {
                e._openLoginModal(t)
            }), n(e.element).on("click", ".ak-comment-textarea", function(t) {
                e._openLoginModal(t)
            }), n(e.element).on("click", ".ak-see-more-trigger", function() {
                var e = n(this).data("toggle-target") + ":hidden:first";
                n(e).slideDown();
                var t = n(this).closest(".ak-subpost").find(".ak-post:hidden").length;
                n(this).find(".ak-see-multiple-comments span").text(t), t == 1 && (n(this).find(".ak-see-multiple-comments").hide(), n(this).find(".ak-see-last-comment").removeClass("hide")), n(e).length == 0 && n(this).hide()
            })
        },
        _openLoginModal: function(e) {
            var r = this;
            if (t.isEmpty(n('input[name="user"]', n(r.element)).val())) {
                e.preventDefault();
                var i = n.openLoginModal();
                i || n.openNicknameModal()
            }
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_forum_post_panel.prototype.enhanceWithin(e.target, !0)
    })
}(this),
function(e, t) {
    "use strict";
    var n = require("lodash"),
        r = require("ankama.widget");
    r.widget("ankama.ak_quick_access", r.ankama.widget, {
        options: {
            initSelector: ".ak-item-aside-nav"
        },
        _bSmartDevices: !0,
        _jqContentsRoot: null,
        _oAnchorsTopOffsets: null,
        _get_oAnchorsTopOffsets: function() {
            var e = this;
            if (n.isNull(e._oAnchorsTopOffsets)) {
                e._oAnchorsTopOffsets = {};
                var t = n.isNull(e._jqContentsRoot) ? r(".ak-quick-access-anchor") : r(".ak-quick-access-anchor", r(e._jqContentsRoot));
                t.each(function(t, n) {
                    var i = r(n),
                        s = i.offset();
                    e._oAnchorsTopOffsets[i.attr("name")] = s.top
                }), e._oAnchorsTopOffsets = e._objectReverse(e._oAnchorsTopOffsets)
            }
            return e._oAnchorsTopOffsets
        },
        _create: function() {
            var t = this;
            t._smartDevicesChecking();
            var i = t.element.akOptions(),
                s = r(t.element),
                o = s.offset(),
                u = s.parents(".ak-panel").css("marginBottom"),
                a = n.isNull(i.sContentsRootSelector) ? null : i.sContentsRootSelector;
            t._jqContentsRoot = n.isNull(a) ? null : r(a);
            var f = n.isNull(i.sTriggerSelector) ? null : i.sTriggerSelector;
            !n.isNull(t._jqContentsRoot) && !n.isNull(f) ? (t._jqContentsRoot.is(":hidden") == 0 && t._get_oAnchorsTopOffsets(), r(f).on("initQuickAccess", function(e, n) {
                n == a && t._get_oAnchorsTopOffsets()
            })) : t._get_oAnchorsTopOffsets();
            var l = this.element.scrollParent(),
                c = r("nav").height() + parseFloat(u),
                h = r("aside.col-md-3").width() + 1;
            r("aside.col-md-3>.ak-main-aside").length > 0 && (h = r("aside.col-md-3>.ak-main-aside").width() + 1), l.scroll(function(e) {
                if (!t._bSmartDevices) {
                    var r = l.scrollTop();
                    r > o.top ? s.css("width", h).css("position", "fixed").css("top", c) : s.css("position", "");
                    var i = null;
                    n.each(t._oAnchorsTopOffsets, function(e, t) {
                        r >= e && n.isNull(i) && (i = t)
                    }), t._selectedLink(i)
                }
            });
            var p = s.find("li.ak-elt");
            r(t.element).on("click", ".ak-bottom-link", function(e) {
                e.preventDefault(), p.filter(".hidden").removeClass("hidden"), r(t.element).find("a.ak-bottom-link").addClass("hidden")
            }), r(t.element).on("click", "ul.ak-list-text a", function(e) {
                p.filter(".selected").removeClass("selected"), r(e.currentTarget).parent("li").addClass("selected")
            });
            var d = null;
            r(e).delegate(t.element, "resize", function() {
                d = clearTimeout(d), d = setTimeout(function() {
                    d = null, t._smartDevicesChecking()
                }, 250)
            })
        },
        _objectReverse: function(e) {
            var t = {},
                n = [];
            for (var r in e) n.push(r);
            for (var i = n.length - 1; i >= 0; i--) t[n[i]] = e[n[i]];
            return t
        },
        _smartDevicesChecking: function() {
            var e = this;
            r.getCurrentProfile() === "mobile" || r.getCurrentProfile() === "tablet" ? e._bSmartDevices || (r(e.element).css("width", "").css("position", "").css("top", ""), e._bSmartDevices = !0) : e._bSmartDevices = !1
        },
        _selectedLink: function(e) {
            var t = this,
                n = r(t.element).find("li.ak-elt");
            n.filter(".selected").removeClass("selected"), n.find('a[href="#' + e + '"]').parent("li").addClass("selected")
        }
    }), r(document).bind("ready widgetcreate", function(e) {
        r.ankama.ak_quick_access.prototype.enhanceWithin(e.target)
    })
}(this),
function(e, t) {
    var n = require("ankama.widget"),
        n = require("jquery.ankama"),
        r = require("lodash");
    n.widget("ankama.ak_item", n.ankama.widget, {
        options: {
            initSelector: ".ak-item"
        },
        _create: function() {
            var e = this,
                t = e.element,
                r = t.akOptions(),
                i = {};
            n.getCurrentProfile() !== "mobile" && n.getCurrentProfile() !== "tablet" && (i = {
                effect: "slide",
                duration: 300
            }), t.on("click", ".ak-item-top-nav-toggle", function(e) {
                e.preventDefault(), n(e.currentTarget).next().toggle(i)
            })
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_item.prototype.enhanceWithin(e.target)
    })
}(this),
function() {
    var e, t = this,
        n = function() {},
        r = function(e) {
            var t = typeof e;
            if ("object" == t) {
                if (!e) return "null";
                if (e instanceof Array) return "array";
                if (e instanceof Object) return t;
                var n = Object.prototype.toString.call(e);
                if ("[object Window]" == n) return "object";
                if ("[object Array]" == n || "number" == typeof e.length && "undefined" != typeof e.splice && "undefined" != typeof e.propertyIsEnumerable && !e.propertyIsEnumerable("splice")) return "array";
                if ("[object Function]" == n || "undefined" != typeof e.call && "undefined" != typeof e.propertyIsEnumerable && !e.propertyIsEnumerable("call")) return "function"
            } else if ("function" == t && "undefined" == typeof e.call) return "object";
            return t
        },
        i = function(e) {
            return "array" == r(e)
        },
        s = function(e) {
            var t = r(e);
            return "array" == t || "object" == t && "number" == typeof e.length
        },
        o = function(e) {
            return "string" == typeof e
        },
        u = function(e) {
            return "function" == r(e)
        },
        a = function(e) {
            var t = typeof e;
            return "object" == t && null != e || "function" == t
        },
        f = function(e, t, n) {
            return e.call.apply(e.bind, arguments)
        },
        l = function(e, t, n) {
            if (!e) throw Error();
            if (2 < arguments.length) {
                var r = Array.prototype.slice.call(arguments, 2);
                return function() {
                    var n = Array.prototype.slice.call(arguments);
                    return Array.prototype.unshift.apply(n, r), e.apply(t, n)
                }
            }
            return function() {
                return e.apply(t, arguments)
            }
        },
        c = function(e, t, n) {
            return c = Function.prototype.bind && -1 != Function.prototype.bind.toString().indexOf("native code") ? f : l, c.apply(null, arguments)
        },
        h = function(e, t) {
            var n = Array.prototype.slice.call(arguments, 1);
            return function() {
                var t = n.slice();
                return t.push.apply(t, arguments), e.apply(this, t)
            }
        },
        p = Date.now || function() {
            return +(new Date)
        },
        d = null,
        v = function(e, n) {
            var r = e.split("."),
                i = t;
            r[0] in i || !i.execScript || i.execScript("var " + r[0]);
            for (var s; r.length && (s = r.shift());) r.length || void 0 === n ? i = i[s] ? i[s] : i[s] = {} : i[s] = n
        },
        m = function(e, t) {
            function n() {}
            n.prototype = t.prototype, e.superClass_ = t.prototype, e.prototype = new n, e.base = function(e, n, r) {
                return t.prototype[n].apply(e, Array.prototype.slice.call(arguments, 2))
            }
        };
    Function.prototype.bind = Function.prototype.bind || function(e, t) {
        if (1 < arguments.length) {
            var n = Array.prototype.slice.call(arguments, 1);
            return n.unshift(this, e), c.apply(null, n)
        }
        return c(this, e)
    };
    var g = {};
    v("RecaptchaTemplates", g), g.VertHtml = '<table id="recaptcha_table" class="recaptchatable" > <tr> <td colspan="6" class=\'recaptcha_r1_c1\'></td> </tr> <tr> <td class=\'recaptcha_r2_c1\'></td> <td colspan="4" class=\'recaptcha_image_cell\'><center><div id="recaptcha_image"></div></center></td> <td class=\'recaptcha_r2_c2\'></td> </tr> <tr> <td rowspan="6" class=\'recaptcha_r3_c1\'></td> <td colspan="4" class=\'recaptcha_r3_c2\'></td> <td rowspan="6" class=\'recaptcha_r3_c3\'></td> </tr> <tr> <td rowspan="3" class=\'recaptcha_r4_c1\' height="49"> <div class="recaptcha_input_area"> <input name="recaptcha_response_field" id="recaptcha_response_field" type="text" autocorrect="off" autocapitalize="off" placeholder="" /> <span id="recaptcha_privacy" class="recaptcha_only_if_privacy"></span> </div> </td> <td rowspan="4" class=\'recaptcha_r4_c2\'></td> <td><a id=\'recaptcha_reload_btn\'><img id=\'recaptcha_reload\' width="25" height="17" /></a></td> <td rowspan="4" class=\'recaptcha_r4_c4\'></td> </tr> <tr> <td><a id=\'recaptcha_switch_audio_btn\' class="recaptcha_only_if_image"><img id=\'recaptcha_switch_audio\' width="25" height="16" alt="" /></a><a id=\'recaptcha_switch_img_btn\' class="recaptcha_only_if_audio"><img id=\'recaptcha_switch_img\' width="25" height="16" alt=""/></a></td> </tr> <tr> <td><a id=\'recaptcha_whatsthis_btn\'><img id=\'recaptcha_whatsthis\' width="25" height="16" /></a></td> </tr> <tr> <td class=\'recaptcha_r7_c1\'></td> <td class=\'recaptcha_r8_c1\'></td> </tr> </table> ', g.CleanCss = ".recaptchatable td img{display:block}.recaptchatable .recaptcha_image_cell center img{height:57px}.recaptchatable .recaptcha_image_cell center{height:57px}.recaptchatable .recaptcha_image_cell{background-color:white;height:57px;padding:7px!important}.recaptchatable,#recaptcha_area tr,#recaptcha_area td,#recaptcha_area th{margin:0!important;border:0!important;border-collapse:collapse!important;vertical-align:middle!important}.recaptchatable *{margin:0;padding:0;border:0;color:black;position:static;top:auto;left:auto;right:auto;bottom:auto}.recaptchatable #recaptcha_image{position:relative;margin:auto;border:1px solid #dfdfdf!important}.recaptchatable #recaptcha_image #recaptcha_challenge_image{display:block}.recaptchatable #recaptcha_image #recaptcha_ad_image{display:block;position:absolute;top:0}.recaptchatable a img{border:0}.recaptchatable a,.recaptchatable a:hover{cursor:pointer;outline:none;border:0!important;padding:0!important;text-decoration:none;color:blue;background:none!important;font-weight:normal}.recaptcha_input_area{position:relative!important;background:none!important}.recaptchatable label.recaptcha_input_area_text{border:1px solid #dfdfdf!important;margin:0!important;padding:0!important;position:static!important;top:auto!important;left:auto!important;right:auto!important;bottom:auto!important}.recaptcha_theme_red label.recaptcha_input_area_text,.recaptcha_theme_white label.recaptcha_input_area_text{color:black!important}.recaptcha_theme_blackglass label.recaptcha_input_area_text{color:white!important}.recaptchatable #recaptcha_response_field{font-size:11pt}.recaptcha_theme_blackglass #recaptcha_response_field,.recaptcha_theme_white #recaptcha_response_field{border:1px solid gray}.recaptcha_theme_red #recaptcha_response_field{border:1px solid #cca940}.recaptcha_audio_cant_hear_link{font-size:7pt;color:black}.recaptchatable{line-height:1em;border:1px solid #dfdfdf!important}.recaptcha_error_text{color:red}.recaptcha_only_if_privacy{float:right;text-align:right;margin-right:7px}#recaptcha-ad-choices{position:absolute;height:15px;top:0;right:0}#recaptcha-ad-choices img{height:15px}.recaptcha-ad-choices-collapsed{width:30px;height:15px;display:block}.recaptcha-ad-choices-expanded{width:75px;height:15px;display:none}#recaptcha-ad-choices:hover .recaptcha-ad-choices-collapsed{display:none}#recaptcha-ad-choices:hover .recaptcha-ad-choices-expanded{display:block}", g.CleanHtml = '<table id="recaptcha_table" class="recaptchatable"> <tr height="73"> <td class=\'recaptcha_image_cell\' width="302"><center><div id="recaptcha_image"></div></center></td> <td style="padding: 10px 7px 7px 7px;"> <a id=\'recaptcha_reload_btn\'><img id=\'recaptcha_reload\' width="25" height="18" alt="" /></a> <a id=\'recaptcha_switch_audio_btn\' class="recaptcha_only_if_image"><img id=\'recaptcha_switch_audio\' width="25" height="15" alt="" /></a><a id=\'recaptcha_switch_img_btn\' class="recaptcha_only_if_audio"><img id=\'recaptcha_switch_img\' width="25" height="15" alt=""/></a> <a id=\'recaptcha_whatsthis_btn\'><img id=\'recaptcha_whatsthis\' width="25" height="16" /></a> </td> <td style="padding: 18px 7px 18px 7px;"> <img id=\'recaptcha_logo\' alt="" width="71" height="36" /> </td> </tr> <tr> <td style="padding-left: 7px;"> <div class="recaptcha_input_area" style="padding-top: 2px; padding-bottom: 7px;"> <input style="border: 1px solid #3c3c3c; width: 302px;" name="recaptcha_response_field" id="recaptcha_response_field" type="text" /> </div> </td> <td colspan=2><span id="recaptcha_privacy" class="recaptcha_only_if_privacy"></span></td> </tr> </table> ', g.VertCss = ".recaptchatable td img{display:block}.recaptchatable .recaptcha_r1_c1{background:url('IMGROOT/sprite.png') 0 -63px no-repeat;width:318px;height:9px}.recaptchatable .recaptcha_r2_c1{background:url('IMGROOT/sprite.png') -18px 0 no-repeat;width:9px;height:57px}.recaptchatable .recaptcha_r2_c2{background:url('IMGROOT/sprite.png') -27px 0 no-repeat;width:9px;height:57px}.recaptchatable .recaptcha_r3_c1{background:url('IMGROOT/sprite.png') 0 0 no-repeat;width:9px;height:63px}.recaptchatable .recaptcha_r3_c2{background:url('IMGROOT/sprite.png') -18px -57px no-repeat;width:300px;height:6px}.recaptchatable .recaptcha_r3_c3{background:url('IMGROOT/sprite.png') -9px 0 no-repeat;width:9px;height:63px}.recaptchatable .recaptcha_r4_c1{background:url('IMGROOT/sprite.png') -43px 0 no-repeat;width:171px;height:49px}.recaptchatable .recaptcha_r4_c2{background:url('IMGROOT/sprite.png') -36px 0 no-repeat;width:7px;height:57px}.recaptchatable .recaptcha_r4_c4{background:url('IMGROOT/sprite.png') -214px 0 no-repeat;width:97px;height:57px}.recaptchatable .recaptcha_r7_c1{background:url('IMGROOT/sprite.png') -43px -49px no-repeat;width:171px;height:8px}.recaptchatable .recaptcha_r8_c1{background:url('IMGROOT/sprite.png') -43px -49px no-repeat;width:25px;height:8px}.recaptchatable .recaptcha_image_cell center img{height:57px}.recaptchatable .recaptcha_image_cell center{height:57px}.recaptchatable .recaptcha_image_cell{background-color:white;height:57px}#recaptcha_area,#recaptcha_table{width:318px!important}.recaptchatable,#recaptcha_area tr,#recaptcha_area td,#recaptcha_area th{margin:0!important;border:0!important;padding:0!important;border-collapse:collapse!important;vertical-align:middle!important}.recaptchatable *{margin:0;padding:0;border:0;font-family:helvetica,sans-serif;font-size:8pt;color:black;position:static;top:auto;left:auto;right:auto;bottom:auto}.recaptchatable #recaptcha_image{position:relative;margin:auto}.recaptchatable #recaptcha_image #recaptcha_challenge_image{display:block}.recaptchatable #recaptcha_image #recaptcha_ad_image{display:block;position:absolute;top:0}.recaptchatable img{border:0!important;margin:0!important;padding:0!important}.recaptchatable a,.recaptchatable a:hover{cursor:pointer;outline:none;border:0!important;padding:0!important;text-decoration:none;color:blue;background:none!important;font-weight:normal}.recaptcha_input_area{position:relative!important;width:153px!important;height:45px!important;margin-left:7px!important;margin-right:7px!important;background:none!important}.recaptchatable label.recaptcha_input_area_text{margin:0!important;padding:0!important;position:static!important;top:auto!important;left:auto!important;right:auto!important;bottom:auto!important;background:none!important;height:auto!important;width:auto!important}.recaptcha_theme_red label.recaptcha_input_area_text,.recaptcha_theme_white label.recaptcha_input_area_text{color:black!important}.recaptcha_theme_blackglass label.recaptcha_input_area_text{color:white!important}.recaptchatable #recaptcha_response_field{width:153px!important;position:relative!important;bottom:7px!important;padding:0!important;margin:15px 0 0 0!important;font-size:10pt}.recaptcha_theme_blackglass #recaptcha_response_field,.recaptcha_theme_white #recaptcha_response_field{border:1px solid gray}.recaptcha_theme_red #recaptcha_response_field{border:1px solid #cca940}.recaptcha_audio_cant_hear_link{font-size:7pt;color:black}.recaptchatable{line-height:1!important}#recaptcha_instructions_error{color:red!important}.recaptcha_only_if_privacy{float:right;text-align:right}#recaptcha-ad-choices{position:absolute;height:15px;top:0;right:0}#recaptcha-ad-choices img{height:15px}.recaptcha-ad-choices-collapsed{width:30px;height:15px;display:block}.recaptcha-ad-choices-expanded{width:75px;height:15px;display:none}#recaptcha-ad-choices:hover .recaptcha-ad-choices-collapsed{display:none}#recaptcha-ad-choices:hover .recaptcha-ad-choices-expanded{display:block}";
    var y = {
            visual_challenge: "Get a visual challenge",
            audio_challenge: "Get an audio challenge",
            refresh_btn: "Get a new challenge",
            instructions_visual: "Type the text:",
            instructions_audio: "Type what you hear:",
            help_btn: "Help",
            play_again: "Play sound again",
            cant_hear_this: "Download sound as MP3",
            incorrect_try_again: "Incorrect. Try again.",
            image_alt_text: "reCAPTCHA challenge image",
            privacy_and_terms: "Privacy & Terms"
        },
        b = {
            visual_challenge: "Ø§Ù„Ø­ØµÙˆÙ„ Ø¹Ù„Ù‰ ØªØ­Ø¯Ù Ù…Ø±Ø¦ÙŠ",
            audio_challenge: "Ø§Ù„Ø­ØµÙˆÙ„ Ø¹Ù„Ù‰ ØªØ­Ø¯Ù ØµÙˆØªÙŠ",
            refresh_btn: "Ø§Ù„Ø­ØµÙˆÙ„ Ø¹Ù„Ù‰ ØªØ­Ø¯Ù Ø¬Ø¯ÙŠØ¯",
            instructions_visual: "ÙŠØ±Ø¬Ù‰ ÙƒØªØ§Ø¨Ø© Ø§Ù„Ù†Øµ:",
            instructions_audio: "Ø§ÙƒØªØ¨ Ù…Ø§ ØªØ³Ù…Ø¹Ù‡:",
            help_btn: "Ù…Ø³Ø§Ø¹Ø¯Ø©",
            play_again: "ØªØ´ØºÙŠÙ„ Ø§Ù„ØµÙˆØª Ù…Ø±Ø© Ø£Ø®Ø±Ù‰",
            cant_hear_this: "ØªÙ†Ø²ÙŠÙ„ Ø§Ù„ØµÙˆØª Ø¨ØªÙ†Ø³ÙŠÙ‚ MP3",
            incorrect_try_again: "ØºÙŠØ± ØµØ­ÙŠØ­. Ø£Ø¹Ø¯ Ø§Ù„Ù…Ø­Ø§ÙˆÙ„Ø©.",
            image_alt_text: "ØµÙˆØ±Ø© Ø§Ù„ØªØ­Ø¯ÙŠ Ù…Ù† reCAPTCHA",
            privacy_and_terms: "Ø§Ù„Ø®ØµÙˆØµÙŠØ© ÙˆØ§Ù„Ø¨Ù†ÙˆØ¯"
        },
        w = {
            visual_challenge: "Obtener una pista visual",
            audio_challenge: "Obtener una pista sonora",
            refresh_btn: "Obtener una pista nueva",
            instructions_visual: "Introduzca el texto:",
            instructions_audio: "Escribe lo que oigas:",
            help_btn: "Ayuda",
            play_again: "Volver a reproducir el sonido",
            cant_hear_this: "Descargar el sonido en MP3",
            incorrect_try_again: "Incorrecto. VuÃ©lvelo a intentar.",
            image_alt_text: "Pista de imagen reCAPTCHA",
            privacy_and_terms: "Privacidad y condiciones"
        },
        E = {
            visual_challenge: "Kumuha ng pagsubok na visual",
            audio_challenge: "Kumuha ng pagsubok na audio",
            refresh_btn: "Kumuha ng bagong pagsubok",
            instructions_visual: "I-type ang teksto:",
            instructions_audio: "I-type ang iyong narinig",
            help_btn: "Tulong",
            play_again: "I-play muli ang tunog",
            cant_hear_this: "I-download ang tunog bilang MP3",
            incorrect_try_again: "Hindi wasto. Muling subukan.",
            image_alt_text: "larawang panghamon ng reCAPTCHA",
            privacy_and_terms: "Privacy at Mga Tuntunin"
        },
        S = {
            visual_challenge: "Test visuel",
            audio_challenge: "Test audio",
            refresh_btn: "Nouveau test",
            instructions_visual: "Saisissez le texteÂ :",
            instructions_audio: "Qu'entendez-vous ?",
            help_btn: "Aide",
            play_again: "RÃ©Ã©couter",
            cant_hear_this: "TÃ©lÃ©charger l'audio au format MP3",
            incorrect_try_again: "Incorrect. Veuillez rÃ©essayer.",
            image_alt_text: "Image reCAPTCHA",
            privacy_and_terms: "ConfidentialitÃ© et conditions d'utilisation"
        },
        x = {
            visual_challenge: "Dapatkan kata pengujian berbentuk visual",
            audio_challenge: "Dapatkan kata pengujian berbentuk audio",
            refresh_btn: "Dapatkan kata pengujian baru",
            instructions_visual: "Ketik teks:",
            instructions_audio: "Ketik yang Anda dengar:",
            help_btn: "Bantuan",
            play_again: "Putar suara sekali lagi",
            cant_hear_this: "Unduh suara sebagai MP3",
            incorrect_try_again: "Salah. Coba lagi.",
            image_alt_text: "Gambar tantangan reCAPTCHA",
            privacy_and_terms: "Privasi & Persyaratan"
        },
        T = {
            visual_challenge: "×§×‘×œ ××ª×’×¨ ×—×–×•×ª×™",
            audio_challenge: "×§×‘×œ ××ª×’×¨ ×©×ž×¢",
            refresh_btn: "×§×‘×œ ××ª×’×¨ ×—×“×©",
            instructions_visual: "×”×§×œ×“ ××ª ×”×˜×§×¡×˜:",
            instructions_audio: "×”×§×œ×“ ××ª ×ž×” ×©××ª×” ×©×•×ž×¢:",
            help_btn: "×¢×–×¨×”",
            play_again: "×”×¤×¢×œ ×©×•×‘ ××ª ×”×©×ž×¢",
            cant_hear_this: "×”×•×¨×“ ×©×ž×¢ ×›-3MP",
            incorrect_try_again: "×©×’×•×™. × ×¡×” ×©×•×‘.",
            image_alt_text: "×ª×ž×•× ×ª ××ª×’×¨ ×©×œ reCAPTCHA",
            privacy_and_terms: "×¤×¨×˜×™×•×ª ×•×ª× ××™×"
        },
        N = {
            visual_challenge: "Obter um desafio visual",
            audio_challenge: "Obter um desafio de Ã¡udio",
            refresh_btn: "Obter um novo desafio",
            instructions_visual: "Digite o texto:",
            instructions_audio: "Digite o que vocÃª ouve:",
            help_btn: "Ajuda",
            play_again: "Reproduzir som novamente",
            cant_hear_this: "Fazer download do som no formato MP3",
            incorrect_try_again: "Incorreto. Tente novamente.",
            image_alt_text: "Imagem de desafio reCAPTCHA",
            privacy_and_terms: "Privacidade e Termos"
        },
        C = {
            visual_challenge: "ObÅ£ineÅ£i un cod captcha vizual",
            audio_challenge: "ObÅ£ineÅ£i un cod captcha audio",
            refresh_btn: "ObÅ£ineÅ£i un nou cod captcha",
            instructions_visual: "IntroduceÈ›i textul:",
            instructions_audio: "IntroduceÅ£i ceea ce auziÅ£i:",
            help_btn: "Ajutor",
            play_again: "RedaÅ£i sunetul din nou",
            cant_hear_this: "DescÄƒrcaÅ£i fiÅŸierul audio ca MP3",
            incorrect_try_again: "Incorect. ÃŽncercaÅ£i din nou.",
            image_alt_text: "Imagine de verificare reCAPTCHA",
            privacy_and_terms: "ConfidenÅ£ialitate ÅŸi termeni"
        },
        k = {
            visual_challenge: "æ”¶åˆ°ä¸€ä¸ªè§†é¢‘é‚€è¯·",
            audio_challenge: "æ¢ä¸€ç»„éŸ³é¢‘éªŒè¯ç ",
            refresh_btn: "æ¢ä¸€ç»„éªŒè¯ç ",
            instructions_visual: "è¾“å…¥æ–‡å­—ï¼š",
            instructions_audio: "è¯·é”®å…¥æ‚¨å¬åˆ°çš„å†…å®¹ï¼š",
            help_btn: "å¸®åŠ©",
            play_again: "é‡æ–°æ’­æ”¾",
            cant_hear_this: "ä»¥ MP3 æ ¼å¼ä¸‹è½½å£°éŸ³",
            incorrect_try_again: "ä¸æ­£ç¡®ï¼Œè¯·é‡è¯•ã€‚",
            image_alt_text: "reCAPTCHA éªŒè¯å›¾ç‰‡",
            privacy_and_terms: "éšç§æƒå’Œä½¿ç”¨æ¡æ¬¾"
        },
        L = {
            en: y,
            af: {
                visual_challenge: "Kry 'n visuele verifiÃ«ring",
                audio_challenge: "Kry 'n klankverifiÃ«ring",
                refresh_btn: "Kry 'n nuwe verifiÃ«ring",
                instructions_visual: "",
                instructions_audio: "Tik wat jy hoor:",
                help_btn: "Hulp",
                play_again: "Speel geluid weer",
                cant_hear_this: "Laai die klank af as MP3",
                incorrect_try_again: "Verkeerd. Probeer weer.",
                image_alt_text: "reCAPTCHA-uitdagingprent",
                privacy_and_terms: "Privaatheid en bepalings"
            },
            am: {
                visual_challenge: "á‹¨áŠ¥á‹­á‰³ á‰°áŒ‹áŒ£áˆš áŠ áŒáŠ",
                audio_challenge: "áˆŒáˆ‹ áŠ á‹²áˆµ á‹¨á‹µáˆáŒ½ áŒ¥á‹«á‰„ á‹­á‰…áˆ¨á‰¥",
                refresh_btn: "áˆŒáˆ‹ áŠ á‹²áˆµ áŒ¥á‹«á‰„ á‹­á‰…áˆ¨á‰¥",
                instructions_visual: "",
                instructions_audio: "á‹¨áˆá‰µáˆ°áˆ›á‹áŠ• á‰°á‹­á‰¥á¡-",
                help_btn: "áŠ¥áŒˆá‹›",
                play_again: "á‹µáˆáŒ¹áŠ• áŠ¥áŠ•á‹°áŒˆáŠ“ áŠ áŒ«á‹á‰µ",
                cant_hear_this: "á‹µáˆáŒ¹áŠ• á‰ MP3 á‰…áˆ­áŒ½ áŠ á‹áˆ­á‹µ",
                incorrect_try_again: "á‰µáŠ­áŠ­áˆ áŠ á‹­á‹°áˆˆáˆá¢ áŠ¥áŠ•á‹°áŒˆáŠ“ áˆžáŠ­áˆ­á¢",
                image_alt_text: "reCAPTCHA áˆáˆµáˆ áŒáŒ áˆ",
                privacy_and_terms: "áŒáˆ‹á‹ŠáŠá‰µ áŠ¥áŠ“ á‹áˆ"
            },
            ar: b,
            "ar-EG": b,
            bg: {
                visual_challenge: "ÐŸÐ¾Ð»ÑƒÑ‡Ð°Ð²Ð°Ð½Ðµ Ð½Ð° Ð²Ð¸Ð·ÑƒÐ°Ð»Ð½Ð° Ð¿Ñ€Ð¾Ð²ÐµÑ€ÐºÐ°",
                audio_challenge: "Ð—Ð°Ñ€ÐµÐ¶Ð´Ð°Ð½Ðµ Ð½Ð° Ð°ÑƒÐ´Ð¸Ð¾Ñ‚ÐµÑÑ‚",
                refresh_btn: "Ð—Ð°Ñ€ÐµÐ¶Ð´Ð°Ð½Ðµ Ð½Ð° Ð½Ð¾Ð² Ñ‚ÐµÑÑ‚",
                instructions_visual: "Ð’ÑŠÐ²ÐµÐ´ÐµÑ‚Ðµ Ñ‚ÐµÐºÑÑ‚Ð°:",
                instructions_audio: "Ð’ÑŠÐ²ÐµÐ´ÐµÑ‚Ðµ Ñ‡ÑƒÑ‚Ð¾Ñ‚Ð¾:",
                help_btn: "ÐŸÐ¾Ð¼Ð¾Ñ‰",
                play_again: "ÐŸÐ¾Ð²Ñ‚Ð¾Ñ€Ð½Ð¾ Ð¿ÑƒÑÐºÐ°Ð½Ðµ Ð½Ð° Ð·Ð²ÑƒÐºÐ°",
                cant_hear_this: "Ð˜Ð·Ñ‚ÐµÐ³Ð»ÑÐ½Ðµ Ð½Ð° Ð·Ð²ÑƒÐºÐ° Ð²ÑŠÐ² Ñ„Ð¾Ñ€Ð¼Ð°Ñ‚ MP3",
                incorrect_try_again: "ÐÐµÐ¿Ñ€Ð°Ð²Ð¸Ð»Ð½Ð¾. ÐžÐ¿Ð¸Ñ‚Ð°Ð¹Ñ‚Ðµ Ð¾Ñ‚Ð½Ð¾Ð²Ð¾.",
                image_alt_text: "Ð˜Ð·Ð¾Ð±Ñ€Ð°Ð¶ÐµÐ½Ð¸Ðµ Ð½Ð° Ð¿Ñ€Ð¾Ð²ÐµÑ€ÐºÐ°Ñ‚Ð° Ñ reCAPTCHA",
                privacy_and_terms: "ÐŸÐ¾Ð²ÐµÑ€Ð¸Ñ‚ÐµÐ»Ð½Ð¾ÑÑ‚ Ð¸ ÐžÐ±Ñ‰Ð¸ ÑƒÑÐ»Ð¾Ð²Ð¸Ñ"
            },
            bn: {
                visual_challenge: "à¦à¦•à¦Ÿà¦¿ à¦¦à§ƒà¦¶à§à¦¯à¦®à¦¾à¦¨ à¦ªà§à¦°à¦¤à¦¿à¦¦à§à¦¬à¦¨à§à¦¦à§à¦¬à¦¿à¦¤à¦¾ à¦ªà¦¾à¦¨",
                audio_challenge: "à¦à¦•à¦Ÿà¦¿ à¦…à¦¡à¦¿à¦“ à¦ªà§à¦°à¦¤à¦¿à¦¦à§à¦¬à¦¨à§à¦¦à§à¦¬à¦¿à¦¤à¦¾  à¦ªà¦¾à¦¨",
                refresh_btn: "à¦à¦•à¦Ÿà¦¿ à¦¨à¦¤à§à¦¨ à¦ªà§à¦°à¦¤à¦¿à¦¦à§à¦¬à¦¨à§à¦¦à§à¦¬à¦¿à¦¤à¦¾  à¦ªà¦¾à¦¨",
                instructions_visual: "",
                instructions_audio: "à¦†à¦ªà¦¨à¦¿ à¦¯à¦¾ à¦¶à§à¦¨à¦›à§‡à¦¨ à¦¤à¦¾ à¦²à¦¿à¦–à§à¦¨:",
                help_btn: "à¦¸à¦¹à¦¾à§Ÿà¦¤à¦¾",
                play_again: "à¦†à¦¬à¦¾à¦° à¦¸à¦¾à¦‰à¦¨à§à¦¡ à¦ªà§à¦²à§‡ à¦•à¦°à§à¦¨",
                cant_hear_this: "MP3 à¦°à§‚à¦ªà§‡ à¦¶à¦¬à§à¦¦ à¦¡à¦¾à¦‰à¦¨à¦²à§‹à¦¡ à¦•à¦°à§à¦¨",
                incorrect_try_again: "à¦¬à§‡à¦ à¦¿à¦•à§· à¦†à¦¬à¦¾à¦° à¦šà§‡à¦·à§à¦Ÿà¦¾ à¦•à¦°à§à¦¨à§·",
                image_alt_text: "reCAPTCHA à¦šà§à¦¯à¦¾à¦²à§‡à¦žà§à¦œ à¦šà¦¿à¦¤à§à¦°",
                privacy_and_terms: "à¦—à§‹à¦ªà¦¨à§€à¦¯à¦¼à¦¤à¦¾ à¦“ à¦¶à¦°à§à¦¤à¦¾à¦¬à¦²à§€"
            },
            ca: {
                visual_challenge: "ObtÃ©n un repte visual",
                audio_challenge: "Obteniu una pista sonora",
                refresh_btn: "Obteniu una pista nova",
                instructions_visual: "Escriviu el text:",
                instructions_audio: "Escriviu el que escolteu:",
                help_btn: "Ajuda",
                play_again: "Torna a reproduir el so",
                cant_hear_this: "Baixa el so com a MP3",
                incorrect_try_again: "No Ã©s correcte. Torna-ho a provar.",
                image_alt_text: "Imatge del repte de reCAPTCHA",
                privacy_and_terms: "Privadesa i condicions"
            },
            cs: {
                visual_challenge: "Zobrazit vizuÃ¡lnÃ­ podobu vÃ½razu",
                audio_challenge: "PÅ™ehrÃ¡t zvukovou podobu vÃ½razu",
                refresh_btn: "Zobrazit novÃ½ vÃ½raz",
                instructions_visual: "Zadejte text:",
                instructions_audio: "NapiÅ¡te, co jste slyÅ¡eli:",
                help_btn: "NÃ¡povÄ›da",
                play_again: "Znovu pÅ™ehrÃ¡t zvuk",
                cant_hear_this: "StÃ¡hnout zvuk ve formÃ¡tu MP3",
                incorrect_try_again: "Å patnÄ›. Zkuste to znovu.",
                image_alt_text: "ObrÃ¡zek reCAPTCHA",
                privacy_and_terms: "Ochrana soukromÃ­ a smluvnÃ­ podmÃ­nky"
            },
            da: {
                visual_challenge: "Hent en visuel udfordring",
                audio_challenge: "Hent en lydudfordring",
                refresh_btn: "Hent en ny udfordring",
                instructions_visual: "Indtast teksten:",
                instructions_audio: "Indtast det, du hÃ¸rer:",
                help_btn: "HjÃ¦lp",
                play_again: "Afspil lyden igen",
                cant_hear_this: "Download lyd som MP3",
                incorrect_try_again: "Forkert. PrÃ¸v igen.",
                image_alt_text: "reCAPTCHA-udfordringsbillede",
                privacy_and_terms: "Privatliv og vilkÃ¥r"
            },
            de: {
                visual_challenge: "Captcha abrufen",
                audio_challenge: "Audio-Captcha abrufen",
                refresh_btn: "Neues Captcha abrufen",
                instructions_visual: "Geben Sie den angezeigten Text ein:",
                instructions_audio: "Geben Sie das GehÃ¶rte ein:",
                help_btn: "Hilfe",
                play_again: "Wort erneut abspielen",
                cant_hear_this: "Wort als MP3 herunterladen",
                incorrect_try_again: "Falsch. Bitte versuchen Sie es erneut.",
                image_alt_text: "reCAPTCHA-Bild",
                privacy_and_terms: "DatenschutzerklÃ¤rung & Nutzungsbedingungen"
            },
            el: {
                visual_challenge: "ÎŸÏ€Ï„Î¹ÎºÎ® Ï€ÏÏŒÎºÎ»Î·ÏƒÎ·",
                audio_challenge: "Î—Ï‡Î·Ï„Î¹ÎºÎ® Ï€ÏÏŒÎºÎ»Î·ÏƒÎ·",
                refresh_btn: "ÎÎ­Î± Ï€ÏÏŒÎºÎ»Î·ÏƒÎ·",
                instructions_visual: "Î Î»Î·ÎºÏ„ÏÎ¿Î»Î¿Î³Î®ÏƒÏ„Îµ Ï„Î¿ ÎºÎµÎ¯Î¼ÎµÎ½Î¿:",
                instructions_audio: "Î Î»Î·ÎºÏ„ÏÎ¿Î»Î¿Î³Î®ÏƒÏ„Îµ ÏŒÏ„Î¹ Î±ÎºÎ¿ÏÏ„Îµ:",
                help_btn: "Î’Î¿Î®Î¸ÎµÎ¹Î±",
                play_again: "Î‘Î½Î±Ï€Î±ÏÎ±Î³Ï‰Î³Î® Î®Ï‡Î¿Ï… Î¾Î±Î½Î¬",
                cant_hear_this: "Î›Î®ÏˆÎ· Î®Ï‡Î¿Ï… Ï‰Ï‚ ÎœÎ¡3",
                incorrect_try_again: "Î›Î¬Î¸Î¿Ï‚. Î”Î¿ÎºÎ¹Î¼Î¬ÏƒÏ„Îµ Î¾Î±Î½Î¬.",
                image_alt_text: "Î•Î¹ÎºÏŒÎ½Î± Ï€ÏÏŒÎºÎ»Î·ÏƒÎ·Ï‚ reCAPTCHA",
                privacy_and_terms: "Î‘Ï€ÏŒÏÏÎ·Ï„Î¿ ÎºÎ±Î¹ ÏŒÏÎ¿Î¹"
            },
            "en-GB": y,
            "en-US": y,
            es: w,
            "es-419": {
                visual_challenge: "Enfrentar un desafÃ­o visual",
                audio_challenge: "Enfrentar un desafÃ­o de audio",
                refresh_btn: "Enfrentar un nuevo desafÃ­o",
                instructions_visual: "Escriba el texto:",
                instructions_audio: "Escribe lo que escuchas:",
                help_btn: "Ayuda",
                play_again: "Reproducir sonido de nuevo",
                cant_hear_this: "Descargar sonido en formato MP3",
                incorrect_try_again: "Incorrecto. Vuelve a intentarlo.",
                image_alt_text: "Imagen del desafÃ­o de la reCAPTCHA",
                privacy_and_terms: "Privacidad y condiciones"
            },
            "es-ES": w,
            et: {
                visual_challenge: "Kuva kuvapÃµhine robotilÃµks",
                audio_challenge: "Kuva helipÃµhine robotilÃµks",
                refresh_btn: "Kuva uus robotilÃµks",
                instructions_visual: "Tippige tekst:",
                instructions_audio: "Tippige, mida kuulete.",
                help_btn: "Abi",
                play_again: "Esita heli uuesti",
                cant_hear_this: "Laadi heli alla MP3-vormingus",
                incorrect_try_again: "Vale. Proovige uuesti.",
                image_alt_text: "reCAPTCHA robotilÃµksu kujutis",
                privacy_and_terms: "Privaatsus ja tingimused"
            },
            eu: {
                visual_challenge: "Eskuratu ikusizko erronka",
                audio_challenge: "Eskuratu audio-erronka",
                refresh_btn: "Eskuratu erronka berria",
                instructions_visual: "",
                instructions_audio: "Idatzi entzuten duzuna:",
                help_btn: "Laguntza",
                play_again: "Erreproduzitu soinua berriro",
                cant_hear_this: "Deskargatu soinua MP3 gisa",
                incorrect_try_again: "Ez da zuzena. Saiatu berriro.",
                image_alt_text: "reCAPTCHA erronkaren irudia",
                privacy_and_terms: "Pribatutasuna eta baldintzak"
            },
            fa: {
                visual_challenge: "Ø¯Ø±ÛŒØ§ÙØª ÛŒÚ© Ù…Ø¹Ù…Ø§ÛŒ Ø¯ÛŒØ¯Ø§Ø±ÛŒ",
                audio_challenge: "Ø¯Ø±ÛŒØ§ÙØª ÛŒÚ© Ù…Ø¹Ù…Ø§ÛŒ ØµÙˆØªÛŒ",
                refresh_btn: "Ø¯Ø±ÛŒØ§ÙØª ÛŒÚ© Ù…Ø¹Ù…Ø§ÛŒ Ø¬Ø¯ÛŒØ¯",
                instructions_visual: "",
                instructions_audio: "Ø¢Ù†Ú†Ù‡ Ø±Ø§ Ú©Ù‡ Ù…ÛŒâ€ŒØ´Ù†ÙˆÛŒØ¯ ØªØ§ÛŒÙ¾ Ú©Ù†ÛŒØ¯:",
                help_btn: "Ø±Ø§Ù‡Ù†Ù…Ø§ÛŒÛŒ",
                play_again: "Ù¾Ø®Ø´ Ù…Ø¬Ø¯Ø¯ ØµØ¯Ø§",
                cant_hear_this: "Ø¯Ø§Ù†Ù„ÙˆØ¯ ØµØ¯Ø§ Ø¨Ù‡ ØµÙˆØ±Øª MP3",
                incorrect_try_again: "Ù†Ø§Ø¯Ø±Ø³Øª. Ø¯ÙˆØ¨Ø§Ø±Ù‡ Ø§Ù…ØªØ­Ø§Ù† Ú©Ù†ÛŒØ¯.",
                image_alt_text: "ØªØµÙˆÛŒØ± Ú†Ø§Ù„Ø´ÛŒ reCAPTCHA",
                privacy_and_terms: "Ø­Ø±ÛŒÙ… Ø®ØµÙˆØµÛŒ Ùˆ Ø´Ø±Ø§ÛŒØ·"
            },
            fi: {
                visual_challenge: "Kuvavahvistus",
                audio_challenge: "Ã„Ã¤nivahvistus",
                refresh_btn: "Uusi kuva",
                instructions_visual: "Kirjoita teksti:",
                instructions_audio: "Kirjoita kuulemasi:",
                help_btn: "Ohje",
                play_again: "Toista Ã¤Ã¤ni uudelleen",
                cant_hear_this: "Lataa Ã¤Ã¤ni MP3-tiedostona",
                incorrect_try_again: "VÃ¤Ã¤rin. YritÃ¤ uudelleen.",
                image_alt_text: "reCAPTCHA-kuva",
                privacy_and_terms: "Tietosuoja ja kÃ¤yttÃ¶ehdot"
            },
            fil: E,
            fr: S,
            "fr-CA": {
                visual_challenge: "Obtenir un test visuel",
                audio_challenge: "Obtenir un test audio",
                refresh_btn: "Obtenir un nouveau test",
                instructions_visual: "Saisissez le texteÂ :",
                instructions_audio: "Tapez ce que vous entendezÂ :",
                help_btn: "Aide",
                play_again: "Jouer le son de nouveau",
                cant_hear_this: "TÃ©lÃ©charger le son en format MP3",
                incorrect_try_again: "Erreur, essayez Ã  nouveau",
                image_alt_text: "Image reCAPTCHA",
                privacy_and_terms: "ConfidentialitÃ© et conditions d'utilisation"
            },
            "fr-FR": S,
            gl: {
                visual_challenge: "Obter unha proba visual",
                audio_challenge: "Obter unha proba de audio",
                refresh_btn: "Obter unha proba nova",
                instructions_visual: "",
                instructions_audio: "Escribe o que escoitas:",
                help_btn: "Axuda",
                play_again: "Reproducir o son de novo",
                cant_hear_this: "Descargar son como MP3",
                incorrect_try_again: "Incorrecto. TÃ©ntao de novo.",
                image_alt_text: "Imaxe de proba de reCAPTCHA",
                privacy_and_terms: "Privacidade e condiciÃ³ns"
            },
            gu: {
                visual_challenge: "àªàª• àª¦à«ƒàª¶à«àª¯àª¾àª¤à«àª®àª• àªªàª¡àª•àª¾àª° àª®à«‡àª³àªµà«‹",
                audio_challenge: "àªàª• àª‘àª¡àª¿àª“ àªªàª¡àª•àª¾àª° àª®à«‡àª³àªµà«‹",
                refresh_btn: "àªàª• àª¨àªµà«‹ àªªàª¡àª•àª¾àª° àª®à«‡àª³àªµà«‹",
                instructions_visual: "",
                instructions_audio: "àª¤àª®à«‡ àªœà«‡ àª¸àª¾àª‚àª­àª³à«‹ àª›à«‹ àª¤à«‡ àª²àª–à«‹:",
                help_btn: "àª¸àª¹àª¾àª¯",
                play_again: "àª§à«àªµàª¨àª¿ àª«àª°à«€àª¥à«€ àªšàª²àª¾àªµà«‹",
                cant_hear_this: "MP3 àª¤àª°à«€àª•à«‡ àª§à«àªµàª¨àª¿àª¨à«‡ àª¡àª¾àª‰àª¨àª²à«‹àª¡ àª•àª°à«‹",
                incorrect_try_again: "àª–à«‹àªŸà«àª‚. àª«àª°à«€ àªªà«àª°àª¯àª¾àª¸ àª•àª°à«‹.",
                image_alt_text: "reCAPTCHA àªªàª¡àª•àª¾àª° àª›àª¬à«€",
                privacy_and_terms: "àª—à«‹àªªàª¨à«€àª¯àª¤àª¾ àª…àª¨à«‡ àª¶àª°àª¤à«‹"
            },
            hi: {
                visual_challenge: "à¤•à¥‹à¤ˆ à¤µà¤¿à¤œà¥à¤…à¤² à¤šà¥à¤¨à¥Œà¤¤à¥€ à¤²à¥‡à¤‚",
                audio_challenge: "à¤•à¥‹à¤ˆ à¤‘à¤¡à¤¿à¤¯à¥‹ à¤šà¥à¤¨à¥Œà¤¤à¥€ à¤²à¥‡à¤‚",
                refresh_btn: "à¤•à¥‹à¤ˆ à¤¨à¤ˆ à¤šà¥à¤¨à¥Œà¤¤à¥€ à¤²à¥‡à¤‚",
                instructions_visual: "à¤Ÿà¥‡à¤•à¥à¤¸à¥à¤Ÿ à¤Ÿà¤¾à¤‡à¤ª à¤•à¤°à¥‡à¤‚:",
                instructions_audio: "à¤œà¥‹ à¤†à¤ª à¤¸à¥à¤¨ à¤°à¤¹à¥‡ à¤¹à¥ˆà¤‚ à¤‰à¤¸à¥‡ à¤²à¤¿à¤–à¥‡à¤‚:",
                help_btn: "à¤¸à¤¹à¤¾à¤¯à¤¤à¤¾",
                play_again: "à¤§à¥â€à¤µà¤¨à¤¿ à¤ªà¥à¤¨: à¤šà¤²à¤¾à¤à¤‚",
                cant_hear_this: "à¤§à¥â€à¤µà¤¨à¤¿ à¤•à¥‹ MP3 à¤•à¥‡ à¤°à¥‚à¤ª à¤®à¥‡à¤‚ à¤¡à¤¾à¤‰à¤¨à¤²à¥‹à¤¡ à¤•à¤°à¥‡à¤‚",
                incorrect_try_again: "à¤—à¤²à¤¤. à¤ªà¥à¤¨: à¤ªà¥à¤°à¤¯à¤¾à¤¸ à¤•à¤°à¥‡à¤‚.",
                image_alt_text: "reCAPTCHA à¤šà¥à¤¨à¥Œà¤¤à¥€ à¤šà¤¿à¤¤à¥à¤°",
                privacy_and_terms: "à¤—à¥‹à¤ªà¤¨à¥€à¤¯à¤¤à¤¾ à¤”à¤° à¤¶à¤°à¥à¤¤à¥‡à¤‚"
            },
            hr: {
                visual_challenge: "Dohvati vizualni upit",
                audio_challenge: "Dohvati zvuÄni upit",
                refresh_btn: "Dohvati novi upit",
                instructions_visual: "Unesite tekst:",
                instructions_audio: "UpiÅ¡ite Å¡to Äujete:",
                help_btn: "PomoÄ‡",
                play_again: "Ponovi zvuk",
                cant_hear_this: "Preuzmi zvuk u MP3 formatu",
                incorrect_try_again: "Nije toÄno. PokuÅ¡ajte ponovno.",
                image_alt_text: "Slikovni izazov reCAPTCHA",
                privacy_and_terms: "Privatnost i odredbe"
            },
            hu: {
                visual_challenge: "VizuÃ¡lis kihÃ­vÃ¡s kÃ©rÃ©se",
                audio_challenge: "HangkihÃ­vÃ¡s kÃ©rÃ©se",
                refresh_btn: "Ãšj kihÃ­vÃ¡s kÃ©rÃ©se",
                instructions_visual: "Ãrja be a szÃ¶veget:",
                instructions_audio: "Ãrja le, amit hall:",
                help_btn: "SÃºgÃ³",
                play_again: "Hang ismÃ©telt lejÃ¡tszÃ¡sa",
                cant_hear_this: "Hang letÃ¶ltÃ©se MP3 formÃ¡tumban",
                incorrect_try_again: "HibÃ¡s. PrÃ³bÃ¡lkozzon Ãºjra.",
                image_alt_text: "reCAPTCHA ellenÅ‘rzÅ‘ kÃ©p",
                privacy_and_terms: "AdatvÃ©delem Ã©s SzerzÅ‘dÃ©si FeltÃ©telek"
            },
            hy: {
                visual_challenge: "ÕÕ¿Õ¡Õ¶Õ¡Õ¬ Õ¿Õ¥Õ½Õ¸Õ²Õ¡Õ¯Õ¡Õ¶ Õ­Õ¶Õ¤Õ«Ö€",
                audio_challenge: "ÕÕ¿Õ¡Õ¶Õ¡Õ¬ Õ±Õ¡ÕµÕ¶Õ¡ÕµÕ«Õ¶ Õ­Õ¶Õ¤Õ«Ö€",
                refresh_btn: "ÕÕ¿Õ¡Õ¶Õ¡Õ¬ Õ¶Õ¸Ö€ Õ­Õ¶Õ¤Õ«Ö€",
                instructions_visual: "Õ„Õ¸Ö‚Õ¿Ö„Õ¡Õ£Ö€Õ¥Ö„ Õ¿Õ¥Ö„Õ½Õ¿Õ¨Õ",
                instructions_audio: "Õ„Õ¸Ö‚Õ¿Ö„Õ¡Õ£Ö€Õ¥Ö„ Õ¡ÕµÕ¶, Õ«Õ¶Õ¹ Õ¬Õ½Õ¸Ö‚Õ´ Õ¥Ö„Õ",
                help_btn: "Õ•Õ£Õ¶Õ¸Ö‚Õ©ÕµÕ¸Ö‚Õ¶",
                play_again: "Õ†Õ¾Õ¡Õ£Õ¡Ö€Õ¯Õ¥Õ¬ Õ±Õ¡ÕµÕ¶Õ¨ Õ¯Ö€Õ¯Õ«Õ¶",
                cant_hear_this: "Ô²Õ¥Õ¼Õ¶Õ¥Õ¬ Õ±Õ¡ÕµÕ¶Õ¨ Õ¸Ö€ÕºÕ¥Õ½ MP3",
                incorrect_try_again: "ÕÕ­Õ¡Õ¬ Õ§: Õ“Õ¸Ö€Õ±Õ¥Ö„ Õ¯Ö€Õ¯Õ«Õ¶:",
                image_alt_text: "reCAPTCHA ÕºÕ¡Õ¿Õ¯Õ¥Ö€Õ¸Õ¾ Õ­Õ¶Õ¤Õ«Ö€",
                privacy_and_terms: "Ô³Õ¡Õ²Õ¿Õ¶Õ«Õ¸Ö‚Õ©ÕµÕ¡Õ¶ & ÕºÕ¡ÕµÕ´Õ¡Õ¶Õ¶Õ¥Ö€"
            },
            id: x,
            is: {
                visual_challenge: "FÃ¡ aÃ°gangsprÃ³f sem mynd",
                audio_challenge: "FÃ¡ aÃ°gangsprÃ³f sem hljÃ³Ã°skrÃ¡",
                refresh_btn: "FÃ¡ nÃ½tt aÃ°gangsprÃ³f",
                instructions_visual: "",
                instructions_audio: "SlÃ¡Ã°u inn Ã¾aÃ° sem Ã¾Ãº heyrir:",
                help_btn: "HjÃ¡lp",
                play_again: "Spila hljÃ³Ã° aftur",
                cant_hear_this: "SÃ¦kja hljÃ³Ã° sem MP3",
                incorrect_try_again: "Rangt. Reyndu aftur.",
                image_alt_text: "mynd reCAPTCHA aÃ°gangsprÃ³fs",
                privacy_and_terms: "PersÃ³nuvernd og skilmÃ¡lar"
            },
            it: {
                visual_challenge: "Verifica visiva",
                audio_challenge: "Verifica audio",
                refresh_btn: "Nuova verifica",
                instructions_visual: "Digita il testo:",
                instructions_audio: "Digita ciÃ² che senti:",
                help_btn: "Guida",
                play_again: "Riproduci di nuovo audio",
                cant_hear_this: "Scarica audio in MP3",
                incorrect_try_again: "Sbagliato. Riprova.",
                image_alt_text: "Immagine di verifica reCAPTCHA",
                privacy_and_terms: "Privacy e Termini"
            },
            iw: T,
            ja: {
                visual_challenge: "ç”»åƒã§ç¢ºèªã—ã¾ã™",
                audio_challenge: "éŸ³å£°ã§ç¢ºèªã—ã¾ã™",
                refresh_btn: "åˆ¥ã®å˜èªžã§ã‚„ã‚Šç›´ã—ã¾ã™",
                instructions_visual: "ãƒ†ã‚­ã‚¹ãƒˆã‚’å…¥åŠ›:",
                instructions_audio: "èžã“ãˆãŸå˜èªžã‚’å…¥åŠ›ã—ã¾ã™:",
                help_btn: "ãƒ˜ãƒ«ãƒ—",
                play_again: "ã‚‚ã†ä¸€åº¦èžã",
                cant_hear_this: "MP3 ã§éŸ³å£°ã‚’ãƒ€ã‚¦ãƒ³ãƒ­ãƒ¼ãƒ‰",
                incorrect_try_again: "æ­£ã—ãã‚ã‚Šã¾ã›ã‚“ã€‚ã‚‚ã†ä¸€åº¦ã‚„ã‚Šç›´ã—ã¦ãã ã•ã„ã€‚",
                image_alt_text: "reCAPTCHA ç¢ºèªç”¨ç”»åƒ",
                privacy_and_terms: "ãƒ—ãƒ©ã‚¤ãƒã‚·ãƒ¼ã¨åˆ©ç”¨è¦ç´„"
            },
            kn: {
                visual_challenge: "à²¦à³ƒà²¶à³à²¯ à²¸à²µà²¾à²²à³Šà²‚à²¦à²¨à³à²¨à³ à²¸à³à²µà³€à²•à²°à²¿à²¸à²¿",
                audio_challenge: "à²†à²¡à²¿à²¯à³‹ à²¸à²µà²¾à²²à³Šà²‚à²¦à²¨à³à²¨à³ à²¸à³à²µà³€à²•à²°à²¿à²¸à²¿",
                refresh_btn: "à²¹à³Šà²¸ à²¸à²µà²¾à²²à³Šà²‚à²¦à²¨à³à²¨à³ à²ªà²¡à³†à²¯à²¿à²°à²¿",
                instructions_visual: "",
                instructions_audio: "à²¨à²¿à²®à²—à³† à²•à³‡à²³à²¿à²¸à³à²µà³à²¦à²¨à³à²¨à³ à²Ÿà³ˆà²ªà³â€Œ à²®à²¾à²¡à²¿:",
                help_btn: "à²¸à²¹à²¾à²¯",
                play_again: "à²§à³à²µà²¨à²¿à²¯à²¨à³à²¨à³ à²®à²¤à³à²¤à³† à²ªà³à²²à³‡ à²®à²¾à²¡à²¿",
                cant_hear_this: "à²§à³à²µà²¨à²¿à²¯à²¨à³à²¨à³ MP3 à²°à³‚à²ªà²¦à²²à³à²²à²¿ à²¡à³Œà²¨à³â€Œà²²à³‹à²¡à³ à²®à²¾à²¡à²¿",
                incorrect_try_again: "à²¤à²ªà³à²ªà²¾à²—à²¿à²¦à³†. à²®à²¤à³à²¤à³Šà²®à³à²®à³† à²ªà³à²°à²¯à²¤à³à²¨à²¿à²¸à²¿.",
                image_alt_text: "reCAPTCHA à²¸à²µà²¾à²²à³ à²šà²¿à²¤à³à²°",
                privacy_and_terms: "à²—à³Œà²ªà³à²¯à²¤à³† à²®à²¤à³à²¤à³ à²¨à²¿à²¯à²®à²—à²³à³"
            },
            ko: {
                visual_challenge: "ê·¸ë¦¼ìœ¼ë¡œ ë³´ì•ˆë¬¸ìž ë°›ê¸°",
                audio_challenge: "ìŒì„±ìœ¼ë¡œ ë³´ì•ˆë¬¸ìž ë°›ê¸°",
                refresh_btn: "ë³´ì•ˆë¬¸ìž ìƒˆë¡œ ë°›ê¸°",
                instructions_visual: "í…ìŠ¤íŠ¸ ìž…ë ¥:",
                instructions_audio: "ìŒì„± ë³´ì•ˆë¬¸ìž ìž…ë ¥:",
                help_btn: "ë„ì›€ë§",
                play_again: "ìŒì„± ë‹¤ì‹œ ë“£ê¸°",
                cant_hear_this: "ìŒì„±ì„ MP3ë¡œ ë‹¤ìš´ë¡œë“œ",
                incorrect_try_again: "í‹€ë ¸ìŠµë‹ˆë‹¤. ë‹¤ì‹œ ì‹œë„í•´ ì£¼ì„¸ìš”.",
                image_alt_text: "reCAPTCHA ë³´ì•ˆë¬¸ìž ì´ë¯¸ì§€",
                privacy_and_terms: "ê°œì¸ì •ë³´ ë³´í˜¸ ë° ì•½ê´€"
            },
            ln: S,
            lt: {
                visual_challenge: "Gauti vaizdinÄ¯ atpaÅ¾inimo testÄ…",
                audio_challenge: "Gauti garso atpaÅ¾inimo testÄ…",
                refresh_btn: "Gauti naujÄ… atpaÅ¾inimo testÄ…",
                instructions_visual: "Ä®veskite tekstÄ…:",
                instructions_audio: "Ä®veskite tai, kÄ… girdite:",
                help_btn: "Pagalba",
                play_again: "Dar kartÄ… paleisti garsÄ…",
                cant_hear_this: "AtsisiÅ³sti garsÄ… kaip MP3",
                incorrect_try_again: "Neteisingai. Bandykite dar kartÄ….",
                image_alt_text: "Testo â€žreCAPTCHAâ€œ vaizdas",
                privacy_and_terms: "Privatumas ir sÄ…lygos"
            },
            lv: {
                visual_challenge: "SaÅ†emt vizuÄlu izaicinÄjumu",
                audio_challenge: "SaÅ†emt audio izaicinÄjumu",
                refresh_btn: "SaÅ†emt jaunu izaicinÄjumu",
                instructions_visual: "Ievadiet tekstu:",
                instructions_audio: "Ierakstiet dzirdamo:",
                help_btn: "PalÄ«dzÄ«ba",
                play_again: "VÄ“lreiz atskaÅ†ot skaÅ†u",
                cant_hear_this: "LejupielÄdÄ“t skaÅ†u MP3Â formÄtÄ",
                incorrect_try_again: "Nepareizi. MÄ“Ä£iniet vÄ“lreiz.",
                image_alt_text: "reCAPTCHA izaicinÄjuma attÄ“ls",
                privacy_and_terms: "KonfidencialitÄte un noteikumi"
            },
            ml: {
                visual_challenge: "à´’à´°àµ à´¦àµƒà´¶àµà´¯ à´šà´²à´žàµà´šàµ à´¨àµ‡à´Ÿàµà´•",
                audio_challenge: "à´’à´°àµ à´“à´¡à´¿à´¯àµ‹ à´šà´²à´žàµà´šàµ à´¨àµ‡à´Ÿàµà´•",
                refresh_btn: "à´’à´°àµ à´ªàµà´¤à´¿à´¯ à´šà´²à´žàµà´šàµ à´¨àµ‡à´Ÿàµà´•",
                instructions_visual: "",
                instructions_audio: "à´•àµ‡àµ¾à´•àµà´•àµà´¨àµà´¨à´¤àµ à´Ÿàµˆà´ªàµà´ªàµ à´šàµ†à´¯àµà´¯àµ‚:",
                help_btn: "à´¸à´¹à´¾à´¯à´‚",
                play_again: "à´¶à´¬àµâ€Œà´¦à´‚ à´µàµ€à´£àµà´Ÿàµà´‚ à´ªàµà´²àµ‡ à´šàµ†à´¯àµà´¯àµà´•",
                cant_hear_this: "à´¶à´¬àµâ€Œà´¦à´‚ MP3 à´†à´¯à´¿ à´¡àµ—àµºà´²àµ‹à´¡àµ à´šàµ†à´¯àµà´¯àµà´•",
                incorrect_try_again: "à´¤àµ†à´±àµà´±à´¾à´£àµ. à´µàµ€à´£àµà´Ÿàµà´‚ à´¶àµà´°à´®à´¿à´•àµà´•àµà´•.",
                image_alt_text: "reCAPTCHA à´šà´²à´žàµà´šàµ à´‡à´®àµ‡à´œàµ",
                privacy_and_terms: "à´¸àµà´µà´•à´¾à´°àµà´¯à´¤à´¯àµà´‚ à´¨à´¿à´¬à´¨àµà´§à´¨à´•à´³àµà´‚"
            },
            mr: {
                visual_challenge: "à¤¦à¥ƒà¤¶à¥â€à¤¯à¤®à¤¾à¤¨ à¤†à¤µà¥à¤¹à¤¾à¤¨ à¤ªà¥à¤°à¤¾à¤ªà¥à¤¤ à¤•à¤°à¤¾",
                audio_challenge: "à¤‘à¤¡à¥€à¤“ à¤†à¤µà¥à¤¹à¤¾à¤¨ à¤ªà¥à¤°à¤¾à¤ªà¥à¤¤ à¤•à¤°à¤¾",
                refresh_btn: "à¤à¤• à¤¨à¤µà¥€à¤¨ à¤†à¤µà¥à¤¹à¤¾à¤¨ à¤ªà¥à¤°à¤¾à¤ªà¥à¤¤ à¤•à¤°à¤¾",
                instructions_visual: "",
                instructions_audio: "à¤†à¤ªà¤²à¥à¤¯à¤¾à¤²à¤¾ à¤œà¥‡ à¤à¤•à¥‚ à¤¯à¥‡à¤ˆà¤² à¤¤à¥‡ à¤Ÿà¤¾à¤‡à¤ª à¤•à¤°à¤¾:",
                help_btn: "à¤®à¤¦à¤¤",
                play_again: "à¤§à¥â€à¤µà¤¨à¥€ à¤ªà¥à¤¨à¥à¤¹à¤¾ à¤ªà¥â€à¤²à¥‡ à¤•à¤°à¤¾",
                cant_hear_this: "MP3 à¤°à¥à¤ªà¤¾à¤¤ à¤§à¥â€à¤µà¤¨à¥€ à¤¡à¤¾à¤‰à¤¨à¤²à¥‹à¤¡ à¤•à¤°à¤¾",
                incorrect_try_again: "à¤…à¤¯à¥‹à¤—à¥â€à¤¯. à¤ªà¥à¤¨à¥â€à¤¹à¤¾ à¤ªà¥à¤°à¤¯à¤¤à¥â€à¤¨ à¤•à¤°à¤¾.",
                image_alt_text: "reCAPTCHA à¤†à¤µà¥â€à¤¹à¤¾à¤¨ à¤ªà¥à¤°à¤¤à¤¿à¤®à¤¾",
                privacy_and_terms: "à¤—à¥‹à¤ªà¤¨à¥€à¤¯à¤¤à¤¾ à¤†à¤£à¤¿ à¤…à¤Ÿà¥€"
            },
            ms: {
                visual_challenge: "Dapatkan cabaran visual",
                audio_challenge: "Dapatkan cabaran audio",
                refresh_btn: "Dapatkan cabaran baru",
                instructions_visual: "Taipkan teksnya:",
                instructions_audio: "Taip apa yang didengari:",
                help_btn: "Bantuan",
                play_again: "Mainkan bunyi sekali lagi",
                cant_hear_this: "Muat turun bunyi sebagai MP3",
                incorrect_try_again: "Tidak betul. Cuba lagi.",
                image_alt_text: "Imej cabaran reCAPTCHA",
                privacy_and_terms: "Privasi & Syarat"
            },
            nl: {
                visual_challenge: "Een visuele uitdaging proberen",
                audio_challenge: "Een audio-uitdaging proberen",
                refresh_btn: "Een nieuwe uitdaging proberen",
                instructions_visual: "Typ de tekst:",
                instructions_audio: "Typ wat u hoort:",
                help_btn: "Help",
                play_again: "Geluid opnieuw afspelen",
                cant_hear_this: "Geluid downloaden als MP3",
                incorrect_try_again: "Onjuist. Probeer het opnieuw.",
                image_alt_text: "reCAPTCHA-uitdagingsafbeelding",
                privacy_and_terms: "Privacy en voorwaarden"
            },
            no: {
                visual_challenge: "FÃ¥ en bildeutfordring",
                audio_challenge: "FÃ¥ en lydutfordring",
                refresh_btn: "FÃ¥ en ny utfordring",
                instructions_visual: "Skriv inn teksten:",
                instructions_audio: "Skriv inn det du hÃ¸rer:",
                help_btn: "Hjelp",
                play_again: "Spill av lyd pÃ¥ nytt",
                cant_hear_this: "Last ned lyd som MP3",
                incorrect_try_again: "Feil. PrÃ¸v pÃ¥ nytt.",
                image_alt_text: "reCAPTCHA-utfordringsbilde",
                privacy_and_terms: "Personvern og vilkÃ¥r"
            },
            pl: {
                visual_challenge: "PokaÅ¼ podpowiedÅº wizualnÄ…",
                audio_challenge: "OdtwÃ³rz podpowiedÅº dÅºwiÄ™kowÄ…",
                refresh_btn: "Nowa podpowiedÅº",
                instructions_visual: "Przepisz tekst:",
                instructions_audio: "Wpisz usÅ‚yszane sÅ‚owa:",
                help_btn: "Pomoc",
                play_again: "OdtwÃ³rz dÅºwiÄ™k ponownie",
                cant_hear_this: "Pobierz dÅºwiÄ™k jako plik MP3",
                incorrect_try_again: "NieprawidÅ‚owo. SprÃ³buj ponownie.",
                image_alt_text: "Zadanie obrazkowe reCAPTCHA",
                privacy_and_terms: "PrywatnoÅ›Ä‡ i warunki"
            },
            pt: N,
            "pt-BR": N,
            "pt-PT": {
                visual_challenge: "Obter um desafio visual",
                audio_challenge: "Obter um desafio de Ã¡udio",
                refresh_btn: "Obter um novo desafio",
                instructions_visual: "Introduza o texto:",
                instructions_audio: "Escreva o que ouvir:",
                help_btn: "Ajuda",
                play_again: "Reproduzir som novamente",
                cant_hear_this: "Transferir som como MP3",
                incorrect_try_again: "Incorreto. Tente novamente.",
                image_alt_text: "Imagem de teste reCAPTCHA",
                privacy_and_terms: "Privacidade e Termos de UtilizaÃ§Ã£o"
            },
            ro: C,
            ru: {
                visual_challenge: "Ð’Ð¸Ð·ÑƒÐ°Ð»ÑŒÐ½Ð°Ñ Ð¿Ñ€Ð¾Ð²ÐµÑ€ÐºÐ°",
                audio_challenge: "Ð—Ð²ÑƒÐºÐ¾Ð²Ð°Ñ Ð¿Ñ€Ð¾Ð²ÐµÑ€ÐºÐ°",
                refresh_btn: "ÐžÐ±Ð½Ð¾Ð²Ð¸Ñ‚ÑŒ",
                instructions_visual: "Ð’Ð²ÐµÐ´Ð¸Ñ‚Ðµ Ñ‚ÐµÐºÑÑ‚:",
                instructions_audio: "Ð’Ð²ÐµÐ´Ð¸Ñ‚Ðµ Ñ‚Ð¾, Ñ‡Ñ‚Ð¾ ÑÐ»Ñ‹ÑˆÐ¸Ñ‚Ðµ:",
                help_btn: "Ð¡Ð¿Ñ€Ð°Ð²ÐºÐ°",
                play_again: "ÐŸÑ€Ð¾ÑÐ»ÑƒÑˆÐ°Ñ‚ÑŒ ÐµÑ‰Ðµ Ñ€Ð°Ð·",
                cant_hear_this: "Ð—Ð°Ð³Ñ€ÑƒÐ·Ð¸Ñ‚ÑŒ MP3-Ñ„Ð°Ð¹Ð»",
                incorrect_try_again: "ÐÐµÐ¿Ñ€Ð°Ð²Ð¸Ð»ÑŒÐ½Ð¾. ÐŸÐ¾Ð²Ñ‚Ð¾Ñ€Ð¸Ñ‚Ðµ Ð¿Ð¾Ð¿Ñ‹Ñ‚ÐºÑƒ.",
                image_alt_text: "ÐŸÑ€Ð¾Ð²ÐµÑ€ÐºÐ° Ð¿Ð¾ ÑÐ»Ð¾Ð²Ñƒ reCAPTCHA",
                privacy_and_terms: "ÐŸÑ€Ð°Ð²Ð¸Ð»Ð° Ð¸ Ð¿Ñ€Ð¸Ð½Ñ†Ð¸Ð¿Ñ‹"
            },
            sk: {
                visual_challenge: "ZobraziÅ¥ vizuÃ¡lnu podobu",
                audio_challenge: "PrehraÅ¥ zvukovÃº podobu",
                refresh_btn: "ZobraziÅ¥ novÃ½ vÃ½raz",
                instructions_visual: "Zadajte text:",
                instructions_audio: "Zadajte, Äo poÄujete:",
                help_btn: "PomocnÃ­k",
                play_again: "Znova prehraÅ¥ zvuk",
                cant_hear_this: "PrevziaÅ¥ zvuk v podobe sÃºboru MP3",
                incorrect_try_again: "NesprÃ¡vne. SkÃºste to znova.",
                image_alt_text: "ObrÃ¡zok zadania reCAPTCHA",
                privacy_and_terms: "Ochrana osobnÃ½ch Ãºdajov a ZmluvnÃ© podmienky"
            },
            sl: {
                visual_challenge: "Vizualni preskus",
                audio_challenge: "ZvoÄni preskus",
                refresh_btn: "Nov preskus",
                instructions_visual: "Vnesite besedilo:",
                instructions_audio: "Natipkajte, kaj sliÅ¡ite:",
                help_btn: "PomoÄ",
                play_again: "Znova predvajaj zvok",
                cant_hear_this: "Prenesi zvok kot MP3",
                incorrect_try_again: "NapaÄno. Poskusite znova.",
                image_alt_text: "Slika izziva reCAPTCHA",
                privacy_and_terms: "Zasebnost in pogoji"
            },
            sr: {
                visual_challenge: "ÐŸÑ€Ð¸Ð¼Ð¸Ñ‚Ðµ Ð²Ð¸Ð·ÑƒÐµÐ»Ð½Ð¸ ÑƒÐ¿Ð¸Ñ‚",
                audio_challenge: "ÐŸÑ€Ð¸Ð¼Ð¸Ñ‚Ðµ Ð°ÑƒÐ´Ð¸Ð¾ ÑƒÐ¿Ð¸Ñ‚",
                refresh_btn: "ÐŸÑ€Ð¸Ð¼Ð¸Ñ‚Ðµ Ð½Ð¾Ð²Ð¸ ÑƒÐ¿Ð¸Ñ‚",
                instructions_visual: "Ð£Ð½ÐµÑÐ¸Ñ‚Ðµ Ñ‚ÐµÐºÑÑ‚:",
                instructions_audio: "ÐžÑ‚ÐºÑƒÑ†Ð°Ñ˜Ñ‚Ðµ Ð¾Ð½Ð¾ ÑˆÑ‚Ð¾ Ñ‡ÑƒÑ˜ÐµÑ‚Ðµ:",
                help_btn: "ÐŸÐ¾Ð¼Ð¾Ñ›",
                play_again: "ÐŸÐ¾Ð½Ð¾Ð²Ð¾ Ð¿ÑƒÑÑ‚Ð¸ Ð·Ð²ÑƒÐº",
                cant_hear_this: "ÐŸÑ€ÐµÑƒÐ·Ð¼Ð¸ Ð·Ð²ÑƒÐº ÐºÐ°Ð¾ MP3 ÑÐ½Ð¸Ð¼Ð°Ðº",
                incorrect_try_again: "ÐÐµÑ‚Ð°Ñ‡Ð½Ð¾. ÐŸÐ¾ÐºÑƒÑˆÐ°Ñ˜Ñ‚Ðµ Ð¿Ð¾Ð½Ð¾Ð²Ð¾.",
                image_alt_text: "Ð¡Ð»Ð¸ÐºÐ° reCAPTCHA Ð¿Ñ€Ð¾Ð²ÐµÑ€Ðµ",
                privacy_and_terms: "ÐŸÑ€Ð¸Ð²Ð°Ñ‚Ð½Ð¾ÑÑ‚ Ð¸ ÑƒÑÐ»Ð¾Ð²Ð¸"
            },
            sv: {
                visual_challenge: "HÃ¤mta captcha i bildformat",
                audio_challenge: "HÃ¤mta captcha i ljudformat",
                refresh_btn: "HÃ¤mta ny captcha",
                instructions_visual: "Skriv texten:",
                instructions_audio: "Skriv det du hÃ¶r:",
                help_btn: "HjÃ¤lp",
                play_again: "Spela upp ljudet igen",
                cant_hear_this: "HÃ¤mta ljud som MP3",
                incorrect_try_again: "Fel. FÃ¶rsÃ¶k igen.",
                image_alt_text: "reCAPTCHA-bild",
                privacy_and_terms: "Sekretess och villkor"
            },
            sw: {
                visual_challenge: "Pata herufi za kusoma",
                audio_challenge: "Pata herufi za kusikiliza",
                refresh_btn: "Pata herufi mpya",
                instructions_visual: "",
                instructions_audio: "Charaza unachosikia:",
                help_btn: "Usaidizi",
                play_again: "Cheza sauti tena",
                cant_hear_this: "Pakua sauti kama MP3",
                incorrect_try_again: "Sio sahihi. Jaribu tena.",
                image_alt_text: "picha ya changamoto ya reCAPTCHA",
                privacy_and_terms: "Faragha & Masharti"
            },
            ta: {
                visual_challenge: "à®ªà®¾à®°à¯à®µà¯ˆ à®šà¯‡à®²à®žà¯à®šà¯ˆà®ªà¯ à®ªà¯†à®±à¯à®•",
                audio_challenge: "à®†à®Ÿà®¿à®¯à¯‹ à®šà¯‡à®²à®žà¯à®šà¯ˆà®ªà¯ à®ªà¯†à®±à¯à®•",
                refresh_btn: "à®ªà¯à®¤à®¿à®¯ à®šà¯‡à®²à®žà¯à®šà¯ˆà®ªà¯ à®ªà¯†à®±à¯à®•",
                instructions_visual: "",
                instructions_audio: "à®•à¯‡à®Ÿà¯à®ªà®¤à¯ˆ à®Ÿà¯ˆà®ªà¯ à®šà¯†à®¯à¯à®•:",
                help_btn: "à®‰à®¤à®µà®¿",
                play_again: "à®’à®²à®¿à®¯à¯ˆ à®®à¯€à®£à¯à®Ÿà¯à®®à¯ à®‡à®¯à®•à¯à®•à¯",
                cant_hear_this: "à®’à®²à®¿à®¯à¯ˆ MP3 à®†à®• à®ªà®¤à®¿à®µà®¿à®±à®•à¯à®•à¯à®•",
                incorrect_try_again: "à®¤à®µà®±à®¾à®©à®¤à¯. à®®à¯€à®£à¯à®Ÿà¯à®®à¯ à®®à¯à®¯à®²à®µà¯à®®à¯.",
                image_alt_text: "reCAPTCHA à®šà¯‡à®²à®žà¯à®šà¯ à®ªà®Ÿà®®à¯",
                privacy_and_terms: "à®¤à®©à®¿à®¯à¯à®°à®¿à®®à¯ˆ & à®µà®¿à®¤à®¿à®®à¯à®±à¯ˆà®•à®³à¯"
            },
            te: {
                visual_challenge: "à°’à°• à°¦à±ƒà°¶à±à°¯à°®à°¾à°¨ à°¸à°µà°¾à°²à±à°¨à± à°¸à±à°µà±€à°•à°°à°¿à°‚à°šà°‚à°¡à°¿",
                audio_challenge: "à°’à°• à°†à°¡à°¿à°¯à±‹ à°¸à°µà°¾à°²à±à°¨à± à°¸à±à°µà±€à°•à°°à°¿à°‚à°šà°‚à°¡à°¿",
                refresh_btn: "à°•à±à°°à±Šà°¤à±à°¤ à°¸à°µà°¾à°²à±à°¨à± à°¸à±à°µà±€à°•à°°à°¿à°‚à°šà°‚à°¡à°¿",
                instructions_visual: "",
                instructions_audio: "à°®à±€à°°à± à°µà°¿à°¨à±à°¨à°¦à°¿ à°Ÿà±ˆà°ªà± à°šà±‡à°¯à°‚à°¡à°¿:",
                help_btn: "à°¸à°¹à°¾à°¯à°‚",
                play_again: "à°§à±à°µà°¨à°¿à°¨à°¿ à°®à°³à±à°²à±€ à°ªà±à°²à±‡ à°šà±‡à°¯à°¿",
                cant_hear_this: "à°§à±à°µà°¨à°¿à°¨à°¿ MP3 à°µà°²à±† à°¡à±Œà°¨à±â€Œà°²à±‹à°¡à± à°šà±‡à°¯à°¿",
                incorrect_try_again: "à°¤à°ªà±à°ªà±. à°®à°³à±à°²à±€ à°ªà±à°°à°¯à°¤à±à°¨à°¿à°‚à°šà°‚à°¡à°¿.",
                image_alt_text: "reCAPTCHA à°¸à°µà°¾à°²à± à°šà°¿à°¤à±à°°à°‚",
                privacy_and_terms: "à°—à±‹à°ªà±à°¯à°¤ & à°¨à°¿à°¬à°‚à°§à°¨à°²à±"
            },
            th: {
                visual_challenge: "à¸£à¸±à¸šà¸„à¸§à¸²à¸¡à¸—à¹‰à¸²à¸—à¸²à¸¢à¸”à¹‰à¸²à¸™à¸ à¸²à¸ž",
                audio_challenge: "à¸£à¸±à¸šà¸„à¸§à¸²à¸¡à¸—à¹‰à¸²à¸—à¸²à¸¢à¸”à¹‰à¸²à¸™à¹€à¸ªà¸µà¸¢à¸‡",
                refresh_btn: "à¸£à¸±à¸šà¸„à¸§à¸²à¸¡à¸—à¹‰à¸²à¸—à¸²à¸¢à¹ƒà¸«à¸¡à¹ˆ",
                instructions_visual: "à¸žà¸´à¸¡à¸žà¹Œà¸‚à¹‰à¸­à¸„à¸§à¸²à¸¡à¸™à¸µà¹‰:",
                instructions_audio: "à¸žà¸´à¸¡à¸žà¹Œà¸ªà¸´à¹ˆà¸‡à¸—à¸µà¹ˆà¸„à¸¸à¸“à¹„à¸”à¹‰à¸¢à¸´à¸™:",
                help_btn: "à¸„à¸§à¸²à¸¡à¸Šà¹ˆà¸§à¸¢à¹€à¸«à¸¥à¸·à¸­",
                play_again: "à¹€à¸¥à¹ˆà¸™à¹€à¸ªà¸µà¸¢à¸‡à¸­à¸µà¸à¸„à¸£à¸±à¹‰à¸‡",
                cant_hear_this: "à¸”à¸²à¸§à¹‚à¸«à¸¥à¸”à¹€à¸ªà¸µà¸¢à¸‡à¹€à¸›à¹‡à¸™ MP3",
                incorrect_try_again: "à¹„à¸¡à¹ˆà¸–à¸¹à¸à¸•à¹‰à¸­à¸‡ à¸¥à¸­à¸‡à¸­à¸µà¸à¸„à¸£à¸±à¹‰à¸‡",
                image_alt_text: "à¸£à¸«à¸±à¸ªà¸ à¸²à¸ž reCAPTCHA",
                privacy_and_terms: "à¸™à¹‚à¸¢à¸šà¸²à¸¢à¸ªà¹ˆà¸§à¸™à¸šà¸¸à¸„à¸„à¸¥à¹à¸¥à¸°à¸‚à¹‰à¸­à¸à¸³à¸«à¸™à¸”"
            },
            tr: {
                visual_challenge: "GÃ¶rsel sorgu al",
                audio_challenge: "Sesli sorgu al",
                refresh_btn: "Yeniden yÃ¼kle",
                instructions_visual: "Metni yazÄ±n:",
                instructions_audio: "DuyduÄŸunuzu yazÄ±n:",
                help_btn: "YardÄ±m",
                play_again: "Sesi tekrar Ã§al",
                cant_hear_this: "Sesi MP3 olarak indir",
                incorrect_try_again: "YanlÄ±ÅŸ. Tekrar deneyin.",
                image_alt_text: "reCAPTCHA sorusu resmi",
                privacy_and_terms: "Gizlilik ve Åžartlar"
            },
            uk: {
                visual_challenge: "ÐžÑ‚Ñ€Ð¸Ð¼Ð°Ñ‚Ð¸ Ð²Ñ–Ð·ÑƒÐ°Ð»ÑŒÐ½Ð¸Ð¹ Ñ‚ÐµÐºÑÑ‚",
                audio_challenge: "ÐžÑ‚Ñ€Ð¸Ð¼Ð°Ñ‚Ð¸ Ð°ÑƒÐ´Ñ–Ð¾Ð·Ð°Ð¿Ð¸Ñ",
                refresh_btn: "ÐžÐ½Ð¾Ð²Ð¸Ñ‚Ð¸ Ñ‚ÐµÐºÑÑ‚",
                instructions_visual: "Ð’Ð²ÐµÐ´Ñ–Ñ‚ÑŒ Ñ‚ÐµÐºÑÑ‚:",
                instructions_audio: "Ð’Ð²ÐµÐ´Ñ–Ñ‚ÑŒ Ð¿Ð¾Ñ‡ÑƒÑ‚Ðµ:",
                help_btn: "Ð”Ð¾Ð²Ñ–Ð´ÐºÐ°",
                play_again: "Ð’Ñ–Ð´Ñ‚Ð²Ð¾Ñ€Ð¸Ñ‚Ð¸ Ð·Ð°Ð¿Ð¸Ñ Ñ‰Ðµ Ñ€Ð°Ð·",
                cant_hear_this: "Ð—Ð°Ð²Ð°Ð½Ñ‚Ð°Ð¶Ð¸Ñ‚Ð¸ Ð·Ð°Ð¿Ð¸Ñ ÑÐº MP3",
                incorrect_try_again: "ÐÐµÐ¿Ñ€Ð°Ð²Ð¸Ð»ÑŒÐ½Ð¾. Ð¡Ð¿Ñ€Ð¾Ð±ÑƒÐ¹Ñ‚Ðµ Ñ‰Ðµ Ñ€Ð°Ð·.",
                image_alt_text: "Ð—Ð¾Ð±Ñ€Ð°Ð¶ÐµÐ½Ð½Ñ Ð·Ð°Ð²Ð´Ð°Ð½Ð½Ñ reCAPTCHA",
                privacy_and_terms: "ÐšÐ¾Ð½Ñ„Ñ–Ð´ÐµÐ½Ñ†Ñ–Ð¹Ð½Ñ–ÑÑ‚ÑŒ Ñ– ÑƒÐ¼Ð¾Ð²Ð¸"
            },
            ur: {
                visual_challenge: "Ø§ÛŒÚ© Ù…Ø±Ø¦ÛŒ Ú†ÛŒÙ„Ù†Ø¬ Ø­Ø§ØµÙ„ Ú©Ø±ÛŒÚº",
                audio_challenge: "Ø§ÛŒÚ© Ø¢ÚˆÛŒÙˆ Ú†ÛŒÙ„Ù†Ø¬ Ø­Ø§ØµÙ„ Ú©Ø±ÛŒÚº",
                refresh_btn: "Ø§ÛŒÚ© Ù†ÛŒØ§ Ú†ÛŒÙ„Ù†Ø¬ Ø­Ø§ØµÙ„ Ú©Ø±ÛŒÚº",
                instructions_visual: "",
                instructions_audio: "Ø¬Ùˆ Ø³Ù†Ø§Ø¦ÛŒ Ø¯ÛŒØªØ§ ÛÛ’ ÙˆÛ Ù¹Ø§Ø¦Ù¾ Ú©Ø±ÛŒÚº:",
                help_btn: "Ù…Ø¯Ø¯",
                play_again: "Ø¢ÙˆØ§Ø² Ø¯ÙˆØ¨Ø§Ø±Û Ú†Ù„Ø§Ø¦ÛŒÚº",
                cant_hear_this: "Ø¢ÙˆØ§Ø² Ú©Ùˆ MP3 Ú©Û’ Ø¨Ø·ÙˆØ± ÚˆØ§Ø¤Ù† Ù„ÙˆÚˆ Ú©Ø±ÛŒÚº",
                incorrect_try_again: "ØºÙ„Ø·Û” Ø¯ÙˆØ¨Ø§Ø±Û Ú©ÙˆØ´Ø´ Ú©Ø±ÛŒÚºÛ”",
                image_alt_text: "reCAPTCHA Ú†ÛŒÙ„Ù†Ø¬ ÙˆØ§Ù„ÛŒ Ø´Ø¨ÛŒÛ",
                privacy_and_terms: "Ø±Ø§Ø²Ø¯Ø§Ø±ÛŒ Ùˆ Ø´Ø±Ø§Ø¦Ø·"
            },
            vi: {
                visual_challenge: "Nháº­n thá»­ thÃ¡ch hÃ¬nh áº£nh",
                audio_challenge: "Nháº­n thá»­ thÃ¡ch Ã¢m thanh",
                refresh_btn: "Nháº­n thá»­ thÃ¡ch má»›i",
                instructions_visual: "Nháº­p vÄƒn báº£n:",
                instructions_audio: "Nháº­p ná»™i dung báº¡n nghe tháº¥y:",
                help_btn: "Trá»£ giÃºp",
                play_again: "PhÃ¡t láº¡i Ã¢m thanh",
                cant_hear_this: "Táº£i Ã¢m thanh xuá»‘ng dÆ°á»›i dáº¡ng MP3",
                incorrect_try_again: "KhÃ´ng chÃ­nh xÃ¡c. HÃ£y thá»­ láº¡i.",
                image_alt_text: "HÃ¬nh xÃ¡c thá»±c reCAPTCHA",
                privacy_and_terms: "Báº£o máº­t vÃ  Ä‘iá»u khoáº£n"
            },
            "zh-CN": k,
            "zh-HK": {
                visual_challenge: "å›žç­”åœ–åƒé©—è­‰å•é¡Œ",
                audio_challenge: "å–å¾—èªžéŸ³é©—è­‰å•é¡Œ",
                refresh_btn: "æ›ä¸€å€‹é©—è­‰å•é¡Œ",
                instructions_visual: "è¼¸å…¥æ–‡å­—ï¼š",
                instructions_audio: "éµå…¥æ‚¨æ‰€è½åˆ°çš„ï¼š",
                help_btn: "èªªæ˜Ž",
                play_again: "å†æ¬¡æ’­æ”¾è²éŸ³",
                cant_hear_this: "å°‡è²éŸ³ä¸‹è¼‰ç‚º MP3",
                incorrect_try_again: "ä¸æ­£ç¢ºï¼Œå†è©¦ä¸€æ¬¡ã€‚",
                image_alt_text: "reCAPTCHA é©—è­‰æ–‡å­—åœ–ç‰‡",
                privacy_and_terms: "ç§éš±æ¬Šèˆ‡æ¢æ¬¾"
            },
            "zh-TW": {
                visual_challenge: "å–å¾—åœ–ç‰‡é©—è­‰å•é¡Œ",
                audio_challenge: "å–å¾—èªžéŸ³é©—è­‰å•é¡Œ",
                refresh_btn: "å–å¾—æ–°çš„é©—è­‰å•é¡Œ",
                instructions_visual: "è«‹è¼¸å…¥åœ–ç‰‡ä¸­çš„æ–‡å­—ï¼š",
                instructions_audio: "è«‹è¼¸å…¥èªžéŸ³å…§å®¹ï¼š",
                help_btn: "èªªæ˜Ž",
                play_again: "å†æ¬¡æ’­æ”¾",
                cant_hear_this: "ä»¥ MP3 æ ¼å¼ä¸‹è¼‰è²éŸ³",
                incorrect_try_again: "é©—è­‰ç¢¼æœ‰èª¤ï¼Œè«‹å†è©¦ä¸€æ¬¡ã€‚",
                image_alt_text: "reCAPTCHA é©—è­‰æ–‡å­—åœ–ç‰‡",
                privacy_and_terms: "éš±ç§æ¬Šèˆ‡æ¢æ¬¾"
            },
            zu: {
                visual_challenge: "Thola inselelo ebonakalayo",
                audio_challenge: "Thola inselelo yokulalelwayo",
                refresh_btn: "Thola inselelo entsha",
                instructions_visual: "",
                instructions_audio: "Bhala okuzwayo:",
                help_btn: "Usizo",
                play_again: "Phinda udlale okulalelwayo futhi",
                cant_hear_this: "Layisha umsindo njenge-MP3",
                incorrect_try_again: "Akulungile. Zama futhi.",
                image_alt_text: "umfanekiso oyinselelo we-reCAPTCHA",
                privacy_and_terms: "Okwangasese kanye nemigomo"
            },
            tl: E,
            he: T,
            "in": x,
            mo: C,
            zh: k
        },
        A = function(e) {
            if (Error.captureStackTrace) Error.captureStackTrace(this, A);
            else {
                var t = Error().stack;
                t && (this.stack = t)
            }
            e && (this.message = String(e))
        };
    m(A, Error), A.prototype.name = "CustomError";
    var O, M = function(e, t) {
            for (var n = e.split("%s"), r = "", i = Array.prototype.slice.call(arguments, 1); i.length && 1 < n.length;) r += n.shift() + i.shift();
            return r + n.join("%s")
        },
        _ = function(e) {
            return I.test(e) ? (-1 != e.indexOf("&") && (e = e.replace(D, "&amp;")), -1 != e.indexOf("<") && (e = e.replace(P, "&lt;")), -1 != e.indexOf(">") && (e = e.replace(H, "&gt;")), -1 != e.indexOf('"') && (e = e.replace(B, "&quot;")), -1 != e.indexOf("'") && (e = e.replace(j, "&#39;")), -1 != e.indexOf("\0") && (e = e.replace(F, "&#0;")), e) : e
        },
        D = /&/g,
        P = /</g,
        H = />/g,
        B = /"/g,
        j = /'/g,
        F = /\x00/g,
        I = /[\x00&<>"']/,
        q = function(e, t) {
            return e < t ? -1 : e > t ? 1 : 0
        },
        R = function(e) {
            return String(e).replace(/\-([a-z])/g, function(e, t) {
                return t.toUpperCase()
            })
        },
        U = function(e) {
            var t = o(void 0) ? "undefined".replace(/([-()\[\]{}+?*.$\^|,:#<!\\])/g, "\\$1").replace(/\x08/g, "\\x08") : "\\s";
            return e.replace(new RegExp("(^" + (t ? "|[" + t + "]+" : "") + ")([a-z])", "g"), function(e, t, n) {
                return t + n.toUpperCase()
            })
        },
        z = function(e, t) {
            t.unshift(e), A.call(this, M.apply(null, t)), t.shift()
        };
    m(z, A), z.prototype.name = "AssertionError";
    var W = function(e, t, n, r) {
            var i = "Assertion failed";
            if (n) var i = i + (": " + n),
                s = r;
            else e && (i += ": " + e, s = t);
            throw new z("" + i, s || [])
        },
        X = function(e, t, n) {
            e || W("", null, t, Array.prototype.slice.call(arguments, 2))
        },
        V = function(e, t) {
            throw new z("Failure" + (e ? ": " + e : ""), Array.prototype.slice.call(arguments, 1))
        },
        $ = function(e, t, n) {
            return o(e) || W("Expected string but got %s: %s.", [r(e), e], t, Array.prototype.slice.call(arguments, 2)), e
        },
        J = function(e, t, n) {
            u(e) || W("Expected function but got %s: %s.", [r(e), e], t, Array.prototype.slice.call(arguments, 2))
        },
        K = Array.prototype,
        Q = K.indexOf ? function(e, t, n) {
            return X(null != e.length), K.indexOf.call(e, t, n)
        } : function(e, t, n) {
            n = null == n ? 0 : 0 > n ? Math.max(0, e.length + n) : n;
            if (o(e)) return o(t) && 1 == t.length ? e.indexOf(t, n) : -1;
            for (; n < e.length; n++)
                if (n in e && e[n] === t) return n;
            return -1
        },
        G = K.forEach ? function(e, t, n) {
            X(null != e.length), K.forEach.call(e, t, n)
        } : function(e, t, n) {
            for (var r = e.length, i = o(e) ? e.split("") : e, s = 0; s < r; s++) s in i && t.call(n, i[s], s, e)
        },
        Y = K.map ? function(e, t, n) {
            return X(null != e.length), K.map.call(e, t, n)
        } : function(e, t, n) {
            for (var r = e.length, i = Array(r), s = o(e) ? e.split("") : e, u = 0; u < r; u++) u in s && (i[u] = t.call(n, s[u], u, e));
            return i
        },
        Z = K.some ? function(e, t, n) {
            return X(null != e.length), K.some.call(e, t, n)
        } : function(e, t, n) {
            for (var r = e.length, i = o(e) ? e.split("") : e, s = 0; s < r; s++)
                if (s in i && t.call(n, i[s], s, e)) return !0;
            return !1
        },
        et = function(e, t) {
            var n = Q(e, t),
                r;
            if (r = 0 <= n) X(null != e.length), K.splice.call(e, n, 1);
            return r
        },
        tt = function(e) {
            var t = e.length;
            if (0 < t) {
                for (var n = Array(t), r = 0; r < t; r++) n[r] = e[r];
                return n
            }
            return []
        },
        nt = function(e, t, n) {
            return X(null != e.length), 2 >= arguments.length ? K.slice.call(e, t) : K.slice.call(e, t, n)
        },
        rt = function(e, t) {
            for (var n in e) t.call(void 0, e[n], n, e)
        },
        it = function(e) {
            var t = [],
                n = 0,
                r;
            for (r in e) t[n++] = r;
            return t
        },
        st = function(e) {
            for (var t in e) return !1;
            return !0
        },
        ot = function() {
            var e = Br() ? t.google_ad : null,
                n = {},
                r;
            for (r in e) n[r] = e[r];
            return n
        },
        ut = "constructor hasOwnProperty isPrototypeOf propertyIsEnumerable toLocaleString toString valueOf".split(" "),
        at = function(e, t) {
            for (var n, r, i = 1; i < arguments.length; i++) {
                r = arguments[i];
                for (n in r) e[n] = r[n];
                for (var s = 0; s < ut.length; s++) n = ut[s], Object.prototype.hasOwnProperty.call(r, n) && (e[n] = r[n])
            }
        },
        ft = function(e) {
            var t = arguments.length;
            if (1 == t && i(arguments[0])) return ft.apply(null, arguments[0]);
            for (var n = {}, r = 0; r < t; r++) n[arguments[r]] = !0;
            return n
        },
        lt;
    e: {
        var ct = t.navigator;
        if (ct) {
            var ht = ct.userAgent;
            if (ht) {
                lt = ht;
                break e
            }
        }
        lt = ""
    }
    var pt = function(e) {
            return -1 != lt.indexOf(e)
        },
        dt = pt("Opera") || pt("OPR"),
        vt = pt("Trident") || pt("MSIE"),
        mt = pt("Gecko") && -1 == lt.toLowerCase().indexOf("webkit") && !pt("Trident") && !pt("MSIE"),
        gt = -1 != lt.toLowerCase().indexOf("webkit"),
        yt = function() {
            var e = t.document;
            return e ? e.documentMode : void 0
        },
        bt = function() {
            var e = "",
                n;
            return dt && t.opera ? (e = t.opera.version, u(e) ? e() : e) : (mt ? n = /rv\:([^\);]+)(\)|;)/ : vt ? n = /\b(?:MSIE|rv)[: ]([^\);]+)(\)|;)/ : gt && (n = /WebKit\/(\S+)/), n && (e = (e = n.exec(lt)) ? e[1] : ""), vt && (n = yt(), n > parseFloat(e)) ? String(n) : e)
        }(),
        wt = {},
        Et = function(e) {
            var t;
            if (!(t = wt[e])) {
                t = 0;
                for (var n = String(bt).replace(/^[\s\xa0]+|[\s\xa0]+$/g, "").split("."), r = String(e).replace(/^[\s\xa0]+|[\s\xa0]+$/g, "").split("."), i = Math.max(n.length, r.length), s = 0; 0 == t && s < i; s++) {
                    var o = n[s] || "",
                        u = r[s] || "",
                        a = RegExp("(\\d*)(\\D*)", "g"),
                        f = RegExp("(\\d*)(\\D*)", "g");
                    do {
                        var l = a.exec(o) || ["", "", ""],
                            c = f.exec(u) || ["", "", ""];
                        if (0 == l[0].length && 0 == c[0].length) break;
                        t = q(0 == l[1].length ? 0 : parseInt(l[1], 10), 0 == c[1].length ? 0 : parseInt(c[1], 10)) || q(0 == l[2].length, 0 == c[2].length) || q(l[2], c[2])
                    } while (0 == t)
                }
                t = wt[e] = 0 <= t
            }
            return t
        },
        St = t.document,
        xt = St && vt ? yt() || ("CSS1Compat" == St.compatMode ? parseInt(bt, 10) : 5) : void 0,
        Tt = function(e) {
            if (8192 > e.length) return String.fromCharCode.apply(null, e);
            for (var t = "", n = 0; n < e.length; n += 8192) var r = nt(e, n, n + 8192),
                t = t + String.fromCharCode.apply(null, r);
            return t
        },
        Nt = function(e) {
            return Y(e, function(e) {
                return e = e.toString(16), 1 < e.length ? e : "0" + e
            }).join("")
        },
        Ct = null,
        kt = null,
        Lt = function(e) {
            if (!Ct) {
                Ct = {}, kt = {};
                for (var t = 0; 65 > t; t++) Ct[t] = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=".charAt(t), kt[Ct[t]] = t, 62 <= t && (kt["ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_.".charAt(t)] = t)
            }
            for (var t = kt, n = [], r = 0; r < e.length;) {
                var i = t[e.charAt(r++)],
                    s = r < e.length ? t[e.charAt(r)] : 0;
                ++r;
                var o = r < e.length ? t[e.charAt(r)] : 64;
                ++r;
                var u = r < e.length ? t[e.charAt(r)] : 64;
                ++r;
                if (null == i || null == s || null == o || null == u) throw Error();
                n.push(i << 2 | s >> 4), 64 != o && (n.push(s << 4 & 240 | o >> 2), 64 != u && n.push(o << 6 & 192 | u))
            }
            return n
        },
        At = function() {
            this.disposed_ = this.disposed_, this.onDisposeCallbacks_ = this.onDisposeCallbacks_
        };
    At.prototype.disposed_ = !1, At.prototype.dispose = function() {
        this.disposed_ || (this.disposed_ = !0, this.disposeInternal())
    };
    var Ot = function(e, t) {
        e.onDisposeCallbacks_ || (e.onDisposeCallbacks_ = []), e.onDisposeCallbacks_.push(t)
    };
    At.prototype.disposeInternal = function() {
        if (this.onDisposeCallbacks_)
            for (; this.onDisposeCallbacks_.length;) this.onDisposeCallbacks_.shift()()
    };
    var Mt = function(e) {
            e && "function" == typeof e.dispose && e.dispose()
        },
        _t = !vt || vt && 9 <= xt;
    !mt && !vt || vt && vt && 9 <= xt || mt && Et("1.9.1"), vt && Et("9");
    var Dt = function(e) {
            return e ? new Wt(Ut(e)) : O || (O = new Wt)
        },
        Pt = function(e, t) {
            return o(t) ? e.getElementById(t) : t
        },
        Ht = function(e, t) {
            rt(t, function(t, n) {
                "style" == n ? e.style.cssText = t : "class" == n ? e.className = t : "for" == n ? e.htmlFor = t : n in Bt ? e.setAttribute(Bt[n], t) : 0 == n.lastIndexOf("aria-", 0) || 0 == n.lastIndexOf("data-", 0) ? e.setAttribute(n, t) : e[n] = t
            })
        },
        Bt = {
            cellpadding: "cellPadding",
            cellspacing: "cellSpacing",
            colspan: "colSpan",
            frameborder: "frameBorder",
            height: "height",
            maxlength: "maxLength",
            role: "role",
            rowspan: "rowSpan",
            type: "type",
            usemap: "useMap",
            valign: "vAlign",
            width: "width"
        },
        jt = function(e, t, n) {
            return Ft(document, arguments)
        },
        Ft = function(e, t) {
            var n = t[0],
                r = t[1];
            if (!_t && r && (r.name || r.type)) {
                n = ["<", n], r.name && n.push(' name="', _(r.name), '"');
                if (r.type) {
                    n.push(' type="', _(r.type), '"');
                    var s = {};
                    at(s, r), delete s.type, r = s
                }
                n.push(">"), n = n.join("")
            }
            return n = e.createElement(n), r && (o(r) ? n.className = r : i(r) ? n.className = r.join(" ") : Ht(n, r)), 2 < t.length && It(e, n, t), n
        },
        It = function(e, t, n) {
            function r(n) {
                n && t.appendChild(o(n) ? e.createTextNode(n) : n)
            }
            for (var i = 2; i < n.length; i++) {
                var u = n[i];
                !s(u) || a(u) && 0 < u.nodeType ?
                    r(u) : G(zt(u) ? tt(u) : u, r)
            }
        },
        qt = function(e) {
            for (var t; t = e.firstChild;) e.removeChild(t)
        },
        Rt = function(e) {
            e && e.parentNode && e.parentNode.removeChild(e)
        },
        Ut = function(e) {
            return X(e, "Node cannot be null or undefined."), 9 == e.nodeType ? e : e.ownerDocument || e.document
        },
        zt = function(e) {
            if (e && "number" == typeof e.length) {
                if (a(e)) return "function" == typeof e.item || "string" == typeof e.item;
                if (u(e)) return "function" == typeof e.item
            }
            return !1
        },
        Wt = function(e) {
            this.document_ = e || t.document || document
        };
    e = Wt.prototype, e.getDomHelper = Dt, e.getElement = function(e) {
        return Pt(this.document_, e)
    }, e.$ = Wt.prototype.getElement, e.createDom = function(e, t, n) {
        return Ft(this.document_, arguments)
    }, e.createElement = function(e) {
        return this.document_.createElement(e)
    }, e.createTextNode = function(e) {
        return this.document_.createTextNode(String(e))
    }, e.appendChild = function(e, t) {
        e.appendChild(t)
    };
    var Xt = function(e) {
            t.setTimeout(function() {
                throw e
            }, 0)
        },
        Vt, $t = function() {
            var e = t.MessageChannel;
            "undefined" == typeof e && "undefined" != typeof window && window.postMessage && window.addEventListener && (e = function() {
                var e = document.createElement("iframe");
                e.style.display = "none", e.src = "", document.documentElement.appendChild(e);
                var t = e.contentWindow,
                    e = t.document;
                e.open(), e.write(""), e.close();
                var n = "callImmediate" + Math.random(),
                    r = "file:" == t.location.protocol ? "*" : t.location.protocol + "//" + t.location.host,
                    e = c(function(e) {
                        (e.origin == r || e.data == n) && this.port1.onmessage()
                    }, this);
                t.addEventListener("message", e, !1), this.port1 = {}, this.port2 = {
                    postMessage: function() {
                        t.postMessage(n, r)
                    }
                }
            });
            if ("undefined" != typeof e && !pt("Trident") && !pt("MSIE")) {
                var n = new e,
                    r = {},
                    i = r;
                return n.port1.onmessage = function() {
                        r = r.next;
                        var e = r.cb;
                        r.cb = null, e()
                    },
                    function(e) {
                        i.next = {
                            cb: e
                        }, i = i.next, n.port2.postMessage(0)
                    }
            }
            return "undefined" != typeof document && "onreadystatechange" in document.createElement("script") ? function(e) {
                var t = document.createElement("script");
                t.onreadystatechange = function() {
                    t.onreadystatechange = null, t.parentNode.removeChild(t), t = null, e(), e = null
                }, document.documentElement.appendChild(t)
            } : function(e) {
                t.setTimeout(e, 0)
            }
        },
        Jt = function(e, t) {
            Kt || Qt(), Gt || (Kt(), Gt = !0), Yt.push(new en(e, t))
        },
        Kt, Qt = function() {
            if (t.Promise && t.Promise.resolve) {
                var e = t.Promise.resolve();
                Kt = function() {
                    e.then(Zt)
                }
            } else Kt = function() {
                var e = Zt;
                !u(t.setImmediate) || t.Window && t.Window.prototype.setImmediate == t.setImmediate ? (Vt || (Vt = $t()), Vt(e)) : t.setImmediate(e)
            }
        },
        Gt = !1,
        Yt = [],
        Zt = function() {
            for (; Yt.length;) {
                var e = Yt;
                Yt = [];
                for (var t = 0; t < e.length; t++) {
                    var n = e[t];
                    try {
                        n.fn.call(n.scope)
                    } catch (r) {
                        Xt(r)
                    }
                }
            }
            Gt = !1
        },
        en = function(e, t) {
            this.fn = e, this.scope = t
        },
        tn = function(e) {
            e.prototype.then = e.prototype.then, e.prototype.$goog_Thenable = !0
        },
        nn = function(e) {
            if (!e) return !1;
            try {
                return !!e.$goog_Thenable
            } catch (t) {
                return !1
            }
        },
        rn = function(e, t) {
            this.state_ = 0, this.result_ = void 0, this.callbackEntries_ = this.parent_ = null, this.hadUnhandledRejection_ = this.executing_ = !1;
            try {
                var n = this;
                e.call(t, function(e) {
                    an(n, 2, e)
                }, function(e) {
                    if (!(e instanceof dn)) try {
                        throw e instanceof Error ? e : Error("Promise rejected.")
                    } catch (t) {}
                    an(n, 3, e)
                })
            } catch (r) {
                an(this, 3, r)
            }
        };
    rn.prototype.then = function(e, t, n) {
        return null != e && J(e, "opt_onFulfilled should be a function."), null != t && J(t, "opt_onRejected should be a function. Did you pass opt_context as the second argument instead of the third?"), un(this, u(e) ? e : null, u(t) ? t : null, n)
    }, tn(rn), rn.prototype.cancel = function(e) {
        0 == this.state_ && Jt(function() {
            var t = new dn(e);
            sn(this, t)
        }, this)
    };
    var sn = function(e, t) {
            if (0 == e.state_)
                if (e.parent_) {
                    var n = e.parent_;
                    if (n.callbackEntries_) {
                        for (var r = 0, i = -1, s = 0, o; o = n.callbackEntries_[s]; s++)
                            if (o = o.child)
                                if (r++, o == e && (i = s), 0 <= i && 1 < r) break;
                        0 <= i && (0 == n.state_ && 1 == r ? sn(n, t) : (r = n.callbackEntries_.splice(i, 1)[0], cn(n), r.onRejected(t)))
                    }
                } else an(e, 3, t)
        },
        on = function(e, t) {
            e.callbackEntries_ && e.callbackEntries_.length || 2 != e.state_ && 3 != e.state_ || ln(e), e.callbackEntries_ || (e.callbackEntries_ = []), e.callbackEntries_.push(t)
        },
        un = function(e, t, n, r) {
            var i = {
                child: null,
                onFulfilled: null,
                onRejected: null
            };
            return i.child = new rn(function(e, s) {
                i.onFulfilled = t ? function(n) {
                    try {
                        var i = t.call(r, n);
                        e(i)
                    } catch (o) {
                        s(o)
                    }
                } : e, i.onRejected = n ? function(t) {
                    try {
                        var i = n.call(r, t);
                        void 0 === i && t instanceof dn ? s(t) : e(i)
                    } catch (o) {
                        s(o)
                    }
                } : s
            }), i.child.parent_ = e, on(e, i), i.child
        };
    rn.prototype.unblockAndFulfill_ = function(e) {
        X(1 == this.state_), this.state_ = 0, an(this, 2, e)
    }, rn.prototype.unblockAndReject_ = function(e) {
        X(1 == this.state_), this.state_ = 0, an(this, 3, e)
    };
    var an = function(e, t, n) {
            if (0 == e.state_) {
                if (e == n) t = 3, n = new TypeError("Promise cannot resolve to itself");
                else {
                    if (nn(n)) {
                        e.state_ = 1, n.then(e.unblockAndFulfill_, e.unblockAndReject_, e);
                        return
                    }
                    if (a(n)) try {
                        var r = n.then;
                        if (u(r)) {
                            fn(e, n, r);
                            return
                        }
                    } catch (i) {
                        t = 3, n = i
                    }
                }
                e.result_ = n, e.state_ = t, ln(e), 3 != t || n instanceof dn || hn(e, n)
            }
        },
        fn = function(e, t, n) {
            e.state_ = 1;
            var r = !1,
                i = function(t) {
                    r || (r = !0, e.unblockAndFulfill_(t))
                },
                s = function(t) {
                    r || (r = !0, e.unblockAndReject_(t))
                };
            try {
                n.call(t, i, s)
            } catch (o) {
                s(o)
            }
        },
        ln = function(e) {
            e.executing_ || (e.executing_ = !0, Jt(e.executeCallbacks_, e))
        };
    rn.prototype.executeCallbacks_ = function() {
        for (; this.callbackEntries_ && this.callbackEntries_.length;) {
            var e = this.callbackEntries_;
            this.callbackEntries_ = [];
            for (var t = 0; t < e.length; t++) {
                var n = e[t],
                    r = this.result_;
                2 == this.state_ ? n.onFulfilled(r) : (cn(this), n.onRejected(r))
            }
        }
        this.executing_ = !1
    };
    var cn = function(e) {
            for (; e && e.hadUnhandledRejection_; e = e.parent_) e.hadUnhandledRejection_ = !1
        },
        hn = function(e, t) {
            e.hadUnhandledRejection_ = !0, Jt(function() {
                e.hadUnhandledRejection_ && pn.call(null, t)
            })
        },
        pn = Xt,
        dn = function(e) {
            A.call(this, e)
        };
    m(dn, A), dn.prototype.name = "cancel";
    var vn = function(e, t) {
        this.sequence_ = [], this.onCancelFunction_ = e, this.defaultScope_ = t || null, this.hadError_ = this.fired_ = !1, this.result_ = void 0, this.silentlyCanceled_ = this.blocking_ = this.blocked_ = !1, this.unhandledErrorId_ = 0, this.parent_ = null, this.branches_ = 0
    };
    vn.prototype.cancel = function(e) {
        if (this.fired_) this.result_ instanceof vn && this.result_.cancel();
        else {
            if (this.parent_) {
                var t = this.parent_;
                delete this.parent_, e ? t.cancel(e) : (t.branches_--, 0 >= t.branches_ && t.cancel())
            }
            this.onCancelFunction_ ? this.onCancelFunction_.call(this.defaultScope_, this) : this.silentlyCanceled_ = !0, this.fired_ || yn(this, new Tn)
        }
    }, vn.prototype.continue_ = function(e, t) {
        this.blocked_ = !1, mn(this, e, t)
    };
    var mn = function(e, t, n) {
            e.fired_ = !0, e.result_ = n, e.hadError_ = !t, Sn(e)
        },
        gn = function(e) {
            if (e.fired_) {
                if (!e.silentlyCanceled_) throw new xn;
                e.silentlyCanceled_ = !1
            }
        };
    vn.prototype.callback = function(e) {
        gn(this), bn(e), mn(this, !0, e)
    };
    var yn = function(e, t) {
            gn(e), bn(t), mn(e, !1, t)
        },
        bn = function(e) {
            X(!(e instanceof vn), "An execution sequence may not be initiated with a blocking Deferred.")
        },
        wn = function(e, t, n, r) {
            X(!e.blocking_, "Blocking Deferreds can not be re-used"), e.sequence_.push([t, n, r]), e.fired_ && Sn(e)
        };
    vn.prototype.then = function(e, t, n) {
        var r, i, s = new rn(function(e, t) {
            r = e, i = t
        });
        return wn(this, r, function(e) {
            e instanceof Tn ? s.cancel() : i(e)
        }), s.then(e, t, n)
    }, tn(vn);
    var En = function(e) {
            return Z(e.sequence_, function(e) {
                return u(e[1])
            })
        },
        Sn = function(e) {
            if (e.unhandledErrorId_ && e.fired_ && En(e)) {
                var n = e.unhandledErrorId_,
                    r = Cn[n];
                r && (t.clearTimeout(r.id_), delete Cn[n]), e.unhandledErrorId_ = 0
            }
            e.parent_ && (e.parent_.branches_--, delete e.parent_);
            for (var n = e.result_, i = r = !1; e.sequence_.length && !e.blocked_;) {
                var s = e.sequence_.shift(),
                    o = s[0],
                    u = s[1],
                    s = s[2];
                if (o = e.hadError_ ? u : o) try {
                    var a = o.call(s || e.defaultScope_, n);
                    void 0 !== a && (e.hadError_ = e.hadError_ && (a == n || a instanceof Error), e.result_ = n = a), nn(n) && (i = !0, e.blocked_ = !0)
                } catch (f) {
                    n = f, e.hadError_ = !0, En(e) || (r = !0)
                }
            }
            e.result_ = n, i && (a = c(e.continue_, e, !0), i = c(e.continue_, e, !1), n instanceof vn ? (wn(n, a, i), n.blocking_ = !0) : n.then(a, i)), r && (n = new Nn(n), Cn[n.id_] = n, e.unhandledErrorId_ = n.id_)
        },
        xn = function() {
            A.call(this)
        };
    m(xn, A), xn.prototype.message = "Deferred has already fired", xn.prototype.name = "AlreadyCalledError";
    var Tn = function() {
        A.call(this)
    };
    m(Tn, A), Tn.prototype.message = "Deferred was canceled", Tn.prototype.name = "CanceledError";
    var Nn = function(e) {
        this.id_ = t.setTimeout(c(this.throwError, this), 0), this.error_ = e
    };
    Nn.prototype.throwError = function() {
        throw X(Cn[this.id_], "Cannot throw an error that is not scheduled."), delete Cn[this.id_], this.error_
    };
    var Cn = {},
        kn = function(e) {
            var t = {},
                n = t.document || document,
                r = document.createElement("SCRIPT"),
                i = {
                    script_: r,
                    timeout_: void 0
                },
                s = new vn(An, i),
                o = null,
                u = null != t.timeout ? t.timeout : 5e3;
            return 0 < u && (o = window.setTimeout(function() {
                On(r, !0), yn(s, new Mn(1, "Timeout reached for loading script " + e))
            }, u), i.timeout_ = o), r.onload = r.onreadystatechange = function() {
                r.readyState && "loaded" != r.readyState && "complete" != r.readyState || (On(r, t.cleanupWhenDone || !1, o), s.callback(null))
            }, r.onerror = function() {
                On(r, !0, o), yn(s, new Mn(0, "Error while loading script " + e))
            }, Ht(r, {
                type: "text/javascript",
                charset: "UTF-8",
                src: e
            }), Ln(n).appendChild(r), s
        },
        Ln = function(e) {
            var t = e.getElementsByTagName("HEAD");
            return t && 0 != t.length ? t[0] : e.documentElement
        },
        An = function() {
            if (this && this.script_) {
                var e = this.script_;
                e && "SCRIPT" == e.tagName && On(e, !0, this.timeout_)
            }
        },
        On = function(e, r, i) {
            null != i && t.clearTimeout(i), e.onload = n, e.onerror = n, e.onreadystatechange = n, r && window.setTimeout(function() {
                Rt(e)
            }, 0)
        },
        Mn = function(e, t) {
            var n = "Jsloader error (code #" + e + ")";
            t && (n += ": " + t), A.call(this, n), this.code = e
        };
    m(Mn, A);
    var _n = function(e) {
        return _n[" "](e), e
    };
    _n[" "] = n;
    var Dn = !vt || vt && 9 <= xt,
        Pn = vt && !Et("9");
    !gt || Et("528"), mt && Et("1.9b") || vt && Et("8") || dt && Et("9.5") || gt && Et("528"), mt && !Et("8") || vt && Et("9");
    var Hn = function(e, t) {
        this.type = e, this.currentTarget = this.target = t, this.defaultPrevented = this.propagationStopped_ = !1, this.returnValue_ = !0
    };
    Hn.prototype.disposeInternal = function() {}, Hn.prototype.dispose = function() {}, Hn.prototype.preventDefault = function() {
        this.defaultPrevented = !0, this.returnValue_ = !1
    };
    var Bn = function(e, t) {
        Hn.call(this, e ? e.type : ""), this.relatedTarget = this.currentTarget = this.target = null, this.charCode = this.keyCode = this.button = this.screenY = this.screenX = this.clientY = this.clientX = this.offsetY = this.offsetX = 0, this.metaKey = this.shiftKey = this.altKey = this.ctrlKey = !1, this.event_ = this.state = null;
        if (e) {
            var n = this.type = e.type;
            this.target = e.target || e.srcElement, this.currentTarget = t;
            var r = e.relatedTarget;
            if (r) {
                if (mt) {
                    var i;
                    e: {
                        try {
                            _n(r.nodeName), i = !0;
                            break e
                        } catch (s) {}
                        i = !1
                    }
                    i || (r = null)
                }
            } else "mouseover" == n ? r = e.fromElement : "mouseout" == n && (r = e.toElement);
            this.relatedTarget = r, this.offsetX = gt || void 0 !== e.offsetX ? e.offsetX : e.layerX, this.offsetY = gt || void 0 !== e.offsetY ? e.offsetY : e.layerY, this.clientX = void 0 !== e.clientX ? e.clientX : e.pageX, this.clientY = void 0 !== e.clientY ? e.clientY : e.pageY, this.screenX = e.screenX || 0, this.screenY = e.screenY || 0, this.button = e.button, this.keyCode = e.keyCode || 0, this.charCode = e.charCode || ("keypress" == n ? e.keyCode : 0), this.ctrlKey = e.ctrlKey, this.altKey = e.altKey, this.shiftKey = e.shiftKey, this.metaKey = e.metaKey, this.state = e.state, this.event_ = e, e.defaultPrevented && this.preventDefault()
        }
    };
    m(Bn, Hn), Bn.prototype.preventDefault = function() {
        Bn.superClass_.preventDefault.call(this);
        var e = this.event_;
        if (e.preventDefault) e.preventDefault();
        else if (e.returnValue = !1, Pn) try {
            if (e.ctrlKey || 112 <= e.keyCode && 123 >= e.keyCode) e.keyCode = -1
        } catch (t) {}
    }, Bn.prototype.disposeInternal = function() {};
    var jn = "closure_listenable_" + (1e6 * Math.random() | 0),
        Fn = 0,
        In = function(e, t, n, r, i) {
            this.listener = e, this.proxy = null, this.src = t, this.type = n, this.capture = !!r, this.handler = i, this.key = ++Fn, this.removed = this.callOnce = !1
        },
        qn = function(e) {
            e.removed = !0, e.listener = null, e.proxy = null, e.src = null, e.handler = null
        },
        Rn = function(e) {
            this.src = e, this.listeners = {}, this.typeCount_ = 0
        };
    Rn.prototype.add = function(e, t, n, r, i) {
        var s = e.toString();
        e = this.listeners[s], e || (e = this.listeners[s] = [], this.typeCount_++);
        var o = zn(e, t, r, i);
        return -1 < o ? (t = e[o], n || (t.callOnce = !1)) : (t = new In(t, this.src, s, !!r, i), t.callOnce = n, e.push(t)), t
    }, Rn.prototype.remove = function(e, t, n, r) {
        e = e.toString();
        if (e in this.listeners) {
            var i = this.listeners[e];
            return t = zn(i, t, n, r), -1 < t ? (qn(i[t]), X(null != i.length), K.splice.call(i, t, 1), 0 == i.length && (delete this.listeners[e], this.typeCount_--), !0) : !1
        }
        return !1
    };
    var Un = function(e, t) {
        var n = t.type;
        if (n in e.listeners) {
            var r = et(e.listeners[n], t);
            return r && (qn(t), 0 == e.listeners[n].length && (delete e.listeners[n], e.typeCount_--)), r
        }
        return !1
    };
    Rn.prototype.removeAll = function(e) {
        e = e && e.toString();
        var t = 0,
            n;
        for (n in this.listeners)
            if (!e || n == e) {
                for (var r = this.listeners[n], i = 0; i < r.length; i++) ++t, qn(r[i]);
                delete this.listeners[n], this.typeCount_--
            }
        return t
    }, Rn.prototype.getListener = function(e, t, n, r) {
        e = this.listeners[e.toString()];
        var i = -1;
        return e && (i = zn(e, t, n, r)), -1 < i ? e[i] : null
    };
    var zn = function(e, t, n, r) {
            for (var i = 0; i < e.length; ++i) {
                var s = e[i];
                if (!s.removed && s.listener == t && s.capture == !!n && s.handler == r) return i
            }
            return -1
        },
        Wn = "closure_lm_" + (1e6 * Math.random() | 0),
        Xn = {},
        Vn = 0,
        $n = function(e, t, n, r, s) {
            if (i(t)) {
                for (var o = 0; o < t.length; o++) $n(e, t[o], n, r, s);
                return null
            }
            n = rr(n);
            if (e && e[jn]) e = e.listen(t, n, r, s);
            else {
                if (!t) throw Error("Invalid event type");
                var o = !!r,
                    u = tr(e);
                u || (e[Wn] = u = new Rn(e)), n = u.add(t, n, !1, r, s), n.proxy || (r = Jn(), n.proxy = r, r.src = e, r.listener = n, e.addEventListener ? e.addEventListener(t.toString(), r, o) : e.attachEvent(Gn(t.toString()), r), Vn++), e = n
            }
            return e
        },
        Jn = function() {
            var e = er,
                t = Dn ? function(n) {
                    return e.call(t.src, t.listener, n)
                } : function(n) {
                    n = e.call(t.src, t.listener, n);
                    if (!n) return n
                };
            return t
        },
        Kn = function(e, t, n, r, s) {
            if (i(t))
                for (var o = 0; o < t.length; o++) Kn(e, t[o], n, r, s);
            else n = rr(n), e && e[jn] ? e.unlisten(t, n, r, s) : e && (e = tr(e)) && (t = e.getListener(t, n, !!r, s)) && Qn(t)
        },
        Qn = function(e) {
            if ("number" == typeof e || !e || e.removed) return !1;
            var t = e.src;
            if (t && t[jn]) return Un(t.eventTargetListeners_, e);
            var n = e.type,
                r = e.proxy;
            return t.removeEventListener ? t.removeEventListener(n, r, e.capture) : t.detachEvent && t.detachEvent(Gn(n), r), Vn--, (n = tr(t)) ? (Un(n, e), 0 == n.typeCount_ && (n.src = null, t[Wn] = null)) : qn(e), !0
        },
        Gn = function(e) {
            return e in Xn ? Xn[e] : Xn[e] = "on" + e
        },
        Yn = function(e, t, n, r) {
            var i = 1;
            if (e = tr(e))
                if (t = e.listeners[t.toString()])
                    for (t = t.concat(), e = 0; e < t.length; e++) {
                        var s = t[e];
                        s && s.capture == n && !s.removed && (i &= !1 !== Zn(s, r))
                    }
            return Boolean(i)
        },
        Zn = function(e, t) {
            var n = e.listener,
                r = e.handler || e.src;
            return e.callOnce && Qn(e), n.call(r, t)
        },
        er = function(e, n) {
            if (e.removed) return !0;
            if (!Dn) {
                var r;
                if (!(r = n)) e: {
                    r = ["window", "event"];
                    for (var i = t, s; s = r.shift();) {
                        if (null == i[s]) {
                            r = null;
                            break e
                        }
                        i = i[s]
                    }
                    r = i
                }
                s = r, r = new Bn(s, this), i = !0;
                if (!(0 > s.keyCode || void 0 != s.returnValue)) {
                    e: {
                        var o = !1;
                        if (0 == s.keyCode) try {
                            s.keyCode = -1;
                            break e
                        } catch (u) {
                            o = !0
                        }
                        if (o || void 0 == s.returnValue) s.returnValue = !0
                    }
                    s = [];
                    for (o = r.currentTarget; o; o = o.parentNode) s.push(o);
                    for (var o = e.type, a = s.length - 1; !r.propagationStopped_ && 0 <= a; a--) r.currentTarget = s[a],
                    i &= Yn(s[a], o, !0, r);
                    for (a = 0; !r.propagationStopped_ && a < s.length; a++) r.currentTarget = s[a],
                    i &= Yn(s[a], o, !1, r)
                }
                return i
            }
            return Zn(e, new Bn(n, this))
        },
        tr = function(e) {
            return e = e[Wn], e instanceof Rn ? e : null
        },
        nr = "__closure_events_fn_" + (1e9 * Math.random() >>> 0),
        rr = function(e) {
            return X(e, "Listener can not be null."), u(e) ? e : (X(e.handleEvent, "An object listener must have handleEvent method."), e[nr] || (e[nr] = function(t) {
                return e.handleEvent(t)
            }), e[nr])
        },
        ir = function(e) {
            At.call(this), this.handler_ = e, this.keys_ = {}
        };
    m(ir, At);
    var sr = [];
    e = ir.prototype, e.listen = function(e, t, n, r) {
        i(t) || (t && (sr[0] = t.toString()), t = sr);
        for (var s = 0; s < t.length; s++) {
            var o = $n(e, t[s], n || this.handleEvent, r || !1, this.handler_ || this);
            if (!o) break;
            this.keys_[o.key] = o
        }
        return this
    }, e.unlisten = function(e, t, n, r, s) {
        if (i(t))
            for (var o = 0; o < t.length; o++) this.unlisten(e, t[o], n, r, s);
        else n = n || this.handleEvent, s = s || this.handler_ || this, n = rr(n), r = !!r, t = e && e[jn] ? e.getListener(t, n, r, s) : e ? (e = tr(e)) ? e.getListener(t, n, r, s) : null : null, t && (Qn(t), delete this.keys_[t.key]);
        return this
    }, e.removeAll = function() {
        rt(this.keys_, Qn), this.keys_ = {}
    }, e.disposeInternal = function() {
        ir.superClass_.disposeInternal.call(this), this.removeAll()
    }, e.handleEvent = function() {
        throw Error("EventHandler.handleEvent not implemented")
    };
    var or = function() {
        At.call(this), this.eventTargetListeners_ = new Rn(this), this.actualEventTarget_ = this, this.parentEventTarget_ = null
    };
    m(or, At), or.prototype[jn] = !0, e = or.prototype, e.setParentEventTarget = function(e) {
        this.parentEventTarget_ = e
    }, e.addEventListener = function(e, t, n, r) {
        $n(this, e, t, n, r)
    }, e.removeEventListener = function(e, t, n, r) {
        Kn(this, e, t, n, r)
    }, e.dispatchEvent = function(e) {
        ar(this);
        var t, n = this.parentEventTarget_;
        if (n) {
            t = [];
            for (var r = 1; n; n = n.parentEventTarget_) t.push(n), X(1e3 > ++r, "infinite loop")
        }
        n = this.actualEventTarget_, r = e.type || e;
        if (o(e)) e = new Hn(e, n);
        else if (e instanceof Hn) e.target = e.target || n;
        else {
            var i = e;
            e = new Hn(r, n), at(e, i)
        }
        var i = !0,
            s;
        if (t)
            for (var u = t.length - 1; !e.propagationStopped_ && 0 <= u; u--) s = e.currentTarget = t[u], i = ur(s, r, !0, e) && i;
        e.propagationStopped_ || (s = e.currentTarget = n, i = ur(s, r, !0, e) && i, e.propagationStopped_ || (i = ur(s, r, !1, e) && i));
        if (t)
            for (u = 0; !e.propagationStopped_ && u < t.length; u++) s = e.currentTarget = t[u], i = ur(s, r, !1, e) && i;
        return i
    }, e.disposeInternal = function() {
        or.superClass_.disposeInternal.call(this), this.eventTargetListeners_ && this.eventTargetListeners_.removeAll(void 0), this.parentEventTarget_ = null
    }, e.listen = function(e, t, n, r) {
        return ar(this), this.eventTargetListeners_.add(String(e), t, !1, n, r)
    }, e.unlisten = function(e, t, n, r) {
        return this.eventTargetListeners_.remove(String(e), t, n, r)
    };
    var ur = function(e, t, n, r) {
        t = e.eventTargetListeners_.listeners[String(t)];
        if (!t) return !0;
        t = t.concat();
        for (var i = !0, s = 0; s < t.length; ++s) {
            var o = t[s];
            if (o && !o.removed && o.capture == n) {
                var u = o.listener,
                    a = o.handler || o.src;
                o.callOnce && Un(e.eventTargetListeners_, o), i = !1 !== u.call(a, r) && i
            }
        }
        return i && 0 != r.returnValue_
    };
    or.prototype.getListener = function(e, t, n, r) {
        return this.eventTargetListeners_.getListener(String(e), t, n, r)
    };
    var ar = function(e) {
            X(e.eventTargetListeners_, "Event target is not initialized. Did you call the superclass (goog.events.EventTarget) constructor?")
        },
        fr = function(e) {
            or.call(this), this.imageIdToRequestMap_ = {}, this.imageIdToImageMap_ = {}, this.handler_ = new ir(this), this.parent_ = e
        };
    m(fr, or);
    var lr = [vt && !Et("11") ? "readystatechange" : "load", "abort", "error"],
        cr = function(e, t, n) {
            (n = o(n) ? n : n.src) && (e.imageIdToRequestMap_[t] = {
                src: n,
                corsRequestType: null
            })
        };
    fr.prototype.start = function() {
        var e = this.imageIdToRequestMap_;
        G(it(e), function(t) {
            var n = e[t];
            if (n && (delete e[t], !this.disposed_)) {
                var r;
                r = this.parent_ ? Dt(this.parent_).createDom("img") : new Image, n.corsRequestType && (r.crossOrigin = n.corsRequestType), this.handler_.listen(r, lr, this.onNetworkEvent_), this.imageIdToImageMap_[t] = r, r.id = t, r.src = n.src
            }
        }, this)
    }, fr.prototype.onNetworkEvent_ = function(e) {
        var t = e.currentTarget;
        if (t) {
            if ("readystatechange" == e.type) {
                if ("complete" != t.readyState) return;
                e.type = "load"
            }
            "undefined" == typeof t.naturalWidth && ("load" == e.type ? (t.naturalWidth = t.width, t.naturalHeight = t.height) : (t.naturalWidth = 0, t.naturalHeight = 0)), this.dispatchEvent({
                type: e.type,
                target: t
            }), !this.disposed_ && (e = t.id, delete this.imageIdToRequestMap_[e], t = this.imageIdToImageMap_[e]) && (delete this.imageIdToImageMap_[e], this.handler_.unlisten(t, lr, this.onNetworkEvent_), st(this.imageIdToImageMap_) && st(this.imageIdToRequestMap_) && this.dispatchEvent("complete"))
        }
    }, fr.prototype.disposeInternal = function() {
        delete this.imageIdToRequestMap_, delete this.imageIdToImageMap_, Mt(this.handler_), fr.superClass_.disposeInternal.call(this)
    };
    var hr = function() {};
    hr.getInstance = function() {
        return hr.instance_ ? hr.instance_ : hr.instance_ = new hr
    }, hr.prototype.nextId_ = 0;
    var pr = function(e) {
        or.call(this), this.dom_ = e || Dt(), this.id_ = null, this.inDocument_ = !1, this.element_ = null, this.googUiComponentHandler_ = void 0, this.childIndex_ = this.children_ = this.parent_ = null, this.wasDecorated_ = !1
    };
    m(pr, or), e = pr.prototype, e.idGenerator_ = hr.getInstance(), e.getElement = function() {
        return this.element_
    }, e.setParentEventTarget = function(e) {
        if (this.parent_ && this.parent_ != e) throw Error("Method not supported");
        pr.superClass_.setParentEventTarget.call(this, e)
    }, e.getDomHelper = function() {
        return this.dom_
    }, e.createDom = function() {
        this.element_ = this.dom_.createElement("div")
    };
    var dr = function(e, t) {
            if (e.inDocument_) throw Error("Component already rendered");
            e.element_ || e.createDom(), t ? t.insertBefore(e.element_, null) : e.dom_.document_.body.appendChild(e.element_), e.parent_ && !e.parent_.inDocument_ || vr(e)
        },
        vr = function(e) {
            e.inDocument_ = !0, gr(e, function(e) {
                !e.inDocument_ && e.getElement() && vr(e)
            })
        },
        mr = function(e) {
            gr(e, function(e) {
                e.inDocument_ && mr(e)
            }), e.googUiComponentHandler_ && e.googUiComponentHandler_.removeAll(), e.inDocument_ = !1
        };
    pr.prototype.disposeInternal = function() {
        this.inDocument_ && mr(this), this.googUiComponentHandler_ && (this.googUiComponentHandler_.dispose(), delete this.googUiComponentHandler_), gr(this, function(e) {
            e.dispose()
        }), !this.wasDecorated_ && this.element_ && Rt(this.element_), this.parent_ = this.element_ = this.childIndex_ = this.children_ = null, pr.superClass_.disposeInternal.call(this)
    };
    var gr = function(e, t) {
        e.children_ && G(e.children_, t, void 0)
    };
    pr.prototype.removeChild = function(e, t) {
        if (e) {
            var n = o(e) ? e : e.id_ || (e.id_ = ":" + (e.idGenerator_.nextId_++).toString(36)),
                r;
            this.childIndex_ && n ? (r = this.childIndex_, r = (n in r ? r[n] : void 0) || null) : r = null, e = r;
            if (n && e) {
                r = this.childIndex_, n in r && delete r[n], et(this.children_, e), t && (mr(e), e.element_ && Rt(e.element_)), n = e;
                if (null == n) throw Error("Unable to set parent component");
                n.parent_ = null, pr.superClass_.setParentEventTarget.call(n, null)
            }
        }
        if (!e) throw Error("Child is not in parent component");
        return e
    };
    var yr = function(e, t, n) {
        pr.call(this, n), this.captchaImage_ = e, this.adImage_ = t && 300 == t.naturalWidth && 57 == t.naturalHeight ? t : null
    };
    m(yr, pr), yr.prototype.createDom = function() {
        yr.superClass_.createDom.call(this);
        var e = this.getElement();
        this.captchaImage_.alt = Kr.image_alt_text, this.getDomHelper().appendChild(e, this.captchaImage_), this.adImage_ && (this.adImage_.alt = Kr.image_alt_text, this.getDomHelper().appendChild(e, this.adImage_), this.adImage_ && br(this.adImage_) && (e.innerHTML += '<div id="recaptcha-ad-choices"><div class="recaptcha-ad-choices-collapsed"><img height="15" width="30" alt="AdChoices" border="0" src="//www.gstatic.com/recaptcha/api/img/adicon.png"/></div><div class="recaptcha-ad-choices-expanded"><a href="https://support.google.com/adsense/troubleshooter/1631343" target="_blank"><img height="15" width="75" alt="AdChoices" border="0" src="//www.gstatic.com/recaptcha/api/img/adchoices.png"/></a></div></div>'))
    };
    var br = function(e) {
            var t = wr(e, "visibility");
            return e = wr(e, "display"), "hidden" != t && "none" != e
        },
        wr = function(e, t) {
            var n;
            e: {
                n = Ut(e);
                if (n.defaultView && n.defaultView.getComputedStyle && (n = n.defaultView.getComputedStyle(e, null))) {
                    n = n[t] || n.getPropertyValue(t) || "";
                    break e
                }
                n = ""
            }
            if (!(n = n || (e.currentStyle ? e.currentStyle[t] : null)) && (n = e.style[R(t)], "undefined" == typeof n)) {
                n = e.style;
                var r;
                e: if (r = R(t), void 0 === e.style[r]) {
                    var i = (gt ? "Webkit" : mt ? "Moz" : vt ? "ms" : dt ? "O" : null) + U(r);
                    if (void 0 !== e.style[i]) {
                        r = i;
                        break e
                    }
                }
                n = n[r] || ""
            }
            return n
        };
    yr.prototype.disposeInternal = function() {
        delete this.captchaImage_, delete this.adImage_, yr.superClass_.disposeInternal.call(this)
    };
    var Er = function(e, t, n) {
        At.call(this), this.listener_ = e, this.interval_ = t || 0, this.handler_ = n, this.callback_ = c(this.doAction_, this)
    };
    m(Er, At), e = Er.prototype, e.id_ = 0, e.disposeInternal = function() {
        Er.superClass_.disposeInternal.call(this), this.stop(), delete this.listener_, delete this.handler_
    }, e.start = function(e) {
        this.stop();
        var n = this.callback_;
        e = void 0 !== e ? e : this.interval_;
        if (!u(n)) {
            if (!n || "function" != typeof n.handleEvent) throw Error("Invalid listener argument");
            n = c(n.handleEvent, n)
        }
        this.id_ = 2147483647 < e ? -1 : t.setTimeout(n, e || 0)
    }, e.stop = function() {
        this.isActive() && t.clearTimeout(this.id_), this.id_ = 0
    }, e.isActive = function() {
        return 0 != this.id_
    }, e.doAction_ = function() {
        this.id_ = 0, this.listener_ && this.listener_.call(this.handler_)
    };
    var Sr = function(e, t) {
        At.call(this), this.listener_ = e, this.handler_ = t, this.delay_ = new Er(c(this.onTick_, this), 0, this)
    };
    m(Sr, At), e = Sr.prototype, e.interval_ = 0, e.runUntil_ = 0, e.disposeInternal = function() {
        this.delay_.dispose(), delete this.listener_, delete this.handler_, Sr.superClass_.disposeInternal.call(this)
    }, e.start = function(e, t) {
        this.stop();
        var n = t || 0;
        this.interval_ = Math.max(e || 0, 0), this.runUntil_ = 0 > n ? -1 : p() + n, this.delay_.start(0 > n ? this.interval_ : Math.min(this.interval_, n))
    }, e.stop = function() {
        this.delay_.stop()
    }, e.isActive = function() {
        return this.delay_.isActive()
    }, e.onSuccess = function() {}, e.onFailure = function() {}, e.onTick_ = function() {
        if (this.listener_.call(this.handler_)) this.onSuccess();
        else if (0 > this.runUntil_) this.delay_.start(this.interval_);
        else {
            var e = this.runUntil_ - p();
            0 >= e ? this.onFailure() : this.delay_.start(Math.min(this.interval_, e))
        }
    }, ft("area base br col command embed hr img input keygen link meta param source track wbr".split(" ")), ft("action", "cite", "data", "formaction", "href", "manifest", "poster", "src"), ft("link", "script", "style");
    var xr = {
            sanitizedContentKindHtml: !0
        },
        Tr = {
            sanitizedContentKindText: !0
        },
        Nr = function() {
            throw Error("Do not instantiate directly")
        };
    Nr.prototype.contentDir = null, Nr.prototype.toString = function() {
        return this.content
    };
    var Cr = function(e) {
            var t = Hr;
            X(t, "Soy template may not be null.");
            var n = Dt().createElement("DIV");
            return e = kr(t(e || Ar, void 0, void 0)), t = e.match(Lr), X(!t, "This template starts with a %s, which cannot be a child of a <div>, as required by soy internals. Consider using goog.soy.renderElement instead.\nTemplate output: %s", t && t[0], e), n.innerHTML = e, 1 == n.childNodes.length && (e = n.firstChild, 1 == e.nodeType) ? e : n
        },
        kr = function(e) {
            if (!a(e)) return String(e);
            if (e instanceof Nr) {
                if (e.contentKind === xr) return $(e.content);
                if (e.contentKind === Tr) return _(e.content)
            }
            return V("Soy template output is unsafe for use as HTML: " + e), "zSoyz"
        },
        Lr = /^<(body|caption|col|colgroup|head|html|tr|td|tbody|thead|tfoot)>/i,
        Ar = {};
    vt && Et(8);
    var Or = function() {
        Nr.call(this)
    };
    m(Or, Nr), Or.prototype.contentKind = xr;
    var Mr = function(e) {
        function t(e) {
            this.content = e
        }
        return t.prototype = e.prototype,
            function(e, n) {
                var r = new t(String(e));
                return void 0 !== n && (r.contentDir = n), r
            }
    }(Or);
    (function(e) {
        function t(e) {
            this.content = e
        }
        return t.prototype = e.prototype,
            function(e, n) {
                var r = String(e);
                return r ? (r = new t(r), void 0 !== n && (r.contentDir = n), r) : ""
            }
    })(Or);
    var _r = {
            "\0": "\\x00",
            "\b": "\\x08",
            "	": "\\t",
            "\n": "\\n",
            "": "\\x0b",
            "\f": "\\f",
            "\r": "\\r",
            '"': "\\x22",
            $: "\\x24",
            "&": "\\x26",
            "'": "\\x27",
            "(": "\\x28",
            ")": "\\x29",
            "*": "\\x2a",
            "+": "\\x2b",
            ",": "\\x2c",
            "-": "\\x2d",
            ".": "\\x2e",
            "/": "\\/",
            ":": "\\x3a",
            "<": "\\x3c",
            "=": "\\x3d",
            ">": "\\x3e",
            "?": "\\x3f",
            "[": "\\x5b",
            "\\": "\\\\",
            "]": "\\x5d",
            "^": "\\x5e",
            "{": "\\x7b",
            "|": "\\x7c",
            "}": "\\x7d",
            "Â…": "\\x85",
            "\u2028": "\\u2028",
            "\u2029": "\\u2029"
        },
        Dr = function(e) {
            return _r[e]
        },
        Pr = /[\x00\x08-\x0d\x22\x26\x27\/\x3c-\x3e\\\x85\u2028\u2029]/g,
        Hr = function(e) {
            return Mr('<script type="text/javascript">var challenge = \'' + String(e.challenge).replace(Pr, Dr) + "'; var publisherId = '" + String(e.publisherId).replace(Pr, Dr) + "';" + ("ca-mongoogle" == e.publisherId ? 'google_page_url = "3pcerttesting.com/dab/recaptcha.html";' : "") + "\n    google_ad_client = publisherId;\n    google_ad_type = 'html';\n    google_ad_output = 'js';\n    google_image_size = '300x57';\n    google_captcha_token = challenge;\n    google_ad_request_done = function(ad) {\n      window.parent.recaptcha.ads.adutils.googleAdRequestDone(ad);\n    };\n    </script><script type=\"text/javascript\" src=\"//pagead2.googlesyndication.com/pagead/show_ads.js\"></script>")
        };
    Hr.soyTemplateName = "recaptcha.soy.ads.iframeAdsLoader.main";
    var Br = function() {
            var e = t.google_ad;
            return !!(e && e.token && e.imageAdUrl && e.hashedAnswer && e.salt && e.delayedImpressionUrl && e.engagementUrl)
        },
        jr = function() {
            t.google_ad && (t.google_ad = null)
        },
        Fr = function(e) {
            e = e || document.body;
            var n = t.google_ad;
            n && n.searchUpliftUrl && (n = jt("iframe", {
                src: 'data:text/html;charset=utf-8,<body><img src="https://' + n.searchUpliftUrl + '"></img></body>',
                style: "display:none"
            }), e.appendChild(n))
        },
        Ir = 0,
        qr = function(e) {
            var t = new fr;
            cr(t, "recaptcha-url-" + Ir++, e), t.start()
        },
        Rr = function(e, n) {
            var r = RecaptchaState.publisher_id;
            jr();
            var i = jt("iframe", {
                id: "recaptcha-loader-" + Ir++,
                style: "display: none"
            });
            document.body.appendChild(i);
            var s = i.contentWindow ? i.contentWindow.document : i.contentDocument;
            s.open("text/html", "replace"), s.write(Cr({
                challenge: e,
                publisherId: r
            }).innerHTML), s.close(), r = new Sr(function() {
                return !!t.google_ad
            }), r.onSuccess = function() {
                Rt(i), n()
            }, r.onFailure = function() {
                Rt(i), n()
            }, r.start(50, 2e3)
        };
    v("recaptcha.ads.adutils.googleAdRequestDone", function(e) {
        t.google_ad = e
    });
    var Ur = function() {
            this.blockSize = -1
        },
        zr = function() {
            this.blockSize = -1, this.blockSize = 64, this.chain_ = Array(4), this.block_ = Array(this.blockSize), this.totalLength_ = this.blockLength_ = 0, this.reset()
        };
    m(zr, Ur), zr.prototype.reset = function() {
        this.chain_[0] = 1732584193, this.chain_[1] = 4023233417, this.chain_[2] = 2562383102, this.chain_[3] = 271733878, this.totalLength_ = this.blockLength_ = 0
    };
    var Wr = function(e, t, n) {
        n || (n = 0);
        var r = Array(16);
        if (o(t))
            for (var i = 0; 16 > i; ++i) r[i] = t.charCodeAt(n++) | t.charCodeAt(n++) << 8 | t.charCodeAt(n++) << 16 | t.charCodeAt(n++) << 24;
        else
            for (i = 0; 16 > i; ++i) r[i] = t[n++] | t[n++] << 8 | t[n++] << 16 | t[n++] << 24;
        t = e.chain_[0], n = e.chain_[1];
        var i = e.chain_[2],
            s = e.chain_[3],
            u = 0,
            u = t + (s ^ n & (i ^ s)) + r[0] + 3614090360 & 4294967295;
        t = n + (u << 7 & 4294967295 | u >>> 25), u = s + (i ^ t & (n ^ i)) + r[1] + 3905402710 & 4294967295, s = t + (u << 12 & 4294967295 | u >>> 20), u = i + (n ^ s & (t ^ n)) + r[2] + 606105819 & 4294967295, i = s + (u << 17 & 4294967295 | u >>> 15), u = n + (t ^ i & (s ^ t)) + r[3] + 3250441966 & 4294967295, n = i + (u << 22 & 4294967295 | u >>> 10), u = t + (s ^ n & (i ^ s)) + r[4] + 4118548399 & 4294967295, t = n + (u << 7 & 4294967295 | u >>> 25), u = s + (i ^ t & (n ^ i)) + r[5] + 1200080426 & 4294967295, s = t + (u << 12 & 4294967295 | u >>> 20), u = i + (n ^ s & (t ^ n)) + r[6] + 2821735955 & 4294967295, i = s + (u << 17 & 4294967295 | u >>> 15), u = n + (t ^ i & (s ^ t)) + r[7] + 4249261313 & 4294967295, n = i + (u << 22 & 4294967295 | u >>> 10), u = t + (s ^ n & (i ^ s)) + r[8] + 1770035416 & 4294967295, t = n + (u << 7 & 4294967295 | u >>> 25), u = s + (i ^ t & (n ^ i)) + r[9] + 2336552879 & 4294967295, s = t + (u << 12 & 4294967295 | u >>> 20), u = i + (n ^ s & (t ^ n)) + r[10] + 4294925233 & 4294967295, i = s + (u << 17 & 4294967295 | u >>> 15), u = n + (t ^ i & (s ^ t)) + r[11] + 2304563134 & 4294967295, n = i + (u << 22 & 4294967295 | u >>> 10), u = t + (s ^ n & (i ^ s)) + r[12] + 1804603682 & 4294967295, t = n + (u << 7 & 4294967295 | u >>> 25), u = s + (i ^ t & (n ^ i)) + r[13] + 4254626195 & 4294967295, s = t + (u << 12 & 4294967295 | u >>> 20), u = i + (n ^ s & (t ^ n)) + r[14] + 2792965006 & 4294967295, i = s + (u << 17 & 4294967295 | u >>> 15), u = n + (t ^ i & (s ^ t)) + r[15] + 1236535329 & 4294967295, n = i + (u << 22 & 4294967295 | u >>> 10), u = t + (i ^ s & (n ^ i)) + r[1] + 4129170786 & 4294967295, t = n + (u << 5 & 4294967295 | u >>> 27), u = s + (n ^ i & (t ^ n)) + r[6] + 3225465664 & 4294967295, s = t + (u << 9 & 4294967295 | u >>> 23), u = i + (t ^ n & (s ^ t)) + r[11] + 643717713 & 4294967295, i = s + (u << 14 & 4294967295 | u >>> 18), u = n + (s ^ t & (i ^ s)) + r[0] + 3921069994 & 4294967295, n = i + (u << 20 & 4294967295 | u >>> 12), u = t + (i ^ s & (n ^ i)) + r[5] + 3593408605 & 4294967295, t = n + (u << 5 & 4294967295 | u >>> 27), u = s + (n ^ i & (t ^ n)) + r[10] + 38016083 & 4294967295, s = t + (u << 9 & 4294967295 | u >>> 23), u = i + (t ^ n & (s ^ t)) + r[15] + 3634488961 & 4294967295, i = s + (u << 14 & 4294967295 | u >>> 18), u = n + (s ^ t & (i ^ s)) + r[4] + 3889429448 & 4294967295, n = i + (u << 20 & 4294967295 | u >>> 12), u = t + (i ^ s & (n ^ i)) + r[9] + 568446438 & 4294967295, t = n + (u << 5 & 4294967295 | u >>> 27), u = s + (n ^ i & (t ^ n)) + r[14] + 3275163606 & 4294967295, s = t + (u << 9 & 4294967295 | u >>> 23), u = i + (t ^ n & (s ^ t)) + r[3] + 4107603335 & 4294967295, i = s + (u << 14 & 4294967295 | u >>> 18), u = n + (s ^ t & (i ^ s)) + r[8] + 1163531501 & 4294967295, n = i + (u << 20 & 4294967295 | u >>> 12), u = t + (i ^ s & (n ^ i)) + r[13] + 2850285829 & 4294967295, t = n + (u << 5 & 4294967295 | u >>> 27), u = s + (n ^ i & (t ^ n)) + r[2] + 4243563512 & 4294967295, s = t + (u << 9 & 4294967295 | u >>> 23), u = i + (t ^ n & (s ^ t)) + r[7] + 1735328473 & 4294967295, i = s + (u << 14 & 4294967295 | u >>> 18), u = n + (s ^ t & (i ^ s)) + r[12] + 2368359562 & 4294967295, n = i + (u << 20 & 4294967295 | u >>> 12), u = t + (n ^ i ^ s) + r[5] + 4294588738 & 4294967295, t = n + (u << 4 & 4294967295 | u >>> 28), u = s + (t ^ n ^ i) + r[8] + 2272392833 & 4294967295, s = t + (u << 11 & 4294967295 | u >>> 21), u = i + (s ^ t ^ n) + r[11] + 1839030562 & 4294967295, i = s + (u << 16 & 4294967295 | u >>> 16), u = n + (i ^ s ^ t) + r[14] + 4259657740 & 4294967295, n = i + (u << 23 & 4294967295 | u >>> 9), u = t + (n ^ i ^ s) + r[1] + 2763975236 & 4294967295, t = n + (u << 4 & 4294967295 | u >>> 28), u = s + (t ^ n ^ i) + r[4] + 1272893353 & 4294967295, s = t + (u << 11 & 4294967295 | u >>> 21), u = i + (s ^ t ^ n) + r[7] + 4139469664 & 4294967295, i = s + (u << 16 & 4294967295 | u >>> 16), u = n + (i ^ s ^ t) + r[10] + 3200236656 & 4294967295, n = i + (u << 23 & 4294967295 | u >>> 9), u = t + (n ^ i ^ s) + r[13] + 681279174 & 4294967295, t = n + (u << 4 & 4294967295 | u >>> 28), u = s + (t ^ n ^ i) + r[0] + 3936430074 & 4294967295, s = t + (u << 11 & 4294967295 | u >>> 21), u = i + (s ^ t ^ n) + r[3] + 3572445317 & 4294967295, i = s + (u << 16 & 4294967295 | u >>> 16), u = n + (i ^ s ^ t) + r[6] + 76029189 & 4294967295, n = i + (u << 23 & 4294967295 | u >>> 9), u = t + (n ^ i ^ s) + r[9] + 3654602809 & 4294967295, t = n + (u << 4 & 4294967295 | u >>> 28), u = s + (t ^ n ^ i) + r[12] + 3873151461 & 4294967295, s = t + (u << 11 & 4294967295 | u >>> 21), u = i + (s ^ t ^ n) + r[15] + 530742520 & 4294967295, i = s + (u << 16 & 4294967295 | u >>> 16), u = n + (i ^ s ^ t) + r[2] + 3299628645 & 4294967295, n = i + (u << 23 & 4294967295 | u >>> 9), u = t + (i ^ (n | ~s)) + r[0] + 4096336452 & 4294967295, t = n + (u << 6 & 4294967295 | u >>> 26), u = s + (n ^ (t | ~i)) + r[7] + 1126891415 & 4294967295, s = t + (u << 10 & 4294967295 | u >>> 22), u = i + (t ^ (s | ~n)) + r[14] + 2878612391 & 4294967295, i = s + (u << 15 & 4294967295 | u >>> 17), u = n + (s ^ (i | ~t)) + r[5] + 4237533241 & 4294967295, n = i + (u << 21 & 4294967295 | u >>> 11), u = t + (i ^ (n | ~s)) + r[12] + 1700485571 & 4294967295, t = n + (u << 6 & 4294967295 | u >>> 26), u = s + (n ^ (t | ~i)) + r[3] + 2399980690 & 4294967295, s = t + (u << 10 & 4294967295 | u >>> 22), u = i + (t ^ (s | ~n)) + r[10] + 4293915773 & 4294967295, i = s + (u << 15 & 4294967295 | u >>> 17), u = n + (s ^ (i | ~t)) + r[1] + 2240044497 & 4294967295, n = i + (u << 21 & 4294967295 | u >>> 11), u = t + (i ^ (n | ~s)) + r[8] + 1873313359 & 4294967295, t = n + (u << 6 & 4294967295 | u >>> 26), u = s + (n ^ (t | ~i)) + r[15] + 4264355552 & 4294967295, s = t + (u << 10 & 4294967295 | u >>> 22), u = i + (t ^ (s | ~n)) +
            r[6] + 2734768916 & 4294967295, i = s + (u << 15 & 4294967295 | u >>> 17), u = n + (s ^ (i | ~t)) + r[13] + 1309151649 & 4294967295, n = i + (u << 21 & 4294967295 | u >>> 11), u = t + (i ^ (n | ~s)) + r[4] + 4149444226 & 4294967295, t = n + (u << 6 & 4294967295 | u >>> 26), u = s + (n ^ (t | ~i)) + r[11] + 3174756917 & 4294967295, s = t + (u << 10 & 4294967295 | u >>> 22), u = i + (t ^ (s | ~n)) + r[2] + 718787259 & 4294967295, i = s + (u << 15 & 4294967295 | u >>> 17), u = n + (s ^ (i | ~t)) + r[9] + 3951481745 & 4294967295, e.chain_[0] = e.chain_[0] + t & 4294967295, e.chain_[1] = e.chain_[1] + (i + (u << 21 & 4294967295 | u >>> 11)) & 4294967295, e.chain_[2] = e.chain_[2] + i & 4294967295, e.chain_[3] = e.chain_[3] + s & 4294967295
    };
    zr.prototype.update = function(e, t) {
        void 0 === t && (t = e.length);
        for (var n = t - this.blockSize, r = this.block_, i = this.blockLength_, s = 0; s < t;) {
            if (0 == i)
                for (; s <= n;) Wr(this, e, s), s += this.blockSize;
            if (o(e)) {
                for (; s < t;)
                    if (r[i++] = e.charCodeAt(s++), i == this.blockSize) {
                        Wr(this, r), i = 0;
                        break
                    }
            } else
                for (; s < t;)
                    if (r[i++] = e[s++], i == this.blockSize) {
                        Wr(this, r), i = 0;
                        break
                    }
        }
        this.blockLength_ = i, this.totalLength_ += t
    };
    var Xr = function() {
        ir.call(this), this.callback_ = this.element_ = null, this.md5_ = new zr
    };
    m(Xr, ir);
    var Vr = function(e, t, n, r, i) {
        e.unwatch(), e.element_ = t, e.callback_ = i, e.listen(t, "keyup", c(e.onChanged_, e, n, r))
    };
    Xr.prototype.unwatch = function() {
        this.element_ && this.callback_ && (this.removeAll(), this.callback_ = this.element_ = null)
    }, Xr.prototype.onChanged_ = function(e, t) {
        var n;
        n = (n = this.element_.value) ? n.replace(/[\s\xa0]+/g, "").toLowerCase() : "", this.md5_.reset(), this.md5_.update(n + "." + t), n = this.md5_;
        var r = Array((56 > n.blockLength_ ? n.blockSize : 2 * n.blockSize) - n.blockLength_);
        r[0] = 128;
        for (var i = 1; i < r.length - 8; ++i) r[i] = 0;
        for (var s = 8 * n.totalLength_, i = r.length - 8; i < r.length; ++i) r[i] = s & 255, s /= 256;
        n.update(r), r = Array(16);
        for (i = s = 0; 4 > i; ++i)
            for (var o = 0; 32 > o; o += 8) r[s++] = n.chain_[i] >>> o & 255;
        Nt(r).toLowerCase() == e.toLowerCase() && this.callback_()
    }, Xr.prototype.disposeInternal = function() {
        this.element_ = null, Xr.superClass_.disposeInternal.call(this)
    };
    var $r = function(e, t, n) {
        this.adObject_ = e, this.captchaImageUrl_ = t, this.opt_successCallback_ = n || null, Jr(this)
    };
    m($r, At);
    var Jr = function(e) {
        var t = new fr;
        Ot(e, h(Mt, t)), cr(t, "recaptcha_challenge_image", e.captchaImageUrl_), cr(t, "recaptcha_ad_image", e.adObject_.imageAdUrl);
        var n = {};
        $n(t, "load", c(function(e, t) {
            e[t.target.id] = t.target
        }, e, n)), $n(t, "complete", c(e.handleImagesLoaded_, e, n)), t.start()
    };
    $r.prototype.handleImagesLoaded_ = function(e) {
        e = new yr(e.recaptcha_challenge_image, e.recaptcha_ad_image), Ot(this, h(Mt, e));
        var t = Pt(document, "recaptcha_image");
        qt(t), dr(e, t), e.adImage_ && br(e.adImage_) && (qr(this.adObject_.delayedImpressionUrl), e = new Xr, Ot(this, h(Mt, e)), Vr(e, Pt(document, "recaptcha_response_field"), this.adObject_.hashedAnswer, this.adObject_.salt, c(function(e, t) {
            e.unwatch(), qr(t)
        }, this, e, this.adObject_.engagementUrl)), this.opt_successCallback_ && this.opt_successCallback_("04" + this.adObject_.token))
    };
    var Kr = y;
    v("RecaptchaStr", Kr);
    var Qr = t.RecaptchaOptions;
    v("RecaptchaOptions", Qr);
    var Gr = {
        tabindex: 0,
        theme: "red",
        callback: null,
        lang: null,
        custom_theme_widget: null,
        custom_translations: null
    };
    v("RecaptchaDefaultOptions", Gr);
    var Yr = {
        widget: null,
        timer_id: -1,
        style_set: !1,
        theme: null,
        type: "image",
        ajax_verify_cb: null,
        th1: null,
        th2: null,
        th3: null,
        element: "",
        ad_captcha_plugin: null,
        reload_timeout: -1,
        force_reload: !1,
        $: function(e) {
            return "string" == typeof e ? document.getElementById(e) : e
        },
        attachEvent: function(e, t, n) {
            e && e.addEventListener ? e.addEventListener(t, n, !1) : e && e.attachEvent && e.attachEvent("on" + t, n)
        },
        create: function(e, t, n) {
            Yr.destroy(), t && (Yr.widget = Yr.$(t), Yr.element = t), Yr._init_options(n), Yr._call_challenge(e)
        },
        destroy: function() {
            var e = Yr.$("recaptcha_challenge_field");
            e && e.parentNode.removeChild(e), -1 != Yr.timer_id && clearInterval(Yr.timer_id), Yr.timer_id = -1;
            if (e = Yr.$("recaptcha_image")) e.innerHTML = "";
            Yr.update_widget(), Yr.widget && ("custom" != Yr.theme ? Yr.widget.innerHTML = "" : Yr.widget.style.display = "none", Yr.widget = null)
        },
        focus_response_field: function() {
            var e = Yr.$("recaptcha_response_field");
            e && e.focus()
        },
        get_challenge: function() {
            return "undefined" == typeof RecaptchaState ? null : RecaptchaState.challenge
        },
        get_response: function() {
            var e = Yr.$("recaptcha_response_field");
            return e ? e.value : null
        },
        ajax_verify: function(e) {
            Yr.ajax_verify_cb = e, e = Yr.get_challenge() || "";
            var t = Yr.get_response() || "";
            e = Yr._get_api_server() + "/ajaxverify?c=" + encodeURIComponent(e) + "&response=" + encodeURIComponent(t), Yr._add_script(e)
        },
        _ajax_verify_callback: function(e) {
            Yr.ajax_verify_cb(e)
        },
        _get_overridable_url: function(e) {
            var t = window.location.protocol;
            if ("undefined" != typeof _RecaptchaOverrideApiServer) e = _RecaptchaOverrideApiServer;
            else if ("undefined" != typeof RecaptchaState && "string" == typeof RecaptchaState.server && 0 < RecaptchaState.server.length) return RecaptchaState.server.replace(/\/+$/, "");
            return t + "//" + e
        },
        _get_api_server: function() {
            return Yr._get_overridable_url("www.google.com/recaptcha/api")
        },
        _get_static_url_root: function() {
            return Yr._get_overridable_url("www.gstatic.com/recaptcha/api")
        },
        _call_challenge: function(e) {
            e = Yr._get_api_server() + "/challenge?k=" + e + "&ajax=1&cachestop=" + Math.random(), Yr.getLang_() && (e += "&lang=" + Yr.getLang_()), "undefined" != typeof Qr.extra_challenge_params && (e += "&" + Qr.extra_challenge_params), Yr._add_script(e)
        },
        _add_script: function(e) {
            var t = document.createElement("script");
            t.type = "text/javascript", t.src = e, Yr._get_script_area().appendChild(t)
        },
        _get_script_area: function() {
            var e = document.getElementsByTagName("head");
            return e = !e || 1 > e.length ? document.body : e[0]
        },
        _hash_merge: function(e) {
            for (var t = {}, n = 0; n < e.length; n++)
                for (var r in e[n]) t[r] = e[n][r];
            return t
        },
        _init_options: function(e) {
            Qr = Yr._hash_merge([Gr, e || {}])
        },
        challenge_callback_internal: function() {
            Yr.update_widget(), Yr._reset_timer(), Kr = Yr._hash_merge([y, L[Yr.getLang_()] || {}, Qr.custom_translations || {}]), window.addEventListener && window.addEventListener("unload", function() {
                Yr.destroy()
            }, !1), Yr._is_ie() && window.attachEvent && window.attachEvent("onbeforeunload", function() {});
            if (0 < navigator.userAgent.indexOf("KHTML")) {
                var e = document.createElement("iframe");
                e.src = "about:blank", e.style.height = "0px", e.style.width = "0px", e.style.visibility = "hidden", e.style.border = "none", e.appendChild(document.createTextNode("This frame prevents back/forward cache problems in Safari.")), document.body.appendChild(e)
            }
            Yr._finish_widget()
        },
        _add_css: function(e) {
            if (-1 != navigator.appVersion.indexOf("MSIE 5")) document.write('<style type="text/css">' + e + "</style>");
            else {
                var t = document.createElement("style");
                t.type = "text/css", t.styleSheet ? t.styleSheet.cssText = e : t.appendChild(document.createTextNode(e)), Yr._get_script_area().appendChild(t)
            }
        },
        _set_style: function(e) {
            Yr.style_set || (Yr.style_set = !0, Yr._add_css(e + "\n\n.recaptcha_is_showing_audio .recaptcha_only_if_image,.recaptcha_isnot_showing_audio .recaptcha_only_if_audio,.recaptcha_had_incorrect_sol .recaptcha_only_if_no_incorrect_sol,.recaptcha_nothad_incorrect_sol .recaptcha_only_if_incorrect_sol{display:none !important}"))
        },
        _init_builtin_theme: function() {
            var e = Yr.$,
                t = Yr._get_static_url_root(),
                n = g.VertCss,
                r = g.VertHtml,
                i = t + "/img/" + Yr.theme,
                s = "gif",
                t = Yr.theme;
            "clean" == t && (n = g.CleanCss, r = g.CleanHtml, s = "png"), n = n.replace(/IMGROOT/g, i), Yr._set_style(n), Yr.update_widget(), Yr.widget.innerHTML = '<div id="recaptcha_area">' + r + "</div>", n = Yr.getLang_(), e("recaptcha_privacy") && null != n && "en" == n.substring(0, 2).toLowerCase() && null != Kr.privacy_and_terms && 0 < Kr.privacy_and_terms.length && (n = document.createElement("a"), n.href = "http://www.google.com/intl/en/policies/", n.target = "_blank", n.innerHTML = Kr.privacy_and_terms, e("recaptcha_privacy").appendChild(n)), n = function(t, n, r, o) {
                var u = e(t);
                u.src = i + "/" + n + "." + s, n = Kr[r], u.alt = n, t = e(t + "_btn"), t.title = n, Yr.attachEvent(t, "click", o)
            }, n("recaptcha_reload", "refresh", "refresh_btn", function() {
                Yr.reload_internal("r")
            }), n("recaptcha_switch_audio", "audio", "audio_challenge", function() {
                Yr.switch_type("audio")
            }), n("recaptcha_switch_img", "text", "visual_challenge", function() {
                Yr.switch_type("image")
            }), n("recaptcha_whatsthis", "help", "help_btn", Yr.showhelp), "clean" == t && (e("recaptcha_logo").src = i + "/logo." + s), e("recaptcha_table").className = "recaptchatable recaptcha_theme_" + Yr.theme, t = function(t, n) {
                var r = e(t);
                r && (RecaptchaState.rtl && "span" == r.tagName.toLowerCase() && (r.dir = "rtl"), r.appendChild(document.createTextNode(Kr[n])))
            }, t("recaptcha_instructions_image", "instructions_visual"), t("recaptcha_instructions_audio", "instructions_audio"), t("recaptcha_instructions_error", "incorrect_try_again"), e("recaptcha_instructions_image") || e("recaptcha_instructions_audio") || (t = "audio" == Yr.type ? Kr.instructions_audio : Kr.instructions_visual, t = t.replace(/:$/, ""), e("recaptcha_response_field").setAttribute("placeholder", t))
        },
        _finish_widget: function() {
            var e = Yr.$,
                t = Qr,
                n = t.theme;
            n in {
                blackglass: 1,
                clean: 1,
                custom: 1,
                red: 1,
                white: 1
            } || (n = "red"), Yr.theme || (Yr.theme = n), "custom" != Yr.theme ? Yr._init_builtin_theme() : Yr._set_style(""), n = document.createElement("span"), n.id = "recaptcha_challenge_field_holder", n.style.display = "none", e("recaptcha_response_field").parentNode.insertBefore(n, e("recaptcha_response_field")), e("recaptcha_response_field").setAttribute("autocomplete", "off"), e("recaptcha_image").style.width = "300px", e("recaptcha_image").style.height = "57px", e("recaptcha_challenge_field_holder").innerHTML = '<input type="hidden" name="recaptcha_challenge_field" id="recaptcha_challenge_field" value=""/>', Yr.th_init(), Yr.should_focus = !1, Yr.th3 || Yr.force_reload ? (Yr._set_challenge(RecaptchaState.challenge, "image", !0), setTimeout(function() {
                Yr.reload_internal("i")
            }, 100)) : Yr._set_challenge(RecaptchaState.challenge, "image", !1), Yr.updateTabIndexes_(), Yr.update_widget(), Yr.widget && (Yr.widget.style.display = ""), t.callback && t.callback()
        },
        updateTabIndexes_: function() {
            var e = Yr.$,
                t = Qr;
            t.tabindex && (t = t.tabindex, e("recaptcha_response_field").tabIndex = t++, "audio" == Yr.type && e("recaptcha_audio_play_again") && (e("recaptcha_audio_play_again").tabIndex = t++, e("recaptcha_audio_download"), e("recaptcha_audio_download").tabIndex = t++), "custom" != Yr.theme && (e("recaptcha_reload_btn").tabIndex = t++, e("recaptcha_switch_audio_btn").tabIndex = t++, e("recaptcha_switch_img_btn").tabIndex = t++, e("recaptcha_whatsthis_btn").tabIndex = t, e("recaptcha_privacy").tabIndex = t++))
        },
        switch_type: function(e) {
            if (!((new Date).getTime() < Yr.reload_timeout) && (Yr.type = e, Yr.reload_internal("audio" == Yr.type ? "a" : "v"), "custom" != Yr.theme)) {
                e = Yr.$;
                var t = "audio" == Yr.type ? Kr.instructions_audio : Kr.instructions_visual,
                    t = t.replace(/:$/, "");
                e("recaptcha_response_field").setAttribute("placeholder", t)
            }
        },
        reload: function() {
            Yr.reload_internal("r")
        },
        reload_internal: function(e) {
            var t = Qr,
                n = RecaptchaState,
                r = (new Date).getTime();
            r < Yr.reload_timeout || (Yr.reload_timeout = r + 1e3, "undefined" == typeof e && (e = "r"), r = Yr._get_api_server() + "/reload?c=" + n.challenge + "&k=" + n.site + "&reason=" + e + "&type=" + Yr.type, Yr.getLang_() && (r += "&lang=" + Yr.getLang_()), "undefined" != typeof t.extra_challenge_params && (r += "&" + t.extra_challenge_params), Yr.th_callback_invoke(), Yr.th1 && (r += "&th=" + Yr.th1, Yr.th1 = ""), "audio" == Yr.type && (r = t.audio_beta_12_08 ? r + "&audio_beta_12_08=1" : r + "&new_audio_default=1"), Yr.should_focus = "t" != e && "i" != e, Yr._add_script(r), Mt(Yr.ad_captcha_plugin), n.publisher_id = null)
        },
        th_callback_invoke: function() {
            if (Yr.th3) try {
                var e = Yr.th3.exec();
                e && 1600 > e.length && (Yr.th1 = e)
            } catch (t) {
                Yr.th1 = ""
            }
        },
        finish_reload: function(e, t, n, r) {
            RecaptchaState.payload_url = n, RecaptchaState.is_incorrect = !1, RecaptchaState.publisher_id = r, Yr._set_challenge(e, t, !1), Yr.updateTabIndexes_()
        },
        _set_challenge: function(e, t, n) {
            "image" == t && RecaptchaState.publisher_id ? Rr(e, function() {
                Yr._set_challenge_internal(e, t, n)
            }) : Yr._set_challenge_internal(e, t, n)
        },
        _set_challenge_internal: function(e, t, n) {
            var r = Yr.$,
                i = RecaptchaState;
            i.challenge = e, Yr.type = t, r("recaptcha_challenge_field").value = i.challenge, n || ("audio" == t ? (r("recaptcha_image").innerHTML = Yr.getAudioCaptchaHtml(), Yr._loop_playback()) : "image" == t && (e = i.payload_url, e || (e = Yr._get_api_server() + "/image?c=" + i.challenge, Yr.th_callback_invoke(), Yr.th1 && (e += "&th=" + Yr.th1, Yr.th1 = "")), Fr(r("recaptcha_widget_div")), Br() ? Yr.ad_captcha_plugin = new $r(ot(), e, function(e) {
                RecaptchaState.challenge = e, r("recaptcha_challenge_field").value = e
            }) : r("recaptcha_image").innerHTML = '<img id="recaptcha_challenge_image" alt="' + Kr.image_alt_text + '" height="57" width="300" src="' + e + '" />', jr())), Yr._css_toggle("recaptcha_had_incorrect_sol", "recaptcha_nothad_incorrect_sol", i.is_incorrect), Yr._css_toggle("recaptcha_is_showing_audio", "recaptcha_isnot_showing_audio", "audio" == t), Yr._clear_input(), Yr.should_focus && Yr.focus_response_field(), Yr._reset_timer()
        },
        _reset_timer: function() {
            clearInterval(Yr.timer_id);
            var e = Math.max(1e3 * (RecaptchaState.timeout - 60), 6e4);
            return Yr.timer_id = setInterval(function() {
                Yr.reload_internal("t")
            }, e), e
        },
        showhelp: function() {
            window.open(Yr._get_help_link(), "recaptcha_popup", "width=460,height=580,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes,resizable=yes")
        },
        _clear_input: function() {
            Yr.$("recaptcha_response_field").value = ""
        },
        _displayerror: function(e) {
            var t = Yr.$;
            t("recaptcha_image").innerHTML = "", t("recaptcha_image").appendChild(document.createTextNode(e))
        },
        reloaderror: function(e) {
            Yr._displayerror(e)
        },
        _is_ie: function() {
            return 0 < navigator.userAgent.indexOf("MSIE") && !window.opera
        },
        _css_toggle: function(e, t, n) {
            Yr.update_widget();
            var r = Yr.widget;
            r || (r = document.body);
            var i = r.className,
                i = i.replace(new RegExp("(^|\\s+)" + e + "(\\s+|$)"), " "),
                i = i.replace(new RegExp("(^|\\s+)" + t + "(\\s+|$)"), " ");
            r.className = i + (" " + (n ? e : t))
        },
        _get_help_link: function() {
            var e = Yr._get_api_server().replace(/\/[a-zA-Z0-9]+\/?$/, "/help"),
                e = e + ("?c=" + RecaptchaState.challenge);
            return Yr.getLang_() && (e += "&hl=" + Yr.getLang_()), e
        },
        playAgain: function() {
            Yr.$("recaptcha_image").innerHTML = Yr.getAudioCaptchaHtml(), Yr._loop_playback()
        },
        _loop_playback: function() {
            var e = Yr.$("recaptcha_audio_play_again");
            e && Yr.attachEvent(e, "click", function() {
                return Yr.playAgain(), !1
            })
        },
        getAudioCaptchaHtml: function() {
            var e = RecaptchaState.payload_url;
            e || (e = Yr._get_api_server() + "/audio.mp3?c=" + RecaptchaState.challenge, Yr.th_callback_invoke(), Yr.th1 && (e += "&th=" + Yr.th1, Yr.th1 = ""));
            var t = Yr._get_api_server() + "/swf/audiocaptcha.swf?v2",
                t = Yr._is_ie() ? '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" id="audiocaptcha" width="0" height="0" codebase="https://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab"><param name="movie" value="' + t + '" /><param name="quality" value="high" /><param name="bgcolor" value="#869ca7" /><param name="allowScriptAccess" value="always" /></object><br/>' : '<embed src="' + t + '" quality="high" bgcolor="#869ca7" width="0" height="0" name="audiocaptcha" align="middle" play="true" loop="false" quality="high" allowScriptAccess="always" type="application/x-shockwave-flash" pluginspage="http://www.adobe.com/go/getflashplayer" /></embed>',
                n = "";
            return Yr.checkFlashVer() && (n = "<br/>" + Yr.getSpan_('<a id="recaptcha_audio_play_again" class="recaptcha_audio_cant_hear_link">' + Kr.play_again + "</a>")), n += "<br/>" + Yr.getSpan_('<a id="recaptcha_audio_download" class="recaptcha_audio_cant_hear_link" target="_blank" href="' + e + '">' + Kr.cant_hear_this + "</a>"), t + n
        },
        getSpan_: function(e) {
            return "<span" + (RecaptchaState && RecaptchaState.rtl ? ' dir="rtl"' : "") + ">" + e + "</span>"
        },
        gethttpwavurl: function() {
            if ("audio" != Yr.type) return "";
            var e = RecaptchaState.payload_url;
            return e || (e = Yr._get_api_server() + "/image?c=" + RecaptchaState.challenge, Yr.th_callback_invoke(), Yr.th1 && (e += "&th=" + Yr.th1, Yr.th1 = "")), e
        },
        checkFlashVer: function() {
            var e = -1 != navigator.appVersion.indexOf("MSIE"),
                t = -1 != navigator.appVersion.toLowerCase().indexOf("win"),
                n = -1 != navigator.userAgent.indexOf("Opera"),
                r = -1;
            if (null != navigator.plugins && 0 < navigator.plugins.length) {
                if (navigator.plugins["Shockwave Flash 2.0"] || navigator.plugins["Shockwave Flash"]) r = navigator.plugins["Shockwave Flash" + (navigator.plugins["Shockwave Flash 2.0"] ? " 2.0" : "")].description.split(" ")[2].split(".")[0]
            } else if (e && t && !n) try {
                r = (new ActiveXObject("ShockwaveFlash.ShockwaveFlash.7")).GetVariable("$version").split(" ")[1].split(",")[0]
            } catch (i) {}
            return 9 <= r
        },
        getLang_: function() {
            return Qr.lang ? Qr.lang : "undefined" != typeof RecaptchaState && RecaptchaState.lang ? RecaptchaState.lang : null
        },
        challenge_callback: function() {
            Yr.force_reload = !!RecaptchaState.force_reload;
            if (RecaptchaState.t3) {
                var e = RecaptchaState.t1 ? Tt(Lt(RecaptchaState.t1)) : "",
                    n = RecaptchaState.t2 ? Tt(Lt(RecaptchaState.t2)) : "",
                    r = RecaptchaState.t3 ? Tt(Lt(RecaptchaState.t3)) : "";
                Yr.th2 = r;
                if (e) n = kn(e), wn(n, Yr.challenge_callback_internal, null, void 0), wn(n, null, Yr.challenge_callback_internal, void 0);
                else {
                    if (t.execScript) t.execScript(n, "JavaScript");
                    else {
                        if (!t.eval) throw Error("goog.globalEval not available");
                        null == d && (t.eval("var _et_ = 1;"), "undefined" != typeof t._et_ ? (delete t._et_, d = !0) : d = !1), d ? t.eval(n) : (e = t.document, r = e.createElement("script"), r.type = "text/javascript", r.defer = !1, r.appendChild(e.createTextNode(n)), e.body.appendChild(r), e.body.removeChild(r))
                    }
                    Yr.challenge_callback_internal()
                }
            } else Yr.challenge_callback_internal()
        },
        th_init: function() {
            try {
                t.thintinel && t.thintinel.th && (Yr.th3 = new t.thintinel.th(Yr.th2), Yr.th2 = "")
            } catch (e) {}
        },
        update_widget: function() {
            Yr.element && (Yr.widget = Yr.$(Yr.element))
        }
    };
    v("Recaptcha", Yr)
}(),
function(e, t) {
    "use strict";
    var n = require("ankama.widget");
    n.widget("ankama.ak_field_recaptcha", n.ankama.widget, {
        options: {
            initSelector: ".ak-recaptcha"
        },
        _create: function() {
            var t = this,
                n = e.location.pathname.substr(1, 2);
            n == "it" && (n = "en"), t.element.on("recaptcha_reload", function() {
                Recaptcha.reload(), Recaptcha._clear_input()
            }), Recaptcha.create("6LcC2_USAAAAABTeDJnHglk2qKomDK03fCh18fLu", "recaptcha_div", {
                theme: "custom",
                lang: n,
                custom_theme_widget: "recaptcha_widget"
            }), this.element.find(".ak-recaptcha-reload").on("click", function() {
                t._onCaptchaChange()
            })
        },
        _onCaptchaChange: function() {
            n("#recaptcha_response_field").val("")
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_field_recaptcha.prototype.enhanceWithin(e.target)
    }), n.widget("ankama.ak_field_recaptchav2", n.ankama.widget, {
        options: {
            initSelector: ".ak-recaptchav2",
            sitekey: ""
        },
        _create: function() {
            var e = this,
                t = function() {
                    grecaptcha.render(n(".ak-recaptcha-container .ak-g-recaptcha")[0], {
                        sitekey: e.options.sitekey,
                        callback: function(t) {
                            e.recaptchaCallBack(t)
                        },
                        "expired-callback": function() {
                            e.recaptchaExpiredCallBack()
                        }
                    })
                },
                r = 0,
                i, s = function() {
                    typeof grecaptcha == "undefined" ? (r++, r > 10 ? clearTimeout(i) : i = setTimeout(s, 500)) : (typeof i != "undefined" && clearTimeout(i), t())
                };
            s()
        },
        recaptchaCallBack: function(e) {
            var t = {
                sAction: "test",
                sTestField: "g-recaptcha-response",
                "g-recaptcha-response": e
            };
            this._recaptchaTest(t)
        },
        recaptchaExpiredCallBack: function() {
            var e = {
                sAction: "test",
                sTestField: "g-recaptcha-response",
                "g-recaptcha-response": ""
            };
            this._recaptchaTest(e)
        },
        _recaptchaTest: function(e) {
            var t = {
                type: "post",
                dataType: "json",
                data: e
            };
            n.ajax(t)
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_field_recaptchav2.prototype.enhanceWithin(e.target)
    })
}(this),
function(e, t) {
    "use strict";

    function r(t) {
        t = t ? t : e.event;
        var n = !1;
        t.which ? n = t.which : t.keyCode && (n = t.keyCode);
        var r = !1;
        return t.shiftKey ? r = t.shiftKey : t.modifiers && (r = !!(t.modifiers & 4)), n >= 97 && n <= 122 && r || n >= 65 && n <= 90 && !r ? !0 : !1
    }
    var n = require("ankama.widget");
    n.widget("ankama.ak_field_password", n.ankama.widget, {
        options: {
            initSelector: ".ak-field-password"
        },
        _create: function() {
            this.element.on("keypress", n.proxy(this._onKeyPress, this))
        },
        _hasShownTooltip: !1,
        _onKeyPress: function(e) {
            r(e) ? (this._hasShownTooltip = !0, n(e.currentTarget).trigger("capslockpassshow")) : this._hasShownTooltip && (this._hasShownTooltip = !1, n(e.currentTarget).trigger("capslockpasshide"))
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_field_password.prototype.enhanceWithin(e.target)
    })
}(this),
function(e, t) {
    "use strict";
    var n = require("ankama.widget"),
        r = require("lodash"),
        i = require("underscore.string"),
        s = require("jquery.mailcheck");
    n = s[0];
    var o = s[1];
    n.widget("ankama.ak_field_email", n.ankama.widget, {
        options: {
            initSelector: ".ak-field-email",
            didyoumeanText: "Did you mean <b>%s</b> ?",
            didyoumeanClass: "ak-field-email-didyoumean",
            parentContainer: ".form-group"
        },
        _create: function() {
            var e = this,
                t = ["ankama-animations.com", "ankama-canada.com", "ankama-convention.com", "ankama-edition.com", "ankama-editions.com", "ankama-events.com", "ankama-games.com", "ankama-group.com", "ankama-groupe.com", "ankama-news.com", "ankama-presse.com", "ankama-referencement.com", "ankama-shop.com", "ankama-studio.com", "ankama-web.com", "ankama.com", "ankama.fr", "ankamafanfest.com", "ankamagames.com", "ankamatvsales.com", "ankamaweb.com", "artyslot.com", "bonzome.com", "boufbowl.com", "callofcookie-thegame.com", "devdofus.com", "dofus-arena.com", "dofus-arena.fr", "dofus-la-serie.com", "dofus-le-film.com", "dofus-shop.com", "dofus-tv-show.com", "dofus.cn", "dofus.co.uk", "dofus.com", "dofus.de", "dofus.es", "dofus.fr", "dofus.jp", "dofus.mobi", "dofus.nl", "dofus2.com", "dofus-battles.com", "fressball.com", "flyn-devblog.com", "gobbowl.com", "heyheyhey.fr", "hotmail.com", "ig-magazine.com", "jalabol.com", "krosmoz.com", "krosmaster.com", "label619.com", "manga-dofus.com", "mangadofus.com", "maxi-mini.fr", "miniwakfumag.com", "mundial-musique.com", "mutafukaz-shop.com", "mutafukaz.com", "my-chacha.com", "paypal.com", "slage.fr", "tofutofu-game.com", "wakfu.biz", "wakfu.cn", "wakfu.co.uk", "wakfu.com", "wakfu.de", "wakfu.es", "wakfu.eu", "wakfu.fr", "wakfu.info", "wakfu.it", "wakfu.jp", "wakfu.mobi", "wakfu.net", "wakfu.nl", "wakfu.ru", "wakfu.asia"];
            t = r.union(t, o.defaultDomains), e.element.parent(e.options.parentContainer).find("." + e.options.didyoumeanClass) && e.element.parent(e.options.parentContainer).append('<p class="' + e.options.didyoumeanClass + ' hide"></p>');
            var s = e.element.parent(e.options.parentContainer).find("." + e.options.didyoumeanClass);
            n(e.element).on("blur", function() {
                n(e.element).mailcheck({
                    domains: t,
                    suggested: function(t, r) {
                        s.off("click"), s.data("suggest", r.full), s.html(i.sprintf(e.options.didyoumeanText, r.full)), s.removeClass("hide"), s.one("click", function(t) {
                            e.element.val(n(this).data("suggest")), s.addClass("hide")
                        })
                    },
                    empty: function(e) {
                        s.addClass("hide")
                    }
                })
            })
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_field_email.prototype.enhanceWithin(e.target)
    })
}(this),
function(e, t) {
    var n = require("ankama.widget"),
        r = require("lodash");
    require("jquery.pjax"), n.widget("ankama.ak_quizform", n.ankama.widget, {
        options: {
            initSelector: ".ak-quizform"
        },
        _create: function() {
            this._superApply(arguments);
            var e = this;
            n(e.element).find('input[type="submit"]').attr("disabled", "1").addClass("disabled"), n(e.element).on("click", function(t) {
                e._checkAnswers() ? n(e.element).find('input[type="submit"]').removeAttr("disabled").removeClass("disabled") : n(e.element).find('input[type="submit"]').attr("disabled", "1").addClass("disabled")
            }), n(e.element).hasClass("ak-not-logged") && n(e.element).on("click", function(t) {
                t.preventDefault(), e._login(t)
            })
        },
        _login: function(e) {
            n.openLoginModal(e)
        },
        _checkAnswers: function() {
            var e = this,
                t = n(e.element).serializeArray(),
                r = [],
                i = n(e.element).find('input[type="radio"]');
            n.each(i, function(e, t) {
                n.inArray(n(t).attr("name"), r) == -1 && r.push(n(t).attr("name"))
            });
            var s = 0;
            for (var o in t) n.inArray(t[o].name, r) > -1 && s++;
            return s != r.length ? !1 : !0
        },
        _submit: function() {
            var e = this;
            e._checkAnswers() && n.ajax({
                type: "post",
                url: n(e.element).attr("action"),
                complete: function(t) {
                    n(e.options.initSelector).html(t.responseText)
                },
                container: e.options.initSelector,
                data: n(e.element).serializeArray()
            })
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_quizform.prototype.enhanceWithin(e.target)
    })
}(this),
function(e, t) {
    var n = require("ankama.widget"),
        r = require("lodash"),
        i = require("jquery.uri");
    require("jquery.loadmask"), n.widget("ankama.ak_map", n.ankama.widget, {
        options: {
            initSelector: ".ak-map-widget",
            mapSelector: ".ak-map",
            locationsListSelector: ".ak-map-locations",
            filtersSelector: ".ak-map-filters",
            country: "",
            maxAddress: 20,
            gmap: {
                markers: [],
                markerConfig: {},
                clusterConfig: {}
            }
        },
        located: !1,
        currentLat: 46,
        currentLng: 2,
        ajaxTimeout: null,
        boundRequest: !0,
        bRequest: !1,
        oCacheParams: {},
        oCluster: null,
        _create: function() {
            var e = this,
                t = e.option("sCountry");
            e.createMap(n(e.option("mapSelector")), t), navigator.geolocation && navigator.geolocation.getCurrentPosition(function(t) {
                e.located = !0, e.currentLat = t.coords.latitude, e.currentLng = t.coords.longitude, map.setCenter(new google.maps.LatLng(e.currentLat, e.currentLng)), map.setZoom(10)
            }), n(e.option("filtersSelector") + " form").bind("submit", function(t) {
                t.preventDefault();
                var i = n(this).serializeArray(),
                    s = r.result(r.find(i, {
                        name: "ADDRESS"
                    }), "value");
                s ? e.setCenterFromAddress(s) : e.filterMarkers()
            }), n(e.option("filtersSelector") + " form select").bind("change", function(e) {
                var t = n(this).closest("form");
                n('input[name="ADDRESS"]', t).val(""), t.submit()
            }), n(".btn-search").bind("click", function() {
                var t = n(this).closest("form"),
                    r = n('input[name="ADDRESS"]', t).val();
                r && e.setCenterFromAddress(r)
            })
        },
        createMap: function() {
            var r = this,
                i = r.option("sCountry");
            r.geocoder = new google.maps.Geocoder, map = new google.maps.Map(n(r.option("mapSelector"))[0], {
                center: new google.maps.LatLng(r.currentLat, r.currentLng),
                zoom: 5,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            }), i != t && i && r.geocoder.geocode({
                address: i
            }, function(e, t) {
                t == google.maps.GeocoderStatus.OK && map.setCenter(e[0].geometry.location)
            }), google.maps.event.addListener(map, "idle", function() {
                r.ajaxTimeout && (r.ajaxTimeout = e.clearTimeout(r.ajaxTimeout));
                if (!r.boundRequest) return r.boundRequest = !1, !1;
                r.doBoundsRequest()
            }), r.initMarkers()
        },
        setCenterFromAddress: function(e) {
            var t = this;
            t.geocoder.geocode({
                address: e
            }, function(e, t) {
                t == google.maps.GeocoderStatus.OK && (map.setCenter(e[0].geometry.location), map.setZoom(12))
            })
        },
        _createOneMarker: function(e) {
            var t = this,
                n = new google.maps.LatLng(e.location[0], e.location[1]),
                i = r.defaults(t.option("gmap.markerConfig"), {
                    title: e.name,
                    url: e.url,
                    position: n,
                    map_location_id: e.map_location_id,
                    map: map,
                    labelVisible: !0,
                    flat: !0
                });
            return new google.maps.Marker(i)
        },
        initMarkers: function() {
            var e = this,
                t = e.option("gmap.markers");
            if (!t.length) return !1;
            r.forEach(t, function(n, r) {
                t[r] = e._createOneMarker(n), google.maps.event.addListener(t[r], "click", function() {
                    e._markerClick.apply(e, [this])
                })
            });
            var n = typeof sCluster != "undefined" ? sCluster : "marker_cluster",
                i = r.defaults(e.option("gmap.clusterConfig"), {
                    maxZoom: 25
                });
            e.oCluster = new MarkerClusterer(map, t, i), t.length == 1 && map.setCenter(t[0].position)
        },
        _markerClick: function(e) {
            var t = this,
                r = n(t.option("locationsListSelector")),
                i = n('.ak-marker[data-id="' + e.map_location_id + '"]', r);
            if (!i.length) return !1;
            var s = i.position().top;
            s += r.scrollTop(), r.animate({
                scrollTop: s
            }), n(".ak-marker", r).removeClass("ak-selected"), i.addClass("ak-selected")
        },
        _getFilters: function() {
            var e = this,
                t = [];
            return n.each(n(e.option("filtersSelector") + " form").serializeArray(), function(e, n) {
                n.name !== "ADDRESS" && t.push([n.name, n.value])
            }), t
        },
        filterMarkers: function() {
            var e = this,
                t, r = {
                    map_action: "markers",
                    map_filters: []
                };
            t = n('input[name="ADDRESS"]', n(e.option("filtersSelector") + " form")).val(), r.map_filters = e._getFilters(), e.geocoder.geocode({
                address: t || e.option("country")
            }, function(n, i) {
                i == google.maps.GeocoderStatus.OK && (map.setCenter(n[0].geometry.location), map.setZoom(t ? 12 : 5), e._request(r, function(t) {
                    e.showHideMarkers(t.markers)
                }))
            })
        },
        showHideMarkers: function(e) {
            var t = this,
                i = t.option("gmap.markers"),
                s = [];
            n.each(e, function(e, n) {
                r.isUndefined(r.find(i, {
                    map_location_id: n.map_location_id
                })) && i.push(t._createOneMarker(n)), s.push(n.map_location_id)
            }), t.oCluster.clearMarkers(), n.each(i, function(n, i) {
                r.isUndefined(r.find(e, {
                    map_location_id: i.map_location_id
                })) ? (i.setVisible(!1), i.setMap(null)) : (i.setVisible(!0), i.setMap(map), t.oCluster.addMarker(i))
            }), t.doBoundsRequest()
        },
        doBoundsRequest: function() {
            var t = this,
                r = t.option("maxAddress"),
                i = {
                    map_action: "bounds",
                    map_locations: t.getVisibleMarkersInBounds(),
                    map_maxaddress: r,
                    map_user_located: t.located === !0 ? 1 : 0
                };
            t.ajaxTimeout = e.setTimeout(function() {
                t._request(i, function(e) {
                    var r = n(t.option("locationsListSelector"));
                    e.html ? r.html(e.html) : r.empty().append(r.next(".ak-map-locations-empty").clone().show()), t.addListEvents()
                })
            }, 0)
        },
        _request: function(r, s) {
            var o = this;
            if (o.bRequest || o.oCacheParams[r.mapaction] && o.oCacheParams[r.mapaction] == JSON.stringify(r)) return !1;
            o.bRequest = !0, o.element.mask();
            var u = new i(decodeURIComponent(e.location.href));
            n.ajax({
                url: u.toString(),
                type: "POST",
                data: r,
                complete: function() {
                    o.bRequest = !1, o.bInit == 0 && (o.bInit = !0), o.element.unmask()
                },
                success: function(e) {
                    typeof e == "string" && (e = n.parseJSON(e)), s !== t && s(e), o.oCacheParams[r.mapaction] = JSON.stringify(r)
                }
            })
        },
        addListEvents: function() {
            var e = this;
            n(e.option("locationsListSelector") + " ul  li").bind("click", function(t) {
                var r = n(t.target),
                    i = n(t.currentTarget);
                if (r.is("a")) {
                    if (r.attr("href") !== "#" && r.attr("href").indexOf("javascript:") === -1) return !0;
                    if (r.hasClass("more-info")) return t.preventDefault(), r.next(".ak-more-details").toggle(), !1
                }
                var s = new google.maps.LatLng(i.attr("data-lat"), i.attr("data-lng"));
                map.setCenter(s), map.setZoom(16), e.boundRequest = !1
            })
        },
        getVisibleMarkersInBounds: function() {
            var e = this,
                t = e.option("gmap.markers"),
                n = e.option("maxAddress");
            if (!map) return !1;
            var r = map.getBounds(),
                i = [];
            for (var s = 0; s < t.length; s++) {
                var o = t[s].map_location_id;
                if (t[s].getVisible() === !0 && r.contains(t[s].position) && o) {
                    var u = google.maps.geometry.spherical.computeDistanceBetween(new google.maps.LatLng(e.currentLat, e.currentLng), t[s].position),
                        a = {
                            location_id: o
                        };
                    a.distance = parseFloat((u / 1e3).toFixed(1)), i.push(a)
                }
            }
            return i.length ? (i.length > n && i.splice(n - 1, i.length - n), i) : !1
        }
    }), n.widget("ankama.ak_simple_map", n.ankama.widget, {
        options: {
            initSelector: ".ak-simple-map-widget",
            mapSelector: ".ak-simple-map",
            gmap: {
                lat: "",
                lng: "",
                zoom: 17
            },
            bDisplayMarker: !0
        },
        _create: function() {
            var e = this,
                t = new google.maps.LatLng(e.options.gmap.lat, e.options.gmap.lng);
            map = new google.maps.Map(n(e.element).find(e.options.mapSelector)[0], {
                center: t,
                zoom: e.options.gmap.zoom,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });
            if (e.options.bDisplayMarker) var r = new google.maps.Marker({
                position: t,
                map: map,
                title: ""
            })
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_map.prototype.enhanceWithin(e.target)
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_simple_map.prototype.enhanceWithin(e.target)
    })
}(this),
function(e) {
    var t = require("lodash"),
        n = require("ankama.widget"),
        r = require("jquery.uri"),
        i = require("moment");
    n.widget("ankama.ak_notification_list", n.ankama.widget, {
        options: {
            initSelector: ".ak-notification-list",
            iNotificationHeight: 40
        },
        _create: function() {
            var e = this;
            e.element.delegate(".ak-notification .ak-close", "click", n.proxy(e._onNotificationClosed, e)), e._calculateNotificationHeight(), t.each(n(".ak-notification", e.element), function(t) {
                var r = n(t),
                    i = r.akOptions();
                i.bHideOnView && !r.hasClass("hide") && e._setCookie(r.data("notificationid"))
            })
        },
        _calculateNotificationHeight: function() {
            var e = this,
                t = n(".ak-notification:first:visible", e.element).height();
            t > e.options.iNotificationHeight && (e.options.iNotificationHeight = t, n("body.notifications-header").css("background-position", "center " + (e.options.iNotificationHeight + 10) + "px"))
        },
        _onNotificationClosed: function(e) {
            var t = this,
                r = n(e.target).closest(".ak-notification");
            options = r.akOptions(), r.remove(), options.bHideOnClose && t._setCookie(r.data("notificationid")), n(".ak-notification.hide", t.element).length ? (n(".ak-notification.hide:first", t.element).removeClass("hide"), t._calculateNotificationHeight()) : (t.element.remove(), n("body.notifications-header").css("background-position", ""))
        },
        _setCookie: function(t) {
            var s = new r(e.location.href),
                o = n.cookie.get("NOTIFS") || [];
            o.indexOf(t) === -1 && (o.push({
                id: t,
                ts: i().unix()
            }), n.cookie.set("NOTIFS", JSON.stringify(o), {
                iExpires: 180,
                sDomain: "." + s.domain()
            }))
        },
        _init: function() {
            var e = this
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_notification_list.prototype.enhanceWithin(e.target, !0)
    })
}(this),
function(e, t) {
    var n = require("ankama.widget"),
        r = require("lodash"),
        i = require("hammer");
    n.widget("ankama.ak_slidemenu", n.ankama.widget, {
        options: {
            initSelector: ".ak-slidemenu",
            initProfile: "mobile",
            triggerElement: null,
            fromTarget: null,
            toTarget: ".menu-container",
            sOpenClass: "ak-slidemenu-open",
            sScrollerClass: "ak-mobile-menu-scroller"
        },
        _bMenuCloned: !1,
        _create: function() {
            var e = this;
            e.jqToTarget = n(e.option("toTarget")), e.jqTriggerElement = n(e.option("triggerElement"));
            var r = {};
            r[this.widgetName + "show"] = n.proxy(e._onShow, e), r[this.widgetName + "hide"] = n.proxy(e._onHide, e), e._on(e.element, r), e.jqTriggerElement.on("click", n.proxy(e.toggle, e));
            var s = {
                inputClass: i.SUPPORT_POINTER_EVENTS ? i.PointerEventInput : i.TouchInput
            };
            e._hammer = new i(n("body").get(0), s);
            var o = e._hammer.get("pan"),
                u = e._hammer.get("swipe");
            o.set({
                threshold: 10,
                direction: i.DIRECTION_HORIZONTAL
            }), u.set({
                velocity: .25,
                direction: i.DIRECTION_HORIZONTAL
            });
            var a = {
                panx: t,
                panpercent: t
            };
            e._hammer.on("swipeleft swiperight panleft panright panend pancancel", n.proxy(e._hammerHandler, e, a)), n("." + e.option("sScrollerClass")).prepend(function() {
                return e.jqOverlay = n('<div class="ak-mobile-menu-overlay"></div>'), e.jqOverlay
            }), e.jqOverlay.bind("click", function() {
                e.close()
            }), e.element.show()
        },
        _init: function() {
            var e = this
        },
        _destroy: function() {
            var e = this,
                t = n(".menu", e.jqToTarget);
            e.jqTriggerElement.off("click", n.proxy(e.toggle, e)).removeClass("active"), e.jqToTarget.empty(), n(".ak-mobile-menu-overlay", n("." + e.option("sScrollerClass"))).remove(), n("body").removeClass("nav-open"), e.element.removeClass(e.option("sOpenClass")), e.element.removeData("open"), e.element.hide(), e._hammer.off("swipeleft swiperight panleft panright panend pancancel", n.proxy(e._hammerHandler, e)), e._bMenuCloned = !1
        },
        open: function() {
            var e = this;
            e.jqOverlay.removeClass("inactive").addClass("active"), e._initShowHideEvent(), e._buildMarkup(), e.element.addClass(this.option("sOpenClass")), e.jqTriggerElement.addClass("active")
        },
        close: function() {
            var e = this,
                t = n(".menu", e.jqToTarget);
            e.jqOverlay.hasClass("active") && (e.jqOverlay.one("animationend animationend webkitAnimationEnd oanimationend MSAnimationEnd", function(e) {
                n(this).removeClass("inactive")
            }), e.jqOverlay.removeClass("active").addClass("inactive")), e._initShowHideEvent(), e.element.removeClass(this.option("sOpenClass")), e.jqTriggerElement.removeClass("active"), e.closeMenu(n("> li > ul.open", t))
        },
        toggle: function() {
            var e = this;
            e.element.hasClass(e.option("sOpenClass")) ? e.close() : e.open()
        },
        _whichTransitionEvent: function(e) {
            var n, r = document.createElement("fakeelement"),
                i = {
                    transition: "transitionend",
                    OTransition: "oTransitionEnd",
                    MozTransition: "transitionend",
                    WebkitTransition: "webkitTransitionEnd"
                };
            for (n in i)
                if (r.style[n] !== t) return i[n]
        },
        _initShowHideEvent: function() {
            this.element.one(this._whichTransitionEvent(), n.proxy(this._menuTransitionEnd, this))
        },
        _menuTransitionEnd: function() {
            this.element.hasClass(this.option("sOpenClass")) ? this._trigger("show") : this._trigger("hide")
        },
        _buildMarkup: function() {
            var e = this,
                r = n(this.option("fromTarget"));
            if (e._bMenuCloned || !r.length) return;
            var i = n("<ul></ul>").addClass("menu");
            n("li", r).each(function(e, t) {
                var s = n(t);
                s.closest("ul").is(r) && n("a", s).get(0).firstChild !== null && i.append(s.clone())
            });
            var s = 0,
                o = function(r) {
                    if (!r) return;
                    ++s, n("> li:not(.back)", r).each(function(r, i) {
                        var u = n(i).akRemoveAttributes(),
                            a = u.children("ul:first");
                        if (a.length) {
                            var f = n("> a", u);
                            f.length && (f.find("b.caret").remove(), f.append(n('<b class="caret"></b>'))), u.addClass("dpdown"), a.akRemoveAttributes().addClass("level-" + s);
                            if (!a.prev().length) {
                                u.removeClass("dpdown");
                                var l = a.parents("ul:first");
                                a.parent().is("li") && (a.parent().parent("ul").append(a.find("> li").akRemoveAttributes()), a.parent().remove(), a = t)
                            } else {
                                var c = u.clone();
                                n("> ul", c).remove(), a.prepend(c.addClass("back")), c.on("click", n.proxy(e._onMenuBack, e)), o(a)
                            }
                        }
                    }), --s
                };
            o(i), e.jqToTarget.prepend(i), n("ul", i).each(function(t, r) {
                n(r).css({
                    "min-height": e.element.height() - n(r).position().top - (n(".ak-navbar-search-mob", e.element).outerHeight(!0) + n(".menu-buttons", e.element).outerHeight(!0)) + "px"
                })
            }), e._initMenuEvents(), e._bMenuCloned = !0
        },
        _initMenuEvents: function() {
            var e = this,
                t = n(".menu", e.jqToTarget);
            t.delegate("li", "click", n.proxy(e._onMenuClick, e))
        },
        _matrixToArray: function(e) {
            return e.match(/\d+/g)
        },
        _hammerHandler: function(s, o) {
            var u = this;
            if (o.offsetDirection === i.DIRECTION_UP || o.offsetDirection === i.DIRECTION_DOWN) return;
            switch (o.type) {
                case "panleft":
                case "panright":
                    if (r.isUndefined(s.panx)) {
                        if (o.type === "panleft" && !u.element.hasClass(u.option("sOpenClass")) || o.type === "panright" && o.center.x > 30) {
                            s.panpercent = t, u._hammer.stop(!0);
                            return
                        }
                        s.panx = -u._matrixToArray(u.element.css("transform"))[4]
                    }
                    o.type === "panright" && u._buildMarkup();
                    var a = s.panx + o.deltaX;
                    a > 0 && (a = 0);
                    var f = u.element.width();
                    a < -f && (a = -f), s.panpercent = 80 - Math.abs(a) * 100 / n(e).width(), u.element.addClass("notransition").css("transform", "translate3d(" + a + "px, 0, 0)");
                    break;
                case "panend":
                case "pancancel":
                    if (r.isUndefined(s.panpercent)) return;
                    u.element.removeClass("notransition"), u.element.css("transform", ""), s.panpercent > 30 ? u.open() : u.close(), s.panx = t, s.panpercent = t;
                    break;
                case "swipeleft":
                case "swiperight":
                    u._hammer.stop(!0), u.element.removeClass("notransition"), n("body").removeClass("nav-open"), u.element.css("transform", ""), o.type == "swiperight" && o.pointers[0].clientX - o.distance < 30 && u.open(), o.type == "swipeleft" && u.close(), s.panx = t, s.panpercent = t
            }
        },
        _onMenuClick: function(e) {
            var t = this,
                r = n(e.currentTarget),
                i = r.children("ul:first");
            e.stopPropagation();
            if (e.isDefaultPrevented()) return !1;
            var s = n("a", r).get(0).hash;
            if (s.length > 1 && s.indexOf("#") === 0) return t.close(), !0;
            n(t.option("toTarget"), t.element).css({
                height: i.position().top + i.outerHeight() + "px"
            }), i.length && i.addClass("open")
        },
        _onMenuBack: function(e) {
            e.preventDefault(), this.closeMenu(n(e.currentTarget))
        },
        closeMenu: function(e) {
            var t = this,
                r = e.is("li") ? e.closest("ul") : e,
                i = r.parents("ul.open"),
                s = r.closest("li.dpdown").find("ul.open");
            n(t.option("toTarget"), t.element).css({
                height: i.length ? i.position().top + i.outerHeight() + "px" : ""
            }), s.removeClass("open")
        },
        _onShow: function() {
            var t = this;
            t.element.data("open") || (n("body").addClass("nav-open"), n(e).scrollTop(0), t.element.data("open", !0))
        },
        _onHide: function() {
            var t = this;
            t.element.data("open") === !0 && (n(e).scrollTop(0), n("body").removeClass("nav-open"), t.element.data("open", !1))
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_slidemenu.prototype.enhanceWithin(e.target)
    })
}(this),
function(e, t) {
    var n = require("ankama.widget"),
        r = require("lodash");
    n.widget("ankama.ak_bbcode_spoiler", n.ankama.widget, {
        options: {
            initSelector: ".ak-bbcode-spoiler"
        },
        _create: function() {
            var e = this.element;
            e.on("click", function() {
                var t = n("> div", n(this));
                t.toggle(), t.is(":visible") ? e.addClass("active") : e.removeClass("active")
            })
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_bbcode_spoiler.prototype.enhanceWithin(e.target)
    })
}(this),
function(e, t) {
    "use strict";
    var n = require("jquery"),
        r = require("lodash");
    n.widget("ankama.ak_sessioncode", n.ankama.ak_simpleform, {
        options: {
            initSelector: ".ak-session-code"
        },
        oCodeInput: null,
        oCodeAddButton: null,
        oResultBlock: null,
        _create: function() {
            var e = this;
            e._superApply(arguments), e.oCodeAddButton = e.element.find(".ak-save-code"), e.oCodeInput = e.element.find('input[name="code"]'), e.oResultBlock = e.element.find(".ak-session-code-result"), n(e.oCodeAddButton).on("click", function(t) {
                n.ajax({
                    type: "post",
                    data: {
                        sessioncode: n(e.oCodeInput).val()
                    },
                    success: function(t) {
                        t.html && (n(e.element).find(".ak-form").hide(), e.oResultBlock.find(".ak-gift").prepend(t.html), e.oResultBlock.show(), n(".ak-redirect-code").each(function(t, r) {
                            n(r).attr("href", n(r).attr("href") + "?code=" + n(e.oCodeInput).val())
                        }))
                    }
                })
            })
        },
        elementError: function(e, t, i) {
            var s = this;
            this._superApply(arguments);
            var o = n(e).data("validate-result") || {};
            if (r.isEmpty(o)) return !1;
            switch (e.name) {
                case "code":
            }
        },
        elementSuccess: function(e, t) {
            var n = this;
            this._superApply(arguments);
            switch (t.name) {
                case "code":
                    n.oCodeAddButton.show()
            }
        }
    }), n(document).bind("ready widgetcreate", function(e) {
        n.ankama.ak_sessioncode.prototype.enhanceWithin(e.target)
    })
}(this);
if (typeof Ankama == "undefined") var Ankama = {};
typeof Ankama.Ads == "undefined" && (Ankama.Ads = {
        bConstruct: null,
        bDomReady: !0,
        iMouseX: null,
        iMouseY: null,
        aZone: new Array,
        aVars: {},
        DIVNAME: "ankama_ads",
        oNoAdsCallBack: null,
        DomReady: function(e) {
            this.bDomReady = e
        },
        CallAgain: function() {
            var e = !1;
            for (i in Ankama.Ads.aZone) typeof Ankama.Ads.aZone[i] != "function" && typeof Ankama.Ads.aZone[i].content != "undefined" && Ankama.Ads.aZone[i].content.type != "HTML" && (e = !0, Ankama.Ads.aZone[i].inserted = !1);
            e && Ankama.Ads._Call()
        },
        Call: function() {
            Ankama.Ads._Call()
        },
        _Domain: function() {
            var e = document.location.host.substring(document.location.host.lastIndexOf(".") + 1);
            return e == "com" || e == "dev" || e == "lan" || e == "tst" ? e : "com"
        },
        Insert: function(e) {
            for (var t in Ankama.Ads.aZone)
                if (typeof Ankama.Ads.aZone[t] != "function") {
                    var n = Ankama.Ads.aZone[t];
                    if (typeof n.inserted == "undefined" && typeof n.inserted != "function" || n.inserted != 1) {
                        var r = document.getElementById(n.id);
                        if (r && e[n.zone]) {
                            if (e[n.zone].type != "HTML")
                                for (var i in r.childNodes) typeof r.childNodes[i] != "function" && r.childNodes[i].className == "ankama_ads_inserted" && r.removeChild(r.childNodes[i]);
                            r.appendChild(this._GenerateAds(n.zone, e[n.zone]));
                            if (e[n.zone].type == "HTML") {
                                var s = document.getElementById(n.display_id);
                                s.style.display = "block", s.style.height = document.body.offsetHeight + "px";
                                var o = "visible",
                                    u = "visible",
                                    a = "auto",
                                    f = "visible",
                                    l = "visible",
                                    c = "auto";
                                document.body.style.overflow != "" && document.body.style.overflow != "undefined" && (o = document.body.style.overflow, u = document.body.style.overflow), document.body.style.overflowX != "" && document.body.style.overflowX != "undefined" && (o = document.body.style.overflowX), document.body.style.overflowY != "" && document.body.style.overflowY != "undefined" && (u = document.body.style.overflowY), document.body.style.height != "" && document.body.style.height != "undefined" && (a = document.body.style.height), window.document.documentElement.style.overflow != "" && window.document.documentElement.style.overflow != "undefined" && (f = window.document.documentElement.style.overflow, l = window.document.documentElement.style.overflow), window.document.documentElement.style.overflowX != "" && window.document.documentElement.style.overflowX != "undefined" && (f = window.document.documentElement.style.overflowX), window.document.documentElement.style.overflowY != "" && window.document.documentElement.style.overflowY != "undefined" && (l = window.document.documentElement.style.overflowY), window.document.documentElement.style.height != "" && window.document.documentElement.style.height != "undefined" && (c = window.document.documentElement.style.height), document.body.style.overflow = "hidden", document.body.style.overflowX = "hidden", document.body.style.overflowY = "hidden", document.body.style.height = "100%", window.document.documentElement.style.overflow = "hidden", window.document.documentElement.style.overflowX = "hidden", window.document.documentElement.style.overflowY = "hidden", window.document.documentElement.style.height = "100%", window.setTimeout(function() {
                                    document.body.style.overflow = "visible", document.body.style.overflowX = o, document.body.style.overflowY = u, document.body.style.height = a, window.document.documentElement.style.overflow = "visible", window.document.documentElement.style.overflowX = f, window.document.documentElement.style.overflowY = l, window.document.documentElement.style.height = c, s.style.display = "none"
                                }, e[n.zone].hidetime ? e[n.zone].hidetime * 1e3 : 1e4)
                            }
                        } else this.oNoAdsCallBack && this.oNoAdsCallBack();
                        n.inserted = !0, n.content = e[n.zone]
                    }
                }
        },
        _GenerateAds: function(e, t) {
            switch (t.type) {
                case "IMAGE":
                    var n = this._GenerateImage(e, t);
                    n.onclick = function() {
                        return Ankama.Ads.Click(e)
                    }, n.onmouseover = function() {
                        return window.status = t.url, !0
                    }, n.onmouseout = function() {
                        return window.status = "", !0
                    };
                    break;
                case "FLASH":
                    var n = this._GenerateFlash(e, t);
                    n && typeof n.children != "undefined" && n.children.length > 1 ? n.children[1].onclick = function() {
                        return Ankama.Ads.Click(e)
                    } : n.onclick = function() {
                        return Ankama.Ads.Click(e)
                    }, n.onmouseover = function() {
                        return window.status = t.url, !0
                    }, n.onmouseout = function() {
                        return window.status = "", !0
                    };
                    break;
                case "HTML":
                    var n = this._GenerateHtml(e, t),
                        r = /<a\s+[^>]*>/gi,
                        i = /href="([^"]*)"/i,
                        s = /target="([^"]*)"/i,
                        o = /\s*href="[^"]*"\s*/gi,
                        u = /\s*target="[^"]*"\s*/gi,
                        a = /\s*onclick="[^"]*"\s*/gi,
                        f = n.innerHTML.match(r);
                    if (f && f.length > 0)
                        for (var l = 0; l < f.length; l++) {
                            var c = f[l],
                                h = new RegExp(c, "g");
                            i.exec(c);
                            var p = RegExp.$1;
                            c = c.replace(o, " "), s.exec(c);
                            var d = RegExp.$1;
                            c = c.replace(u, " "), c = c.replace(a, " "), c = c.replace(/^<a\s+/i, '<a href="javascript:void(0);" onclick="return Ankama.Ads.Click(' + e + ", '" + p + "', '" + d + "')\" ");
                            var v = n.innerHTML.replace(h, c);
                            n.innerHTML = v
                        } else n.style.cursor = "pointer", n.onclick = function() {
                            return Ankama.Ads.Click(e)
                        }, n.onmouseover = function() {
                            return window.status = t.url, !0
                        }, n.onmouseout = function() {
                            return window.status = "", !0
                        }
            }
            return n
        },
        _GenerateFlash: function(e, t) {
            var n = new Array;
            n.quality = "high", n.menu = "false", n.AllowScriptAccess = "always", n.wmode = "transparent", n.flashvars = "linkfunction=Ankama.Ads.Click&zone=" + e;
            var r = '<object id="ak_ads_zone_' + e + '" ' + (window.ActiveXObject ? ' classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" data="' + t.file + '"' : "");
            r += ' width="' + t.width + '"  height="' + t.height + '">', r += '<param name="movie" value="' + t.file + '">';
            var i = "",
                s = "";
            for (var o in n) typeof n[o] != "function" && (i += '<param name="' + o + '" value="' + n[o] + '">', s += " " + o + '="' + n[o] + '"');
            r += i, r += '<embed id="ak_ads_emb_zone_' + e + '" src="' + t.file + '" width="' + t.width + '" height="' + t.height + '"', r += ' type="application/x-shockwave-flash"' + s + "></embed>", r += "</object>";
            var u = document.createElement("div"),
                a = document.createElement("div");
            return u.style.position = "relative", a.style.position = "absolute", u.style.cssFloat = "left", a.style.cssFloat = "left", a.style.zIndex = "9999", a.style.cursor = "pointer", a.style.left = "0px", a.style.top = "0px", a.style.height = t.height + "px", a.style.width = t.width + "px", u.innerHTML = r, u.appendChild(a), u.className = "ankama_ads_inserted", u
        },
        _GenerateHtml: function(e, t) {
            var n = document.createElement("div");
            return n.innerHTML = t.html, n.className = "ankama_ads_inserted", n
        },
        _GenerateImage: function(e, t) {
            var n = document.createElement("a");
            if (t) {
                n.href = t.link, n.className = "ankama_ads_inserted";
                switch (t.target) {
                    case "BLANK":
                        n.target = "_blank"
                }
                var r = document.createElement("img");
                r.style.border = 0, r.src = t.file, r.className = "img-maxresponsive", t.alt && (r.alt = t.alt), n.appendChild(r)
            }
            return n
        },
        _GetPosition: function(e) {
            var t = e,
                n = 0,
                r = 0;
            while (t != null && t.tagName != "BODY") n += t.offsetTop, r += t.offsetLeft, t = t.offsetParent;
            return {
                x: r,
                y: n
            }
        },
        _GetClickZone: function(e) {
            if (this.iMouseX != null && this.iMouseY != null) {
                var t = this._GetPosition(e);
                return {
                    x: this.iMouseX - t.x,
                    y: this.iMouseY - t.y
                }
            }
        },
        _ReturnVars: function(e) {
            sReturn = "", document.referrer && typeof document.referrer == "string" && document.referrer.indexOf("?") != -1 ? sReturn += "&referer=" + escape(document.referrer.substr(0, document.referrer.indexOf("?"))) : document.referrer && typeof document.referrer == "string" && (sReturn += "&referer=" + escape(document.referrer)), document.location.href && typeof document.location.href == "string" && document.location.href.indexOf("?") != -1 ? sReturn += "&url=" + escape(document.location.href.substr(0, document.location.href.indexOf("?"))) : document.location.href && typeof document.location.href == "string" && (sReturn += "&url=" + escape(document.location.href));
            for (i in this.aVars) typeof this.aVars[i] != "undefined" && typeof this.aVars[i] != "function" && (sReturn += "&" + i + "=" + this.aVars[i]);
            for (i in e) typeof e[i] != "undefined" && typeof e[i] != "function" && (sReturn += "&" + i + "=" + e[i]);
            return sReturn
        },
        _Call: function() {
            var e = !1,
                t = new Array;
            for (i in Ankama.Ads.aZone) typeof Ankama.Ads.aZone[i] != "function" && (e = !0, t.push(Ankama.Ads.aZone[i].zone));
            if (e) {
                var n = document.createElement("script");
                n.type = "text/javascript", n.src = (window.location.protocol == "https:" ? "https:" : "http:") + "//aas.ankama." + Ankama.Ads._Domain() + "/view?q=" + t.join("-") + Ankama.Ads._ReturnVars(), document.body.insertBefore(n, document.body.firstChild)
            }
        },
        MousePosition: function(e) {
            if (typeof e == "undefined" || e === null) e = window.event;
            if (e == null) return;
            e.pageX = typeof e.pageX != "undefined" ? e.pageX : null, e.pageY = typeof e.pageY != "undefined" ? e.pageY : null;
            if (e.pageX != null || e.pageY != null) this.iMouseX = e.pageX, this.iMouseY = e.pageY;
            else if (e.clientX || e.clientY) this.iMouseX = parseInt(e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft), this.iMouseY = parseInt(e.clientY + document.body.scrollTop + document.documentElement.scrollTop)
        },
        _Construct: function() {
            this.bConstruct = !0, this.bDomReady == 1 && (Ankama._DomReady = {}, this._DomReady(this._Call));
            var e = document.onmousemove;
            document.onmousemove = function(t) {
                Ankama.Ads.MousePosition(t), e && e(t)
            }
        },
        Zone: function(e, t) {
            this.bConstruct != 1 && this._Construct();
            if (typeof t == "undefined" && typeof t != "function") {
                t = this.DIVNAME + "_" + e;
                var n = !0
            } else var n = !1;
            typeof t == "string" ? (n && document.write('<div id="' + this.DIVNAME + "_" + e + '"></div>'), this.aZone[e] = {
                zone: e,
                id: t
            }) : this.aZone[e] = {
                zone: e,
                id: t[0],
                display_id: t[1]
            }
        },
        Vars: function(e) {
            for (i in e) typeof e[i] != "undefined" && typeof e[i] != "function" && (this.aVars[i] = e[i])
        },
        Click: function(e, t, n) {
            for (i in Ankama.Ads.aZone)
                if (typeof Ankama.Ads.aZone[i] != "function" && Ankama.Ads.aZone[i].zone == e) {
                    var r = Ankama.Ads.aZone[i],
                        s = typeof t != "undefined" ? t : r.content.link,
                        o = r.id ? r.id : this.DIVNAME + "_" + r.zone,
                        u = this._GetClickZone(document.getElementById(o));
                    u && typeof t == "undefined" && (s += "&x=" + u.x + "&y=" + u.y);
                    if (typeof n == "string") n == "" || n == "_self" ? window.location.href = s : window.open(s, n);
                    else switch (r.content.target) {
                        case "BLANK":
                            window.open(s);
                            break;
                        default:
                            window.location.href = s
                    }
                }
            return !1
        },
        _DomReady: function(e) {
            if (Ankama._DomReady.loaded) return e();
            var t = Ankama._DomReady.observers;
            t || (t = Ankama._DomReady.observers = []), t[t.length] = e;
            if (Ankama._DomReady.callback) return;
            Ankama._DomReady.callback = function() {
                if (Ankama._DomReady.loaded) return;
                Ankama._DomReady.loaded = !0, Ankama._DomReady.timer && (clearInterval(Ankama._DomReady.timer), Ankama._DomReady.timer = null);
                var e = Ankama._DomReady.observers;
                for (var t = 0, n = e.length; t < n; t++) {
                    var r = e[t];
                    e[t] = null, r()
                }
                Ankama._DomReady.callback = Ankama._DomReady.observers = null
            };
            var n = !!window.attachEvent && !window.opera,
                r = navigator.userAgent.indexOf("AppleWebKit/") > -1;
            if (document.readyState && r) Ankama._DomReady.timer = setInterval(function() {
                var e = document.readyState;
                (e == "loaded" || e == "complete") && Ankama._DomReady.callback()
            }, 50);
            else if (window.addEventListener) document.addEventListener("DOMContentLoaded", Ankama._DomReady.callback, !1), window.addEventListener("load", Ankama._DomReady.callback, !1);
            else if (window.attachEvent) window.attachEvent("onload", Ankama._DomReady.callback);
            else {
                var e = window.onload;
                window.onload = function() {
                    Ankama._DomReady.callback(), e && e()
                }
            }
        }
    }),
    function(e) {
        var t = require("lodash"),
            n = require("ankama.widget");
        n.widget("ankama.ak_ads", n.ankama.widget, {
            options: {
                initSelector: "body"
            },
            _create: function() {
                if (typeof Ankama.Ads != "undefined") {
                    Ankama.Ads.Vars({
                        lang: e.location.pathname.substr(1, 2)
                    }), Ankama.Ads.DomReady(!1);
                    var t = n(".ak-ads");
                    t.length && t.each(function(e, t) {
                        var r = n(t).akOptions().iZoneId,
                            i = n(t).akOptions().sAdsZoneName;
                        r && Ankama.Ads.Zone(r, i)
                    }), Ankama.Ads.oNoAdsCallBack = function() {
                        n(".ak-ads").remove()
                    }, Ankama.Ads.Call()
                }
            }
        }), n(document).bind("ready widgetcreate", function(e) {
            n.ankama.ak_ads.prototype.enhanceWithin(e.target, !0)
        })
    }(this),
    function(e) {
        var t = require("ankama.widget");
        t.widget("ankama.ak_shoptrack", t.ankama.widget, {
            options: {
                initSelector: "body",
                matchPattern: "(ankama-shop.com)",
                sCookieKey: "AADST"
            },
            _create: function() {
                var e = this,
                    n = document.cookie.split(";"),
                    r = "";
                for (var i in n) {
                    var s = n[i].split("="),
                        o = s[0].charAt(0) == " " ? s[0].substring(1) : s[0];
                    o == e.options.sCookieKey && (r = s[1])
                }
                if (!r) return;
                oReg = new RegExp(e.options.matchPattern, "i"), e.element.find("a[href]").each(function(n, i) {
                    if (t(i).attr("href").match(oReg)) {
                        var s = e._addParameter(e.options.sCookieKey, r, t(i).attr("href"));
                        t(i).attr("href", s)
                    }
                })
            },
            _addParameter: function(e, t, n) {
                if (!n) return;
                var r = new RegExp("([?&])" + e + "=.*?(&|#|$)(.*)", "gi"),
                    i;
                if (r.test(n)) return typeof t != "undefined" && t !== null ? n.replace(r, "$1" + e + "=" + t + "$2$3") : (i = n.split("#"), n = i[0].replace(r, "$1$3").replace(/(&|\?)$/, ""), typeof i[1] != "undefined" && i[1] !== null && (n += "#" + i[1]), n);
                if (typeof t != "undefined" && t !== null) {
                    var s = n.indexOf("?") !== -1 ? "&" : "?";
                    return i = n.split("#"), n = i[0] + s + e + "=" + t, typeof i[1] != "undefined" && i[1] !== null && (n += "#" + i[1]), n
                }
                return n
            }
        }), t(document).bind("ready widgetcreate", function(e) {
            t.ankama.ak_shoptrack.prototype.enhanceWithin(e.target)
        })
    }(),
    function(e, t) {
        var n = require("ankama.widget"),
            r = require("lodash");
        n.widget("ankama.ak_homepage_menu", n.ankama.widget, {
            options: {
                initSelector: ".ak-homepage-menu"
            },
            _create: function() {
                var e = this,
                    t = n(".ak-homepage-menu-content", e.element),
                    r = n(".ak-homepage-menu-handler", e.element);
                t.on("click", "a", function(e) {
                    r.html(n(e.target).clone()), t.css("display", "none")
                }), r.on("click", "a", function(e) {
                    e.preventDefault(), t.css("display", t.css("display") == "none" ? "block" : "none")
                })
            }
        }), n(document).bind("ready widgetcreate", function(e) {
            n.ankama.ak_homepage_menu.prototype.enhanceWithin(e.target)
        })
    }(this),
    function(e) {
        var t = require("ankama.widget");
        t.widget("ankama.ak_img_gif_player", t.ankama.widget, {
            options: {
                initSelector: ".ak-img-gif-player"
            },
            _sState: "ready",
            _bBusy: !1,
            _jqGif: null,
            _create: function() {
                var e = this;
                e._jqGif = e.element.find(".ak-img-gif:first"), e.element.click(function() {
                    if (e._bBusy) return;
                    e._sState == "ready" && e.loadGif()
                })
            },
            loadGif: function() {
                var e = this;
                e._bBusy = !0, e._jqGif.load(function() {
                    e._jqGif.unbind("load"), e._setState("loading", "loaded"), e._bBusy = !1
                }), e._jqGif.attr("src", e._jqGif.attr("data-gif-src")), e._setState("ready", "loading")
            },
            _setState: function(e, t) {
                this.element.removeClass("ak-state-" + e).addClass("ak-state-" + t), this._sState = t
            }
        }), t(document).bind("ready widgetcreate", function(e) {
            t.ankama.ak_img_gif_player.prototype.enhanceWithin(e.target, !0)
        })
    }(this),
    function(e, t) {
        var n = require("ankama.widget"),
            n = require("jquery.ankama"),
            r = require("lodash");
        n.widget("ankama.ak_select_download", n.ankama.widget, {
            options: {
                initSelector: ".ak-download-select"
            },
            _create: function() {
                var t = this,
                    r = t.element;
                r.on("change", function(t) {
                    t.preventDefault();
                    var i = n(this).val();
                    i.indexOf("http://download.") != -1 ? n("<iframe/>").attr({
                        src: i,
                        style: "visibility:hidden; display:none;"
                    }).appendTo(r) : e.open(i, "_blank")
                })
            }
        }), n(document).bind("ready widgetcreate", function(e) {
            n.ankama.ak_select_download.prototype.enhanceWithin(e.target)
        })
    }(this),
    function(e) {
        var t = require("jquery"),
            n = require("lodash"),
            r = require("jquery.complexify");
        t.widget("ankama.ak_registerform", t.ankama.ak_simpleform, {
            options: {
                initSelector: ".ak-nonused"
            },
            _create: function() {
                var e = this;
                t(".ak-submit").on("click", function(n) {
                    if (t(".ak-recaptchav2").length > 0) {
                        var r = grecaptcha.getResponse();
                        r.length > 0 ? e.element.submit() : (t(".ak-recaptcha-container .form-group").addClass("has-error"), t(".ak-recaptcha-fail").show())
                    } else e.element.submit()
                }), this._superApply(arguments)
            },
            remoteValidation: function() {
                this._superApply(arguments)
            },
            _init: function() {
                var e = this;
                this._superApply(arguments), this.getField("userpassword").complexify({
                    minimumChars: 8
                }, function(e, n) {
                    var r = Math.round(n / 100 * 5),
                        i = /^(?=.*[0-9]+.*)(?=.*[a-zA-Z]+.*).+$/gi,
                        s = i.exec(t(this).val());
                    s || (r = 1);
                    var o;
                    r == 0 ? o = "" : r == 1 ? o = "bad" : r == 2 ? o = "low" : r == 3 ? o = "medium" : r == 4 ? o = "good" : r > 4 && (o = "high");
                    var u = t(this).closest(".form-group").find("#passwordpower").get(0);
                    u && (u.className = o)
                }), this._bind()
            },
            _oErrorFieldHandlers: {
                recaptcha_response_field: function(e, n) {
                    t(e).trigger("recaptcha_reload")
                }
            },
            formInvalidHandler: function() {
                this._superApply(arguments)
            },
            elementFocusOut: function(e, t) {
                if (e.name == "recaptcha_response_field" || e.name == "usercaptcha") return;
                this._superApply(arguments)
            },
            elementSuccess: function(e, n) {
                var r = this;
                this._superApply(arguments);
                switch (n.name) {
                    case "userlogin":
                    case "usernickname":
                        !t(n).hasClass("generate") && !t(n).hasClass("genfocusattached") && (t(n).addClass("genfocusattached"), t(n).closest(".form-group").find("input").on("focus.genbutton", function(e) {
                            e.currentTarget.name != "userlogin" && (t(n).removeClass("genfocusattached"), t(n).closest(".form-group").find("input").off("focus.genbutton"), t(n).closest(".form-group").find(".genlink").hide())
                        }));
                        break;
                    case "usercaptcha":
                        this.element.find(".block_captcha .form-group").addClass("has-success")
                }
            },
            elementError: function(e, r, i) {
                var s = this;
                this._superApply(arguments);
                var o = t(e).data("validate-result") || {};
                if (n.isEmpty(o)) return !1;
                switch (e.name) {
                    case "usernickname":
                    case "userlogin":
                        if (o.result.suggests) {
                            t(e).closest(".form-group").find(".genlink").show();
                            var u = t(e).closest(".form-group").find(".genlink button").first();
                            u.bind("click", function() {
                                t(e).val(n.take(n.shuffle(o.result.suggests))), t(e).addClass("generate"), t(e).one("blur", function() {
                                    t(e).removeClass("generate")
                                }), s.jqValidator.element(e)
                            }), u.one("mouseleave", function() {
                                t(e).removeClass("generate"), s.jqValidator.element(e)
                            })
                        }
                }
            },
            checkPasswordStrength: function() {
                var e = -2,
                    n = t(this);
                n.val() != "" && (e = -1), n.val().search("[a-z]") != -1 && (e = 0), n.val().search("[ -/:-@[-`{-~]") != -1 && e++, n.val().search("[0-9]") != -1 && e++, n.val().length > 12 && e++, e == 0 && n.val().search("[A-Z]") != -1 && e++;
                var r = t("#" + n.attr("aria-describedby")).find("#passwordpower").get(0);
                r.className = e == -1 ? "bad" : e == 0 ? "low" : e == 1 ? "medium" : e == 2 ? "good" : e == 3 ? "high" : ""
            },
            _giveCaptcha: function() {
                var e = this,
                    n = this.element.find("img.captcha");
                n.attr("src", n.get(0).src.replace(/(t=[0-9]+)/i, "t=" + Math.round(t.now() / 1e3)))
            },
            _bind: function() {
                var e = this
            }
        }), t(document).bind("ready widgetcreate", function(e) {
            t.ankama.ak_registerform.prototype.enhanceWithin(e.target)
        })
    }(),
    function(e, t) {
        var n = require("jquery");
        n.widget("ankama.ak_registertracker", n.ankama.widget, {
            options: {
                initSelector: ".ak-registertracker",
                code: ""
            },
            _create: function() {
                this._superApply(arguments);
                if (this.options.code) {
                    var n = e.createElement("iframe");
                    n.style.display = "none", e.body.appendChild(n), n = n.contentWindow ? n.contentWindow : n.contentDocument.document ? n.contentDocument.document : n.contentDocument, n.document.open(), n.document.write(this.options.code), n.document.close()
                }
            }
        }), n(e).bind("ready widgetcreate", function(e) {
            n.ankama.ak_registertracker.prototype.enhanceWithin(e.target)
        })
    }(document),
    function(e, t) {
        "use strict";
        var n = require("jquery"),
            r = require("lodash");
        n.widget("ankama.ak_nicknameform", n.ankama.ak_simpleform, {
            options: {
                initSelector: ".ak-nicknameform",
                sRedirectUrl: ""
            },
            _onSubmitted: function(n, r, i, s, o) {
                var u = this;
                setTimeout(function() {
                    "location" in e && (u.option("sRedirectUrl") ? e.location.href = u.option("sRedirectUrl") : e.location.reload())
                }, 3e3)
            },
            elementSuccess: function(e, t) {
                this._superApply(arguments);
                switch (t.name) {
                    case "usernickname":
                        this.element.find(".genlink").hide()
                }
            },
            elementError: function(e, t, i) {
                var s = this;
                this._superApply(arguments);
                var o = n(e).data("validate-result") || {};
                if (r.isEmpty(o)) return !1;
                switch (e.name) {
                    case "usernickname":
                        if (o.result.suggests) {
                            this.element.find(".genlink").show();
                            var u = this.element.find(".genlink button").first();
                            u.bind("click", function() {
                                n(e).val(r.take(r.shuffle(o.result.suggests))), s.jqValidator.element(e)
                            })
                        }
                }
            }
        }), n(document).bind("ready widgetcreate", function(e) {
            n.ankama.ak_nicknameform.prototype.enhanceWithin(e.target)
        })
    }(this),
    function(e, t) {
        "use strict";
        var n = require("ankama.widget"),
            r = require("lodash"),
            i = window.location.hash;
        n.widget("ankama.ak_account_login", n.ankama.widget, {
            options: {
                initSelector: ".ak-account-login"
            },
            _create: function() {
                this._superApply(arguments);
                var e = i.split("&"),
                    t = e && e[0].split("=").length > 0 ? e[0].split("=")[1] : null,
                    r = e.length > 1 && e[1].split("=").length > 0 ? e[1].split("=")[1] : null;
                r != null ? (n("#userpass").focus(), n("#userlogin", this.element).val(r)) : n("#userlogin", this.element).focus(), t && (this.element.find(".infos_box").css("display", "block"), this.element.find(".errors_login_" + t).show(), window.location.hash = "")
            }
        }), n(document).bind("ready widgetcreate", function(e) {
            n.ankama.ak_account_login.prototype.enhanceWithin(e.target)
        })
    }(this),
    function(e, t) {
        var n = require("ankama.widget"),
            n = require("jquery.ankama"),
            r = require("lodash");
        n.widget("ankama.ak_characterbanner", n.ankama.widget, {
            options: {
                initSelector: ".ak-character-banner"
            },
            _create: function() {
                var e = this,
                    t = e.element,
                    n = t.akOptions();
                t.find(".ak-character-picture-show").click(function(e) {
                    t.find(".ak-character-picture").show("fast", function() {
                        t.find(".ak-character-picture-show").addClass("on")
                    }), t.find(".ak-character-ornament").hide("fast", function() {
                        t.find(".ak-character-ornament-show").removeClass("on")
                    })
                }), t.find(".ak-character-ornament-show").click(function(e) {
                    t.find(".ak-character-picture").hide("fast", function() {
                        t.find(".ak-character-picture-show").removeClass("on")
                    }), t.find(".ak-character-ornament").show("fast", function() {
                        t.find(".ak-character-ornament-show").addClass("on")
                    })
                })
            }
        }), n(document).bind("ready widgetcreate", function(e) {
            n.ankama.ak_characterbanner.prototype.enhanceWithin(e.target)
        })
    }(this),
    function(e, t) {
        var n = require("ankama.widget");
        n.widget("ankama.ak_backgroundbanner", n.ankama.widget, {
            options: {
                initSelector: ".ak-backgroundbanner",
                bannerSelector: ".ak-character-banner"
            },
            _create: function() {
                var e = this,
                    t = n(e.options.bannerSelector),
                    r = n(e.element).closest(".ak-modal"),
                    i = n(e.element).find(".ak-confirmation");
                n(e.element).on("click", function(e) {
                    if (n(e.target).closest("button.ak-modal-close").length) {
                        n(r).ak_modal("close");
                        return
                    }
                    if (n(e.target).closest("button.ak-cancel-choice").length) return;
                    n(e.target).closest(".ak-image-block").length && (n(t).css({
                        backgroundImage: n(e.target).closest(".ak-image-block").find(".ak-image-container").css("backgroundImage")
                    }), n(".ak-adminbanner").ak_adminbanner("setValue", n(e.target).closest(".ak-image-block").data("id")), n(".ak-adminbanner .confirmation-message").hide(), n(i).show(), n(r).ak_modal("close"))
                })
            },
            _confirm: function() {},
            _cancel: function() {}
        }), n(document).bind("ready widgetcreate", function(e) {
            n.ankama.ak_backgroundbanner.prototype.enhanceWithin(e.target)
        })
    }(this),
    function(e, t) {
        var n = require("ankama.widget");
        n.widget("ankama.ak_adminbanner", n.ankama.widget, {
            options: {
                initSelector: ".ak-adminbanner",
                bannerSelector: ".ak-character-banner"
            },
            _create: function() {
                var e = this;
                n(e.element).on("click", function(t) {
                    if (n(t.target).closest(".ak-confirm-choice").length) {
                        e._confirm();
                        return
                    }
                    if (n(t.target).closest(".ak-cancel-choice").length) {
                        e._cancel();
                        return
                    }
                })
            },
            setValue: function(e) {
                var t = this;
                t.iSelectedItem = e, n(t.element).find(".ak-confirmation").fadeIn()
            },
            _confirm: function() {
                var e = this;
                n(e.element).find(".ak-confirmation").fadeOut(), e.iSelectedItem && (n.ajax({
                    type: "post",
                    url: e.options.postback_url,
                    data: {
                        iBackgroundId: e.iSelectedItem,
                        postback: "admin_background"
                    }
                }), n(e.element).find(".confirmation-message").fadeIn())
            },
            _cancel: function() {
                var e = this;
                n(e.element).find(".ak-confirmation").fadeOut(), n(e.options.bannerSelector).removeAttr("style"), n(e.options.bannerSelector).data("background") && n(e.options.bannerSelector).css({
                    backgroundImage: 'url("' + n(e.options.bannerSelector).data("background") + '")'
                })
            }
        }), n(document).bind("ready widgetcreate", function(e) {
            n.ankama.ak_adminbanner.prototype.enhanceWithin(e.target)
        })
    }(this),
    function(e, t) {
        var n = require("ankama.widget"),
            n = require("jquery.ankama"),
            r = require("lodash");
        n.widget("ankama.ak_characterequipment", n.ankama.widget, {
            options: {
                initSelector: ".ak-caracteristics-content"
            },
            _create: function() {}
        }), n(document).bind("ready widgetcreate", function(e) {
            n.ankama.ak_characterequipment.prototype.enhanceWithin(e.target)
        })
    }(this),
    function(e, t) {
        "use strict";
        var n = require("ankama.widget"),
            r = require("lodash");
        n = require("jquery.ankama"), n.widget("ankama.ak_breed_render", n.ankama.widget, {
            options: {
                initSelector: ".ak-breed-render-container",
                url: null,
                sRenderDestination: ".ak-breed-render",
                sNextBreedSelector: ".ak-breed-next-render",
                sPreviousBreedSelector: ".ak-breed-previous-render",
                sBreedChoiceMaleSelector: ".ak-breed-sex-choice-male",
                sBreedChoiceFemaleSelector: ".ak-breed-sex-choice-female",
                sBreedSelectedClass: "ak-breed-sex-selected"
            },
            _iCurrentDirection: 1,
            _sCurrentSex: "M",
            _aData: null,
            _getCreateOptions: function() {
                var e = this.element.data("options") || {};
                return e = r.extend(r.clone(this.options), this.element.akOptions(), e), e
            },
            _create: function() {
                this.element.on("click", this.options.sNextBreedSelector, n.proxy(this._onClickNextDirection, this)), this.element.on("click", this.options.sPreviousBreedSelector, n.proxy(this._onClickPreviousDirection, this)), this.element.on("click", this.options.sBreedChoiceMaleSelector, n.proxy(this._onClickSexMale, this)), this.element.on("click", this.options.sBreedChoiceFemaleSelector, n.proxy(this._onClickSexFemale, this))
            },
            _onClickPreviousDirection: function() {
                this._iCurrentDirection != 7 ? this._iCurrentDirection++ : this._iCurrentDirection = 0, this._displayRender()
            },
            _onClickNextDirection: function() {
                this._iCurrentDirection != 0 ? this._iCurrentDirection-- : this._iCurrentDirection = 7, this._displayRender()
            },
            _onClickSexFemale: function() {
                this._sCurrentSex == "M" && (n(this.options.sBreedChoiceMaleSelector).removeClass(this.options.sBreedSelectedClass), this._sCurrentSex = "F", n(this.options.sBreedChoiceFemaleSelector).addClass(this.options.sBreedSelectedClass), this._displayRender())
            },
            _onClickSexMale: function() {
                this._sCurrentSex == "F" && (n(this.options.sBreedChoiceFemaleSelector).removeClass(this.options.sBreedSelectedClass), this._sCurrentSex = "M", n(this.options.sBreedChoiceMaleSelector).addClass(this.options.sBreedSelectedClass), this._displayRender())
            },
            _displayRender: function() {
                var e = this,
                    t = this.options.url + "/" + this._iCurrentDirection + "-" + this._sCurrentSex;
                n.ajax({
                    url: t
                }).done(function(r) {
                    var i = function() {
                            n(".ak-entitylook", e.element).replaceWith(r)
                        },
                        s = n(r);
                    s.length && s.is("img") ? s.bind("load", i) : i()
                })
            }
        }), n(document).bind("ready widgetcreate", function(e) {
            n.ankama.ak_breed_render.prototype.enhanceWithin(e.target)
        })
    }(this),
    function(e, t) {
        var n = require("ankama.widget"),
            n = require("jquery.ankama"),
            r = require("lodash");
        n.widget("ankama.ak_ridesdetails", n.ankama.widget, {
            options: {
                initSelector: ".ak-rides-details"
            },
            _create: function() {
                n(this.element).on("change", ".ak-rides-details-level", function(e) {
                    e.preventDefault(), n(this).parent("form").submit()
                })
            }
        }), n(document).bind("ready widgetcreate", function(e) {
            n.ankama.ak_ridesdetails.prototype.enhanceWithin(e.target)
        })
    }(this),
    function(e, t) {
        var n = require("ankama.widget"),
            n = require("jquery.ankama"),
            r = require("lodash");
        n.widget("ankama.ak_setsbonus", n.ankama.widget, {
            options: {
                initSelector: ".ak-set-bonus"
            },
            _create: function() {
                var e = this;
                n(this.element).on("change", ".ak-set-bonus-select", function(e) {
                    e.preventDefault(), n(".set-bonus-list").hide(), n(".set-bonus-" + n(this).val()).show()
                })
            }
        }), n(document).bind("ready widgetcreate", function(e) {
            n.ankama.ak_setsbonus.prototype.enhanceWithin(e.target)
        })
    }(this),
    function(e, t) {
        var n = require("ankama.widget"),
            n = require("jquery.ankama"),
            r = require("lodash");
        n.widget("ankama.ak_companionsdetails", n.ankama.widget, {
            options: {
                initSelector: ".ak-companions-details"
            },
            _create: function() {
                n(this.element).on("change", ".ak-companions-details-level", function(e) {
                    e.preventDefault(), n(this).parent("form").submit()
                })
            }
        }), n(document).bind("ready widgetcreate", function(e) {
            n.ankama.ak_companionsdetails.prototype.enhanceWithin(e.target)
        })
    }(this),
    function(e, t) {
        var n = require("ankama.widget"),
            r = require("lodash");
        n.widget("ankama.ak_widget_video", n.ankama.widget, {
            options: {
                initSelector: ".ak-widget-video",
                sources: []
            },
            _create: function() {
                var t = this,
                    i = t.element.get(0);
                if (!t.options.sources) return;
                var s = function() {
                        if (n("source", t.element).length > 0 || n.getCurrentProfile() === "mobile" || n.getCurrentProfile() === "tablet") return;
                        r.each(t.options.sources, function(e) {
                            t.element.append(n("<source>").attr({
                                type: e.type,
                                src: e.src
                            }))
                        }), i.load(), i.play()
                    },
                    o = null;
                n(e).delegate(t.element, "resize", function() {
                    clearTimeout(o), o = setTimeout(function() {
                        o = null, s()
                    }, 500)
                }), s()
            }
        }), n(document).bind("ready widgetcreate", function(e) {
            n.ankama.ak_widget_video.prototype.enhanceWithin(e.target)
        })
    }(this),
    function(e, t) {
        "use strict";
        var n = require("ankama.widget"),
            r = require("lodash");
        n = require("jquery.ankama");
        var i = function() {
                e.scrollTo(0, this.element.find(this.options.animSelector).offset().top - 100)
            },
            s = function() {};
        n.widget("ankama.ak_monster_spells", n.ankama.widget, {
            options: {
                initSelector: ".ak-monster-spells",
                firstUrl: "",
                containerSelector: ".ak-anim",
                animSelector: ".ak-dofus-animation"
            },
            _jqAnimContainer: null,
            _sCurrentAnim: null,
            _bAnimInited: !1,
            _create: function() {
                this._jqAnimContainer = this.element.find(this.options.containerSelector), this.element.on("click", "a", n.proxy(this._onClickAnimation, this)), this.options.firstUrl && this._initAnimation(this.options.firstUrl)
            },
            _initAnimation: function(e) {
                var t = this;
                this._bAnimInited = !0, this._jqAnimContainer.html(n("<div>").addClass(this.options.animSelector.substr(1)).data("url", e)), setTimeout(function() {
                    t._jqAnimContainer.find(t.options.animSelector).ak_dofusanimation({
                        clickable: !0,
                        autostart: !0,
                        loop: !1,
                        start: n.proxy(i, t),
                        end: n.proxy(s, t)
                    })
                }, 0)
            },
            _onClickAnimation: function(e) {
                e.preventDefault(), "href" in e.currentTarget && (this._bAnimInited ? this._jqAnimContainer.find(this.options.animSelector).ak_dofusanimation("changeURL", e.currentTarget.href).ak_dofusanimation("play") : this._initAnimation(e.currentTarget.href), this.element.find("a").closest(".ak-list-element").removeClass("selected"), n(e.currentTarget, this.element).closest(".ak-list-element").addClass("selected"))
            }
        }), n(document).bind("ready widgetcreate", function(e) {
            n.ankama.ak_monster_spells.prototype.enhanceWithin(e.target)
        })
    }(this),
    function(e, t) {
        var n = require("ankama.widget"),
            n = require("jquery.ankama"),
            r = require("lodash");
        n.widget("ankama.ak_dofusanimation", n.ankama.widget, {
                options: {
                    initSelector: ".ak-dofus-animation",
                    loadingClass: "ak-dofus-animation-loading",
                    autostart: !0,
                    clickable: !0,
                    loop: !0,
                    fps: 25,
                    ready: null,
                    start: null,
                    progress: null,
                    end: null
                },
                _bLoaded: !1,
                _currentTime: null,
                _currentFrame: null,
                _totalFrames: null,
                _bStop: !1,
                _onClickElement: function(e) {
                    if (!this._bLoaded) return;
                    this._bStop = !this._bStop, this._bStop || this.play()
                },
                _create: function() {
                    var e = this.element.width(),
                        t = this.element.data("url");
                    if (!t) throw new Error("Please provide a data-url");
                    this.element.addClass(this.options.loadingClass), this._bLoaded = this._bStop = !1, this.options.autostart || (this._bStop = !0), this.options.clickable && (this.element.addClass("ak-dofus-animation-clickable"), this.element.on("click", n.proxy(this._onClickElement, this))), this.elImage = new Image, n(this.elImage).on("load", n.proxy(this._onLoadImage, this)), this.elImage.src = t, this.elImage.complete && n(this.elImage).load()
                },
                changeURL: function(t) {
                    t != this.elImage.src && (this.element.css("backgroundImage", "none"), this.element.addClass(this.options.loadingClass), cancelAnimationFrame(n.proxy(this._animLoop, this)), this._bLoaded = !1, this.elImage.src = t, this.elImage.complete && n(this.elImage).load())
                },
                _onLoadImage: function() {
                    n(this.element).removeClass(this.options.loadingClass);
                    if (!this.elImage.complete) return;
                    this._bLoaded = !0, this._totalFrames = this.elImage.naturalWidth / this.element.width(), this.element.css("backgroundImage", 'url("' + this.elImage.src + '")'), this._trigger("ready", null), this.options.autostart && this.play()
                },
                play: function() {
                    this._bStop = !1, this.element[0].style.backgroundPosition = "-0px 0", this._currentFrame = 0, this._currentTime = !1, this._trigger("start", null);
                    if (!this._bLoaded) {
                        this.element.one(this.widgetName + "ready", n.proxy(this.play, this));
                        return
                    }
                    requestAnimationFrame(n.proxy(this._animLoop, this))
                },
                _animLoop: function(t) {
                    this._currentTime || (this._currentTime = t);
                    var r = (t - this._currentTime) / 1e3;
                    this._currentFrame += r * this.options.fps;
                    var i = Math.floor(this._currentFrame);
                    i >= this._totalFrames && (this.options.loop || (this._bStop = !0), this._bStop == 1 && (cancelAnimationFrame(n.proxy(this._animLoop, this)), this._trigger("end", null)), this._currentFrame = i = 0), this._bStop || (this._trigger("progress", null, {
                        value: i
                    }), requestAnimationFrame(n.proxy(this._animLoop, this)), this.element[0].style.backgroundPosition = "-" + i * this.element.width() + "px 0", this._currentTime = t)
                },
                _destroy: function() {
                    n(this.elImage).off("load"), this.element.off("click"), cancelAnimationFrame(n.proxy(this._animLoop, this))
                },
                _init: function() {}
            }), n(document).bind("ready widgetcreate", function(e) {
                n.ankama.ak_dofusanimation.prototype.enhanceWithin(e.target)
            }),
            function() {
                var t = 0,
                    n = ["ms", "moz", "webkit", "o"];
                for (var r = 0; r < n.length && !e.requestAnimationFrame; ++r) e.requestAnimationFrame = e[n[r] + "RequestAnimationFrame"], e.cancelAnimationFrame = e[n[r] + "CancelAnimationFrame"] || e[n[r] + "CancelRequestAnimationFrame"];
                e.requestAnimationFrame || (e.requestAnimationFrame = function(n, r) {
                    var i = (new Date).getTime(),
                        s = Math.max(0, 16 - (i - t)),
                        o = e.setTimeout(function() {
                            n(i + s)
                        }, s);
                    return t = i + s, o
                }), e.cancelAnimationFrame || (e.cancelAnimationFrame = function(e) {
                    clearTimeout(e)
                })
            }()
    }(this),
    function(e) {
        var t = require("lodash"),
            n = require("ankama.widget");
        n.widget("ankama.ak_ga_event_tracking", n.ankama.widget, {
            _sMethod: null,
            _sHitType: null,
            _oFieldObject: null,
            options: {
                initSelector: ".ak-ga-event-tracking"
            },
            _create: function() {
                var e = this,
                    r = e.element.akOptions(),
                    i = t.isNull(r.aGaEventTracking) ? null : r.aGaEventTracking;
                return t.isArray(i) ? (e._sMethod = i[0], e._sHitType = i[1], e._oFieldObject = i[2], e.element.prop("tagName") == "A" || e.element.prop("tagName") == "INPUT" ? n(e.element).on("click", function(t) {
                    e._sendEvent(t)
                }) : e._sendEvent(), !0) : !1
            },
            _sendEvent: function(t) {
                var n = this;
                typeof e.ga == "function" ? e.ga(n._sMethod, n._sHitType, n._oFieldObject) : console.log("Google Analytics :", n._sMethod, n._sHitType, n._oFieldObject)
            }
        }), n(document).bind("ready widgetcreate", function(e) {
            n.ankama.ak_ga_event_tracking.prototype.enhanceWithin(e.target, !0)
        })
    }(this),
    function(e) {
        var t = require("jquery"),
            n = require("lodash");
        t.widget("ankama.ak_form_builder", t.ankama.ak_simpleform, {
            options: {
                initSelector: ".ak-form-buildernonused",
                ajaxSubmit: !1
            },
            _init: function() {
                var e = this;
                e._super(), e.jqValidator.settings.ignore = function(e, n) {
                    return t(n).closest(".ak-ignore-validation").length > 0
                }
            },
            _create: function() {
                var e = this,
                    n = t(".ak-button-submit", this.element).not(".nosticky");
                n.sticky({
                    topSpacing: 43
                }), e.element.bind("submit", function() {
                    return typeof CKEDITOR != "undefined" && t.each(CKEDITOR.instances, function(e, t) {
                        t.editable().isInline() && t.fire("blur"), t.element.$.value = t.getData()
                    }), t(window).off("beforeunload"), !0
                }), t(window).on("beforeunload", function(e) {
                    var n = !1;
                    typeof CKEDITOR != "undefined" && t.each(CKEDITOR.instances, function(e, t) {
                        if (t.checkDirty()) return n = !0, !1
                    });
                    if (n) {
                        var r = t("#ak-warning-changedfields").text();
                        return e.returnValue = r, r
                    }
                }), this._superApply(arguments)
            },
            elementErrorPlacement: function(e, n) {
                t(e).addClass("control-label");
                if (t(e).html() != "")
                    if (!n.is(":checkbox")) {
                        var r = n.next('script[type="application/json"]').first();
                        t(e).insertAfter(r.length > 0 ? r : n)
                    } else n.closest(".form-group").append(t(e))
            }
        }), t(document).bind("ready widgetcreate", function(e) {
            t.ankama.ak_form_builder.prototype.enhanceWithin(e.target)
        })
    }(),
    function(e, t) {
        "use strict";
        var n = require("ankama.widget"),
            r = require("lodash");
        n.widget("ankama.ak_objectslist", n.ankama.widget, {
            options: {
                initSelector: ".ak-objectslist",
                sName: null
            },
            _iIndex: 0,
            _bInAnOtherObjectsList: !1,
            _create: function() {
                var e = this,
                    t = n(e.element);
                this._bInAnOtherObjectsList = t.parents(".ak-objectslist-objects").length > 0;
                var r = t.find(".ak-objectslist-clonebtn:last"),
                    i = t.find(".ak-objectslist-clonedlist:first");
                if (!r.length || !i.length) return;
                e._iIndex = i.children(".ak-objectslist-objects").length, r.click(function() {
                    e.addLine()
                }), t.on("click", ".ak-objectslist-removebtn", function() {
                    e.removeLine(this)
                }), t.on("click", ".ak-objectslist-moveupbtn", function() {
                    e.moveLine(this)
                }), t.on("click", ".ak-objectslist-movedownbtn", function() {
                    e.moveLine(this)
                })
            },
            addLine: function() {
                var e = this,
                    t = n(e.element),
                    i = t.find(".ak-objectslist-toclone:first"),
                    s = t.find(".ak-objectslist-clonedlist:first");
                if (!i.length || !s.length) return;
                var o = r.unescape(i.html().trim()),
                    u = n(o.replace(/{ID}/g, ++e._iIndex)).addClass("ak-objectslist-objects");
                return e._processInputIndex(u, e._iIndex - 1), s.append(u), n(document).trigger("widgetcreate"), u
            },
            removeLine: function(e) {
                var t = this,
                    r = n(t.element).find(".ak-objectslist-clonedlist:first"),
                    i = n(".ak-objectslist-objects", r);
                if (!i.length || i.length == 1) return;
                var s = n(e).closest(".ak-objectslist-objects");
                s.length && s.remove(), t._iIndex = i.length, r.length || t.addLine(), i.each(function(e, r) {
                    t._processInputIndex(n(r), e)
                })
            },
            _processInputIndex: function(e, t) {
                var i = this;
                if (!this.options.sName) return !1;
                var s = this.options.sName,
                    o = function(e) {
                        return e.replace(/[-\/\\^$*+?.()|[\]{}]/g, "\\$&")
                    },
                    u;
                i._bInAnOtherObjectsList ? (u = new RegExp("\\[" + o(s) + "\\]\\[(.*?)\\]", "g"), s = "[" + s + "]") : u = new RegExp(o(s) + "\\[(.*?)\\]", "g"), r.map(n(":input[name]", e), function(e) {
                    var r = n(e).attr("name"),
                        o = r.replace(u, s + "[" + t + "]");
                    i._bInAnOtherObjectsList && (o = o.replace("[0]", "[" + (n(i.element).parents(".ak-objectslist").last().ak_objectslist("getIndex") - 1) + "]")), n(e).attr("name", o)
                })
            },
            removeAllLines: function() {
                n(this.element).find(".ak-objectslist-objects").remove(), this._iIndex = 0
            },
            moveLine: function(e) {
                var t = n(e).closest(".ak-objectslist-objects");
                t.find("textarea").data("ankama-ak_field_bbcode_editor") && t.find("textarea").ak_field_bbcode_editor("destroy"), t.next().hasClass("ak-objectslist-objects") && n(e).hasClass("ak-objectslist-movedownbtn") ? t.next().after(t) : t.prev().hasClass("ak-objectslist-objects") && n(e).hasClass("ak-objectslist-moveupbtn") && t.prev().before(t), setTimeout(function() {
                    n(document).trigger("widgetcreate")
                }, 0)
            },
            setValues: function(e) {
                var t = this;
                n.each(e, function(e, r) {
                    var i = t.addLine(),
                        s = {};
                    n.each(r, function(n, r) {
                        t.option("sName") !== null && (n = t.option("sName") + "[" + e + "][" + n + "]"), typeof r == "object" ? n += "[]" : r = [r], s[n] = r
                    }), n.each(s, function(e, t) {
                        var r = i.find('[name="' + e + '"]');
                        r.length && (r.closest(".ak-cascading").length ? r.closest(".ak-cascading").ak_cascading("setValues", s) : (n.each(t, function(e, t) {
                            r.val([t])
                        }), r.is("select") && (r.ak_select("update"), r.trigger("change"))))
                    })
                })
            },
            getIndex: function() {
                return this._iIndex
            }
        }), n(document).bind("ready widgetcreate", function(e) {
            n.ankama.ak_objectslist.prototype.enhanceWithin(e.target)
        })
    }(this),
    function(e, t) {
        var n = require("ankama.widget");
        n.widget("ankama.ak_dofusuniverse_form", n.ankama.ak_simpleform, {
            options: {
                initSelector: ".ak-dofusuniverse-form"
            },
            _create: function() {
                var e = this;
                n(".ak-submit").on("click", function(t) {
                    var n = e.element.find('[name="DOFUSUNIVERSE_ANSWER"]');
                    n.is(":checked") ? (n.closest(".form-group.ak-radios").removeClass("has-error"), n.closest(".form-group.ak-radios").find("label.ak-error").hide()) : (n.closest(".form-group.ak-radios").addClass("has-error"), n.closest(".form-group.ak-radios").find("label.ak-error").show()), e.element.submit()
                }), this._superApply(arguments)
            },
            _init: function() {
                this._superApply(arguments)
            }
        }), n(document).bind("ready widgetcreate", function(e) {
            n.ankama.ak_dofusuniverse_form.prototype.enhanceWithin(e.target)
        })
    }(this);