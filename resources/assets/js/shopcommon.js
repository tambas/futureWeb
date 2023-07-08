(function(window) {
    var _ = require("lodash"),
        $ = require("ankama.widget");
    $.widget("ankama.ak_toogle", $.ankama.widget, {
        options: {
            initSelector: ".ak-toggle",
            toggleTarget: null
        },
        _create: function() {
            var self = this;
            var sTargetSelector = self.option("toggleTarget");
            var jqTarget = $(sTargetSelector);
            self._manageClasses(jqTarget);
            self.element.on("click", function(event) {
                if (self.element.is("a")) event.preventDefault();
                jqTarget.toggle({
                    duration: 0,
                    complete: function(event) {
                        self._manageClasses($(this))
                    }
                })
            })
        },
        _manageClasses: function(jqTarget) {
            var self = this;
            if (jqTarget.is(":hidden")) {
                self.element.removeClass("ak-toggle-open").addClass("ak-toggle-closed")
            } else {
                self.element.removeClass("ak-toggle-closed").addClass("ak-toggle-open")
            }
        }
    });
    $(document).bind("ready widgetcreate", function(e) {
        $.ankama.ak_toogle.prototype.enhanceWithin(e.target, true)
    })
})(this);
(function(oContext, undefined) {
    var $ = require("ankama.widget"),
        _ = require("lodash");
    $.widget("ankama.ak_form_certification", $.ankama.ak_simpleform, {
        options: {
            initSelector: ".ak-form-certificationnonused"
        },
        _create: function() {
            var self = this;
            self.options.ajaxSubmit = false;
            self._superApply(arguments);
            var jqStateSelect = $("#state", self.element),
                aGroups = jqStateSelect.find("optgroup");
            console.log(jqStateSelect);
            $("#country").change(function() {
                var sCountry = $(this).val().toUpperCase();
                jqStateSelect.empty();
                if (sCountry == "AU" || sCountry == "CA" || sCountry == "US") {
                    $("#p_state").show();
                    oOptions = jqStateSelect.akOptions();
                    console.log("oOptions", oOptions);
                    jqStateSelect.find("option").remove();
                    jqStateSelect.append(new Option("", ""));
                    $.each(oOptions["state"][sCountry], function(index, value) {
                        jqStateSelect.append(new Option(value, index))
                    });
                    console.log(jqStateSelect)
                } else {
                    $("#p_state").hide();
                    jqStateSelect.val("")
                }
            }).trigger("change");
            $("#no_phone").click(function() {
                $("#gsm").val("");
                $("#gsmfieldset").hide();
                $("#phonefieldset").show();
                $(this).hide()
            });
            $(self.element).find('input[type="text"], select, textarea').on("focus", function(oEvent) {
                $(self.element).find(".ak-certification-fieldset-info").removeClass("open");
                $(oEvent.target).closest(".ak-fieldset").find(".ak-certification-fieldset-info").addClass("open")
            })
        },
        _init: function() {
            this._superApply(arguments);
            this.jqValidator.settings.ignore = ""
        },
        bConfirmed: false,
        setConfirmed: function(bConfirmed) {
            var self = this;
            self.bConfirmed = bConfirmed
        },
        beforeSubmit: function() {
            var self = this;
            if (!this._super()) return false;
            if (self.bConfirmed) return true;
            $(".ak-modal-certification-confirmation").ak_modal("open");
            $(".ak-modal-certification-confirmation").find(".ak-identity-name").html($(self.element).find('input[name="fname"]').val() + " " + $(self.element).find('input[name="lname"]').val().toUpperCase());
            return false
        }
    });
    $(document).bind("ready widgetcreate", function(oEvent) {
        $.ankama.ak_form_certification.prototype.enhanceWithin(oEvent.target)
    })
})(this);
(function(window, undefined) {
    var $ = require("ankama.widget");
    $.widget("ankama.ak_certificationform_modal", $.ankama.ak_modal, {
        options: {
            initSelector: ".ak-certificationform-modal"
        },
        _create: function() {
            var self = this;
            var oElement = $(self.element);
            oElement.on("click", function(oEvent) {
                if ($(oEvent.target).closest(".ak-certification-cancel").length) {
                    oElement.ak_modal("close");
                    oEvent.stopPropagation();
                    return false
                }
                if ($(oEvent.target).closest(".ak-certification-confirm").length) {
                    $("#ak-form-certification").ak_form_certification("setConfirmed", true);
                    $("#ak-form-certification").submit()
                }
            });
            $(oElement).find(".ak-identity-name").focus();
            self._superApply(arguments)
        },
        close: function() {
            var self = this;
            self._superApply(arguments);
            $(".ak-cancel-certification-trigger").click();
            $("#ak-form-certification").ak_form_certification("setConfirmed", false)
        }
    });
    $(document).bind("ready widgetcreate", function(oEvent) {
        $.ankama.ak_certificationform_modal.prototype.enhanceWithin(oEvent.target)
    })
})(this);
(function(oContext, undefined) {
    "use strict";
    var $ = require("ankama.widget");
    var _ = require("lodash");
    var Q = require("q");
    var hammer = require("jquery.hammer");
    $.widget("ankama.ak_carouseltouch", $.ankama.widget, {
        options: {
            initSelector: ".ak-carouseltouch",
            showArrows: true,
            paginationcontrol: "",
            startindex: 0,
            viewwidth: 0,
            width: 0,
            height: 0,
            autoroll: false,
            itemwidth: 0,
            spacebetween: 6,
            circular: true,
            responsive: false,
            snap: true,
            animationclick: "slide",
            animationduration: 300,
            select: null,
            loaded: null,
            ready: null,
            tap: null
        },
        iCurrentIndex: 0,
        _bCanMove: true,
        _bFirstInitDone: false,
        _jqItemsContainer: null,
        _autoRollInterval: null,
        oPaginationModule: null,
        _destroy: function() {
            this._jqItemsContainer.off("release dragstart dragend dragleft dragright swipeleft swiperight", this._handleHammer);
            this._jqItemsContainer = null;
            if (this.oPaginationModule) {
                this.oPaginationModule.off("click");
                this.oPaginationModule.destroy();
                this.oPaginationModule = null
            }
        },
        _create: function() {
            var self = this;
            $(oContext).on("resize", $.proxy(this._onResize, self));
            this._iTotalItems = this.element.children().length;
            if (this.options.autoroll) {
                this.options.itemwidth = 1;
                this.options.circular = true;
                self.element.hover(function() {
                    self._stopAutoRoll()
                }, function() {
                    self._startAutoRoll()
                });
                this._startAutoRoll()
            }
            this._generateMarkup();
            this.element.addClass("loading");
            this.aPositions = [0];
            this.element.find(".carouselcontainer").hammer({
                drag_lock_to_axis: true,
                stop_browser_behavior: {
                    touchAction: "pan-y"
                }
            }).on("tap release dragstart dragend dragleft dragright swipeleft swiperight", $.proxy(this._handleHammer, this));
            this._trigger("ready", null)
        },
        _startAutoRoll: function() {
            var self = this;
            if (!this.options.autoroll) return false;
            this._stopAutoRoll();
            this._autoRollInterval = window.setTimeout(function() {
                self.gotoIndex(++self.iCurrentIndex)
            }, this.options.autoroll === true ? 5e3 : this.options.autoroll)
        },
        _stopAutoRoll: function() {
            clearTimeout(this._autoRollInterval)
        },
        _onClickAction: function(oEvent) {
            var self = this;
            var curX, itemW, viewportW, indexToGo;
            switch ($(oEvent.currentTarget).data("action")) {
                case "prev":
                    curX = Math.abs(this._curX);
                    viewportW = +this.option("viewwidth");
                    var currentPanel = 0;
                    _.each(self.aPositions, function(iPos, iIndex) {
                        if (curX <= iPos) {
                            currentPanel = iPos;
                            return false
                        }
                    });
                    _.each(self.aPositions, function(iPos, iIndex) {
                        indexToGo = iIndex;
                        if (currentPanel - viewportW <= iPos) {
                            return false
                        }
                    });
                    if (indexToGo === this.iCurrentIndex) {
                        indexToGo--
                    }
                    if (indexToGo < 0) {
                        if (curX != 0 || !this.option("circular")) {
                            indexToGo = 0
                        } else {
                            indexToGo = this._iTotalItems - 1
                        }
                    }
                    break;
                case "next":
                    curX = Math.abs(this._curX);
                    viewportW = +this.option("viewwidth");
                    _.each(self.aPositions, function(iPos, iIndex) {
                        if (curX + viewportW < iPos) {
                            return false
                        }
                        indexToGo = iIndex
                    });
                    if (indexToGo === this.iCurrentIndex) {
                        indexToGo++
                    }
                    if (indexToGo >= this._iTotalItems) {
                        if (!this.option("circular")) {
                            indexToGo = this._iTotalItems - 1
                        } else {
                            indexToGo = 0
                        }
                    }
                    break
            }
            self.gotoIndex(indexToGo)
        },
        iMaxWidth: 0,
        _iInitTimes: 0,
        aPositions: null,
        _bLoading: false,
        _init: function() {
            var self = this;
            if (self._bLoading) return;
            self._bLoading = true;
            self.element.css("opacity", 1);
            self._jqItemsContainer = this.element.find(".carouselcontainer");
            self._bCanMove = true;
            self.bShouldRedraw = !self._bFirstInitDone || self.options.responsive || self.bShouldRedraw;
            if (self.options.itemwidth !== undefined && self.options.itemwidth === 1) {
                self.element.css("width", self.element.offsetParent().outerWidth());
                if (self.element.find("img").length == 1) self.element.find("img").addClass("img-maxresponsive")
            } else {
                if (!self.element.width()) self.element.css("width", self.element.parent().outerWidth())
            }
            if (self.options.viewwidth) self.element.width(self.options.viewwidth);
            else if (self.element.width() === 0) {
                if (self._iInitTimes < 30) {
                    setTimeout(function() {
                        self._iInitTimes++;
                        self._init()
                    }, 0);
                    return
                } else {
                    throw new Error("Impossible de dÃ©finir la taille du conteneur")
                }
            }
            self._setOption("viewwidth", self.element.width());
            self.iTotalItems = self.element.find(".item").length;
            self.iMaxWidth = 0;
            self._maxX = 0;
            var queueImgs;
            self.element.find(".item").each(function(iIndex, elLi) {
                var iItemWidth;
                var jqLi = $(elLi);
                var jqChild = jqLi.children().first();
                if (self.options.itemwidth && self.options.itemwidth <= 1) {
                    self.options.spacebetween = self.options.itemwidth === 1 ? 0 : self.options.spacebetween;
                    jqLi.css("width", self.options.viewwidth * self.options.itemwidth + "px");
                    iItemWidth = jqLi.width() + self.options.spacebetween;
                    self.aPositions[iIndex + 1] = iItemWidth
                } else {
                    if (jqChild.attr("width")) {
                        jqLi.css("width", jqChild.attr("width") + "px");
                        iItemWidth = jqChild.width() + self.options.spacebetween;
                        self.aPositions[iIndex + 1] = iItemWidth
                    } else {
                        if (jqChild[0].tagName == "IMG" || jqChild.find("img").length) {
                            if (!queueImgs) queueImgs = [];
                            var elImgs;
                            if (jqChild.find("img").length) {
                                elImgs = jqChild.find("img")
                            } else {
                                elImgs = jqChild
                            }
                            var countLoadPer = 0;
                            var deferred = Q.defer();
                            queueImgs.push(deferred.promise);
                            self.aPositions[iIndex + 1] = 0;
                            elImgs.each(function(iIndexImg, elImgPer) {
                                if (elImgPer.complete) {
                                    if (jqLi.width() < elImgPer.width) jqLi.css("width", elImgPer.width + "px");
                                    iItemWidth = jqLi.width() + self.options.spacebetween;
                                    self.aPositions[iIndex + 1] = Math.max(self.aPositions[iIndex + 1], iItemWidth);
                                    countLoadPer++;
                                    if (elImgs.length == countLoadPer) {
                                        deferred.resolve("loaded")
                                    }
                                } else {
                                    $(elImgPer).one("error", {
                                        jqLi: jqLi,
                                        iIndex: iIndex
                                    }, function(oEvent) {
                                        oEvent.preventDefault();
                                        deferred.reject(new Error("Error load"))
                                    });
                                    $(elImgPer).one("load", {
                                        jqLi: jqLi,
                                        iIndex: iIndex
                                    }, function(oEvent) {
                                        if (jqLi.width() < elImgPer.width) oEvent.data.jqLi.css("width", this.width + "px");
                                        iItemWidth = oEvent.data.jqLi.width() + self.options.spacebetween;
                                        self.aPositions[oEvent.data.iIndex + 1] = Math.max(self.aPositions[oEvent.data.iIndex + 1], iItemWidth);
                                        countLoadPer++;
                                        if (elImgs.length == countLoadPer) {
                                            deferred.resolve("loaded")
                                        }
                                    })
                                }
                            })
                        } else if (jqChild.css("width") != "0px") {
                            self.aPositions[iIndex + 1] = jqChild.width() + self.options.spacebetween
                        } else {
                            jqLi.css("display", "none")
                        }
                    }
                }
            });
            if (queueImgs) {
                Q.all(queueImgs).then(function(results) {
                    self._trigger("loaded", null);
                    self._onLoaded()
                })
            } else {
                self._trigger("loaded", null);
                self._onLoaded()
            }
        },
        _firstAnimated: false,
        _bLoaded: false,
        _onLoaded: function() {
            var self = this;
            self._bLoading = false;
            if (self.bShouldRedraw) {
                var valCumul = 0;
                _.each(self.aPositions, function(val, ind) {
                    valCumul += val;
                    self.aPositions[ind] = valCumul
                });
                self.iMaxWidth = self.aPositions[self.aPositions.length - 1]
            }
            self._bLoaded = true;
            self._setOption("width", self.iMaxWidth);
            self._maxX = self.option("width") - self.option("viewwidth");
            if (self.options.height) {
                self.element.height(self.options.height ? self.options.height : self.element.outerHeight(true))
            }
            var iPrecLeft = 0;
            self.element.find(".item").each(function(iIndex, elLi) {
                if (self.bShouldRedraw) {
                    $(elLi).height(self.element.height());
                    $(elLi).css("line-height", self.element.height() + "px")
                }
                elLi.style.left = iPrecLeft + "px";
                iPrecLeft += $(elLi).width() + self.options.spacebetween
            });
            if (self.options.width < self.options.viewwidth || self.options.itemwidth === 1 && self.iTotalItems <= 1) {
                self.element.find("[data-action]").hide();
                self._jqItemsContainer.animate({
                    left: 0
                }, self.options.animationduration);
                self._bCanMove = false
            } else {
                self.element.find("[data-action]").show();
                self._bCanMove = true
            }
            if (!self._firstAnimated && !self.element.hasClass("firstanim")) {
                self.element.addClass("firstanim");
                self.element.animate({
                    opacity: 0
                }, 200, function() {
                    self.element.removeClass("loading");
                    self.element.animate({
                        opacity: 1
                    }, 500, function() {
                        self._firstAnimated = true;
                        self.element.removeClass("firstanim");
                        self._onFinishInit()
                    })
                })
            } else {
                self._onFinishInit()
            }
        },
        _onFinishInit: function() {
            var self = this;
            self.bShouldRedraw = false;
            if (typeof self.options.startindex !== "undefined") {
                self.gotoIndex(self.options.startindex, false);
                delete self.options.startindex
            }
            self._bFirstInitDone = true;
            self._trigger("inited")
        },
        resize: function() {
            self._onResize()
        },
        _iResizeTimerId: null,
        _onResize: function() {
            var self = this;
            self.bShouldRedraw = true;
            if (typeof this._iResizeTimerId !== "undefined") {
                clearTimeout(this._iResizeTimerId)
            }
            this._iResizeTimerId = setTimeout(function() {
                self._init();
                if (self._bCanMove && self.iCurrentIndex !== undefined) {
                    self.gotoIndex(self.iCurrentIndex)
                }
            }, 100)
        },
        _curX: 0,
        _maxX: 0,
        _bSaveX: true,
        _animateTo: function(iIndex, bAnimate) {
            var self = this;
            if (!self._bCanMove) {
                return
            }
            bAnimate = bAnimate === undefined ? true : bAnimate;
            this._curX = -this.element.find(".items .item:eq(" + iIndex + ")").css("left").replace("px", "");
            if (Math.abs(this._curX) > this._maxX) {
                this._curX = -this._maxX
            }
            this._bBlockUI = true;
            if (self.options.animationclick === "fade") {
                self._jqItemsContainer.fadeTo(bAnimate ? self.options.animationduration : 0, 0, function() {
                    self._jqItemsContainer.css("left", self._curX + "px");
                    self._jqItemsContainer.fadeTo(bAnimate ? self.options.animationduration : 0, 1, function() {
                        self.iCurrentIndex = iIndex;
                        self._bBlockUI = false
                    })
                })
            } else {
                self._jqItemsContainer.animate({
                    left: this._curX
                }, bAnimate ? self.options.animationduration : 0, function() {
                    self.iCurrentIndex = iIndex;
                    self._bBlockUI = false
                })
            }
        },
        gotoIndex: function(indexToGo, bAnimate) {
            if (this._bBlockUI) {
                return false
            }
            if (indexToGo < 0) {
                if (!this.option("circular")) {
                    indexToGo = 0
                } else {
                    indexToGo = this._iTotalItems - 1
                }
            } else {
                if (indexToGo >= this._iTotalItems) {
                    if (!this.option("circular")) {
                        indexToGo = this._iTotalItems - 1
                    } else {
                        indexToGo = 0
                    }
                }
            }
            if (indexToGo !== undefined) {
                this._startAutoRoll();
                this._animateTo(indexToGo, bAnimate);
                if (this.oPaginationModule) {
                    this.oPaginationModule.gotoIndex(indexToGo)
                }
                var jqItem = this.element.find(".item:eq(" + indexToGo + ")");
                this._trigger("select", null, {
                    index: indexToGo,
                    item: jqItem
                })
            }
        },
        _getCurrentIndex: function(iX) {
            var self = this,
                iReturn = 0;
            _.each(self.aPositions, function(iWidth, iIndex) {
                if (iX < iWidth) {
                    return false
                }
                iReturn = iIndex
            });
            return iReturn
        },
        _getLeftForIndex: function(iIndex) {
            return this.aPositions[iIndex]
        },
        _handleHammer: function CarouselHandleHammer(ev) {
            if (!("gesture" in ev)) return;
            var self = this;
            if (!_.contains([hammer.DIRECTION_UP, hammer.DIRECTION_DOWN], ev.gesture.direction)) ev.gesture.preventDefault();
            var bShouldSwipe = self.options.itemwidth === 1 ? true : false;
            if (!bShouldSwipe) return false;
            switch (ev.type) {
                case "dragright":
                case "dragleft":
                    if (bShouldSwipe) {
                        return
                    }
                    var newX = this._curX + +ev.gesture.deltaX;
                    var currentItem = this._getCurrentIndex(Math.abs(newX));
                    if (ev.gesture.direction == hammer.DIRECTION_RIGHT && newX > 0) {
                        this._curX = 0;
                        this._bSaveX = false;
                        ev.gesture.stopPropagation();
                        return
                    }
                    if (ev.gesture.direction == hammer.DIRECTION_LEFT && Math.abs(newX) > this._maxX) {
                        this._curX = -this._maxX;
                        this._bSaveX = false;
                        ev.gesture.stopPropagation();
                        return
                    }
                    this.iCurrentIndex = currentItem;
                    if (this.oPaginationModule) {
                        this.oPaginationModule.gotoIndex(currentItem)
                    }
                    this._bSaveX = true;
                    this._jqItemsContainer.css("left", newX + "px");
                    break;
                case "swipeleft":
                    if (!bShouldSwipe) {
                        return false
                    }
                    this._curX += ev.gesture.deltaX;
                    var iIndex = this.iCurrentIndex;
                    iIndex++;
                    this._bSaveX = true;
                    this.gotoIndex(iIndex);
                    ev.gesture.stopDetect();
                    break;
                case "swiperight":
                    if (!bShouldSwipe) {
                        return false
                    }
                    this._curX += ev.gesture.deltaX;
                    var iIndex = this.iCurrentIndex;
                    iIndex--;
                    this._bSaveX = true;
                    this.gotoIndex(iIndex);
                    this._bSaveX = true;
                    ev.gesture.stopDetect();
                    break;
                case "release":
                    if (this._bSaveX) {
                        this._curX += ev.gesture.deltaX
                    }
                    this._bSaveX = true;
                    break;
                case "tap":
                    this._trigger("tap", ev);
                    ev.gesture.stopDetect();
                    return;
                    break
            }
            if (_.contains(["swipeleft", "swiperight"], ev.type)) {
                return
            }
            if (this.options.snap && _.contains(["release"], ev.type)) {
                ev.stopImmediatePropagation();
                ev.preventDefault();
                var curX = Math.abs(self._curX);
                var iIndex = self._getCurrentIndex(curX);
                var iLeftCurrent = self._getLeftForIndex(iIndex);
                var iLeftNext = self._getLeftForIndex(iIndex + 1);
                var iDiff = iLeftNext - iLeftCurrent;
                if (curX - iLeftCurrent > iDiff / 2) {
                    iIndex++
                }
                this.gotoIndex(iIndex)
            }
        },
        _generateMarkup: function() {
            var aChildrens = this.element.children();
            _.each(aChildrens, function(elValue, iIndex) {
                $(elValue).wrap($('<li class="item">'));
                $(elValue).parent().data($(elValue).data());
                $(elValue).data({})
            }, this);
            this.element.find(".item").wrapAll('<ul class="items">');
            this.element.find(".items").wrap('<div class="carouselcontainer">');
            if (this.options.showArrows === true) {
                if (!this.element.siblings("[data-action]").length) {
                    this.element.on("click", "[data-action]", $.proxy(this._onClickAction, this));
                    this.element.prepend('<button data-action="prev" class="prev arrow arrow-left">');
                    this.element.prepend('<button data-action="next" class="next arrow arrow-right">')
                } else {
                    this.element.siblings("[data-action]").on("click", $.proxy(this._onClickAction, this))
                }
            }
            if (this.options.paginationcontrol !== "") {
                var sPaginationList = "",
                    iPaginationIndex;
                for (iPaginationIndex = 0; iPaginationIndex < this._iTotalItems; iPaginationIndex++) {
                    sPaginationList += "<li><span>" + (iPaginationIndex + 1) + "</span></li>"
                }
                this.element[this.option("paginationcontrol") == "next" ? "after" : "before"]('<ul class="pagination">');
                this.element[this.option("paginationcontrol") == "next" ? "next" : "prev"]().append($("<ul>").append(sPaginationList));
                var mPaginator = require("utils/pagination");
                var myPag = new mPaginator({
                    element: this.element[this.option("paginationcontrol") == "next" ? "next" : "prev"](),
                    itemelement: "li",
                    circular: this.options.circular
                });
                this.oPaginationModule = myPag;
                this.oPaginationModule.on("click", $.proxy(function(ev, data) {
                    this.gotoIndex(data.clickedindex)
                }, this))
            }
        }
    });
    $(document).bind("ready widgetcreate", function(oEvent) {
        $.ankama.ak_carouseltouch.prototype.enhanceWithin(oEvent.target)
    })
})(this);
(function(oContext, undefined) {
    "use strict";
    var $ = require("ankama.widget"),
        _ = require("lodash");
    require("jquery.magnific-popup");
    $.widget("ankama.ak_lightboxtouch", $.ankama.widget, {
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
        _destroy: function onLightBoxDestroy() {
            self.jqContainerCarousel.remove();
            self.jqContainerCarousel = null;
            if (this.oMasterCarousel) {
                this.oMasterCarousel = this.oThumbsCarousel = null
            }
        },
        _onResize: function(oEvent) {
            this.resize()
        },
        resize: function onLightBoxResize() {
            if (this.oMasterCarousel && this.oThumbsCarousel) {
                this.oMasterCarousel.option({
                    viewwidth: $(oContext).width(),
                    itemwidth: 1,
                    circular: true,
                    responsive: true,
                    snap: true,
                    height: $(oContext).height() * .8
                });
                this.oThumbsCarousel.option({
                    viewwidth: $(oContext).width(),
                    circular: true,
                    snap: false,
                    height: this.options.thumbsHeight
                })
            }
        },
        _create: function onLightBoxCreate() {
            var self = this;
            if (!this.element.attr("id")) {
                this.element.uniqueId()
            }
            if (!this.options.type) {
                throw new Error("A type is needed")
            }
            if (this.options.gallery) {
                this.sGalleryClassName = "lb-" + this.options.gallery;
                this.element.addClass(this.sGalleryClassName);
                this.element.off("click").click(function(oEvent) {
                    self._initGallery();
                    oEvent.preventDefault();
                    var iIndex = _.findIndex($.ankama.ak_lightboxtouch.oCachesGalleries[self.sGalleryClassName], function(elLink) {
                        return oEvent.currentTarget.id === elLink.id
                    });
                    self._initCarousels(iIndex);
                    $.magnificPopup.open({
                        closeOnBgClick: true,
                        items: [{
                            src: self.jqContainerCarousel,
                            type: "inline"
                        }],
                        callbacks: {
                            open: function() {
                                $.ankama.ak_lightboxtouch.bActive = true;
                                $(oContext).on("keyup", $.proxy(self._onContextKeyUp, self));
                                $(oContext).trigger("resize")
                            },
                            close: function() {
                                $.ankama.ak_lightboxtouch.bActive = false;
                                $.magnificPopup.instance.container.off("tap");
                                $(oContext).off("resize", $.proxy(this._onResize, this));
                                $(oContext).off("keyup", $.proxy(self._onContextKeyUp, self));
                                if (!_.isNull(self.oMasterCarousel)) {
                                    self.oMasterCarousel.destroy()
                                }
                                if (!_.isNull(self.oThumbsCarousel)) {
                                    self.oThumbsCarousel.destroy()
                                }
                                self.jqContainerCarousel.remove();
                                self.jqContainerCarousel = null;
                                self.oMasterCarousel = null;
                                self.oThumbsCarousel = null
                            }
                        },
                        modal: true
                    });
                    var magnificPopup = $.magnificPopup.instance;
                    magnificPopup.container.attr("id", Math.ceil(Math.random() * 1e5));
                    magnificPopup.container.on("tap", function(ev) {
                        if (ev.target.tagName == "BUTTON") return;
                        else if (ev.target.tagName == "IMG" && self.oMasterCarousel) {
                            self.oMasterCarousel.gotoIndex(self.oMasterCarousel.iCurrentIndex + 1)
                        } else {
                            magnificPopup.close()
                        }
                        if (self.oMasterCarousel) {
                            self.oMasterCarousel.gotoIndex(self.oMasterCarousel.iCurrentIndex + 1);
                            self.oThumbsCarousel.gotoIndex(self.oThumbsCarousel.iCurrentIndex + 1)
                        }
                    })
                });
                $(oContext).off("resize", $.proxy(this._onResize, this));
                $(oContext).on("resize", $.proxy(this._onResize, this))
            } else {
                if (self.options.type == "swf" && "swfPath" in self.options) {
                    self.options = _.merge(_.clone(self.options), {
                        type: "inline",
                        midClick: true,
                        items: {
                            src: '<button title="Close (Esc)" type="button" class="mfp-close">Ã—</button>' + '<object data="' + self.options.swfPath + '" ' + 'type="application/x-shockwave-flash" id="sb-content" ' + 'width="' + ("swfFlashvars" in self.options ? self.options.swfWidth : "800") + '" ' + 'height="' + ("swfFlashvars" in self.options ? self.options.swfHeight : "600") + '">' + '<param name="bgcolor" value="">' + '<param name="allowFullscreen" value="true">' + '<param name="flashvars" value="' + ("swfFlashvars" in self.options ? self.options.swfFlashvars : "") + '">' + '<param name="expressInstaller" value="http://staticns.ankama.com/global/swf/expressInstall.swf">' + '<param name="movie" value="' + self.options.swfPath + '">' + '<param name="wmode" value="opaque">' + "</object>"
                        }
                    })
                }
                var oOpts = _.merge({
                    callbacks: {
                        beforeOpen: function() {
                            $(oContext.document).trigger("widgetcreate")
                        },
                        open: function() {
                            self._trigger("open")
                        },
                        close: function() {
                            if (!_.isNull(self.oMasterCarousel)) {
                                self.oMasterCarousel.destroy()
                            }
                            if (!_.isNull(self.oThumbsCarousel)) {
                                self.oThumbsCarousel.destroy()
                            }
                            self._trigger("close")
                        }
                    }
                }, _.clone(self.options));
                $(this.element).magnificPopup(oOpts)
            }
        },
        _initGallery: function onLightBoxGalleryInit() {
            if (!$.ankama.ak_lightboxtouch.oCachesGalleries) {
                $.ankama.ak_lightboxtouch.oCachesGalleries = {}
            }
            if (!$.ankama.ak_lightboxtouch.oCachesGalleries || !(this.sGalleryClassName in $.ankama.ak_lightboxtouch.oCachesGalleries)) {
                $.ankama.ak_lightboxtouch.oCachesGalleries[this.sGalleryClassName] = oContext.document.getElementsByClassName(this.sGalleryClassName)
            }
        },
        _initCarousels: function getCarouselsHTML(iIndex) {
            var self = this;
            var aGalleryElements = $.ankama.ak_lightboxtouch.oCachesGalleries[self.sGalleryClassName];
            var jqMasterCarousel = $('<div class="lb-master ' + self.options.touchSelector.slice(1) + " gallery-" + self.sGalleryClassName + '">');
            var jqThumbsCarousel = $('<div class="lb-thumbs ' + self.options.touchSelector.slice(1) + " gallery-" + self.sGalleryClassName + '">');
            _(aGalleryElements).each(function(elValue) {
                var jqValue = $(elValue);
                var sType = jqValue.ak_lightboxtouch("option", "type");
                var jqToAdd = null;
                switch (sType) {
                    case "image":
                        jqToAdd = $('<img style="max-height: 100%;max-width:100%" src="' + elValue.href + '" />');
                        break;
                    case "iframe":
                        jqToAdd = $('<iframe width="90%" height="90%" src="' + elValue.href + '" frameborder="0" allowfullscreen></iframe>');
                        break;
                    case "inline":
                        if (jqValue.data("src")) {
                            jqToAdd = $(jqValue.data("src")).clone().css("display", "block")
                        } else {
                            jqToAdd = jqValue.next().clone().css("display", "block")
                        }
                        break
                }
                if (!jqValue.data("title")) {
                    jqValue.data("title", jqValue.attr("title"))
                }
                jqToAdd.data(jqValue.data());
                jqMasterCarousel.append(jqToAdd);
                jqThumbsCarousel.append('<img src="' + jqValue.data("thumb") + '" />')
            });
            self.jqContainerCarousel = $('<div class="lb-container ak-carousel">').uniqueId();
            self.jqContainerCarousel.append('<button title="Close (Esc)" type="button" class="mfp-close">Ã—</button>');
            self.jqContainerCarousel.append(jqMasterCarousel);
            self.jqContainerCarousel.append($('<div class="mfp-bottom-bar">').append($('<div class="mfp-title">').text(""), jqThumbsCarousel));
            jqMasterCarousel.ak_carouseltouch({
                viewwidth: $(oContext).width(),
                itemwidth: 1,
                startindex: iIndex,
                circular: true,
                responsive: true,
                animationclick: "fade",
                snap: true,
                height: $(oContext).height() * .8,
                select: $.proxy(self._onMasterSelect, self)
            });
            jqThumbsCarousel.ak_carouseltouch({
                viewwidth: $(oContext).width(),
                startindex: iIndex,
                circular: true,
                snap: false,
                height: this.options.thumbsHeight
            });
            self.oMasterCarousel = jqMasterCarousel.data("ankama-ak_carouseltouch");
            self.oThumbsCarousel = jqThumbsCarousel.data("ankama-ak_carouseltouch");
            jqThumbsCarousel.on("tap", ".item", $.proxy(self._onClickThumbItem, self));
            $(".item", jqThumbsCarousel).css("cursor", "pointer")
        },
        _jqTitle: null,
        _onMasterSelect: function(oEvent, oData) {
            var self = this;
            self._jqTitle = self.jqContainerCarousel.find(".mfp-title");
            setTimeout(function() {
                var sTitle = oData.item.data("title") || oData.item.attr("title");
                if (sTitle) {
                    self._jqTitle.text(sTitle);
                    if (oData.item.data("description")) {
                        self._jqTitle.append("<small>" + oData.item.data("description") + "</small>")
                    }
                } else {
                    self._jqTitle.text("")
                }
                setTimeout(function() {
                    if (self.oThumbsCarousel && self.oMasterCarousel) {
                        self.oThumbsCarousel.gotoIndex(oData.index)
                    }
                }, 0)
            }, 0)
        },
        _onClickThumbItem: function(oEvent) {
            var iIndex = $(oEvent.currentTarget).index();
            this.oMasterCarousel.gotoIndex(iIndex);
            var elClickedLink = this._getElementByIndex(iIndex)
        },
        _getElementByIndex: function(iIndex) {
            var aGalleryElements = $.ankama.ak_lightboxtouch.oCachesGalleries[this.sGalleryClassName];
            var iCnt = 0;
            var elSearch = _.find(aGalleryElements, function(elLink) {
                return iCnt === iIndex
            });
            return elSearch
        },
        _onContextKeyUp: function(oEvent) {
            if (!$.ankama.ak_lightboxtouch.bActive) {
                return
            }
            if (oEvent.keyCode == 37 || oEvent.keyCode == 38) {
                this.oMasterCarousel.gotoIndex(this.oMasterCarousel.iCurrentIndex - 1)
            }
            if (oEvent.keyCode == 39 || oEvent.keyCode == 40) {
                this.oMasterCarousel.gotoIndex(this.oMasterCarousel.iCurrentIndex + 1)
            }
            if (oEvent.keyCode == 27) {
                $.magnificPopup.instance.close()
            }
        }
    });
    $.ankama.ak_lightboxtouch.bActive = false;
    $(oContext.document).bind("ready widgetcreate", function(oEvent) {
        require("utils/shadowshim")();
        $.ankama.ak_lightboxtouch.prototype.enhanceWithin(oEvent.target)
    })
})(this);
(function(oContext, undefined) {
    "use strict";
    var _ = require("lodash"),
        $ = require("ankama.widget");
    $.widget("ankama.ak_imagesviewer", $.ankama.widget, {
        options: {
            initSelector: ".ak-imagesviewer"
        },
        _create: function() {
            var self = this;
            var jqImages = $(".ak-images", self.element);
            var jqThumbs = $(".ak-thumbs", self.element);
            jqThumbs.on("click tap", ".ak-thumb", function(event) {
                event.preventDefault();
                $(".ak-image", jqImages).removeClass("show").addClass("hide");
                $("." + $(event.currentTarget).attr("id"), jqImages).removeClass("hide").addClass("show")
            })
        }
    });
    $(document).bind("ready widgetcreate", function(e) {
        $.ankama.ak_imagesviewer.prototype.enhanceWithin(e.target, true)
    })
})(this);
(function(oContext, undefined) {
    var $ = require("ankama.widget"),
        _ = require("lodash");
    $.widget("ankama.ak_box_bag", $.ankama.widget, {
        options: {
            initSelector: ".ak-box-bag"
        },
        iTimeout: null,
        _create: function() {
            var self = this,
                jqElement = self.element;
            jqElement.bind("ajaxloaded", function(oEvent) {
                if (jqElement.is(":visible")) {
                    $.openBasketModal();
                    clearTimeout(self.iTimeout);
                    self.iTimeout = setTimeout(function() {
                        self.iTimeout = null;
                        $(".qtip").qtip("hide")
                    }, 5e3)
                }
            });
            jqElement.find(".ak-box-bag-close").click(function() {
                $(this).closest(".qtip").qtip("hide")
            })
        }
    });
    $.widget("ankama.ak_box_basket", $.ankama.widget, {
        options: {
            initSelector: ".ak-box-basket"
        },
        _create: function() {
            var self = this,
                jqElement = self.element;
            jqElement.find(".ak-box-bag-close").click(function() {
                $(this).closest(".ak-button-modal-content").data("button").ak_button_modal("hide")
            })
        }
    });
    $(document).bind("ready widgetcreate", function(oEvent) {
        $.ankama.ak_box_bag.prototype.enhanceWithin(oEvent.target);
        $.ankama.ak_box_basket.prototype.enhanceWithin(oEvent.target)
    })
})(this);
(function(undefined) {
    var $ = require("ankama.widget");
    _ = require("lodash");
    require("jquery.ui.slider");
    $.widget("ankama.ak_objectslider", $.ankama.widget, {
        options: {
            initSelector: ".ak-object-slider",
            selectors: {
                specs: ".ak-object-spec",
                slider: ".ak-object-specs-slider"
            }
        },
        aLevels: null,
        jqSlider: null,
        jqInput: null,
        heLast: undefined,
        _destroy: function() {
            this.jqSlider.slider("destroy");
            this.jqInput.unbind()
        },
        _create: function() {},
        _init: function() {
            var self = this;
            this.aLevels = this.element.find(this.options.selectors.specs);
            if (this.aLevels < 2) return false;
            this.jqInput = this.element.find("input");
            if (this.jqSlider !== null) this.jqSlider.slider("destroy");
            this.jqSlider = this.element.find("div.slider").slider({
                orientation: "horizontal",
                min: 0,
                max: this.aLevels.length - 1,
                range: "min",
                create: $.proxy(self._sliderCreate, self),
                slide: $.proxy(self._sliderChange, self)
            });
            this.value(this.aLevels.length - 1)
        },
        value: function(value) {
            if (this.heLast !== undefined) this.heLast.style.display = "none";
            this.heLast = this.aLevels[value];
            this.heLast.style.display = "";
            this.jqInput.val(value);
            this.jqSlider.slider("value", value)
        },
        _sliderCreate: function(jqEvent, oUi) {
            var self = this;
            this.jqInput.bind("change", function() {
                self.value(this.value)
            });
            if (this.jqInput.get(0).type == "text") {
                this.jqInput.bind("keyup", function(jqEvent) {
                    switch (jqEvent.keyCode) {
                        case 13:
                            self.value(this.value);
                            break;
                        case 38:
                            var nVal = parseInt(this.value) + 1;
                            if (nVal > self.aLevels.length - 1) nVal = self.aLevels.length - 1;
                            this.value = nVal;
                            self.value(this.value);
                            break;
                        case 40:
                            var nVal = parseInt(this.value) - 1;
                            if (nVal < 0) nVal = 0;
                            this.value = nVal;
                            self.value(this.value);
                            break
                    }
                })
            }
        },
        _sliderChange: function(jqEvent, oUi) {
            this.value(oUi.value)
        }
    });
    $(document).bind("ready widgetcreate", function(oEvent) {
        $.ankama.ak_objectslider.prototype.enhanceWithin(oEvent.target)
    })
})();
(function(oContext, undefined) {
    var $ = require("ankama.widget"),
        _ = require("lodash");
    $.widget("ankama.ak_carouselpack", $.ankama.widget, {
        options: {
            initSelector: ".ak-carouselpack",
            detailsSelector: ".ak-carouselpack-details",
            detailItemSelector: ".ak-carouselpack-detail",
            carouItemSelector: ".ak-carouselpack-item",
            itemChooseSelector: ".ak-carouselpack-detail .ak-carousel-ref-selection .ak-mosaic-item-choose"
        },
        jqCarouItems: null,
        jqDetails: null,
        jqDetailsItems: null,
        jqSubmitInputs: null,
        aIntervals: [],
        _destroy: function() {
            var self = this;
            self.jqCarouItems.unbind("click");
            self.element.find(self.options.itemChooseSelector).unbind("click");
            self._destroySlideShow();
            if (self.jqLastItemShow) self.jqLastItemShow.hide();
            self.aIntervals = []
        },
        _create: function() {},
        _init: function() {
            var self = this;
            this.jqCarouItems = this.element.find(".ak-carouseltouch " + this.options.carouItemSelector);
            this.jqDetails = this.element.find(this.options.detailsSelector);
            if (this.jqDetails.exists()) {
                this.jqDetailsItems = this.jqDetails.find(this.options.detailItemSelector);
                this.jqCarouItems.bind("click", function() {
                    self.showDetail($(this))
                });
                if (self._haveChoice()) {
                    this._startSlideShow();
                    this.jqSubmitInputs = $("input.ak-bagsubmit-off");
                    this.element.find(self.options.itemChooseSelector).bind("click", function() {
                        var iLineNumber = $(this).closest(self.options.detailItemSelector).data().linenumber;
                        var iRefId = $(this).data().refid;
                        $(this).find(":radio").get(0).checked = true;
                        $("input[type=hidden][name='line[" + iLineNumber + "]']").val(iRefId);
                        self._stopSlideShow(iLineNumber, iRefId);
                        self.enableSubmit()
                    });
                    self.enableSubmit()
                }
                self.showDetail(this.jqCarouItems.first())
            }
        },
        enableSubmit: function() {
            if (this.jqSubmitInputs && this._selectionValid() === true) {
                this.jqSubmitInputs.removeClass("ak-bagsubmit-off").addClass("btn-success");
                this.jqSubmitInputs.siblings(".ak-tooltip").ak_tooltip("disable")
            }
        },
        showDetail: function(jqItem) {
            var self = this;
            var iLineNumber = jqItem.data().linenumber;
            jqItem.addClass("active");
            if (self.jqLastItemShow) {
                if (self.jqLastItemShow.get(0) == jqItem.get(0)) return false;
                self.jqLastItemShow.removeClass("active");
                self.jqDetailsItems.filter("[data-linenumber=" + self.jqLastItemShow.data().linenumber + "]").hide()
            }
            var jqItemToShow = self.jqDetailsItems.filter("[data-linenumber=" + iLineNumber + "]");
            jqItemToShow.show();
            self.jqLastItemShow = jqItem
        },
        _startSlideShow: function() {
            var self = this;
            self.jqCarouItems.each(function(iIndex, jqCarouItem) {
                var jqImagesContainer = $(jqCarouItem).find(".ak-carouselpack-item-images"),
                    jqImages = jqImagesContainer.find("img");
                if (jqImages.length > 1) {
                    jqImages.first().addClass("active");
                    self.aIntervals[$(jqCarouItem).data().linenumber] = setInterval($.proxy(self._swapImages, self, jqImagesContainer), 1500)
                }
            })
        },
        _swapImages: function(jqImagesContainer) {
            var jqImageActive = jqImagesContainer.find("img.active").length > 0 ? jqImagesContainer.find("img.active") : jqImagesContainer.find("img:first");
            var jqImageNext = jqImagesContainer.find("img.active").next().length > 0 ? jqImagesContainer.find("img.active").next() : jqImagesContainer.find("img:first");
            jqImageActive.fadeOut(function() {
                jqImageActive.removeClass("active");
                jqImageNext.fadeIn().addClass("active")
            })
        },
        _stopSlideShow: function(iLineNumber, iRefId) {
            var self = this;
            self.aIntervals[iLineNumber] = window.clearInterval(self.aIntervals[iLineNumber]);
            var jqImages = self.jqCarouItems.filter("[data-linenumber=" + iLineNumber + "]").find(".ak-carouselpack-item-images img");
            jqImages.removeClass("active").hide();
            jqImages.filter("[data-refid=" + iRefId + "]").show()
        },
        _destroySlideShow: function() {
            var self = this;
            _.each(self.aIntervals, function(iInterval, iLineNumber) {
                if (iInterval === undefined) return;
                window.clearInterval(self.aIntervals[iLineNumber]);
                var jqImages = self.jqCarouItems.filter("[data-linenumber=" + iLineNumber + "]").find(".ak-carouselpack-item-images img");
                jqImages.removeClass("active").hide().first().addClass("active").show()
            })
        },
        _getLines: function() {
            return $("input[type=hidden]").filter(function() {
                return this.name.match(/line\[[0-9]+\]/)
            })
        },
        _haveChoice: function() {
            return this._getLines().length > 0
        },
        _selectionValid: function() {
            return !this._getLines().filter(function() {
                return $.trim(this.value) == ""
            }).length > 0
        }
    });
    $.widget("ankama.ak_addtobagformtunnel", $.ankama.widget, {
        options: {
            initSelector: ".ak-addtobagformtunnel"
        },
        _create: function() {
            this._super();
            this.element.on("submit", function() {
                var self = $(this);
                var oLines = $("input[type=hidden]", self).filter(function() {
                    return this.name.match(/line\[[0-9]+\]/) && $.trim(this.value) == ""
                });
                if (oLines.length) {
                    /line\[([0-9]*)\]/.exec(oLines.first().prop("name"));
                    var iLine = RegExp.$1;
                    if (iLine) {
                        var jqCarousel = self.parent().find(".ak-carouselpack");
                        jqCarousel.ak_carouselpack("showDetail", jqCarousel.find(jqCarousel.ak_carouselpack("option", "detailItemSelector") + "[data-linenumber=" + iLine + "]"))
                    }
                    return false
                }
            })
        }
    });
    $(document).bind("ready widgetcreate", function(oEvent) {
        $.ankama.ak_carouselpack.prototype.enhanceWithin(oEvent.target);
        $.ankama.ak_addtobagformtunnel.prototype.enhanceWithin(oEvent.target)
    })
})(this);
(function() {
    var $ = require("ankama.widget"),
        _ = require("lodash");
    $.widget("ankama.ak_product_options", $.ankama.widget, {
        _jqProductOptionsSelect: null,
        _jqForm: null,
        _jqSubmitButton: null,
        _sTooltipText: "",
        _bFormIsValid: false,
        _aMetaToReference: [],
        _aReferenceStock: [],
        options: {
            initSelector: ".ak-product-options"
        },
        _create: function() {
            var self = this;
            self._jqProductOptionsSelect = $(".ak-product-options-select", self.element);
            self._jqForm = $(".ak-product-form");
            self._jqSubmitButton = $(".ak-submit", self._jqForm);
            self._sTooltipText = self._jqSubmitButton.attr("title");
            var oAkOptions = self.element.akOptions();
            if (!_.isEmpty(oAkOptions)) {
                self._aMetaToReference = !_.isEmpty(oAkOptions["aMetaToReference"]) ? oAkOptions["aMetaToReference"] : self._aMetaToReference;
                self._aReferenceStock = !_.isEmpty(oAkOptions["aReferenceStock"]) ? oAkOptions["aReferenceStock"] : self._aReferenceStock
            }
            self._setFormIsValid();
            self._addEvents()
        },
        _addEvents: function() {
            var self = this;
            self.element.on("change", ".ak-product-options-select", function(event) {
                event.preventDefault();
                var jqSelect = $(this);
                var oAkOptions = jqSelect.akOptions();
                var iLineNumber = oAkOptions["iLineNumber"];
                var jqLineInputHiden = $("input.ak-line-number-" + iLineNumber, self._jqForm);
                var aRemainingReferences = self._getRemainingReferences(iLineNumber);
                if (_.isArray(aRemainingReferences[iLineNumber]) && aRemainingReferences[iLineNumber].length == 1) {
                    if (_.indexOf(self._aReferenceStock, aRemainingReferences[iLineNumber][0]) !== -1) jqLineInputHiden.val(aRemainingReferences[iLineNumber][0]);
                    else jqLineInputHiden.val("")
                } else jqLineInputHiden.val("");
                self._setFormIsValid()
            });
            self._jqForm.on("submit", function() {
                if (!self._bFormIsValid) return false
            })
        },
        _getRemainingReferences: function(iLineNumber) {
            var self = this;
            var aRemainingReferences = [];
            self._jqProductOptionsSelect.filter(".ak-line-number-" + iLineNumber).each(function() {
                var jqSelect = $(this);
                var oAkOptions = jqSelect.akOptions();
                var iLineNumber = oAkOptions["iLineNumber"];
                if (!_.isEmpty(jqSelect.val())) {
                    if (!_.isArray(aRemainingReferences[iLineNumber]) || aRemainingReferences.length == 0) aRemainingReferences[iLineNumber] = self._aMetaToReference[iLineNumber][jqSelect.val()];
                    else {
                        var aReferenceTmp = [];
                        _.forEach(self._aMetaToReference[iLineNumber][jqSelect.val()], function(iValue) {
                            if (_.indexOf(aRemainingReferences[iLineNumber], iValue) !== -1) aReferenceTmp[aReferenceTmp.length] = iValue
                        });
                        aRemainingReferences[iLineNumber] = aReferenceTmp
                    }
                }
            });
            return aRemainingReferences
        },
        _setFormIsValid: function() {
            var self = this;
            self._bFormIsValid = true;
            self._jqSubmitButton.removeClass("btn-info").addClass("btn-success");
            self._jqSubmitButton.attr("title", null);
            self._jqProductOptionsSelect.each(function() {
                var jqSelect = $(this);
                var oAkOptions = jqSelect.akOptions();
                var iLineNumber = oAkOptions["iLineNumber"];
                var jqLineInputHiden = $("input.ak-line-number-" + iLineNumber, self._jqForm);
                if (_.isEmpty(jqSelect.val()) || _.isEmpty(jqLineInputHiden.val())) {
                    self._bFormIsValid = false;
                    self._jqSubmitButton.removeClass("btn-success").addClass("btn-info");
                    self._jqSubmitButton.attr("title", self._sTooltipText);
                    return false
                }
            });
            return true
        }
    });
    $(document).bind("ready widgetcreate", function(oEvent) {
        $.ankama.ak_product_options.prototype.enhanceWithin(oEvent.target)
    })
})();
(function(oContext, undefined) {
    var $ = require("ankama.widget"),
        _ = require("lodash");
    $.widget("ankama.ak_catalog_article_list_filters", $.ankama.widget, {
        options: {
            initSelector: ".ak-catalog-article-list-filters"
        },
        _create: function() {
            var jqElem = this.element;
            $(jqElem).change(".ak_filter_meta", function() {
                var aSelectedFilters = [],
                    aSelectedFiltersLabel = [];
                $(".ak_filter_meta:checked", jqElem).each(function() {
                    aSelectedFilters.push($(this).val());
                    aSelectedFiltersLabel.push(encodeURIComponent($(this).prop("title").replace(/ /g, "-").toLowerCase()))
                });
                var sHref = window.location.protocol + "//" + window.location.host + window.location.pathname.split(/\/f([0-9])+/g)[0];
                if (aSelectedFilters.length > 0) sHref += "/f" + aSelectedFilters.join("_") + "-" + aSelectedFiltersLabel.join("-");
                $.pjax({
                    url: sHref,
                    container: ".ak-catalog-article-list",
                    fragment: ".ak-catalog-article-list",
                    timeout: 2e4
                })
            })
        }
    });
    $.widget("ankama.ak_catalog_article_list_inlinefilter", $.ankama.widget, {
        options: {
            initSelector: ".ak-catalog-article-list-inlinefilter",
            sButtonSelector: ".ak-catalog-article-list-inlinefilter-button",
            sContentSelector: ".ak-catalog-article-list-inlinefilter-content"
        },
        _create: function() {
            var self = this;
            $(self.option("sButtonSelector"), self.element).bind("click", function() {
                $(self.option("sContentSelector"), self.element).toggleClass("hide")
            });
            $(".ak-select-category-list", self.element).bind("change", function() {
                document.location.href = $(this).val()
            })
        }
    });
    $(document).bind("ready widgetcreate", function(oEvent) {
        $.ankama.ak_catalog_article_list_filters.prototype.enhanceWithin(oEvent.target);
        $.ankama.ak_catalog_article_list_inlinefilter.prototype.enhanceWithin(oEvent.target)
    })
})(this);
(function(oContext, undefined) {
    var $ = require("ankama.widget"),
        _ = require("lodash");
    $.widget("ankama.ak_bag_quantity", $.ankama.widget, {
        options: {
            initSelector: ".ak-bag-quantity"
        },
        _create: function() {
            var jqElem = this.element;
            jqElem.on("click", "button", function(oEvent) {
                var jqQuantityInput = $(jqElem).find('[name="quantity"]'),
                    iQuantity = parseInt(jqQuantityInput.val()),
                    iNewQuantity = 0;
                if (isNaN(iQuantity)) iQuantity = 0;
                if ($(this).hasClass("ak-less-item")) iNewQuantity = iQuantity > 0 ? iQuantity - 1 : 0;
                else iNewQuantity = iQuantity > 0 ? iQuantity + 1 : 0;
                if (iNewQuantity == 0) {
                    var jqTooltip = $(this).closest("tr").find(".ak-tooltip-remove");
                    jqTooltip.ak_tooltip("show");
                    oEvent.preventDefault()
                } else jqQuantityInput.val(iNewQuantity)
            })
        }
    });
    $.widget("ankama.ak_bag_shippings", $.ankama.widget, {
        options: {
            initSelector: ".ak-bag-shippings"
        },
        _create: function() {
            var jqElem = this.element;
            jqElem.on("click", "tr", function(oEvent) {
                if ($(oEvent.target).is(":radio")) return;
                var jqRadio = $(this).find(':radio[name="delivery"]');
                jqRadio.prop("checked", true).trigger("change")
            });
            jqElem.on("change", ':radio[name="delivery"]', function() {
                $(this).closest("form").submit()
            })
        }
    });
    $(document).bind("ready widgetcreate", function(oEvent) {
        $.ankama.ak_bag_quantity.prototype.enhanceWithin(oEvent.target);
        $.ankama.ak_bag_shippings.prototype.enhanceWithin(oEvent.target)
    })
})(this);
! function(oContext, undefined) {
    "use strict";
    var $, _;
    $ = require("ankama.widget");
    _ = require("lodash");
    $.widget("ankama.ak_reserve_money", $.ankama.widget, {
        options: {
            initSelector: ".ak-reserve-money"
        },
        _destroy: function() {},
        refresh_money: function() {
            var self = this;
            this.element.trigger("direct.ajaxloader")
        }
    });
    $(document).bind("ready widgetcreate", function(oEvent) {
        $.ankama.ak_reserve_money.prototype.enhanceWithin(oEvent.target)
    })
}(this);
! function(oContext, undefined) {
    "use strict";
    var $, _;
    $ = require("ankama.widget");
    _ = require("lodash");
    $.widget("ankama.ak_unblock_tooltip", $.ankama.widget, {
        options: {
            initSelector: ".ak-unblock-tooltip"
        },
        _destroy: function() {},
        _create: function() {
            var self = this,
                jqElement = self.element;
            jqElement.bind("ajaxloaded", function(oEvent) {
                $(".ak-reserve-money").ak_reserve_money("refresh_money");
                if ($(".ak-choice-transfer").length) {
                    setTimeout(function() {
                        $(".ak-choice-transfer").ak_choice_transfer("unselect")
                    }, 3e3)
                }
            })
        }
    });
    $(document).bind("ready widgetcreate", function(oEvent) {
        $.ankama.ak_unblock_tooltip.prototype.enhanceWithin(oEvent.target)
    })
}(this);
(function(window, undefined) {
    var $ = require("ankama.widget"),
        $ = require("jquery.ankama");
    $.widget("ankama.ak_payments_process_choice", $.ankama.widget, {
        options: {
            initSelector: ".ak-payments-process-choice",
            bCheckboxCGV: true,
            sError: "",
            oErrorsText: {
                sCGU: "",
                sPaymentMode: "",
                sOGR: "",
                sPayemntOptions: ""
            }
        },
        _bPaymentSelected: false,
        _bPaymentOptionsFilled: false,
        _init: function() {
            this._isPaymentSelected()
        },
        _create: function() {
            var self = this,
                jqElement = self.element,
                oTooltipInstance = $(".btn-pay-now", jqElement);
            jqElement.on("click", ".ak-list-paymentmode .ak-list-element", function(jqEvent) {
                if ($(jqEvent.currentTarget).hasClass("unavailable")) return false;
                if ($(jqEvent.target).hasClass("ak-picto-help")) return false;
                if ($(jqEvent.target).is(':input[name="payment"]')) {
                    self._isPaymentSelected();
                    return true
                }
                var jqInput = $(':input[name="payment"]', this);
                jqInput.prop("checked", $(jqEvent.target).closest(".ak-option").length ? true : !jqInput.prop("checked"));
                self._isPaymentSelected();
                self._checkOptions()
            });
            oTooltipInstance.ak_tooltip("disable");
            $(".btn-pay-now", self.element).bind("click", function(jqEvent) {
                var oOptions = $('form :radio[name="payment"]:checked', this.element).first().closest(".ak-list-element").find(".ak-payment-mode-options");
                self._bPaymentOptionsFilled = oOptions.length ? oOptions.first().data("filled") : true;
                var sTooltipError = null;
                if (false === self._bPaymentSelected) {
                    sTooltipError = self.options.oErrorsText.sPaymentMode
                } else if (false == self._bPaymentOptionsFilled) {
                    sTooltipError = self.options.oErrorsText.sPaymentOptions
                } else if (self.option("bCheckboxCGV") && !$(".ak-payment-cgu").find("input").prop("checked")) {
                    sTooltipError = self.options.oErrorsText.sCGU
                } else if (self.option("sError") !== "") {
                    sTooltipError = self.option("sError")
                }
                if (sTooltipError) {
                    oTooltipInstance.ak_tooltip("enable");
                    oTooltipInstance.ak_tooltip("show");
                    oTooltipInstance.ak_tooltip("api").set("content.text", sTooltipError)
                } else {
                    oTooltipInstance.ak_tooltip("disable");
                    oTooltipInstance.ak_tooltip("hide");
                    $("form", self.element).submit()
                }
            })
        },
        _checkOptions: function() {
            var self = this;
            var jqElement = this.element;
            if ($('input[name="payment"]:checked', self.element).length) {
                var oOptions = $('input[name="payment"]:checked', self.element).first().closest(".ak-list-element").find(".ak-payment-mode-options").first();
                if (oOptions) {
                    oOptions.trigger("isComplete")
                }
            }
        },
        _isPaymentSelected: function() {
            this._bPaymentSelected = $('form :radio[name="payment"]', this.element).filter(function(iIndex, heInput) {
                return $(heInput).prop("checked") === true
            }).length > 0
        }
    });
    $(document).bind("ready widgetcreate", function(oEvent) {
        $.ankama.ak_payments_process_choice.prototype.enhanceWithin(oEvent.target)
    })
})(this);
(function(window, undefined) {
    var $ = require("ankama.widget"),
        $ = require("jquery.ankama");
    $.widget("ankama.ak_payment_mode_options", $.ankama.widget, {
        options: {
            initSelector: ".ak-payment-mode-options",
            dataFilledAttribute: "filled"
        },
        _create: function() {
            var self = this,
                jqElement = self.element;
            jqElement.on("isComplete", function(oEvent) {
                setTimeout(function() {
                    self.element.data(self.options.dataFilledAttribute, self.isFilled())
                }, 50)
            })
        },
        isFilled: function() {
            return false
        }
    });
    $(document).bind("ready widgetcreate", function(oEvent) {
        $.ankama.ak_payment_mode_options.prototype.enhanceWithin(oEvent.target)
    })
})(this);
(function(window, undefined) {
    var $ = require("ankama.widget"),
        $ = require("jquery.ankama");
    $.widget("ankama.ak_payment_choice_options_hc", $.ankama.ak_payment_mode_options, {
        options: {
            initSelector: ".ak-payment-mode-options-hc"
        },
        _create: function() {
            var self = this,
                jqElement = self.element;
            self._super();
            var i = setInterval(function() {
                var oCheckbox = $(jqElement).closest(".ak-list-element").find('input[name="payment"]');
                if ($(jqElement).data("animating")) return;
                if (oCheckbox.prop("checked") && $(jqElement).height() == 0) {
                    $(jqElement).data("animating", 1);
                    $(jqElement).animate({
                        height: $(jqElement).get(0).scrollHeight
                    }, 400, function() {
                        $(jqElement).data("animating", 0)
                    });
                    return
                }
                if (!oCheckbox.prop("checked")) {
                    $(jqElement).data("animating", 1);
                    $(jqElement).animate({
                        height: 0
                    }, 400, function() {
                        $(jqElement).data("animating", 0)
                    });
                    return
                }
            }, 200)
        },
        isFilled: function() {
            var self = this,
                jqElement = self.element;
            if (!jqElement.find(':input[name="ak-hc-store"]').is(":checked")) return true;
            if (jqElement.find(':input[name="ak-security-procedure"]:checked').length < 1) return false;
            return true
        }
    });
    $(document).bind("ready widgetcreate", function(oEvent) {
        $.ankama.ak_payment_choice_options_hc.prototype.enhanceWithin(oEvent.target)
    })
})(this);
(function(window, undefined) {
    var $ = require("ankama.widget"),
        $ = require("jquery.ankama");
    $.widget("ankama.ak_payment_choice_options_hk", $.ankama.ak_payment_mode_options, {
        options: {
            initSelector: ".ak-payment-mode-options-hk"
        },
        _create: function() {
            var self = this,
                jqElement = self.element;
            jqElement.closest(".ak-list-element").on("click", function(oEvent) {
                self._handlePreSelected()
            });
            self._handlePreSelected();
            self._super()
        },
        _handlePreSelected: function(oEvent) {
            var self = this,
                jqElement = self.element;
            setTimeout(function() {
                jqElement.find('input[name="payment-hk-token"]').prop("checked", $(self.element).closest(".ak-list-element").find('input[name="payment"]').prop("checked"))
            }, 10)
        },
        isFilled: function() {
            var self = this,
                jqElement = self.element;
            return jqElement.closest(".ak-list-element").find('input[name="payment-hk-token"]').is(":checked")
        }
    });
    $(document).bind("ready widgetcreate", function(oEvent) {
        $.ankama.ak_payment_choice_options_hk.prototype.enhanceWithin(oEvent.target)
    })
})(this);
(function(oContext, undefined) {
    var $ = require("ankama.widget"),
        _ = require("lodash");
    $.widget("ankama.ak_payments_waiting", $.ankama.widget, {
        options: {
            initSelector: ".ak-payments-waiting"
        },
        _create: function() {
            var self = this;
            var iTimerId = 0;
            var sUrl = !_.isNull(this.options) && !_.isNull(this.options.sUrl) ? this.options.sUrl : null;
            iTimerId = window.setInterval(function() {
                $.ajax({
                    url: sUrl,
                    type: "post",
                    data: {
                        status: "payments-waiting"
                    },
                    success: function(oData, sTextStatus, jqXHR) {
                        var sResultStatus = oData.sResultStatus;
                        var bForceRedirect = oData.bForceRedirect;
                        if (bForceRedirect) {
                            clearInterval(iTimerId);
                            window.location.reload()
                        }
                    }
                })
            }, 15e3)
        }
    });
    $(document).bind("ready widgetcreate", function(oEvent) {
        $.ankama.ak_payments_waiting.prototype.enhanceWithin(oEvent.target)
    })
})(this);
(function(window, undefined) {
    var $ = require("ankama.widget"),
        $ = require("jquery.ankama");
    $.widget("ankama.ak_security_method_modal", $.ankama.widget, {
        options: {
            initSelector: ".ak-security-method-modal"
        },
        _init: function() {},
        _create: function() {
            var self = this,
                jqElement = self.element;
            jqElement.closest(".ak-modal").ak_modal("set_closemodal", function() {
                self._refreshAfterLastStep()
            });
            $(".ak-modal-close", jqElement).click(function() {
                jqElement.closest(".ak-modal").ak_modal("close")
            })
        },
        _refreshAfterLastStep: function() {
            var self = this,
                jqElement = self.element;
            var jqHiddenGsm = $(':input[name="ak-gsm-success"]', jqElement);
            if (jqHiddenGsm.length == 1) {
                var jqGsm = $(".ak-security-gsm");
                jqGsm.html(jqHiddenGsm.val());
                jqGsm.next("button").hide();
                $('input[value="PHONE"]').removeAttr("disabled")
            }
            var jqHiddenEmail = $(':input[name="ak-email-success"]', jqElement);
            if (jqHiddenEmail.length == 1) {
                var jqEmail = $(".ak-security-email");
                jqEmail.html(jqHiddenEmail.val());
                jqEmail.next("button").hide()
            }
        }
    });
    $(document).bind("ready widgetcreate", function(oEvent) {
        $.ankama.ak_security_method_modal.prototype.enhanceWithin(oEvent.target)
    })
})(this);
(function(oContext, undefined) {
    var $ = require("ankama.widget"),
        _ = require("lodash");
    $.widget("ankama.ak_addtobagformtunnel", $.ankama.widget, {
        options: {
            initSelector: ".ak-addtobagformtunnel"
        },
        _create: function() {
            this._super();
            this.element.on("submit", function() {
                var self = $(this);
                var oLines = $("input[type=hidden]", self).filter(function() {
                    return this.name.match(/line\[[0-9]+\]/) && $.trim(this.value) == ""
                });
                if (oLines.length) {
                    /line\[([0-9]*)\]/.exec(oLines.first().prop("name"));
                    var iLine = RegExp.$1;
                    if (iLine) {
                        var jqCarousel = self.parent().find(".ak-carouselpack");
                        jqCarousel.ak_carouselpack("showDetail", jqCarousel.find(jqCarousel.ak_carouselpack("option", "detailItemSelector") + "[data-linenumber=" + iLine + "]"))
                    }
                    return false
                }
            })
        }
    });
    $(document).bind("ready widgetcreate", function(oEvent) {
        $.ankama.ak_addtobagformtunnel.prototype.enhanceWithin(oEvent.target)
    })
})(this);
(function(oContext, undefined) {
    var $ = require("ankama.widget"),
        _ = require("lodash");
    $.widget("ankama.ak_choice_subscription", $.ankama.widget, {
        options: {
            initSelector: ".ak-choice-subscription"
        },
        jqSubscriptionchoiceForm: null,
        _create: function() {
            var self = this;
            self._on({
                "click .ak-list-element": function(oEvent) {
                    var jqCurrentTarget = $(oEvent.currentTarget);
                    if (jqCurrentTarget.hasClass("ak-disabled")) return;
                    jqCurrentTarget.addClass("ak-choice-selected");
                    jqCurrentTarget.find("input[type='radio']").prop("checked", true);
                    self.jqSubscriptionchoiceForm.submit()
                }
            });
            self.element.bind("pjax:complete", function(jqEvent, jqPromise, sStatus) {
                var mValue = self._getChooseValue();
                if (sStatus === "success" && mValue) $(document).trigger("ak:referenceselected", [mValue]);
                var iArticleId = $(self.element).find(".ak-choice-subscription-buttons").data("article-id");
                $('.ak-choice-subscription-duplicate-buttons[data-article-id="' + iArticleId + '"]').html($(self.element).find(".ak-choice-subscription-buttons").html())
            })
        },
        _init: function() {
            var self = this;
            self.jqSubscriptionchoiceForm = $(".ak-subscriptionchoice-form", self.element)
        },
        _getChooseValue: function() {
            var self = this;
            jqOptionChoose = $(":radio", self.jqSubscriptionchoiceForm).filter(function(iIndex, heInput) {
                return $(heInput).prop("checked") === true
            });
            return jqOptionChoose.map(function() {
                return parseInt($(this).val(), 10)
            })
        }
    });
    $(document).bind("ready widgetcreate", function(oEvent) {
        $.ankama.ak_choice_subscription.prototype.enhanceWithin(oEvent.target)
    })
})(this);
(function(window, undefined) {
    var $ = require("ankama.widget");
    $.widget("ankama.ak_choice_subscription_duplicate_buttons", $.ankama.widget, {
        options: {
            initSelector: ".ak-choice-subscription-duplicate-buttons"
        },
        _create: function() {
            var self = this;
            var iArticleId = $(self.element).data("article-id");
            $(self.element).on("click", function(oEvent) {
                if ($(oEvent.target).closest("button").length && ($(oEvent.target).closest("button").is(".ak-ignore-button-classes") || !$(oEvent.target).closest("button").is(".ak-need-login") && !$(oEvent.target).closest("button").is(".ak-need-money"))) {
                    var iScroll = $('.ak-choice-subscription[data-article-id="' + iArticleId + '"]').offset().top - 50;
                    setTimeout(function() {
                        $("html, body").animate({
                            scrollTop: iScroll + "px"
                        })
                    }, 2e3)
                }
            })
        }
    });
    $(document).bind("ready widgetcreate", function(oEvent) {
        $.ankama.ak_choice_subscription_duplicate_buttons.prototype.enhanceWithin(oEvent.target)
    })
})(this);
(function(oContext) {
    var $, _;
    $ = require("ankama.widget");
    _ = require("lodash");
    $.widget("ankama.ak_choice", $.ankama.widget, {
        options: {
            initSelector: ".ak-choice"
        },
        _create: function() {
            return this._on({
                "click .ak-list-element": function(oEvent) {
                    if (oEvent.target.tagName === "INPUT") {
                        return
                    }
                    return $(oEvent.currentTarget).find("input[type='radio']").prop("checked", true).trigger("change")
                }
            })
        },
        unselect: function(oEvent) {
            return $(this.element).find('input[type="radio"]').prop("checked", false).trigger("change")
        }
    });
    return $(document).bind("ready widgetcreate", function(oEvent) {
        return $.ankama.ak_choice.prototype.enhanceWithin(oEvent.target)
    })
})(this);
(function(oContext) {
    var $, _;
    $ = require("ankama.widget");
    _ = require("lodash");
    $.widget("ankama.ak_choice_transfer", $.ankama.ak_choice, {
        options: {
            initSelector: ".ak-choice-transfer"
        },
        _create: function() {
            this._on({
                "change input[type='radio']": this._onChangeForm,
                "change select": this._onChangeForm
            });
            return this._superApply(arguments)
        },
        _onChangeForm: function(oEvent) {
            var oForm;
            oForm = $(oEvent.currentTarget).parents("form");
            if (oEvent.currentTarget.name.indexOf("choice_char") !== -1) {
                $(".ak-choice-server", oForm).remove()
            }
            return oForm.submit()
        }
    });
    return $(document).bind("ready widgetcreate", function(oEvent) {
        return $.ankama.ak_choice_transfer.prototype.enhanceWithin(oEvent.target)
    })
})(this);
(function(oContext) {
    var $, _;
    $ = require("ankama.widget");
    _ = require("lodash");
    $.widget("ankama.ak_choice_transfer_account", $.ankama.ak_choice, {
        options: {
            initSelector: ".ak-choice-transfer-account"
        },
        _create: function() {
            console.log("create  transfer account");
            this._on({
                "change input[type='radio']": this._onChangeForm,
                "change select": this._onChangeForm
            });
            return this._superApply(arguments)
        },
        _onChangeForm: function(oEvent) {
            var oForm;
            oForm = $(oEvent.currentTarget).parents("form");
            return oForm.submit()
        }
    });
    return $(document).bind("ready widgetcreate", function(oEvent) {
        return $.ankama.ak_choice_transfer_account.prototype.enhanceWithin(oEvent.target)
    })
})(this);
(function(oContext, undefined) {
    var $ = require("ankama.widget"),
        _ = require("lodash");
    $.widget("ankama.ak_article_compositionlist", $.ankama.widget, {
        options: {
            initSelector: ".ak-article-compositionlist"
        },
        _create: function() {
            var self = this;
            self.jqScrollerItems = $(self.option("initSelector") + "-scroller", self.element);
            $(document).bind("ak:referenceselected", function(jqEvent, aReferenceId) {
                _.each(self.aIntervals, function(oInterval) {
                    var jqListItem = oInterval.items.filter(function(iIndex, heItem) {
                        return _.indexOf(aReferenceId, $(heItem).data("refid")) !== -1
                    });
                    if (jqListItem.length == 1) {
                        oInterval.interval = clearInterval(oInterval.interval);
                        oInterval.interval = undefined;
                        self._startItemHidden(oInterval.items.filter(".active:first"));
                        self._startItemVisible($(jqListItem[0]));
                        $(jqListItem[0]).find(".ak-main-content").children().show()
                    } else {
                        if (_.isUndefined(oInterval.interval)) oInterval.interval = setInterval($.proxy(self._swapItems, self, $(oInterval.scroller)), 3e3)
                    }
                })
            });
            self._startSlideShow()
        },
        _startSlideShow: function() {
            var self = this;
            self.aIntervals = [];
            self.jqScrollerItems.each(function(iIndex, jqScrollerItem) {
                var jqItems = $(".ak-list-element", jqScrollerItem);
                if (jqItems.length > 1) {
                    jqItems.first().addClass("active");
                    self.aIntervals[iIndex] = {
                        scroller: jqScrollerItem,
                        items: jqItems,
                        interval: setInterval($.proxy(self._swapItems, self, $(jqScrollerItem)), 3e3)
                    }
                }
            })
        },
        _swapItems: function(jqScrollerItem) {
            var self = this;
            var jqItemActive = jqScrollerItem.find(".ak-list-element.active").length > 0 ? jqScrollerItem.find(".ak-list-element.active") : jqScrollerItem.find(".ak-list-element:first");
            var jqItemNext = jqScrollerItem.find(".ak-list-element.active").nextAll(".ak-list-element:first").length > 0 ? jqScrollerItem.find(".ak-list-element.active").nextAll(".ak-list-element:first") : jqScrollerItem.find(".ak-list-element:first");
            jqItemActive.find(".ak-main-content").children().fadeOut("fast", function() {
                self._startItemHidden(jqItemActive);
                self._startItemVisible(jqItemNext);
                jqItemNext.find(".ak-main-content").children().fadeIn("fast")
            })
        },
        _startItemVisible: function(jqItem) {
            jqItem.addClass("active").removeClass("hidden").css("display", "table")
        },
        _startItemHidden: function(jqItem) {
            jqItem.removeClass("active").addClass("hidden")
        }
    });
    $(document).bind("ready widgetcreate", function(oEvent) {
        $.ankama.ak_article_compositionlist.prototype.enhanceWithin(oEvent.target)
    })
})(this);
(function(window, undefined) {
    var $ = require("ankama.widget");
    $.widget("ankama.ak_article_compositionpreview", $.ankama.widget, {
        options: {
            initSelector: ".ak-article-composition-preview",
            iTimer: 3e3
        },
        currentIndex: null,
        _create: function() {
            var self = this;
            $(self.element).find(".ak-article-composition-thumbs .ak-thumb").on("click", function(oEvent) {
                self.currentIndex = $(oEvent.target).closest(".ak-thumb").data("index");
                $(self.element).find(".ak-article-composition-thumbs .ak-thumb").removeClass("ak-selected");
                $(oEvent.target).closest(".ak-thumb").addClass("ak-selected");
                $(self.element).find(".ak-article-composition-detail").hide();
                $(self.element).find('.ak-article-composition-detail[data-index="' + self.currentIndex + '"]').show();
                $(self.element).find('.ak-article-composition-detail[data-index="' + self.currentIndex + '"] .ak-article-illu img').load(function() {
                    $(self.element).find(".ak-article-flag").css({
                        maxHeight: $(this).closest(".ak-article-illu").height() + 7 + "px"
                    })
                })
            });
            $(document).bind("ak:referenceselected", function(jqEvent, aReferenceId) {
                var oTargetElement = $(self.element).find(".ak-thumb img").filter(function(iIndex, heItem) {
                    return _.indexOf(aReferenceId, $(heItem).data("id")) !== -1
                });
                oTargetElement.each(function(i, o) {
                    $(o).parent().addClass("ak-referenceselected");
                    self._fadeTo($(o).parent(), $(o).index())
                })
            });
            self.iInterval = setInterval(function() {
                $.proxy(self._redraw(), self)
            }, self.options.iTimer);
            self._redraw();
            $(self.element).find(".ak-article-composition-thumbs .ak-thumb").first().click()
        },
        _redraw: function() {
            var self = this;
            $(self.element).find(".ak-article-composition-thumbs .ak-thumb").each(function(i, o) {
                if ($(o).find("img").length == 1 || $(o).is(".ak-referenceselected")) return;
                self._fadeTo($(o))
            })
        },
        _fadeTo: function(oContainer, iNextIndex) {
            var self = this;
            var iIndex = $(oContainer).find("img:visible").index();
            var iDataIndex = $(oContainer).data("index");
            $(self.element).find('.ak-article-composition-detail[data-index="' + iDataIndex + '"] >').hide();
            var iNext = iNextIndex || ($(oContainer).find("img").length == iIndex + 1 ? 0 : iIndex + 1);
            $(oContainer).find("img:visible").fadeOut("fast", function() {
                $($(oContainer).find("img").get(iNext)).fadeIn("fast")
            });
            $($(self.element).find('.ak-article-composition-detail[data-index="' + iDataIndex + '"] >').get(iNext)).show()
        }
    });
    $(document).bind("ready widgetcreate", function(oEvent) {
        $.ankama.ak_article_compositionpreview.prototype.enhanceWithin(oEvent.target)
    })
})(this);
! function(oContext, undefined) {
    "use strict";
    var $ = require("ankama.widget");
    _ = require("lodash");
    require("jquery.loadmask");
    $.widget("ankama.ak_shop_mosaic", $.ankama.widget, {
        options: {
            initSelector: ".ak-shop-mosaic",
            sWrapperClass: "ak-mosaic-article-item-inline",
            sTargetSelector: ".js-mosaic-item-illu, .js-mosaic-item-name, .js-article-directlink",
            sTargetCloseSelector: ".ak-item-inline-close",
            iArrowPosition: 0
        },
        _lastContainer: null,
        _lastArticleItem: null,
        _create: function() {
            var self = this,
                jqElement = self.element;
            $(self.element).on("click", self.option("sTargetCloseSelector"), function(jqEvent) {
                $(jqEvent.currentTarget).closest(".ak-mosaic-article-item-inline").fadeOut(200, function() {
                    $("." + self.option("sWrapperClass")).remove()
                });
                $(self.element).find(".js-mosaic-item-article").removeClass("inlineopen")
            });
            $(self.option("sTargetSelector"), self.element).delegate("a:not(.nopjax)", "click", function(jqEvent) {
                var jqMosaicArticleItem = $(jqEvent.currentTarget).closest(".js-mosaic-item-article"),
                    jqContainer = jqMosaicArticleItem.parent().nextAll(".ak-row-break:visible:first");
                $(".js-mosaic-item-article").removeClass("ak-selected");
                jqContainer.addClass("ak-selected");
                if (jqContainer.closest("." + self.option("sWrapperClass")).length) return true;
                jqEvent.preventDefault();
                if ($("." + self.option("sWrapperClass")).length && self._lastArticleItem && self._lastArticleItem[0] === jqMosaicArticleItem[0]) return;
                var sId = _.uniqueId("ak-inline-article-");
                jqContainer.attr("id", sId);
                var _fnOuterHtml = function(jqElement) {
                    return jqElement.clone().wrap("<div></div>").parent().html()
                };
                jqContainer.on("pjax:start", function(jqEvent) {
                    jqEvent.stopPropagation();
                    $(self.element).mask();
                    jqContainer.off("pjax:start")
                });
                jqContainer.on("pjax:end", function(jqEvent, oXhr) {
                    jqEvent.stopPropagation();
                    $(self.element).unmask();
                    if (self._lastArticleItem !== null) self._lastArticleItem.removeClass("inlineopen");
                    if (self._lastContainer !== null) {
                        self._lastContainer.remove();
                        self._lastContainer = null
                    }
                    jqMosaicArticleItem.addClass("inlineopen");
                    var jqLastContainer = $(this),
                        jqContent = $(jqLastContainer.html()),
                        jqWrapper = $('<div class="' + self.option("sWrapperClass") + '"></div>').css({
                            clear: "both"
                        });
                    jqWrapper.html('<div class="arrowup"></div><a class="' + self.option("sTargetCloseSelector").substr(1) + '"></a>' + _fnOuterHtml(jqContent));
                    jqLastContainer.html(_fnOuterHtml(jqWrapper));
                    $("." + self.option("sWrapperClass")).animate({
                        opacity: 1
                    });
                    var jqArrow = jqLastContainer.find(".arrowup");
                    jqArrow.css({
                        left: self.options.iArrowPosition + "px"
                    });
                    var iArrowPosition = jqMosaicArticleItem.parent().position().left + (jqMosaicArticleItem.width() / 2 - jqArrow.outerWidth() / 2);
                    self.options.iArrowPosition = iArrowPosition;
                    jqArrow.animate({
                        left: iArrowPosition + "px"
                    });
                    self._lastContainer = $("." + self.option("sWrapperClass"), jqLastContainer);
                    self._lastArticleItem = jqMosaicArticleItem;
                    self.element.trigger("widgetcreate");
                    jqContainer.off("pjax:end");
                    $("html,body").animate({
                        scrollTop: $(jqLastContainer).offset().top - $("header").outerHeight() - 10
                    })
                });
                $.pjax({
                    method: "post",
                    url: jqEvent.currentTarget.href,
                    container: "#" + sId,
                    fragment: ".ak-container.ak-main-center",
                    push: false,
                    scrollTo: $(window).scrollTop(),
                    timeout: 3e4,
                    data: {
                        bMosaicInline: 1
                    }
                })
            });
            $(document).on("profilechange", function() {
                $("." + self.option("sWrapperClass"), self.element).remove()
            })
        }
    });
    $(document).bind("ready widgetcreate", function(oEvent) {
        $.ankama.ak_shop_mosaic.prototype.enhanceWithin(oEvent.target)
    })
}(this);
(function(undefined) {
    var $ = require("ankama.widget");
    var _ = require("lodash");
    require("ankama.ui.dialog");
    require("jquery.pjax");
    require("jquery.validate");
    require("jquery.ankama");
    require("jquery.serialize-object");
    $.validator.addMethod("server", function(name, element, param) {
        var sFullName = $(arguments[1].form).data().options.wn;
        var widget = $(arguments[1].form).data()["ankama-" + sFullName];
        Object.getPrototypeOf(widget).remoteValidation.apply(widget, arguments);
        return "pending"
    });
    $.validator.addMethod("alphanum", function(value, element) {
        return this.optional(element) || /^[a-zA-Z0-9\-]+$/i.test(value)
    });
    $.validator.addMethod("alpha", function(value, element) {
        return this.optional(element) || /^[a-zA-Z\-]+$/i.test(value)
    });
    $.validator.addMethod("editor_required", function(value, element, param) {
        if (typeof CKEDITOR !== "undefined") {
            var oInstanceCurrent;
            $.each(CKEDITOR.instances, function(iIndex, oInstance) {
                if (oInstance.editable().isInline()) oInstance.fire("blur");
                if (oInstance.element.getNameAtt() == element.name) {
                    oInstanceCurrent = oInstance
                }
            });
            if (oInstanceCurrent) {
                return oInstanceCurrent.getData() != ""
            }
        }
        return true
    });
    $.widget("ankama.ak_simpleform", $.ankama.widget, {
        options: {
            initSelector: ".ak-simpleform",
            helpPopupBtnCls: "ak-btn-popup",
            server: {
                xhrFields: {
                    withCredentials: true
                }
            },
            disabledClass: "disabled",
            ajaxSubmit: true,
            ajaxFragment: null,
            ajaxTarget: null,
            pjaxSettings: {
                timeout: 2e4,
                scrollTo: false
            },
            validate: {
                rules: {},
                messages: {},
                onkeyup: true,
                focusCleanup: false,
                focusInvalid: true,
                ignoreTitle: true,
                debug: false
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
            var self = this,
                jqElement = $(this.element),
                sTarget = jqElement.akmData("target") || self.option("ajaxTarget") || "body",
                sFragment = jqElement.akmData("fragment") || self.option("ajaxFragment") || sTarget;
            self._invalidServersValidations = [];
            self.option("wn", self.widgetName);
            _.merge(self.options.pjaxSettings, {
                container: sTarget,
                fragment: sFragment
            });
            jqElement.ak_ajaxloader({
                bindSubmitEvent: false,
                pjax: self.options.pjaxSettings
            });
            jqElement.on("reset_form", function(oEvent, fCallback) {
                self._reset(fCallback)
            });
            this.element.data("options", this.options);
            this._superApply(arguments)
        },
        iTimerId: null,
        _init: function() {
            var self = this,
                jqElement = $(this.element),
                sLang = $.akLang(),
                sClientLang = $.akClientLang(),
                sDefaultErrorMessage = oDefaultErrorMessages[sLang] || oDefaultErrorMessages[sClientLang] || oDefaultErrorMessages["en"];
            if (this.element.data("validator")) {
                this.element.removeData("validator");
                this.element.unbind("submit")
            }
            this._addHelpTooltip();
            self.aFieldsOptions = [];
            this.options.validate.rules = {};
            this.options.validate.messages = {};
            this.options.validate.groups = {};
            _.each(this.element.find(":input"), $.proxy(function(elInput) {
                var jqInput = $(elInput);
                var oOptions = jqInput.akOptions();
                if (_.isEmpty(oOptions)) return;
                self.aFieldsOptions[elInput.name] = oOptions;
                if (jqInput.data("alt-fieldname")) {
                    this.options.validate.groups[elInput.name] = elInput.name + " " + jqInput.data("alt-fieldname")
                }
                if (!jqInput.attr("id")) jqInput.attr("id", _.uniqueId("ak_field_"));
                var sField = jqInput.attr("name");
                if ("validation" in oOptions) var aRules = oOptions.validation.rules;
                if (aRules !== undefined) {
                    this.options.validate.rules[sField] = {};
                    this.options.validate.messages[sField] = {};
                    _.each(aRules, function(oRule) {
                        var sCustomMessage;
                        if (_.has(oRule, "message")) {
                            if (!_.isEmpty(oRule.message)) sCustomMessage = oRule.message;
                            delete oRule.message
                        }
                        _.forIn(oRule, function(mValue, sName) {
                            var sRuleErrorMessage;
                            if (sName in oErrorMessages) sRuleErrorMessage = oErrorMessages[sName][sLang] || oErrorMessages[sName][sClientLang];
                            if (typeof sRuleErrorMessage === "function") sRuleErrorMessage = sRuleErrorMessage.apply(this, _.isArray(mValue) ? mValue : [mValue]);
                            self.options.validate.rules[sField][sName] = mValue;
                            self.options.validate.messages[sField][sName] = sCustomMessage || sRuleErrorMessage || oOptions.validation.message || sDefaultErrorMessage
                        })
                    })
                }
                if ("validation" in oOptions && (oOptions.validation.remote === true || oOptions.validation.remote === "true")) {
                    self.options.validate.rules[sField].server = true
                }
            }, this));
            $(this.element).validate($.extend(this.options.validate, {
                submitHandler: $.proxy(this.submit, this),
                invalidHandler: $.proxy(this.formInvalidHandler, this),
                showErrors: $.proxy(this.formShowErrors, this),
                onfocusout: $.proxy(this.elementFocusOut, this),
                onclick: $.proxy(this.elementClick, this),
                onkeyup: $.proxy(this.elementKeyUp, this),
                highlight: $.proxy(this.elementError, this),
                success: $.proxy(this.elementSuccess, this),
                errorPlacement: $.proxy(this.elementErrorPlacement, this)
            }));
            this.jqValidator = this.element.data("validator");
            $(window).delegate(self.element, "resize", function() {
                clearTimeout(self.iTimerId);
                self.iTimerId = setTimeout($.proxy(self._addHelpTooltip, self), 250)
            })
        },
        _addHelpTooltip: function() {
            var jqModal = this.element.closest(".ak-modal-wrapper");
            _.each(this.element.find(":input"), $.proxy(function(elInput) {
                var jqInput = $(elInput);
                var oOptions = jqInput.akOptions();
                if (_.isEmpty(oOptions) || !("tooltip" in oOptions)) return;
                var jqBtnPopup = jqInput.nextAll(".ak-btn-popup:visible");
                if (jqBtnPopup.length) {
                    if (!jqInput.data("btnpopued") && !jqInput.nextAll(".ak-tooltip").length) {
                        var oldOptions = jqInput.ak_tooltip("getOptions");
                        jqInput.nextAll(".ak-btn-popup:visible").ak_tooltip(_.merge(oldOptions, {
                            manual: true,
                            tooltip: {
                                position: {
                                    my: "center left",
                                    at: "center right",
                                    viewport: $(window)
                                },
                                show: {
                                    event: "click",
                                    delay: 0,
                                    effect: false
                                },
                                hide: {
                                    event: "click unfocus",
                                    delay: 0,
                                    effect: false
                                }
                            }
                        }));
                        jqInput.data("btnpopued", true)
                    }
                    if (jqInput.data("ankama-ak_tooltip") && jqModal.length) jqInput.ak_tooltip("destroy");
                    else jqInput.ak_tooltip("disable")
                } else if (jqInput.data("btnpopued")) {
                    jqInput.ak_tooltip("enable")
                } else if (jqInput.attr("type") == "text" || jqInput.attr("type") == "password" || jqInput.attr("type") == "email") {
                    jqInput.ak_tooltip("disable")
                }
            }, this))
        },
        submit: function(oForm, oEvent) {
            var self = this,
                jqElement = $(this.element);
            if (self.beforeSubmit(arguments) === false) return false;
            if (jqElement.triggerHandler("beforesubmit") === false) return false;
            var jqSubmits = self.element.find("input[type=submit]");
            if (self.options.ajaxSubmit && $.support.pjax) {
                jqSubmits.addClass(self.options.disabledClass);
                if (self.element.parents(".qtip").length) self.element.parents(".qtip").qtip("disable");
                $(self.options.pjaxSettings.container).one("pjax:requested", function() {
                    if (self.element.parents(".qtip").length) self.element.parents(".qtip").qtip("enable");
                    jqSubmits.removeClass(self.options.disabledClass);
                    self._onSubmitted.apply(self, arguments)
                });
                $(self.options.pjaxSettings.container).one("pjax:end", function() {
                    if (self.element.parents(".qtip").length) {
                        setTimeout(function() {
                            self.element.parents(".qtip").qtip("api").reposition(null, false)
                        }, 0)
                    }
                });
                jqElement.ak_ajaxloader();
                jqElement.ak_ajaxloader("option", "pjax", _.merge(jqElement.ak_ajaxloader("option", "pjax"), self.options.pjaxSettings));
                jqElement.ak_ajaxloader("processSubmit", oEvent)
            } else {
                jqSubmits.addClass(self.options.disabledClass);
                oForm.submit()
            }
        },
        _reset: function(fCallback) {
            var self = this,
                jqElement = $(this.element);
            jqElement.get(0).reset();
            jqElement.find(".ak-cascading").each(function(iIndex, elInput) {
                $(".form-group", this).each(function(iIndex, elFormGroup) {
                    if (iIndex > 0) $(elFormGroup).remove()
                })
            });
            jqElement.find(".ak-select").each(function(iIndex, elInput) {
                $(this).trigger("chosen:updated")
            });
            if (_.isFunction(fCallback)) fCallback.call(self)
        },
        _onSubmitted: function(oEvent, oResult, sStatus, oXhr, oOptions) {},
        beforeSubmit: function() {
            return !(this._invalidServersValidations.length > 0)
        },
        getField: function(sName) {
            return this.element.find(':input[name="' + sName + '"]')
        },
        elementFocusOut: function(elElement, jqEvent) {
            $.validator.defaults.onfocusout.apply(this.jqValidator, arguments)
        },
        elementClick: function(elElement, jqEvent) {
            $.validator.defaults.onclick.apply(this.jqValidator, arguments)
        },
        elementKeyUp: function(elElement, jqEvent) {
            var self = this;
            if (jqEvent.keyCode === 9 && this.jqValidator.elementValue(elElement) === "") {
                return false
            }
        },
        elementError: function(elElement, sErrorClass, sValidClass) {
            var self = this,
                jqElement = $(elElement);
            jqElement.closest(".form-group").removeClass("has-success").addClass("has-error");
            self.toogleTooltip(jqElement, true)
        },
        elementUnError: function(elElement, sErrorClass, sValidClass) {
            var self = this,
                jqElement = $(elElement);
            jqElement.closest(".form-group").removeClass("has-error")
        },
        elementSuccess: function(jqLabel, elElement) {
            var self = this,
                jqElement = $(elElement);
            if (elElement.type !== "checkbox") jqLabel.closest(".form-group").removeClass("has-error").addClass("has-success");
            else jqLabel.closest(".form-group").removeClass("has-error");
            self.toogleTooltip(jqElement, false)
        },
        elementErrorPlacement: function(jqLabelError, jqElement) {
            $(jqLabelError).addClass("control-label");
            if (!jqElement.is(":checkbox")) {
                var jqScript = jqElement.next('script[type="application/json"]').first();
                $(jqLabelError).insertAfter(jqScript.length > 0 ? jqScript : jqElement)
            } else $(jqLabelError).insertAfter(jqElement.parent())
        },
        toogleTooltip: function(jqElement, bEnable) {
            var oOptions = jqElement.akOptions();
            if (!_.isEmpty(oOptions) && "tooltip" in oOptions) {
                bEnable = !_.isUndefined(bEnable) && _.isBoolean(bEnable) ? bEnable : true;
                if (!jqElement.data("ankama-ak_tooltip")) return;
                if (!jqElement.data("ankama-ak_tooltip")) return;
                if (bEnable) {
                    jqElement.ak_tooltip("enable");
                    jqElement.ak_tooltip("show")
                } else {
                    jqElement.ak_tooltip("hide");
                    jqElement.ak_tooltip("disable")
                }
            }
        },
        beforeSerialize: function(jqForm, oOptions) {},
        formInvalidHandler: function(jqEvent, jqValidator) {},
        formShowErrors: function(oErrorMap, aErrorList) {
            this.jqValidator.defaultShowErrors()
        },
        remoteValidation: function(value, element, param) {
            var self = this;
            var rules = _.clone(self.options.validate.rules[element.name]);
            delete rules.server;
            var oServer = _.clone(self.options.server);
            var bJSON = oServer.contentType === "application/json";
            var oParams = _.merge(oServer, {
                url: self.element.attr("action") || self.options.server.url || window.location.href,
                type: self.element.attr("method") || "POST",
                beforeSend: function(oXhr, oOptions) {
                    if (!bJSON) oXhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
                    $(element).parent(".controls").addClass("working")
                },
                complete: function() {
                    $(element).parent(".controls").removeClass("working")
                },
                data: {
                    sTestField: $(element).data("alt-fieldname") || $(element).attr("name"),
                    aFormData: self.element.serializeObject()
                },
                dataFilter: $.proxy(self._parseResult, self)
            }, param);
            if (bJSON && _.isObject(oParams.data)) {
                oParams.data = JSON.stringify(oParams.data)
            }
            var jqElement = self._getAlternativeElement(element),
                element = jqElement.get(0),
                value = jqElement.val();
            return $.validator.methods.remote.call(self.jqValidator, value, element, oParams)
        },
        _parseResult: function(sData) {
            var self = this,
                oResult = $.parseJSON(sData),
                isValid = oResult.result.valid === true,
                element = this.jqValidator.findByName(oResult.result.field).get(0);
            var jqElement = $(element);
            if (isValid && this._invalidServersValidations.indexOf(element.name) > -1) {
                this._invalidServersValidations.splice(this._invalidServersValidations.indexOf(element.name), 1)
            }
            jqElement.data("validate-result", oResult);
            this.jqValidator.stopRequest(element, isValid);
            if (!isValid) {
                if (this._invalidServersValidations.indexOf(element.name) == -1) this._invalidServersValidations.push(element.name);
                if (this._oErrorFieldHandlers && typeof this._oErrorFieldHandlers[element.name] != "undefined") {
                    this._oErrorFieldHandlers[element.name].apply(this, [element, oResult])
                }
                this.jqValidator.settings.messages[self._getOriginalElement(element).get(0).name].server = oResult.result.errors[element.name];
                this.jqValidator.settings.messages[element.name].server = oResult.result.errors[element.name];
                return '"' + oResult.result.errors[element.name].replace(/"/g, "&quot;") + '"'
            }
            return true
        },
        _getAlternativeElement: function(element) {
            var self = this,
                jqElement = $(element);
            if (jqElement.data("alt-fieldname")) return $(':input[name="' + jqElement.data("alt-fieldname") + '"]');
            return jqElement
        },
        _getOriginalElement: function(element) {
            var self = this,
                jqElement = $(element);
            if (jqElement.data("ori-fieldname")) return $(':input[name="' + jqElement.data("ori-fieldname") + '"]', self.element);
            return jqElement
        }
    });
    var oDefaultErrorMessages = {
        fr: "[FR] Ce champ contient des erreurs",
        en: "This field contains errors",
        de: "[DE] This field contains errors",
        es: "[ES] This field contains errors",
        it: "[IT] This field contains errors",
        pt: "[PT] This field contains errors"
    };
    var oErrorMessages = {
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
            fr: $.validator.format("[FR] Please enter no more than {0} characters."),
            en: $.validator.format("Please enter no more than {0} characters."),
            de: $.validator.format("[DE] Please enter no more than {0} characters."),
            es: $.validator.format("[ES] Please enter no more than {0} characters."),
            it: $.validator.format("[IT] Please enter no more than {0} characters."),
            pt: $.validator.format("[PT] Please enter no more than {0} characters.")
        },
        minlength: {
            fr: $.validator.format("[FR] Please enter at least {0} characters."),
            en: $.validator.format("Please enter at least {0} characters."),
            de: $.validator.format("[DE] Please enter at least {0} characters."),
            es: $.validator.format("[ES] Please enter at least {0} characters."),
            it: $.validator.format("[IT] Please enter at least {0} characters."),
            pt: $.validator.format("[PT] Please enter at least {0} characters.")
        },
        rangelength: {
            fr: $.validator.format("[FR] Please enter a value between {0} and {1} characters long."),
            en: $.validator.format("Please enter a value between {0} and {1} characters long."),
            de: $.validator.format("[DE] Please enter a value between {0} and {1} characters long."),
            es: $.validator.format("[ES] Please enter a value between {0} and {1} characters long."),
            it: $.validator.format("[IT] Please enter a value between {0} and {1} characters long."),
            pt: $.validator.format("[PT] Please enter a value between {0} and {1} characters long.")
        },
        range: {
            fr: $.validator.format("[FR] Please enter a value between {0} and {1}."),
            en: $.validator.format("Please enter a value between {0} and {1}."),
            de: $.validator.format("[DE] Please enter a value between {0} and {1}."),
            es: $.validator.format("[ES] Please enter a value between {0} and {1}."),
            it: $.validator.format("[IT] Please enter a value between {0} and {1}."),
            pt: $.validator.format("[PT] Please enter a value between {0} and {1}.")
        },
        max: {
            fr: $.validator.format("[FR] Please enter a value less than or equal to {0}."),
            en: $.validator.format("Please enter a value less than or equal to {0}."),
            de: $.validator.format("[DE] Please enter a value less than or equal to {0}."),
            es: $.validator.format("[ES] Please enter a value less than or equal to {0}."),
            it: $.validator.format("[IT] Please enter a value less than or equal to {0}."),
            pt: $.validator.format("[PT] Please enter a value less than or equal to {0}.")
        },
        min: {
            fr: $.validator.format("[FR] Please enter a value greater than or equal to {0}."),
            en: $.validator.format("Please enter a value greater than or equal to {0}."),
            de: $.validator.format("[DE] Please enter a value greater than or equal to {0}."),
            es: $.validator.format("[ES] Please enter a value greater than or equal to {0}."),
            it: $.validator.format("[IT] Please enter a value greater than or equal to {0}."),
            pt: $.validator.format("[PT] Please enter a value greater than or equal to {0}.")
        }
    };
    var oDefaultErrorMessages = {
        fr: "[FR] Ce champ contient des erreurs",
        en: "This field contains errors",
        de: "[DE] This field contains errors",
        es: "[ES] This field contains errors",
        it: "[IT] This field contains errors",
        pt: "[PT] This field contains errors"
    };
    var oErrorMessages = {
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
            fr: $.validator.format("[FR] Please enter no more than {0} characters."),
            en: $.validator.format("Please enter no more than {0} characters."),
            de: $.validator.format("[DE] Please enter no more than {0} characters."),
            es: $.validator.format("[ES] Please enter no more than {0} characters."),
            it: $.validator.format("[IT] Please enter no more than {0} characters."),
            pt: $.validator.format("[PT] Please enter no more than {0} characters.")
        },
        minlength: {
            fr: $.validator.format("[FR] Please enter at least {0} characters."),
            en: $.validator.format("Please enter at least {0} characters."),
            de: $.validator.format("[DE] Please enter at least {0} characters."),
            es: $.validator.format("[ES] Please enter at least {0} characters."),
            it: $.validator.format("[IT] Please enter at least {0} characters."),
            pt: $.validator.format("[PT] Please enter at least {0} characters.")
        },
        rangelength: {
            fr: $.validator.format("[FR] Please enter a value between {0} and {1} characters long."),
            en: $.validator.format("Please enter a value between {0} and {1} characters long."),
            de: $.validator.format("[DE] Please enter a value between {0} and {1} characters long."),
            es: $.validator.format("[ES] Please enter a value between {0} and {1} characters long."),
            it: $.validator.format("[IT] Please enter a value between {0} and {1} characters long."),
            pt: $.validator.format("[PT] Please enter a value between {0} and {1} characters long.")
        },
        range: {
            fr: $.validator.format("[FR] Please enter a value between {0} and {1}."),
            en: $.validator.format("Please enter a value between {0} and {1}."),
            de: $.validator.format("[DE] Please enter a value between {0} and {1}."),
            es: $.validator.format("[ES] Please enter a value between {0} and {1}."),
            it: $.validator.format("[IT] Please enter a value between {0} and {1}."),
            pt: $.validator.format("[PT] Please enter a value between {0} and {1}.")
        },
        max: {
            fr: $.validator.format("[FR] Please enter a value less than or equal to {0}."),
            en: $.validator.format("Please enter a value less than or equal to {0}."),
            de: $.validator.format("[DE] Please enter a value less than or equal to {0}."),
            es: $.validator.format("[ES] Please enter a value less than or equal to {0}."),
            it: $.validator.format("[IT] Please enter a value less than or equal to {0}."),
            pt: $.validator.format("[PT] Please enter a value less than or equal to {0}.")
        },
        min: {
            fr: $.validator.format("[FR] Please enter a value greater than or equal to {0}."),
            en: $.validator.format("Please enter a value greater than or equal to {0}."),
            de: $.validator.format("[DE] Please enter a value greater than or equal to {0}."),
            es: $.validator.format("[ES] Please enter a value greater than or equal to {0}."),
            it: $.validator.format("[IT] Please enter a value greater than or equal to {0}."),
            pt: $.validator.format("[PT] Please enter a value greater than or equal to {0}.")
        }
    };
    $(document).bind("ready widgetcreate", function(oEvent) {
        $.ankama.ak_simpleform.prototype.enhanceWithin(oEvent.target)
    })
})();
(function(oContext, undefined) {
    var $ = require("ankama.widget"),
        _ = require("lodash");
    $.widget("ankama.ak_form_certification", $.ankama.ak_simpleform, {
        options: {
            initSelector: ".ak-form-certificationnonused"
        },
        _create: function() {
            var self = this;
            self.options.ajaxSubmit = false;
            self._superApply(arguments);
            var jqStateSelect = $("#state", self.element),
                aGroups = jqStateSelect.find("optgroup");
            console.log(jqStateSelect);
            $("#country").change(function() {
                var sCountry = $(this).val().toUpperCase();
                jqStateSelect.empty();
                if (sCountry == "AU" || sCountry == "CA" || sCountry == "US") {
                    $("#p_state").show();
                    oOptions = jqStateSelect.akOptions();
                    console.log("oOptions", oOptions);
                    jqStateSelect.find("option").remove();
                    jqStateSelect.append(new Option("", ""));
                    $.each(oOptions["state"][sCountry], function(index, value) {
                        jqStateSelect.append(new Option(value, index))
                    });
                    console.log(jqStateSelect)
                } else {
                    $("#p_state").hide();
                    jqStateSelect.val("")
                }
            }).trigger("change");
            $("#no_phone").click(function() {
                $("#gsm").val("");
                $("#gsmfieldset").hide();
                $("#phonefieldset").show();
                $(this).hide()
            });
            $(self.element).find('input[type="text"], select, textarea').on("focus", function(oEvent) {
                $(self.element).find(".ak-certification-fieldset-info").removeClass("open");
                $(oEvent.target).closest(".ak-fieldset").find(".ak-certification-fieldset-info").addClass("open")
            })
        },
        _init: function() {
            this._superApply(arguments);
            this.jqValidator.settings.ignore = ""
        },
        bConfirmed: false,
        setConfirmed: function(bConfirmed) {
            var self = this;
            self.bConfirmed = bConfirmed
        },
        beforeSubmit: function() {
            var self = this;
            if (!this._super()) return false;
            if (self.bConfirmed) return true;
            $(".ak-modal-certification-confirmation").ak_modal("open");
            $(".ak-modal-certification-confirmation").find(".ak-identity-name").html($(self.element).find('input[name="fname"]').val() + " " + $(self.element).find('input[name="lname"]').val().toUpperCase());
            return false
        }
    });
    $(document).bind("ready widgetcreate", function(oEvent) {
        $.ankama.ak_form_certification.prototype.enhanceWithin(oEvent.target)
    })
})(this);
! function(oContext, undefined) {
    "use strict";
    var $, _;
    $ = require("ankama.widget");
    _ = require("lodash");
    $.widget("ankama.ak_subscription_compare", $.ankama.widget, {
        options: {
            initSelector: ".ak-subscription-compare"
        },
        _create: function() {
            var self = this;
            self.element.on("click", function(oEvent) {
                oEvent.preventDefault();
                if (!$(".ak-subscription-compare-modal iframe").attr("src")) $(".ak-subscription-compare-modal iframe").attr("src", self.element.attr("href") + "?iframe=1");
                $(".ak-subscription-compare-modal").closest(".ak-modal-wrapper").css({
                    "max-width": "90%",
                    left: "5%"
                })
            })
        }
    });
    $(document).bind("ready widgetcreate", function(oEvent) {
        $.ankama.ak_subscription_compare.prototype.enhanceWithin(oEvent.target)
    })
}(this);
(function(undefined) {
    var $ = require("jquery"),
        _ = require("lodash"),
        complexify = require("jquery.complexify");
    $.widget("ankama.ak_form_account", $.ankama.ak_registerform, {
        options: {
            initSelector: ".ak-form-accountnonused",
            ajaxSubmit: false
        },
        _create: function() {
            var self = this;
            if ($(self.element).hasClass("ak-ajaxloader")) {
                self.options.ajaxSubmit = true
            }
            self._superApply(arguments);
            self.element.find(".ak-form-remove").on("click", function() {
                self.element.find("input[name=postback]").val("remove")
            })
        },
        elementError: function(elElement, sErrorClass, sValidClass) {
            var self = this,
                jqElement = $(elElement);
            jqElement.closest("fieldset").next("label.ak-error-radio").removeClass("hidden");
            self._superApply(arguments)
        },
        elementUnError: function(elElement, sErrorClass, sValidClass) {
            var self = this,
                jqElement = $(elElement);
            jqElement.closest("fieldset").next("label.ak-error-radio").addClass("hidden");
            self._superApply(arguments)
        },
        elementSuccess: function(oLabel, oElement) {
            var self = this;
            self._superApply(arguments);
            $(oElement).closest(".form-group").find(".error").remove()
        }
    });
    $(document).bind("ready widgetcreate", function(oEvent) {
        $.ankama.ak_form_account.prototype.enhanceWithin(oEvent.target)
    })
})();