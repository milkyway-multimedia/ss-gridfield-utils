! function(i) {
    i.entwine("ss", function(i) {
        i(".js-mwm-gridfield-saveall").entwine({
            onmatch: function(){
                // Remove 'changed' state from form anytime a SaveAll button is created.
                i(this[0].form).removeClass('changed');
                this._super();
            }
        }),
        i(".ss-gridfield").entwine({
            showNoItemsMessage: function() {
                0 === this.find(".ss-gridfield-items:first").children().not(".ss-gridfield-no-items").length && this.find(".ss-gridfield-no-items").show()
            }
        }), i(".gridfield-help-button-dialog").entwine({
            loadDialog: function(i) {
                var e = this.addClass("loading").children(".ui-dialog-content").empty();
                i.done(function(i) {
                    e.html(i).parent().removeClass("loading")
                })
            }
        }), i(".ss-gridfield .gridfield-help-button").entwine({
            onclick: function() {
                var e = i("<div></div>").appendTo("body").dialog({
                    modal: !1,
                    resizable: !0,
                    width: 600,
                    height: 600,
                    close: function() {
                        i(this).dialog("destroy").remove()
                    }
                });
                return e.parent().addClass("gridfield-help-button-dialog").loadDialog(i.get(this.prop("href"))), e.data("grid", this.closest(".ss-gridfield")), !1
            }
        }), window.GridFieldUtils_Modal = {
            $trigger: null,
            $dialog: null,
            $grid: null,
            reload: !1,
            closeOnStateChange: !1
        }, window.GridFieldUtils_Modal.open = function(e) {
            var t = this;
            t.$trigger = e;
            var d = e.closest(".ss-gridfield"),
                s = i('<div class="ss-gridfield-modal"></div>'),
                n = i.extend({}, {
                    width: .8,
                    height: .8,
                    resizable: !1
                }, d.data("modalDimensions") || {}),
                a = function(i) {
                    i.find("html").addClass("ss-gridfield-modal--body"), i.find(".cms-menu").remove()
                };
            t.$grid = d, t.$dialog = s, t.$dialog.ssdialog({
                iframeUrl: e[0].href,
                width: n.width < 1 ? n.width * window.innerWidth : n.width,
                minWidth: n.width < 1 ? n.width * window.innerWidth : n.width,
                height: n.height < 1 ? n.height * window.innerHeight : n.height,
                minHeight: n.height < 1 ? n.height * window.innerHeight : n.height,
                resizable: n.resizable,
                close: function() {
                    t.close()
                }
            }), t.$dialog.find("iframe").addClass("loading").off("load.reloading").one("load.reloading", function(e) {
                var t = i(this),
                    d = t.contents();
                d.find(".ss-loading-screen").show(), d.find("body").addClass("loading"), t.addClass("ss-gridfield-modal_reloaded").attr("src", function(i, e) {
                    return e
                })
            }), t.$dialog.find("iframe").on("load.gridfield", function() {
                var e = i(this),
                    d = e.contents();
                a(d), e.hasClass("ss-gridfield-modal_reloaded") && e.removeClass("loading"), t.initModal(d)
            }), t.$dialog.ssdialog("open")
        }, window.GridFieldUtils_Modal.initModal = function(e) {
            e || (e = this.$dialog.find("iframe").contents()), e.find(".ss-gridfield-modal-form:first").each(function() {
                var t = i(this),
                    d = t.data("nonModalLink"),
                    s = t.data("modalLink"),
                    n = function(i, e) {
                        i.attr(e, i.attr(e).replace(d, s))
                    };
                e.find('[src^="' + d + '"]').each(function() {
                    n(i(this), "src")
                }), e.find('[action^="' + d + '"]').each(function() {
                    n(i(this), "action")
                }), e.find('[href^="' + d + '"]').each(function() {
                    n(i(this), "href")
                })
            })
        }, window.GridFieldUtils_Modal.close = function(i) {
            this.$trigger.Dialog = null, this.$dialog.ssdialog("destroy").remove(), (this.reload || i) && this.$grid.reload(), this.$trigger = null, this.$grid = null, this.$dialog = null, this.reload = !1
        }, window.GridFieldUtils_Modal.reloadGrid = function() {
            this.$grid.reload()
        }, window.GridFieldUtils_Modal.reloadOnClose = function() {
            this.reload = !0
        }, window.GridFieldUtils_Modal.doCloseOnStateChange = function() {
            this.closeOnStateChange = !0
        }, i(".ss-gridfield-modal--button").entwine({
            Dialog: null,
            onclick: function(e) {
                e.preventDefault(), e.stopPropagation(), window.GridFieldUtils_Modal.open(i(this))
            }
        }), i(".ss-gridfield-modal--body").entwine({
            onadd: function() {
                window.parent.GridFieldUtils_Modal.initModal()
            }
        }), i(".ss-gridfield-modal--body .cms_backlink a").entwine({
            onclick: function(i) {
                i.preventDefault(), i.stopPropagation(), window.parent.GridFieldUtils_Modal.close()
            }
        }), i(".ss-gridfield-modal--body .breadcrumbs-wrapper a").entwine({
            onclick: function(i) {
                i.preventDefault(), i.stopPropagation(), this[0].href == window.parent.location.href ? window.parent.GridFieldUtils_Modal.close() : window.parent.location.href = this[0].href
            }
        }), i(".ss-gridfield-modal--body form").entwine({
            onsubmit: function() {
                window.parent.GridFieldUtils_Modal.reloadOnClose()
            },
            onaftersubmitform: function(i, e, t) {
                window.parent.GridFieldUtils_Modal.initModal()
            }
        }), i(".ss-gridfield-modal--body .cms-container").entwine({
            onafterstatechange: function() {
                window.parent.GridFieldUtils_Modal.initModal()
            },
            handleAjaxResponse: function(e, t, d, s) {
                window.parent.GridFieldUtils_Modal.initModal();
                var n = this.find(".ss-gridfield-modal-form:first"),
                    a = n.data("nonModalLink"),
                    l = n.data("modalLink");
                if (a && l && d.getResponseHeader("X-ControllerURL")) {
                    var r = i("base").attr("href"),
                        o = d.getResponseHeader("X-ControllerURL"),
                        g = i.path.isAbsoluteUrl(o) ? o : i.path.makeUrlAbsolute(o, r);
                    return g.startsWith(i.path.isAbsoluteUrl(a) ? a : i.path.makeUrlAbsolute(a, r)) && (g = g.replace(a, l)), void(window.location.href = g)
                }
                return this._super(e, t, d, s)
            }
        }), i(".ss-gridfield-hasOneSelector-reset_button[data-relation]").entwine({
            onclick: function() {
                return this.parents(".ss-gridfield:first").find('td[data-relation="' + this.data("relation") + '"] ').each(function() {
                    i(this).find("input").prop("checked", !1)
                }), !1
            }
        }), i(".ss-gridfield-hasOneSelector-toggle[data-relation]").entwine({
            onclick: function() {
                var e = this.prop("checked");
                this.parents(".ss-gridfield:first").find('td[data-relation="' + this.data("relation") + '"] ').each(function() {
                    i(this).find("input").prop("checked", !e)
                })
            }
        }), i(".ss-gridfield.ss-gridfield-add-inline-extended--table").entwine({
            reload: function(i, e) {
                var t = this,
                    d = t.find("tbody:first").find(".ss-gridfield-inline-new-extended--row").detach(),
                    s = arguments;
                this._super(i, function() {
                    d.length && (t.data("prepend") ? d.prependTo(t.find("tbody:first")) : d.appendTo(t.find("tbody:first")), t.find("tbody:first").children(".ss-gridfield-no-items:first").hide()), e && e.apply(t, s)
                })
            },
            onaddnewinlinextended: function(e, t) {
                if (e.target == this[0]) {
                    var d = this,
                        s = d.find("tbody:first"),
                        n = d.data("add-inline-num") || 1;
                    if (t && t.data("ajax")) {
                        var a = t.hasClass("ss-ui-action-constructive"),
                            l = t.siblings(".ss-gridfield-inline-new-extended--class-selector:first").find(":input"),
                            r = {
                                _datanum: n
                            };
                        if (l.length) {
                            if (!l.val()) return void alert("Please select a type to create");
                            r[d.attr("id") + "_modelClass"] = l.val()
                        }
                        t.addClass("ss-gridfield-add-inline-extended--loading disabled ss-ui-button-loading"), t.find(".ui-icon").addClass("ss-ui-loading-icon"), a && t.removeClass("ss-ui-action-constructive"), i.ajax({
                            url: t[0].href,
                            dataType: "html",
                            data: r,
                            success: function(e) {
                                t.removeClass("ss-gridfield-add-inline-extended--loading disabled ss-ui-button-loading"), t.find(".ui-icon").removeClass("ss-ui-loading-icon");
                                var n = i(e);
                                n.find("ss-gridfield-editable-row--toggle"), d.data("prepend") ? s.prepend(n) : s.append(n), s.children(".ss-gridfield-no-items:first").hide(), n.find("input:first").focus(), a && t.addClass("ss-ui-action-constructive")
                            },
                            error: function(i) {
                                alert(ss.i18n._t("GRIDFIELD.ERRORINTRANSACTION")), t.removeClass("ss-gridfield-add-inline-extended--loading disabled")
                            }
                        })
                    } else {
                        var o = window.tmpl,
                            g = this.find(".ss-gridfield-add-inline-extended--template:last");
                        o.cache[this[0].id + "-ss-gridfield-add-inline-extended--template"] = o(g.html());
                        var f = i(o(this[0].id + "-ss-gridfield-add-inline-extended--template", {
                            num: n
                        }));
                        s.append(f), s.children(".ss-gridfield-no-items:first").hide(), f.find("input:first").focus()
                    }
                    this.data("add-inline-num", n + 1)
                }
            }
        }), i(".ss-gridfield-add-new-inline-extended--button").entwine({
            onclick: function() {
                return this.getGridField().trigger("addnewinlinextended", [this]), !1
            }
        }), i(".ss-gridfield-inline-new-extended--row-delete").entwine({
            onclick: function() {
                var i = this.parents(".ss-gridfield:first");
                return confirm(ss.i18n._t("GridFieldExtensions.CONFIRMDEL", "Are you sure you want to delete this?")) && (this.parents("tbody:first").find("tr[data-inline-new-extended-row=" + this.parents("tr[data-inline-new-extended-row]:first").data("inlineNewExtendedRow") + "]").remove(), i.showNoItemsMessage()), !1
            }
        }), i(".ss-gridfield-add-inline-extended--toggle").entwine({
            onclick: function() {
                var i = this.parents("tr:first");
                return this.toggleClass("ss-gridfield-add-inline-extended--toggle_open"), i.parent().find(".ss-gridfield-inline-new-extended--row--has-columns[data-inline-new-extended-row=" + i.data("inlineNewExtendedRow") + "]").toggleClass("ss-gridfield-inline-new-extended--row--has-columns_open"), !1
            }
        }), i(".cms-container").entwine({
            OpenGridFieldToggles: {},
            saveTabState: function() {
                var e = this,
                    t = e.getOpenGridFieldToggles();
                e._super(), e.find(".ss-gridfield.ss-gridfield-editable-rows").each(function() {
                    var e = i(this),
                        d = e.getOpenToggles();
                    d.length && (t[e.attr("id")] = e.getOpenToggles())
                })
            },
            restoreTabState: function(e) {
                var t = this,
                    d = t.getOpenGridFieldToggles();
                t._super(e), i.each(d, function(i, e) {
                    t.find("#" + i + ".ss-gridfield.ss-gridfield-editable-rows").reopenToggles(e)
                }), t.find(".ss-gridfield-editable-row--toggle_start").click(), t.setOpenGridFieldToggles({})
            }
        }), i(".ss-gridfield.ss-gridfield-editable-rows").entwine({
            reload: function(i, e) {
                var t = this,
                    d = t.getOpenToggles(),
                    s = arguments;
                this._super(i, function() {
                    t.reopenToggles(d), t.find(".ss-gridfield-editable-row--toggle_start").click(), e && e.apply(t, s)
                })
            },
            getOpenToggles: function() {
                var e = this,
                    t = [];
                return e.hasClass("ss-gridfield-editable-rows_disableToggleState") ? t : (e.find(".ss-gridfield-editable-row--toggle_open").each(function(e) {
                    var d = i(this),
                        s = d.parents("td:first"),
                        n = d.parents("tr:first"),
                        a = n.parents(".ss-gridfield:first"),
                        l = n.next();
                    if (l.hasClass("ss-gridfield-editable-row--row") && l.data("id") == n.data("id") && l.data("class") == n.data("class")) {
                        if (a.hasClass("ss-gridfield-editable-rows_disableToggleState")) return !0
                    } else l = null;
                    t[e] = {
                        link: s.data("link")
                    }, l && (l.find(".ss-tabset.ui-tabs").each(function() {
                        t[e].tabs || (t[e].tabs = {}), t[e].tabs[this.id] = i(this).tabs("option", "selected")
                    }), a.hasClass("ss-gridfield-editable-rows_allowCachedToggles") && (t[e].row = l.detach()))
                }), t)
            },
            reopenToggles: function(e) {
                var t = this,
                    d = function(e, t) {
                        e.hasOwnProperty("tabs") && e.tabs && i.each(e.tabs, function(i, e) {
                            t.find("#" + i + ".ss-tabset.ui-tabs").tabs({
                                active: e
                            })
                        })
                    };
                t.hasClass("ss-gridfield-editable-rows_disableToggleState") || i.each(e, function(i) {
                    if (!e[i].hasOwnProperty("link") || !e[i].link) return !0;
                    var s = t.find("td.ss-gridfield-editable-row--icon-holder[data-link='" + e[i].link + "']");
                    if (!s.length) return !0;
                    if (e[i].hasOwnProperty("row") && e[i].row) {
                        var n = s.parents("tr:first");
                        s.find(".ss-gridfield-editable-row--toggle").addClass("ss-gridfield-editable-row--toggle_loaded ss-gridfield-editable-row--toggle_open"), n.next().hasClass("ss-gridfield-editable-row--row") || n.after(e[i].row)
                    } else e[i].hasOwnProperty("link") && e[i].link && s.find(".ss-gridfield-editable-row--toggle").trigger("click", function(s) {
                        t.find(".ss-gridfield.ss-gridfield-editable-rows").reopenToggles(e), d(e[i], s)
                    }, !1)
                })
            }
        }), i(".ss-gridfield-editable-row--toggle").entwine({
            onclick: function(e, t, d) {
                var s = this,
                    n = s.parents("td:first"),
                    a = n.data("link"),
                    l = s.parents("tr:first");
                if (s.removeClass("ss-gridfield-editable-row--toggle_start"), l.hasClass("ss-gridfield-editable-row--loading")) return !1;
                if (a && !s.hasClass("ss-gridfield-editable-row--toggle_loaded")) l.addClass("ss-gridfield-editable-row--loading"), i.ajax({
                    url: a,
                    dataType: "html",
                    success: function(e) {
                        var n = i(e);
                        s.addClass("ss-gridfield-editable-row--toggle_loaded ss-gridfield-editable-row--toggle_open"), l.addClass("ss-gridfield-editable-row--reference").removeClass("ss-gridfield-editable-row--loading"), l.after(n), n.find(".ss-gridfield-editable-row--toggle_start").click(), d !== !1 && n.find("input:first").focus(), "function" == typeof t && t(n, s, l)
                    },
                    error: function(i) {
                        alert(ss.i18n._t("GRIDFIELD.ERRORINTRANSACTION")), l.removeClass("ss-gridfield-editable-row--loading")
                    }
                });
                else if (a) {
                    var r = l.next();
                    r.hasClass("ss-gridfield-editable-row--row") && r.data("id") == l.data("id") && r.data("class") == l.data("class") && (s.toggleClass("ss-gridfield-editable-row--toggle_open"), s.hasClass("ss-gridfield-editable-row--toggle_open") ? r.removeClass("ss-gridfield-editable-row--row_hide").find("input:first").focus() : r.addClass("ss-gridfield-editable-row--row_hide"))
                }
                return !1
            }
        });
        var e;
        i(".ss-gridfield-range-slider--field.rangeslider.has-rangeslider").entwine({
            onchange: function(i) {
                if (i.timeStamp != e) {
                    e = i.timeStamp;
                    var t = this.parents(".ss-gridfield-range-slider:first"),
                        d = t.find(".ss-gridfield-range-slider--button");
                    d.length && d.click()
                }
            }
        }), i(".add-existing-picker-actions--add-items").entwine({
            onclick: function(i, e) {
                var t = this.getDialog();
                this.addCloseDialogIfRequired(t), this.addItems(null, null, "", function() {
                    e && e(), t.data("grid").reload(), t.dialog("close")
                }), i.preventDefault()
            },
            getDialog: function() {
                return this.closest(".add-existing-search-dialog").children(".ui-dialog-content:first")
            },
            addItems: function(e, t, d, s, n) {
                var a = this;
                e || (e = a.getDialog()), t || (t = e.data("items-to-add") || []), t.length || (statusMessage(ss.i18n._t("GridField_AddExistingPicker.NO_ITEMS", "No items selected"), "error"), e.find(".add-existing-search-items").children().length || e.dialog("close")), d || (d = e.find(".add-existing-picker-items").data("add-link")), s || (s = function() {
                    e.data("grid") && e.data("grid").reload()
                }), a.clearItemsToAdd(e), n || e.parent().addClass("loading loading_hiddenContents"), i.post(d, {
                    ids: t
                }, s).always(function() {
                    e.parent().removeClass("loading loading_hiddenContents")
                })
            },
            undoItems: function(e, t, d, s, n) {
                var a = this;
                t || (t = a.getDialog()), e.length && (d || (d = t.find(".add-existing-picker-items").data("undo-link")), s || (s = function() {
                    t.data("grid") && t.data("grid").reload()
                }), n || t.parent().addClass("loading loading_hiddenContents"), i.post(d, {
                    ids: e
                }, function() {
                    var i = [].slice.call(arguments);
                    i.push(t), s.apply(this, i)
                }).always(function() {
                    t.parent().removeClass("loading loading_hiddenContents")
                }))
            },
            clearItemsToAdd: function(i) {
                i || (i = this.getDialog()), i.data("items-to-add", [])
            },
            addCloseDialogIfRequired: function(e) {
                if (e || (e = this.getDialog()), !e.data("onClose")) {
                    var t = this;
                    e.on("dialogbeforeclose", function(e, d) {
                        var s = i(this),
                            n = s.data("items-to-add") || [];
                        n.length && confirm(ss.i18n.sprintf(ss.i18n._t("GridField_AddExistingPicker.CONFIRM", "Would you like to add %s selected items to the current list?"), n.length)) ? t.addItems(s, n, "", function() {
                            s.data("grid").reload()
                        }) : t.clearItemsToAdd(s)
                    }), e.data("onClose", !0)
                }
            }
        }), i(".add-existing-picker-item--undo-holder .add-existing-picker-item--undo").entwine({
            onclick: function(e) {
                var t = this.data("id"),
                    d = this.data("undoLink"),
                    s = this.data("title"),
                    n = i('.add-existing-picker-items[data-undo-link="' + d + '"]').find('.add-existing-picker-item--link[data-id="' + t + '"]');
                n.undoItem(t, !0, function(e, t, d, a) {
                    a.data("grid").reload(), i.noticeRemove(i(".notice-item.add-existing-picker-item--undo-holder")), statusMessage(ss.i18n.sprintf(ss.i18n._t("GridField_AddExistingPicker.ITEM_REMOVED", "%s has been removed."), s)), n.parent("li:first").show()
                }), e.preventDefault()
            }
        }), i(".add-existing-search-dialog .add-existing-search-items.add-existing-picker-items a").entwine({
            onclick: function(e) {
                var t = this.getButton(),
                    d = t.getDialog(),
                    s = d.data("grid"),
                    n = this.parent(),
                    a = d.data("items-to-add") || [],
                    l = this.data("id");
                if (s && s.hasClass("ss-gridfield-add-existing-picker_async")) {
                    var r = d.find(".add-existing-picker-items").data("undo-link"),
                        o = this.text();
                    n.addClass("loading add-existing-picker-item_toAdd"), t.addItems(d, [l], "", function(e) {
                        var t = e && e.hasOwnProperty("keep") && e.keep;
                        d.data("grid") && d.data("grid").reload(), n.removeClass("add-existing-picker-item_toAdd loading"), t || n.hide(), i.noticeAdd({
                            text: ss.i18n.sprintf(ss.i18n._t("GridField_AddExistingPicker.ITEM_ADDED", "%s has been added. %s"), o, ss.i18n.sprintf('<button type="button" class="add-existing-picker-item--undo" data-id="%s" data-undo-link="%s" data-title="%s" data-keep="%s">%s</button>', l, r, o, t ? "true" : "false", ss.i18n._t("UNDO", "Undo"))),
                            type: "add-existing-picker-item--undo-holder",
                            stayTime: 5e3
                        })
                    }, !0)
                } else t.addCloseDialogIfRequired(d), -1 === a.indexOf(l) ? (a.push(l), n.addClass("add-existing-picker-item_toAdd")) : (a.splice(a.indexOf(l), 1), n.removeClass("add-existing-picker-item_toAdd")), d.data("items-to-add", a);
                e.preventDefault()
            },
            getButton: function() {
                return this.closest(".add-existing-search-dialog").find(".add-existing-picker-actions--add-items:first")
            },
            undoItem: function(i, e, t) {
                i || (i = this.data("id"));
                var d = this.getButton();
                d.undoItems([i], null, "", t, e)
            }
        }), i("select.ss-gridfield--tags-column--selector.select2").entwine({
            onmatch: function(e) {
                this._super();
                var t = this,
                    d = t.parents(".gridfield:first"),
                    s = t.data("gfNewLink");
                s && this.data("select2").on("select", function(e) {
                    e && e.data.hasOwnProperty("isNew") && e.data.isNew && (t.prop("disabled", !0), i.ajax({
                        type: "POST",
                        url: s.replace("{{ tag }}", e.data.id),
                        success: function(i) {},
                        complete: function() {
                            t.prop("disabled", !1), d.reload()
                        }
                    }))
                })
            },
            configuration: function(e) {
                var t = this._super(e);
                return t.createTag = function(e) {
                    var t = i.trim(e.term);
                    return "" === t ? null : {
                        id: t,
                        text: t,
                        isNew: !0
                    }
                }, t
            }
        }), i(".ss-gridfield-timeline.ss-gridfield-editable-rows .ss-gridfield-editable-row--toggle").entwine({
            onmatch: function() {
                this._super(), this.parents("tr:first").addClass("ss-gridfield-timeline--row_with-details")
            }
        }), i(".ss-gridfield-timeline.ss-gridfield-editable-rows tr.ss-gridfield-item").entwine({
            onclick: function(e) {
                e.target && (i(e.target).is("a,button,input") || i(e.target).parent().is("a,button,input")) || this.find(".ss-gridfield-editable-row--toggle:first").first().click()
            }
        }), i(".ss-gridfield").entwine({
            reload: function(e, t) {
                var d = this;
                this._super(e, function(e) {
                    d.attr("class", i(e).attr("class")), t && t.apply(this, arguments)
                })
            }
        })
    })
}(jQuery);