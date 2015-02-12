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
                    $added = $grid.find(".ss-gridfield-inline-new-extended--row").detach();

                this._super(opts, function() {
                    if($added.length) {
                        $added.appendTo($grid.find("tbody"));
                        $grid.find(".ss-gridfield-no-items").hide();
                    }

                    if(success) success.apply($grid, arguments);
                });
            },
            onaddnewinlinextended: function() {
                var tmpl = window.tmpl;
                var row = this.find(".ss-gridfield-add-inline-extended--template");
                var num = this.data("add-inline-num") || 1;

                tmpl.cache["ss-gridfield-add-inline-extended--template"] = tmpl(row.html());

                this.find("tbody").append(tmpl("ss-gridfield-add-inline-extended--template", { num: num }));
                this.find(".ss-gridfield-no-items").hide();
                this.data("add-inline-num", num + 1);
            }
        });

        $(".ss-gridfield-add-new-inline-extended--button").entwine({
            onclick: function() {
                this.getGridField().trigger("addnewinlinextended");
                return false;
            }
        });
	});
})(jQuery);
