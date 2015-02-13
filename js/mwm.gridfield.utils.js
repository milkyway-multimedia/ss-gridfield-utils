(function($) {
	$.entwine("ss", function($) {
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

        $(".ss-gridfield.ss-gridfield-add-inline-extended--table").entwine({
            reload: function(opts, success) {
                var $grid  = this,
                    $added = $grid.find("tbody:first").find(".ss-gridfield-inline-new-extended--row").detach();

                this._super(opts, function() {
                    if($added.length) {
                        $added.appendTo($grid.find("tbody:first"));
                        $grid.find("tbody:first").children(".ss-gridfield-no-items:first").hide();
                    }

                    if(success) success.apply($grid, arguments);
                });
            },
            onaddnewinlinextended: function(e) {
                if(e.target != this[0])
                    return;

                var tmpl = window.tmpl,
                    row = this.find(".ss-gridfield-add-inline-extended--template:last"),
                    num = this.data("add-inline-num") || 1;

                tmpl.cache["ss-gridfield-add-inline-extended--template"] = tmpl(row.html());

                var item = $(tmpl("ss-gridfield-add-inline-extended--template", { num: num }));

                this.find("tbody:first").append(item);
                this.find("tbody:first").children(".ss-gridfield-no-items:first").hide();
                this.data("add-inline-num", num + 1);

                item.find("input:first").focus();
            }
        });

        $(".ss-gridfield-add-new-inline-extended--button").entwine({
            onclick: function() {
                this.getGridField().trigger("addnewinlinextended");
                return false;
            }
        });

        $(".ss-gridfield-inline-new-extended--row-delete").entwine({
            onclick: function() {
                if(confirm(ss.i18n._t("GridFieldExtensions.CONFIRMDEL", "Are you sure you want to delete this?"))) {
                    this.parents('.ss-gridfield-inline-new-extended--row:first').remove();
                }

                return false;
            }
        });

        $(".ss-gridfield-editable-row--toggle").entwine({
            onclick: function() {
                var $this = this,
                    $holder = $this.parents('td:first'),
                    link = $holder.data('link'),
                    $parent = $this.parents('tr:first');

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
                            $data.find("input:first").focus();
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
                            $editable.removeClass('ss-gridfield-editable-row--row_hide');
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
