(function($) {
    $.entwine("ss", function($) {
        $('.cms-container').entwine({
            OpenGridFieldToggles: {},
            saveTabState: function() {
                var $that = this,
                    OpenGridFieldToggles = $that.getOpenGridFieldToggles();

                $that._super();

                $that.find('.ss-gridfield.ss-gridfield-editable-rows').each(function() {
                    var $this = $(this),
                        openToggles = $this.getOpenToggles();

                    if(openToggles.length)
                        OpenGridFieldToggles[$this.attr('id')] = $this.getOpenToggles();
                });
            },
            restoreTabState: function(overrideStates) {
                var $that = this,
                    OpenGridFieldToggles = $that.getOpenGridFieldToggles();

                $that._super(overrideStates);

                $.each(OpenGridFieldToggles, function(id, openToggles) {
                    $that.find('#' + id + '.ss-gridfield.ss-gridfield-editable-rows').reopenToggles(openToggles);
                });

                $that.setOpenGridFieldToggles({});
            }
        });

        $(".ss-gridfield").entwine({
            showNoItemsMessage: function() {
                if(this.find('.ss-gridfield-items:first').children().not('.ss-gridfield-no-items').length === 0) {
                    this.find('.ss-gridfield-no-items').show();
                }
            }
        });

        $(".gridfield-help-button-dialog").entwine({
            loadDialog: function(deferred) {
                var dialog = this.addClass("loading").children(".ui-dialog-content").empty();

                deferred.done(function(data) {
                    dialog.html(data).parent().removeClass("loading");
                });
            }
        });

        $(".ss-gridfield .gridfield-help-button").entwine({
            onclick: function() {
                var dialog = $("<div></div>").appendTo("body").dialog({
                    modal: false,
                    resizable: true,
                    width: 600,
                    height: 600,
                    close: function() {
                        $(this).dialog("destroy").remove();
                    }
                });

                dialog.parent().addClass("gridfield-help-button-dialog").loadDialog(
                    $.get(this.prop("href"))
                );

                dialog.data("grid", this.closest(".ss-gridfield"));

                return false;
            }
        });

        $(".ss-gridfield-treeView--toggle").entwine({
            onclick: function() {
                return false;
            }
        });

        $(".ss-gridfield-hasOneSelector-reset_button[data-relation]").entwine({
            onclick: function() {
                this
                    .parents('.ss-gridfield:first')
                    .find('td[data-relation="' + this.data('relation') + '"] ')
                    .each(function() {
                        $(this).find('input').prop('checked', false);
                });

                return false;
            }
        });

        $(".ss-gridfield-hasOneSelector-toggle[data-relation]").entwine({
            onclick: function() {
                var checked = this.prop('checked');

                this
                    .parents('.ss-gridfield:first')
                    .find('td[data-relation="' + this.data('relation') + '"] ')
                    .each(function() {
                        $(this).find('input').prop('checked', !checked);
                    });
            }
        });

        $(".ss-gridfield.ss-gridfield-add-inline-extended--table").entwine({
            reload: function(opts, success) {
                var $grid  = this,
                    $added = $grid.find("tbody:first").find(".ss-gridfield-inline-new-extended--row").detach(),
                    args = arguments;

                this._super(opts, function() {
                    if($added.length) {
                        $added.appendTo($grid.find("tbody:first"));
                        $grid.find("tbody:first").children(".ss-gridfield-no-items:first").hide();
                    }

                    if(success) success.apply($grid, args);
                });
            },
            onaddnewinlinextended: function(e, $trigger) {
                if(e.target != this[0])
                    return;

                var $tbody = this.find("tbody:first"),
                    num = this.data("add-inline-num") || 1;

                if($trigger && $trigger.data('ajax')) {
                    //if(!$trigger.hasClass('ss-gridfield-add-inline-extended--loading')) {
                        var isConstructive = $trigger.hasClass('ss-ui-action-constructive'),
                            $classSelector = $trigger.siblings('.ss-gridfield-inline-new-extended--class-selector:first'),
                            data ={
                                '_datanum': num
                            };

                        if($classSelector.length) {
                            if(!$classSelector.val()) {
                                alert('Please select a type to create');
                                return;
                            }

                            data[this.attr('id') + '_modelClass'] = $classSelector.val();
                        }

                        $trigger.addClass('ss-gridfield-add-inline-extended--loading disabled ss-ui-button-loading');
                        $trigger.find('.ui-icon').addClass('ss-ui-loading-icon');

                        if(isConstructive)
                            $trigger.removeClass('ss-ui-action-constructive');

                        $.ajax({
                            url:      $trigger[0].href,
                            dataType: 'html',
                            data: data,
                            success:  function (data) {
                                $trigger.removeClass('ss-gridfield-add-inline-extended--loading disabled ss-ui-button-loading');
                                $trigger.find('.ui-icon').removeClass('ss-ui-loading-icon');

                                var $data = $(data);
                                $data.find('ss-gridfield-editable-row--toggle');
                                $tbody.append($data);
                                $tbody.children(".ss-gridfield-no-items:first").hide();
                                $data.find("input:first").focus();

                                if(isConstructive)
                                    $trigger.addClass('ss-ui-action-constructive');
                            },
                            error:    function (e) {
                                alert(ss.i18n._t('GRIDFIELD.ERRORINTRANSACTION'));
                                $trigger.removeClass('ss-gridfield-add-inline-extended--loading disabled');
                            }
                        });
                    //}
                }
                else {
                    var tmpl = window.tmpl,
                        row = this.find(".ss-gridfield-add-inline-extended--template:last");

                    tmpl.cache[this[0].id + "-ss-gridfield-add-inline-extended--template"] = tmpl(row.html());

                    var item = $(tmpl(this[0].id + "-ss-gridfield-add-inline-extended--template", {num: num}));

                    $tbody.append(item);
                    $tbody.children(".ss-gridfield-no-items:first").hide();

                    item.find("input:first").focus();
                }

                this.data("add-inline-num", num + 1);
            }
        });

        $(".ss-gridfield-add-new-inline-extended--button").entwine({
            onclick: function() {
                this.getGridField().trigger("addnewinlinextended", [this]);
                return false;
            }
        });

        $(".ss-gridfield-inline-new-extended--row-delete").entwine({
            onclick: function() {
                var $grid = this.parents('.ss-gridfield:first');

                if(confirm(ss.i18n._t("GridFieldExtensions.CONFIRMDEL", "Are you sure you want to delete this?"))) {
                    this.parents('tbody:first')
                        .find('tr[data-inline-new-extended-row=' +
                            this.parents('tr[data-inline-new-extended-row]:first').data('inlineNewExtendedRow') + ']'
                        ).remove();

                    $grid.showNoItemsMessage();
                }

                return false;
            }
        });

        $(".ss-gridfield-add-inline-extended--toggle").entwine({
            onclick: function() {
                var $parent = this.parents('tr:first');

                this.toggleClass('ss-gridfield-add-inline-extended--toggle_open');

                $parent.parent().find('.ss-gridfield-inline-new-extended--row--has-columns[data-inline-new-extended-row=' + $parent.data('inlineNewExtendedRow') + ']').toggleClass('ss-gridfield-inline-new-extended--row--has-columns_open');

                return false;
            }
        });

        $(".ss-gridfield.ss-gridfield-editable-rows").entwine({
            reload: function (opts, success) {
                var $grid = this,
                    openToggles = $grid.getOpenToggles(),
                    args = arguments;

                this._super(opts, function () {
                    $grid.reopenToggles(openToggles);
                    if (success) success.apply($grid, args);
                });
            },
            getOpenToggles: function() {
                var $grid = this,
                    openToggles = [];

                if($grid.hasClass('ss-gridfield-editable-rows_disableToggleState'))
                    return openToggles;

                $grid.find(".ss-gridfield-editable-row--toggle_open").each(function(key) {
                    var $this = $(this),
                        $holder = $this.parents('td:first'),
                        $parent = $this.parents('tr:first'),
                        $currentGrid = $parent.parents('.ss-gridfield:first'),
                        $editable = $parent.next();

                    if(!$editable.hasClass('ss-gridfield-editable-row--row') || $editable.data('id') != $parent.data('id') || $editable.data('class') != $parent.data('class')) {
                        $editable = null;
                    }
                    else if($currentGrid.hasClass('ss-gridfield-editable-rows_disableToggleState')) {
                        return true;
                    }

                    openToggles[key] =  {
                        link: $holder.data('link')
                    };

                    if($editable) {
                        $editable.find('.ss-tabset.ui-tabs').each(function() {
                            if(!openToggles[key].tabs)
                                openToggles[key].tabs = {};

                            openToggles[key].tabs[this.id] = $(this).tabs('option', 'selected');
                        });

                        if($currentGrid.hasClass('ss-gridfield-editable-rows_allowCachedToggles'))
                            openToggles[key].row = $editable.detach();
                    }
                });

                return openToggles;
            },
            reopenToggles: function(openToggles) {
                var $grid = this,
                    openTabsInToggle = function(currentToggle, $row) {
                        if(currentToggle.hasOwnProperty('tabs') && currentToggle.tabs) {
                            $.each(currentToggle.tabs, function(key, value) {
                                $row.find('#' + key + '.ss-tabset.ui-tabs').tabs({
                                    active: value
                                });
                            });
                        }
                    };

                if($grid.hasClass('ss-gridfield-editable-rows_disableToggleState'))
                    return;

                $.each(openToggles, function(key) {
                    if(openToggles[key].hasOwnProperty('link') && openToggles[key].link) {
                        var $toggleHolder = $grid.find("td.ss-gridfield-editable-row--icon-holder[data-link='" + openToggles[key].link + "']");

                        if(!$toggleHolder.length)
                            return true;
                    }
                    else  {
                        return true;
                    }

                    if(openToggles[key].hasOwnProperty('row') && openToggles[key].row) {
                        var $parent = $toggleHolder.parents('tr:first');

                        $toggleHolder
                            .find(".ss-gridfield-editable-row--toggle")
                            .addClass('ss-gridfield-editable-row--toggle_loaded ss-gridfield-editable-row--toggle_open');

                        if(!$parent.next().hasClass('ss-gridfield-editable-row--row'))
                            $parent.after(openToggles[key].row);
                    }
                    else if(openToggles[key].hasOwnProperty('link') && openToggles[key].link) {
                        $toggleHolder.find(".ss-gridfield-editable-row--toggle").trigger('click', function($newRow) {
                            $grid.find('.ss-gridfield.ss-gridfield-editable-rows').reopenToggles(openToggles);
                            openTabsInToggle(openToggles[key], $newRow);
                        }, false);
                    }
                });
            }
        });

        $(".ss-gridfield-editable-row--toggle").entwine({
            onclick: function(e, callback, noFocus) {
                var $this = this,
                    $holder = $this.parents('td:first'),
                    link = $holder.data('link'),
                    $parent = $this.parents('tr:first');

                if($parent.hasClass('ss-gridfield-editable-row--loading'))
                    return false;

                if(link && !$this.hasClass('ss-gridfield-editable-row--toggle_loaded')) {
                    $parent.addClass('ss-gridfield-editable-row--loading');

                    $.ajax({
                        url: link,
                        dataType: 'html',
                        success: function(data) {
                            var $data = $(data);
                            $this.addClass('ss-gridfield-editable-row--toggle_loaded ss-gridfield-editable-row--toggle_open');
                            $parent.removeClass('ss-gridfield-editable-row--loading');
                            $parent.after($data);

                            if(noFocus !== false)
                                $data.find("input:first").focus();

                            if(typeof callback === 'function') {
                                callback($data, $this, $parent);
                            }
                        },
                        error: function(e) {
                            alert(ss.i18n._t('GRIDFIELD.ERRORINTRANSACTION'));
                            $parent.removeClass('ss-gridfield-editable-row--loading');
                        }
                    });
                }
                else if(link) {
                    var $editable = $parent.next();

                    if($editable.hasClass('ss-gridfield-editable-row--row') && $editable.data('id') == $parent.data('id') && $editable.data('class') == $parent.data('class')) {
                        $this.toggleClass('ss-gridfield-editable-row--toggle_open');

                        if($this.hasClass('ss-gridfield-editable-row--toggle_open')) {
                            $editable.removeClass('ss-gridfield-editable-row--row_hide').find("input:first").focus();
                        }
                        else {
                            $editable.addClass('ss-gridfield-editable-row--row_hide');
                        }
                    }
                }

                return false;
            }
        });

        var sliderTimeStamp;
        $(".ss-gridfield-range-slider--field.rangeslider.has-rangeslider").entwine({
            onchange: function(e) {
                if(e.timeStamp == sliderTimeStamp)
                    return;

                sliderTimeStamp = e.timeStamp;

                var $parent = this.parents('.ss-gridfield-range-slider:first'),
                    $button = $parent.find('.ss-gridfield-range-slider--button');

                if($button.length) {
                    $button.click();
                }
            }
        });
    });
})(jQuery);
